@props(['page', 'status'])

<div {{ $attributes->merge(['class' => 'col-lg-3 col-md-4 col-sm-6']) }}>
	<div class="group-box">
		<figure>
			<a href="{{ route('pages.show', $page) }}">
				<img src="{{ $page->coverUrl() ?? $page->avatarUrl() }}" alt="" style="height:130px;width:100%;object-fit:cover;">
			</a>
		</figure>
		<a href="{{ route('pages.show', $page) }}" title="">{{ $page->name }}</a>
		<span>{{ $page->category }} · {{ $page->likers_count }} {{ Str::plural('like', $page->likers_count) }}</span>
		@switch($status)
			@case('owner')
				<a href="{{ route('pages.manage', $page) }}"><button type="button">manage page</button></a>
				@break
			@case('liked')
				<button type="button" wire:click="unlike({{ $page->id }})">unlike</button>
				@break
			@default
				<button type="button" wire:click="like({{ $page->id }})">like page</button>
		@endswitch
	</div>
</div>
