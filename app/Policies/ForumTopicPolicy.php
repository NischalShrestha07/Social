<?php

namespace App\Policies;

use App\Models\ForumTopic;
use App\Models\User;

class ForumTopicPolicy
{
    /** The topic's author can edit and delete their own topic. */
    public function update(User $user, ForumTopic $topic): bool
    {
        return $user->id === $topic->user_id;
    }

    public function delete(User $user, ForumTopic $topic): bool
    {
        return $user->id === $topic->user_id;
    }

    /**
     * Anyone can reply to an open topic. Locked topics stay readable — the author
     * and whoever locked it are the only ones who can post again.
     */
    public function reply(User $user, ForumTopic $topic): bool
    {
        return ! $topic->is_locked || $user->id === $topic->user_id || $user->id === $topic->locked_by;
    }

    /** Locking is a moderation action: the topic author or the one who locked it. */
    public function toggleLock(User $user, ForumTopic $topic): bool
    {
        return $topic->is_locked
            ? $user->id === $topic->locked_by
            : $user->id === $topic->user_id;
    }

    public function pin(User $user, ForumTopic $topic): bool
    {
        return $user->id === $topic->user_id;
    }
}
