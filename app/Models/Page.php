<?php

namespace App\Models;

use App\Notifications\PageLiked;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['owner_id', 'name', 'slug', 'category', 'description', 'website', 'avatar_path', 'cover_path'])]
class Page extends Model
{
    public const CATEGORIES = [
        'Brand or Product',
        'Business',
        'Community',
        'Education',
        'Entertainment',
        'News & Media',
        'Public Figure',
        'Sports',
        'Technology',
        'Other',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'page_likes')->withTimestamps();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class)->latest();
    }

    public function isLikedBy(?User $user): bool
    {
        return $user && $this->likers()->where('users.id', $user->id)->exists();
    }

    // Owners can't like their own page.
    public function like(User $user): void
    {
        if ($user->id === $this->owner_id || $this->isLikedBy($user)) {
            return;
        }

        $this->likers()->attach($user->id);
        $this->owner->notify(new PageLiked($user, $this));
    }

    public function unlike(User $user): void
    {
        $this->likers()->detach($user->id);
    }

    public function coverUrl(): ?string
    {
        return $this->cover_path ? asset('storage/'.$this->cover_path) : null;
    }

    public function avatarUrl(): string
    {
        return $this->avatar_path
            ? asset('storage/'.$this->avatar_path)
            : 'https://ui-avatars.com/api/?background=fa6342&color=fff&name='.urlencode($this->name);
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'page';
        $slug = $base;
        $suffix = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
