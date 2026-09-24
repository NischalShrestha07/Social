@props(['user', 'mode' => 'friend', 'friendshipId' => null])

<div class="col-lg-3 col-md-6 col-sm-6">
	<div class="friend-box">
		<figure>
			<img src="{{ $user->avatarUrl() }}" alt="">
		</figure>
		<div class="frnd-meta">
			<img src="{{ $user->avatarUrl() }}" alt="">
			<div class="frnd-name">
				<a href="{{ route('profile.show', $user) }}" title="">{{ $user->name }}</a>
				<span>{{ '@'.$user->username }}</span>
			</div>

			@if($mode === 'friend')
				<a class="send-mesg" href="#" title="" wire:click.prevent="unfriend({{ $user->id }})">Unfriend</a>
			@elseif($mode === 'request')
				<a class="send-mesg" href="#" title="" wire:click.prevent="acceptRequest({{ $friendshipId }})">Accept</a>
				<a class="send-mesg" href="#" title="" wire:click.prevent="declineRequest({{ $friendshipId }})" style="margin-left:6px;">Decline</a>
			@endif
		</div>
	</div>
</div>
