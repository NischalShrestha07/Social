<?php

namespace App\Livewire;

use App\Models\Friendship;
use App\Notifications\FriendRequestAccepted;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class FriendRequestBell extends Component
{
    public function acceptRequest(int $friendshipId): void
    {
        $friendship = Friendship::where('friend_id', auth()->id())->findOrFail($friendshipId);
        $friendship->update(['status' => 'accepted']);

        $friendship->user->notify(new FriendRequestAccepted(auth()->user()));
    }

    public function declineRequest(int $friendshipId): void
    {
        Friendship::where('friend_id', auth()->id())->findOrFail($friendshipId)->delete();
    }

    public function render(): View
    {
        return view('livewire.friend-request-bell', [
            'requests' => auth()->user()->pendingRequestsReceived()->take(5),
        ]);
    }
}
