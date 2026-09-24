@props(['storyImage' => null, 'groups' => []])

<div class="story-postbox">
	<div class="row">
		<div class="col-lg-3 col-md-3 col-sm-3">
			<div class="story-box" data-toggle="modal" data-target="#add-story-modal">
				<figure>
					<img src="{{ auth()->user()->avatarUrl() }}" alt="">
					<span>Add Your Story</span>
				</figure>
				<div class="story-thumb" title="Add Your Story">
					<i class="fa fa-plus"></i>
				</div>
			</div>
		</div>

		@foreach($groups as $userId => $stories)
			@php($storyUser = $stories->first()->user)
			<div class="col-lg-3 col-md-3 col-sm-3">
				<div class="story-box" data-toggle="modal" data-target="#story-viewer-{{ $userId }}">
					<figure>
						<img src="{{ asset('storage/'.$stories->first()->media_path) }}" alt="">
						<span>{{ $storyUser->name }}</span>
					</figure>
					<div class="story-thumb" title="{{ $storyUser->name }}">
						<img src="{{ $storyUser->avatarUrl() }}" alt="">
					</div>
				</div>
			</div>
		@endforeach
	</div>
</div>

<div class="modal fade" id="add-story-modal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Add Your Story</h5>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<form wire:submit="createStory">
					<input type="file" wire:model="storyImage" accept="image/*">

					@error('storyImage') <span style="color:#e0245e;">{{ $message }}</span> @enderror

					@if($storyImage)
						<img src="{{ $storyImage->temporaryUrl() }}" alt="" style="max-width:100%;max-height:200px;margin-top:10px;border-radius:6px;">
					@endif

					<input type="text" wire:model="storyCaption" placeholder="Say something about your story (optional)" style="width:100%;margin-top:10px;">

					<button type="submit" class="main-btn" style="margin-top:10px;" wire:loading.attr="disabled">Share Story</button>
				</form>
			</div>
		</div>
	</div>
</div>

@foreach($groups as $userId => $stories)
	<div class="modal fade" id="story-viewer-{{ $userId }}" tabindex="-1" x-data="{ i: 0 }">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content" style="background:#000;">
				<div class="modal-header" style="border:none;">
					<h6 style="color:#fff;">{{ $stories->first()->user->name }}</h6>
					<button type="button" class="close" style="color:#fff;" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body text-center">
					@foreach($stories as $index => $story)
						<div x-show="i === {{ $index }}">
							<img src="{{ asset('storage/'.$story->media_path) }}" alt="" style="max-width:100%;max-height:60vh;">
							@if($story->caption)
								<p style="color:#fff;margin-top:10px;">{{ $story->caption }}</p>
							@endif
							<p style="color:#aaa;">{{ $story->created_at->diffForHumans() }}</p>
						</div>
					@endforeach

					<div class="d-flex justify-content-between mt-2">
						<button type="button" class="main-btn" x-show="i > 0" x-on:click="i--">Prev</button>
						<button type="button" class="main-btn" x-show="i < {{ $stories->count() - 1 }}" x-on:click="i++">Next</button>
					</div>
				</div>
			</div>
		</div>
	</div>
@endforeach

<script>
	document.addEventListener('livewire:init', () => {
		Livewire.on('story-created', () => window.jQuery && jQuery('#add-story-modal').modal('hide'));
	});
</script>
