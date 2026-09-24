<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupJoinApproved extends Notification
{
    use Queueable;

    public function __construct(public User $actor, public Group $group) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'actor_name' => $this->actor->name,
            'actor_avatar' => $this->group->avatarUrl(),
            'message' => "Your request to join {$this->group->name} was approved.",
            'url' => route('groups.show', $this->group),
        ];
    }
}
