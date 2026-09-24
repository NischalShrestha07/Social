<li wire:poll.10s>
	<a href="{{ route('messages') }}" title="Messages" data-ripple=""><i><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#30305b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-square">
		<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
	</svg></i>@if($unreadCount)<em class="bg-blue">{{ $unreadCount }}</em>@endif</a>
	<div class="dropdowns">
		<span>{{ $unreadCount }} New Messages</span>
		<ul class="drops-menu">
			@forelse($conversations as $conversation)
				@php
					$peer = $conversation->otherParticipant(auth()->user());
					$unread = $conversation->unreadCountFor(auth()->user());
				@endphp
				@continue(! $peer)
				<li wire:key="msg-bell-{{ $conversation->id }}">
					<a href="{{ route('messages', $peer) }}" title="">
						<figure><img src="{{ $peer->avatarUrl() }}" alt=""></figure>
						<div class="mesg-meta">
							<h6>{{ $peer->name }}</h6>
							<span>{{ \Illuminate\Support\Str::limit($conversation->latestMessage?->body, 40) }}</span>
							<i>{{ $conversation->last_message_at?->diffForHumans() }}</i>
						</div>
					</a>
					@if($unread)
						<span class="tag">New</span>
					@endif
				</li>
			@empty
				<li><div class="mesg-meta"><span>No messages yet</span></div></li>
			@endforelse
		</ul>
		<a href="{{ route('messages') }}" title="" class="more-mesg">View All</a>
	</div>
</li>
