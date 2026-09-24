@props(['group', 'status'])

<div {{ $attributes->merge(['class' => 'col-lg-3 col-md-4 col-sm-6']) }}>
	<div class="group-box">
		<figure>
			<a href="{{ route('groups.show', $group) }}">
				<img src="{{ $group->coverUrl() ?? $group->avatarUrl() }}" alt="" style="height:130px;width:100%;object-fit:cover;">
			</a>
		</figure>
		<a href="{{ route('groups.show', $group) }}" title="">{{ $group->name }}</a>
		<span>
			{{ $group->approved_members_count }} {{ Str::plural('Member', $group->approved_members_count) }}
			· {{ $group->isPrivate() ? 'Private' : 'Public' }}
		</span>
		@switch($status)
			@case('none')
				<button type="button" wire:click="join({{ $group->id }})">{{ $group->isPrivate() ? 'request to join' : 'join group' }}</button>
				@break
			@case('pending')
				<button type="button" wire:click="leave({{ $group->id }})">cancel request</button>
				@break
			@default
				<a href="{{ route('groups.show', $group) }}"><button type="button">view group</button></a>
		@endswitch
	</div>
</div>
