<li>
	<a href="{{ route('notifications') }}" title="Notification" data-ripple="">
		@if($unreadCount)
			<em class="bg-purple">{{ $unreadCount }}</em>
		@endif
		<i><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
			<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
			<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
		</svg></i>
	</a>
	<div class="dropdowns">
		<span>{{ $unreadCount }} New Notifications <a href="#" title="" wire:click.prevent="markAllAsRead">Mark all as read</a></span>
		<ul class="drops-menu">
			@forelse($notifications as $notification)
				<li wire:key="notif-bell-{{ $notification->id }}">
					<a href="{{ $notification->data['url'] }}" title="" wire:click.prevent="markAsRead('{{ $notification->id }}'); window.location.href = '{{ $notification->data['url'] }}'">
						<figure>
							<img src="{{ $notification->data['actor_avatar'] }}" alt="">
						</figure>
						<div class="mesg-meta">
							<span>{{ $notification->data['message'] }}</span>
							<i>{{ $notification->created_at->diffForHumans() }}</i>
						</div>
					</a>
					@if(is_null($notification->read_at))
						<span class="tag">New</span>
					@endif
				</li>
			@empty
				<li><div class="mesg-meta"><span>No notifications yet</span></div></li>
			@endforelse
		</ul>
		<a href="{{ route('notifications') }}" title="" class="more-mesg">View All</a>
	</div>
</li>
