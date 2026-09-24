<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Notifications'), Layout('layouts::bare')] class extends Component
{
    public function mount(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function notifications(): \Illuminate\Support\Collection
    {
        return auth()->user()->notifications()->latest()->get();
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Notifications'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="editing-interest">
								<span class="create-post"><i class="ti-bell"></i> All Notifications</span>
								<div class="notification-box">
									<ul>
										@forelse($this->notifications() as $notification)
											<li wire:key="notif-{{ $notification->id }}">
												<figure><img src="{{ $notification->data['actor_avatar'] }}" alt=""></figure>
												<div class="notifi-meta">
													<p><a href="{{ $notification->data['url'] }}">{{ $notification->data['message'] }}</a></p>
													<span>{{ $notification->created_at->diffForHumans() }}</span>
												</div>
											</li>
										@empty
											<li>
												<div class="notifi-meta">
													<p>No notifications yet.</p>
												</div>
											</li>
										@endforelse
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
