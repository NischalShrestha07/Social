<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class MessageBell extends Component
{
    public function render(): View
    {
        $me = auth()->user();

        return view('livewire.message-bell', [
            'conversations' => $me->conversations()
                ->whereNotNull('last_message_at')
                ->with(['users', 'latestMessage'])
                ->orderByDesc('last_message_at')
                ->take(5)
                ->get(),
            'unreadCount' => $me->unreadMessagesCount(),
        ]);
    }
}
