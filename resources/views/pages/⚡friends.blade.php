<?php

use App\Models\Friendship;
use App\Models\User;
use Livewire\Component;

new #[\Livewire\Attributes\Layout('layouts::bare')] class extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function title(): string
    {
        return $this->user->name."'s Friends";
    }

    public function friends(): \Illuminate\Support\Collection
    {
        return $this->user->acceptedFriends();
    }

    public function unfriend(int $friendUserId): void
    {
        if (auth()->id() !== $this->user->id) {
            return;
        }

        Friendship::where('status', 'accepted')
            ->where(function ($query) use ($friendUserId) {
                $query->where('user_id', auth()->id())->where('friend_id', $friendUserId);
            })->orWhere(function ($query) use ($friendUserId) {
                $query->where('user_id', $friendUserId)->where('friend_id', auth()->id());
            })->delete();
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $this->title()])
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
											<h5>{{ $user->name }}'s Friends <span>{{ $this->friends()->count() }}</span></h5>
										</div>
									</div>
								</div>
							</div>
						</div><!-- title block -->
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->friends() as $friend)
									<x-pitnik.friend-box :user="$friend" :mode="auth()->id() === $user->id ? 'friend' : 'view'" wire:key="friend-{{ $friend->id }}" />
								@empty
									<div class="col-lg-12">
										<p>No friends yet.</p>
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
