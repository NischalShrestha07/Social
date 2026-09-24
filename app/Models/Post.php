<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['user_id', 'group_id', 'page_id', 'body', 'image_path'])]
class Post extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    // Page posts are authored "as the page"; user_id stays the owner for the audit trail.
    public function authorName(): string
    {
        return $this->page_id ? $this->page->name : $this->user->name;
    }

    public function authorAvatar(): string
    {
        return $this->page_id ? $this->page->avatarUrl() : $this->user->avatarUrl();
    }

    public function authorUrl(): string
    {
        return $this->page_id ? route('pages.show', $this->page) : route('profile.show', $this->user);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->latest();
    }

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
