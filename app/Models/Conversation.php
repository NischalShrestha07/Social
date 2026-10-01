<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

#[Fillable(['last_message_at'])]
class Conversation extends Model
{
    protected function casts(): array
    {
        return ['last_message_at' => 'datetime'];
    }

    /** @return BelongsToMany<User, $this, ConversationUser, 'pivot'> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(ConversationUser::class)->withPivot('last_read_at')->withTimestamps();
    }

    /** @return HasMany<Message, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /** @return HasOne<Message, $this> */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public static function findBetween(User $a, User $b): ?self
    {
        return static::whereHas('users', fn ($q) => $q->whereKey($a->id))
            ->whereHas('users', fn ($q) => $q->whereKey($b->id))
            ->has('users', '=', 2)
            ->first();
    }

    public static function between(User $a, User $b): self
    {
        return DB::transaction(function () use ($a, $b) {
            if ($existing = static::findBetween($a, $b)) {
                return $existing;
            }

            $conversation = static::create();
            $conversation->users()->attach([$a->id, $b->id]);

            return $conversation;
        });
    }

    public function otherParticipant(User $me): ?User
    {
        /** @var User|null $other */
        $other = $this->users->firstWhere('id', '!=', $me->id);

        return $other;
    }

    public function hasParticipant(User $user): bool
    {
        return $this->users()->whereKey($user->id)->exists();
    }

    public function unreadCountFor(User $user): int
    {
        /** @var ConversationUser|null $cu */
        $cu = $this->users->firstWhere('id', $user->id);
        $lastReadAt = $cu?->last_read_at;

        return $this->messages()
            ->where('user_id', '!=', $user->id)
            ->when($lastReadAt, fn ($q) => $q->where('created_at', '>', $lastReadAt))
            ->count();
    }

    public function markReadFor(User $user): void
    {
        $this->users()->updateExistingPivot($user->id, ['last_read_at' => now()]);
    }
}
