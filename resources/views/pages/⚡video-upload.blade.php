<?php

use App\Models\Video;
use App\Models\VideoChannel;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Upload Video'), Layout('layouts::bare')] class extends Component
{
    use WithFileUploads;

    public int $channelId;

    public string $title = '';

    public string $description = '';

    public $durationMinutes = '';

    public $durationSeconds = '';

    public string $visibility = 'public';

    public $video = null;

    public $thumbnail = null;

    public function mount(VideoChannel $channel): void
    {
        Gate::authorize('upload', $channel);

        $this->channelId = $channel->id;
    }

    public function save(): void
    {
        $channel = VideoChannel::findOrFail($this->channelId);
        Gate::authorize('upload', $channel);

        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:5000',
            'durationMinutes' => 'nullable|integer|min:0|max:600',
            'durationSeconds' => 'nullable|integer|min:0|max:59',
            'visibility' => ['required', Rule::in(Video::VISIBILITIES)],
            'video' => 'nullable|mimetypes:video/mp4,video/webm,video/ogg|max:51200',
            'thumbnail' => 'nullable|image|max:5120',
        ]);

        $duration = ((int) $validated['durationMinutes'] * 60) + (int) $validated['durationSeconds'];

        $created = Video::create([
            'video_channel_id' => $channel->id,
            'user_id' => $channel->user_id,
            'title' => $validated['title'],
            'slug' => Video::uniqueSlug($validated['title']),
            'description' => $validated['description'] ?: null,
            'duration_seconds' => $duration > 0 ? $duration : null,
            'visibility' => $validated['visibility'],
            'video_path' => $this->video?->store('videos', 'public'),
            'thumbnail_path' => $this->thumbnail?->store('videos/thumbnails', 'public'),
            'views_count' => 0,
        ]);

        $this->redirectRoute('videos.show', $created);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Upload Video'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-pencil-alt"></i> Upload Video</h5>
								<form wire:submit="save" class="c-form">
									<div>
										<label>Title</label>
										<input type="text" wire:model="title" required maxlength="150">
										@error('title') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Description (optional)</label>
										<textarea rows="6" wire:model="description" maxlength="5000"></textarea>
										@error('description') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div class="row">
										<div class="col-md-4">
											<label>Minutes</label>
											<input type="number" wire:model="durationMinutes" min="0" max="600">
											@error('durationMinutes') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-4">
											<label>Seconds</label>
											<input type="number" wire:model="durationSeconds" min="0" max="59">
											@error('durationSeconds') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-4">
											<label>Visibility</label>
											<select wire:model="visibility" required>
												@foreach(\App\Models\Video::VISIBILITIES as $v)
													<option value="{{ $v }}">{{ \App\Models\Video::visibilityLabel($v) }}</option>
												@endforeach
											</select>
											@error('visibility') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
									</div>
									<div>
										<label>Video file (optional)</label>
										<input type="file" wire:model="video" accept="video/mp4,video/webm,video/ogg">
										@error('video') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Thumbnail (optional)</label>
										<input type="file" wire:model="thumbnail" accept="image/*">
										@error('thumbnail') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										@if($thumbnail)
											<img src="{{ $thumbnail->temporaryUrl() }}" alt="" style="margin-top:12px;max-width:260px;border-radius:4px;">
										@endif
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Publish</button>
										<a href="{{ route('videos.channel', \App\Models\VideoChannel::findOrFail($this->channelId)) }}" class="main-btn3">Cancel</a>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
