<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'username', 'email', 'password', 'bio', 'avatar_path', 'cover_path'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    public function getRouteKeyName(): string
    {
        return 'username';
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->latest();
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class)->withPivot('last_read_at')->withTimestamps();
    }

    public function unreadMessagesCount(): int
    {
        return Message::where('messages.user_id', '!=', $this->id)
            ->join('conversation_user as cu', function ($join) {
                $join->on('cu.conversation_id', '=', 'messages.conversation_id')
                    ->where('cu.user_id', '=', $this->id);
            })
            ->where(fn ($q) => $q->whereNull('cu.last_read_at')->orWhereColumn('messages.created_at', '>', 'cu.last_read_at'))
            ->count();
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class)->latest();
    }

    public function avatarUrl(): string
    {
        return $this->avatar_path
            ? asset('storage/'.$this->avatar_path)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name);
    }

    public function friendshipStatusWith(User $other): string
    {
        if ($other->id === $this->id) {
            return 'self';
        }

        $friendship = Friendship::where(function ($query) use ($other) {
            $query->where('user_id', $this->id)->where('friend_id', $other->id);
        })->orWhere(function ($query) use ($other) {
            $query->where('user_id', $other->id)->where('friend_id', $this->id);
        })->first();

        if (! $friendship) {
            return 'none';
        }

        if ($friendship->status === 'accepted') {
            return 'friends';
        }

        return $friendship->user_id === $this->id ? 'pending_sent' : 'pending_received';
    }

    public function acceptedFriends(): Collection
    {
        $friendIds = Friendship::where('status', 'accepted')
            ->where(function ($query) {
                $query->where('user_id', $this->id)->orWhere('friend_id', $this->id);
            })
            ->get()
            ->map(fn (Friendship $friendship) => $friendship->user_id === $this->id
                ? $friendship->friend_id
                : $friendship->user_id
            );

        return User::whereIn('id', $friendIds)->get();
    }

    public function pendingRequestsReceived(): Collection
    {
        return Friendship::with('user')
            ->where('friend_id', $this->id)
            ->where('status', 'pending')
            ->latest()
            ->get();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        $initials = Str::initials($this->name, true);

        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }
}
