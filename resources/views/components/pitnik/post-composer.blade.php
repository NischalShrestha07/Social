@props(['image' => null])

<div class="central-meta postbox">
	<span class="create-post">Create post</span>
	<div class="new-postbox">
		<figure>
			<img src="{{ auth()->user()->avatarUrl() }}" alt="">
		</figure>
		<div class="newpst-input">
			<form wire:submit="createPost">
				<textarea rows="2" placeholder="Share some what you are thinking?" wire:model="body"></textarea>

				@error('body') <span style="color:#e0245e;">{{ $message }}</span> @enderror

				<div class="attachments">
					<ul>
						<li>
							<i class="fa-light fa-image"></i>
							<label class="fileContainer">
								<input type="file" wire:model="image">
							</label>
						</li>
						<li class="preview-btn">
							@if($image)
								<img src="{{ $image->temporaryUrl() }}" alt="" style="max-height:60px;border-radius:6px;">
							@endif
						</li>
					</ul>
					<button class="post-btn" type="submit" data-ripple="" wire:loading.attr="disabled">Post</button>
				</div>

				@error('image') <span style="color:#e0245e;">{{ $message }}</span> @enderror
			</form>
		</div>
	</div>
</div><!-- add post new box -->
