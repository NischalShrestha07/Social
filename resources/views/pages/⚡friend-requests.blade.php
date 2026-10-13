<?php

use App\Models\Friendship;
use App\Notifications\FriendRequestAccepted;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Friend Requests'), \Livewire\Attributes\Layout('layouts::bare')] class extends Component
{
    public function requests(): \Illuminate\Support\Collection
    {
        return auth()->user()->pendingRequestsReceived();
    }

    public function acceptRequest(int $friendshipId): void
    {
        $friendship = Friendship::where('friend_id', auth()->id())->where('status', 'pending')->findOrFail($friendshipId);
        $friendship->update(['status' => 'accepted']);

        $friendship->user->notify(new FriendRequestAccepted(auth()->user()));
    }

    public function declineRequest(int $friendshipId): void
    {
        Friendship::where('friend_id', auth()->id())->where('status', 'pending')->findOrFail($friendshipId)->delete();
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Friend Requests'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="title-block">
								<div class="row">
									<div class="col-lg-6">
										<div class="align-left">
											<h5>Friend Requests <span>{{ $this->requests()->count() }}</span></h5>
										</div>
									</div>
								</div>
							</div>
						</div><!-- title block -->
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->requests() as $friendship)
									<x-pitnik.friend-box :user="$friendship->user" mode="request" :friendshipId="$friendship->id" wire:key="request-{{ $friendship->id }}" />
								@empty
									<div class="col-lg-12">
										<p>No pending friend requests.</p>
									</div>
								@endforelse
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
