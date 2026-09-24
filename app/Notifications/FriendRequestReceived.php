<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FriendRequestReceived extends Notification
{
    use Queueable;

    public function __construct(public User $actor) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'actor_name' => $this->actor->name,
            'actor_avatar' => $this->actor->avatarUrl(),
            'message' => "{$this->actor->name} sent you a friend request.",
            'url' => route('friend-requests'),
        ];
    }
}
