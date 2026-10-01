<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VideoChannel;

class VideoChannelPolicy
{
    public function update(User $user, VideoChannel $channel): bool
    {
        return $user->id === $channel->user_id;
    }

    public function delete(User $user, VideoChannel $channel): bool
    {
        return $user->id === $channel->user_id;
    }

    /** Only the owner publishes to their channel. */
    public function upload(User $user, VideoChannel $channel): bool
    {
        return $user->id === $channel->user_id;
    }
}
