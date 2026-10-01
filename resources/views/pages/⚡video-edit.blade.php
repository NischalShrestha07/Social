<?php

use App\Models\Video;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Edit Video'), Layout('layouts::bare')] class extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $videoId;

    public string $title = '';

    public string $description = '';

    public string $visibility = 'public';

    public $thumbnail = null;

    public function mount(Video $video): void
    {
        Gate::authorize('update', $video);

        $this->videoId = $video->id;
        $this->title = $video->title;
        $this->description = (string) $video->description;
        $this->visibility = $video->visibility;
    }

    public function save(): void
    {
        $video = Video::findOrFail($this->videoId);
        Gate::authorize('update', $video);

        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:5000',
            'visibility' => ['required', Rule::in(Video::VISIBILITIES)],
            'thumbnail' => 'nullable|image|max:5120',
        ]);

        // Retitling regenerates the slug, so redirect to the new URL.
        if ($validated['title'] !== $video->title) {
            $video->slug = Video::uniqueSlug($validated['title'], $video->id);
        }

        $video->update([
            'title' => $validated['title'],
            'slug' => $video->slug,
            'description' => $validated['description'] ?: null,
            'visibility' => $validated['visibility'],
            'thumbnail_path' => $this->thumbnail?->store('videos/thumbnails', 'public') ?? $video->thumbnail_path,
        ]);

        $this->redirectRoute('videos.show', $video);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Edit Video'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-pencil-alt"></i> Edit Video</h5>
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
									<div>
										<label>Visibility</label>
										<select wire:model="visibility" required>
											@foreach(\App\Models\Video::VISIBILITIES as $v)
												<option value="{{ $v }}">{{ \App\Models\Video::visibilityLabel($v) }}</option>
											@endforeach
										</select>
										@error('visibility') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Replace thumbnail (optional)</label>
										<input type="file" wire:model="thumbnail" accept="image/*">
										@error('thumbnail') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										@if($thumbnail)
											<img src="{{ $thumbnail->temporaryUrl() }}" alt="" style="margin-top:12px;max-width:260px;border-radius:4px;">
										@endif
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Save Changes</button>
										<a href="{{ route('videos.show', \App\Models\Video::findOrFail($this->videoId)) }}" class="main-btn3">Cancel</a>
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
