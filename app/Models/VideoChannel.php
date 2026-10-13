<?php

namespace App\Models;

use App\Notifications\NewChannelSubscriber;
use Database\Factories\VideoChannelFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * @property int $user_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $avatar_path
 * @property string|null $banner_path
 * @property int $subscribers_count
 * @property Carbon|null $created_at
 */
#[Fillable(['user_id', 'name', 'slug', 'description', 'avatar_path', 'banner_path'])]
class VideoChannel extends Model
{
    /** @use HasFactory<VideoChannelFactory> */
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @return BelongsTo<User, $this> */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return HasMany<Video, $this> */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    /** @return BelongsToMany<User, $this> */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'channel_subscriptions')->withTimestamps();
    }

    /** Owners do not subscribe to themselves; the counter is the source of truth. */
    public function subscribe(User $user): void
    {
        if ($user->id === $this->user_id) {
            return;
        }

        DB::transaction(function () use ($user) {
            if ($this->subscribers()->where('users.id', $user->id)->exists()) {
                return;
            }

            $this->subscribers()->attach($user->id);
            $this->increment('subscribers_count');
        });

        Notification::send($this->owner, new NewChannelSubscriber($user, $this));
    }

    public function unsubscribe(User $user): void
    {
        DB::transaction(function () use ($user) {
            if (! $this->subscribers()->where('users.id', $user->id)->exists()) {
                return;
            }

            $this->subscribers()->detach($user->id);
            $this->decrement('subscribers_count');
        });
    }

    public function isSubscribedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->subscribers()->where('users.id', $user->id)->exists();
    }

    public function avatarUrl(): string
    {
        return $this->avatar_path
            ? asset('storage/'.$this->avatar_path)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name);
    }

    public function bannerUrl(): ?string
    {
        return $this->banner_path ? asset('storage/'.$this->banner_path) : null;
    }

    /** @param Builder<VideoChannel> $query */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%");
        });
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'channel';
        $slug = $base;
        $suffix = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
