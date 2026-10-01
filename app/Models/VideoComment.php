<?php

namespace App\Models;

use Database\Factories\VideoCommentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $video_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $body
 */
#[Fillable(['video_id', 'user_id', 'parent_id', 'body'])]
class VideoComment extends Model
{
    /** @use HasFactory<VideoCommentFactory> */
    use HasFactory;

    /** @return BelongsTo<Video, $this> */
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<VideoComment, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(VideoComment::class, 'parent_id');
    }

    /** @return HasMany<VideoComment, $this> */
    public function replies(): HasMany
    {
        return $this->hasMany(VideoComment::class, 'parent_id')->oldest();
    }
}
