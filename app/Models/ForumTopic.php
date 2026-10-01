<?php

namespace App\Models;

use App\Notifications\ForumPostReplied;
use Database\Factories\ForumTopicFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * @property int $forum_category_id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $body
 * @property bool $is_pinned
 * @property bool $is_locked
 * @property int|null $locked_by
 * @property Carbon|null $locked_at
 * @property Carbon|null $last_posted_at
 */
#[Fillable(['forum_category_id', 'user_id', 'title', 'slug', 'body', 'is_pinned', 'is_locked', 'locked_by', 'locked_at', 'last_posted_at'])]
class ForumTopic extends Model
{
    /** @use HasFactory<ForumTopicFactory> */
    use HasFactory;

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
            'last_posted_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<ForumCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<ForumPost, $this> */
    public function posts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    /**
     * Replies exclude the topic's opening post, which lives in `body`.
     *
     * @return HasMany<ForumPost, $this>
     */
    public function replies(): HasMany
    {
        return $this->posts()->oldest();
    }

    /** @param Builder<ForumTopic> $query */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")->orWhere('body', 'like', "%{$term}%");
        });
    }

    public function isLockedBy(?User $user): bool
    {
        return $this->is_locked && $user !== null && $user->id === $this->locked_by;
    }

    /** Locks stop new replies but leave the locker and author a way back in. */
    public function reply(string $body, ?User $author = null): ForumPost
    {
        $author ??= auth()->user();

        $post = $this->posts()->create([
            'user_id' => $author->id,
            'body' => $body,
        ]);

        $this->update(['last_posted_at' => now()]);

        // Everyone who already took part hears about the new reply, once each.
        $notifiableIds = $this->posts()
            ->where('user_id', '!=', $author->id)
            ->distinct()
            ->pluck('user_id')
            ->push($this->user_id)
            ->unique()
            ->reject(fn ($id) => $id === $author->id);

        Notification::send(User::whereIn('id', $notifiableIds)->get(), new ForumPostReplied($post));

        return $post;
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'topic';
        $slug = $base;
        $suffix = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
