<?php

namespace App\Livewire;

use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendRequestAccepted;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FriendRequestBell extends Component
{
    public function acceptRequest(int $friendshipId): void
    {
        /** @var User $me */
        $me = Auth::user();
        $friendship = Friendship::where('friend_id', $me->id)->where('status', 'pending')->findOrFail($friendshipId);
        $friendship->update(['status' => 'accepted']);

        /** @var User $requester */
        $requester = $friendship->user;
        $requester->notify(new FriendRequestAccepted($me));
    }

    public function declineRequest(int $friendshipId): void
    {
        /** @var User $me */
        $me = Auth::user();
        Friendship::where('friend_id', $me->id)->where('status', 'pending')->findOrFail($friendshipId)->delete();
    }

    public function render(): View
    {
        return view('livewire.friend-request-bell');
    }
}
