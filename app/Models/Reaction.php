<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'type'])]
class Reaction extends Model
{
    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return MorphTo<Model, $this> */
    public function reactable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Insert a reaction without blowing up on the (user_id, reactable) unique
     * index when two requests race (e.g. a double-click on the like button).
     * Returns true when the reaction was created, false when it already exists.
     *
     * @param  Post|Comment|Video|ForumPost  $reactable
     */
    public static function createSafely(Model $reactable, User $user, string $type = 'like'): bool
    {
        try {
            $reactable->reactions()->create(['user_id' => $user->id, 'type' => $type]);

            return true;
        } catch (QueryException $exception) {
            if (! Str::contains((string) $exception->getMessage(), 'UNIQUE')) {
                throw $exception;
            }

            // A concurrent request already recorded this reaction.
            return false;
        }
    }
}
