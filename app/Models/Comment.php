<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

#[Fillable(['post_id', 'user_id', 'body', 'parent_id'])]
class Comment extends Model
{
    protected static function booted(): void
    {
        static::deleting(fn (self $comment) => $comment->reactions()->delete());
    }

    /** @return BelongsTo<Post, $this> */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Comment, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /** @return HasMany<Comment, $this> */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->oldest();
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
}
