<?php

namespace App\Notifications;

use App\Models\Page;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PageLiked extends Notification
{
    use Queueable;

    public function __construct(public User $actor, public Page $page) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'actor_name' => $this->actor->name,
            'actor_avatar' => $this->actor->avatarUrl(),
            'message' => "{$this->actor->name} liked your page {$this->page->name}.",
            'url' => route('pages.show', $this->page),
        ];
    }
}
