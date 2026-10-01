<?php

namespace App\Notifications;

use App\Models\VideoComment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VideoCommented extends Notification
{
    use Queueable;

    public function __construct(public VideoComment $comment) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $video = $this->comment->video;
        $parent = $this->comment->parent;

        return [
            'actor_name' => $this->comment->user->name,
            'actor_avatar' => $this->comment->user->avatarUrl(),
            'message' => $parent
                ? $this->comment->user->name.' replied to your comment on '.$video->title.'.'
                : $this->comment->user->name.' commented on '.$video->title.'.',
            'url' => route('videos.show', $video),
        ];
    }
}
