<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;

class VideoPolicy
{
    /**
     * Public and unlisted videos are open to everyone. Private videos stay with
     * the uploader and the channel owner.
     */
    public function view(?User $user, Video $video): bool
    {
        if ($video->visibility !== 'private') {
            return true;
        }

        return $user !== null && ($user->id === $video->user_id || $user->id === $video->channel->user_id);
    }

    /** The uploader manages their video; the channel owner manages the channel. */
    public function update(User $user, Video $video): bool
    {
        return $user->id === $video->user_id;
    }

    public function delete(User $user, Video $video): bool
    {
        return $user->id === $video->user_id;
    }

    /** Only the uploader or the channel owner can moderate comments. */
    public function moderateComments(User $user, Video $video): bool
    {
        return $user->id === $video->user_id || $user->id === $video->channel->user_id;
    }

    public function comment(User $user, Video $video): bool
    {
        return $video->visibility !== 'private' || $user->id === $video->user_id || $user->id === $video->channel->user_id;
    }
}
