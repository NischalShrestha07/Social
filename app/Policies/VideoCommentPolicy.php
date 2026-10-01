<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VideoComment;

class VideoCommentPolicy
{
    /** Authors delete their own comments; video moderators can remove anything on their video. */
    public function delete(User $user, VideoComment $comment): bool
    {
        return $user->id === $comment->user_id
            || $user->id === $comment->video->user_id
            || $user->id === $comment->video->channel->user_id;
    }
}
