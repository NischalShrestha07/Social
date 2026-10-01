<?php

namespace App\Notifications;

use App\Models\ForumPost;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ForumPostReplied extends Notification
{
    use Queueable;

    public function __construct(public ForumPost $post) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'actor_name' => $this->post->user->name,
            'actor_avatar' => $this->post->user->avatarUrl(),
            'message' => "{$this->post->user->name} replied to \"{$this->post->topic->title}\".",
            'url' => route('forum.topic', $this->post->topic),
        ];
    }
}
