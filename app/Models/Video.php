<?php

namespace App\Models;

use App\Notifications\VideoCommented;
use App\Notifications\VideoLiked;
use Database\Factories\VideoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

/**
 * @property int $video_channel_id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string|null $video_path
 * @property string|null $thumbnail_path
 * @property int|null $duration_seconds
 * @property string $visibility public|unlisted|private
 * @property int $views_count
 * @property Carbon|null $created_at
 */
#[Fillable(['video_channel_id', 'user_id', 'title', 'slug', 'description', 'video_path', 'thumbnail_path', 'duration_seconds', 'visibility'])]
class Video extends Model
{
    /** @use HasFactory<VideoFactory> */
    use HasFactory;

    public const VISIBILITIES = ['public', 'unlisted', 'private'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'duration_seconds' => 'integer',
            'views_count' => 'integer',
        ];
    }

    /** @return BelongsTo<VideoChannel, $this> */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(VideoChannel::class, 'video_channel_id');
    }

    /** @return BelongsTo<User, $this> */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return HasMany<VideoComment, $this> */
    public function comments(): HasMany
    {
        return $this->hasMany(VideoComment::class)->whereNull('parent_id')->latest();
    }

    /**
     * Every comment on the video, replies included.
     *
     * @return HasMany<VideoComment, $this>
     */
    public function allComments(): HasMany
    {
        return $this->hasMany(VideoComment::class);
    }

    /** @return MorphMany<Reaction, $this> */
    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactable');
    }

    /** Only public videos appear in listings; unlisted and private stay off the index. */
    public function isPubliclyVisible(): bool
    {
        return $this->visibility === 'public';
    }

    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->reactions()->where('user_id', $user->id)->where('type', 'like')->exists();
    }

    /** Reactions are polymorphic, so videos share one `Reaction` table with posts. */
    public function toggleLike(User $user): void
    {
        $existing = $this->reactions()->where('user_id', $user->id)->where('type', 'like')->first();

        if ($existing) {
            $existing->delete();

            return;
        }

        $this->reactions()->create(['user_id' => $user->id, 'type' => 'like']);

        Notification::send($this->uploader, new VideoLiked($this, $user));
    }

    public function likesCount(): int
    {
        return $this->reactions()->where('type', 'like')->count();
    }

    public function comment(string $body, User $author, ?VideoComment $parent = null): VideoComment
    {
        // allComments(), because comments() filters out replies and a reply needs a parent_id.
        $comment = $this->allComments()->create([
            'user_id' => $author->id,
            'body' => $body,
            'parent_id' => $parent?->id,
        ]);

        if ($parent && $parent->user_id !== $author->id) {
            Notification::send($parent->user, new VideoCommented($comment));
        }

        if ($parent === null && $this->user_id !== $author->id) {
            Notification::send($this->uploader, new VideoCommented($comment));
        }

        return $comment;
    }

    public function thumbnailUrl(): string
    {
        return $this->thumbnail_path
            ? asset('storage/'.$this->thumbnail_path)
            : 'https://placehold.it/640x360/e8e9f2/9aa0b5?text='.urlencode($this->title);
    }

    public function videoUrl(): ?string
    {
        return $this->video_path ? asset('storage/'.$this->video_path) : null;
    }

    public function durationLabel(): ?string
    {
        if (! $this->duration_seconds) {
            return null;
        }

        $hours = intdiv($this->duration_seconds, 3600);
        $minutes = intdiv($this->duration_seconds % 3600, 60);
        $seconds = $this->duration_seconds % 60;

        return $hours > 0
            ? sprintf('%d:%02d:%02d', $hours, $minutes, $seconds)
            : sprintf('%d:%02d', $minutes, $seconds);
    }

    /** @param Builder<Video> $query */
    public function scopePublic(Builder $query): void
    {
        $query->where('visibility', 'public');
    }

    /** @param Builder<Video> $query */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")->orWhere('description', 'like', "%{$term}%");
        });
    }

    public static function visibilityLabel(?string $visibility): string
    {
        return match ($visibility) {
            'unlisted' => 'Unlisted',
            'private' => 'Private',
            default => 'Public',
        };
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'video';
        $slug = $base;
        $suffix = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
