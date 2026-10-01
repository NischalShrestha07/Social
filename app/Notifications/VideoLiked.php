<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VideoLiked extends Notification
{
    use Queueable;

    public function __construct(public Video $video, public User $liker) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'actor_name' => $this->liker->name,
            'actor_avatar' => $this->liker->avatarUrl(),
            'message' => $this->liker->name.' liked your video '.$this->video->title.'.',
            'url' => route('videos.show', $this->video),
        ];
    }
}
