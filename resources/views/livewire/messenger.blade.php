<div wire:poll.3s>
	<div class="message-users">
		<div class="message-head">
			<h4>Chat Messages</h4>
		</div>
		<div class="mesg-peple">
			<ul class="nav nav-tabs nav-tabs--vertical msg-pepl-list">
				@forelse($conversations as $conversation)
					@php
						$peer = $conversation->otherParticipant(auth()->user());
						$unread = $conversation->unreadCountFor(auth()->user());
					@endphp
					@continue(! $peer)
					<li class="nav-item {{ $unread ? 'unread' : '' }}" wire:key="conv-{{ $conversation->id }}">
						<a class="{{ $active?->id === $conversation->id ? 'active' : '' }}" href="{{ route('messages', $peer) }}">
							<figure><img src="{{ $peer->avatarUrl() }}" alt=""></figure>
							<div class="user-name">
								<h6>{{ $peer->name }} @if($unread)<em class="bg-blue" style="font-style:normal;color:#fff;border-radius:10px;padding:0 6px;font-size:11px;">{{ $unread }}</em>@endif</h6>
								<span>
									@if($conversation->latestMessage?->user_id === auth()->id())You: @endif{{ \Illuminate\Support\Str::limit($conversation->latestMessage?->body, 30) }}
									- {{ $conversation->last_message_at?->diffForHumans() }}
								</span>
							</div>
						</a>
					</li>
				@empty
					<li class="nav-item"><div class="user-name"><span>No conversations yet.</span></div></li>
				@endforelse
			</ul>
		</div>
	</div>

	<div class="tab-content messenger">
		<div class="tab-pane active fade show">
			@if($other)
				<div class="row merged">
					<div class="col-lg-12">
						<div class="mesg-area-head">
							<div class="active-user">
								<figure><img src="{{ $other->avatarUrl() }}" alt=""></figure>
								<div>
									<h6><a href="{{ route('profile.show', $other) }}">{{ $other->name }}</a></h6>
									<span>{{ '@'.$other->username }}</span>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-8 col-md-8">
						<div
							class="mesge-area"
							x-data="{ stick: true }"
							x-init="$el.scrollTop = $el.scrollHeight; new MutationObserver(() => { if (stick) $el.scrollTop = $el.scrollHeight }).observe($el, { childList: true, subtree: true })"
							x-on:scroll="stick = $el.scrollHeight - $el.scrollTop - $el.clientHeight < 80"
							x-on:message-sent.window="stick = true; $nextTick(() => $el.scrollTop = $el.scrollHeight)"
						>
							@if($messages->isEmpty())
								<div class="empty-chat">
									<div class="no-messages">
										<i class="ti-comments"></i>
										<p>No messages yet. Break the ice and send the first message.</p>
									</div>
								</div>
							@else
								<ul class="conversations">
									@foreach($messages as $message)
										<li class="{{ $message->user_id === auth()->id() ? 'me' : 'you' }}" wire:key="msg-{{ $message->id }}">
											<figure><img src="{{ $message->user->avatarUrl() }}" alt=""></figure>
											<div class="text-box">
												<p>{!! nl2br(e($message->body)) !!}</p>
												<span>{{ $message->created_at->diffForHumans() }}</span>
											</div>
										</li>
									@endforeach
								</ul>
							@endif
						</div>
						<div class="message-writing-box">
							<form wire:submit="send">
								<div class="text-area">
									<input type="text" placeholder="write your message here.." wire:model="body" maxlength="5000" autocomplete="off">
									<button type="submit" wire:loading.attr="disabled" wire:target="send"><i class="fa-regular fa-paper-plane"></i></button>
								</div>
								@error('body') <span style="color:#e0245e;">{{ $message }}</span> @enderror
							</form>
						</div>
					</div>
					<div class="col-lg-4 col-md-4">
						<div class="chater-info">
							<figure><img src="{{ $other->avatarUrl() }}" alt=""></figure>
							<h6><a href="{{ route('profile.show', $other) }}">{{ $other->name }}</a></h6>
							<span>{{ '@'.$other->username }}</span>
							@if($other->bio)
								<div class="userabout">
									<span>About</span>
									<p>{{ $other->bio }}</p>
								</div>
							@endif
						</div>
					</div>
				</div>
			@else
				<div class="row merged">
					<div class="col-lg-12">
						<div class="mesge-area">
							<div class="empty-chat">
								<div class="no-messages">
									<i class="ti-comments"></i>
									<p>No conversation selected. Open someone's profile and hit Message to start chatting.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			@endif
		</div>
	</div>
</div>
