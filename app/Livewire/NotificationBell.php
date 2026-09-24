<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class NotificationBell extends Component
{
    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function markAsRead(string $notificationId): void
    {
        auth()->user()->notifications()->find($notificationId)?->markAsRead();
    }

    public function render(): View
    {
        return view('livewire.notification-bell', [
            'notifications' => auth()->user()->notifications()->latest()->take(5)->get(),
            'unreadCount' => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
