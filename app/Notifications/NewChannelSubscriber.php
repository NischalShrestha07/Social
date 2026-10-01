<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\VideoChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewChannelSubscriber extends Notification
{
    use Queueable;

    public function __construct(public User $subscriber, public VideoChannel $channel) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'actor_name' => $this->subscriber->name,
            'actor_avatar' => $this->subscriber->avatarUrl(),
            'message' => $this->subscriber->name.' subscribed to '.$this->channel->name.'.',
            'url' => route('videos.channel', $this->channel),
        ];
    }
}
