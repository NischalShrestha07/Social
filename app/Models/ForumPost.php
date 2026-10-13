<?php

namespace App\Models;

use Database\Factories\ForumPostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property int $forum_topic_id
 * @property int $user_id
 * @property string $body
 */
#[Fillable(['forum_topic_id', 'user_id', 'body'])]
class ForumPost extends Model
{
    /** @use HasFactory<ForumPostFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::deleting(fn (self $post) => $post->reactions()->delete());
    }

    /** @return BelongsTo<ForumTopic, $this> */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'forum_topic_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return MorphMany<Reaction, $this> */
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if (! $this->relationLoaded('reactions')) {
            return $this->reactions()->where('user_id', $user->id)->where('type', 'like')->exists();
        }

        /** @var Collection<int, Reaction> $reactions */
        $reactions = $this->reactions;

        return $reactions->where('user_id', $user->id)->where('type', 'like')->isNotEmpty();
    }

    /** Reactions are polymorphic, so posts and comments share one `Reaction` table. */
    public function toggleLike(User $user): void
    {
        $existing = $this->reactions()->where('user_id', $user->id)->where('type', 'like')->first();

        if ($existing) {
            $existing->delete();

            return;
        }

        Reaction::createSafely($this, $user);
    }

    public function likesCount(): int
    {
        if (! $this->relationLoaded('reactions')) {
            return $this->reactions()->where('type', 'like')->count();
        }

        /** @var Collection<int, Reaction> $reactions */
        $reactions = $this->reactions;

        return $reactions->where('type', 'like')->count();
    }
}
