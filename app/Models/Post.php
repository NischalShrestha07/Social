<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property int $user_id
 * @property int|null $group_id
 * @property int|null $page_id
 * @property string $body
 * @property string|null $image_path
 */
#[Fillable(['user_id', 'group_id', 'page_id', 'body', 'image_path'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Group, $this> */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /** @return BelongsTo<Page, $this> */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    // Page posts are authored "as the page"; user_id stays the owner for the audit trail.
    public function authorName(): string
    {
        /** @var User|Page $post */
        $post = $this->page_id ? $this->page : $this->user;

        return $post->name;
    }

    public function authorAvatar(): string
    {
        /** @var User|Page $post */
        $post = $this->page_id ? $this->page : $this->user;

        return $post->avatarUrl();
    }

    public function authorUrl(): string
    {
        return $this->page_id ? route('pages.show', $this->page) : route('profile.show', $this->user);
    }

    /** @return HasMany<Comment, $this> */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->latest();
    }

    /** @return MorphMany<Reaction, $this> */
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    public function likesCount(): int
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->reactions()->where('user_id', $user->id)->where('type', 'like')->exists();
    }
}
