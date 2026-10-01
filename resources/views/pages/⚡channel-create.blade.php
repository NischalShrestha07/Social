<?php

use App\Models\VideoChannel;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Create Channel'), Layout('layouts::bare')] class extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $description = '';

    public $avatar = null;

    public $banner = null;

    /** One channel per user keeps ownership unambiguous in v1. */
    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:150', 'unique:video_channels,name'],
            'description' => 'nullable|string|max:2000',
            'avatar' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:5120',
        ]);

        $channel = DB::transaction(fn () => VideoChannel::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'slug' => VideoChannel::uniqueSlug($validated['name']),
            'description' => $validated['description'] ?: null,
            'avatar_path' => $this->avatar?->store('channels', 'public'),
            'banner_path' => $this->banner?->store('channels', 'public'),
        ]));

        $this->redirectRoute('videos.channel', $channel);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Create Channel'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-pencil-alt"></i> Create Channel</h5>
								<form wire:submit="save" class="c-form">
									<div>
										<label>Channel name</label>
										<input type="text" wire:model="name" required maxlength="150">
										@error('name') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Description (optional)</label>
										<textarea rows="5" wire:model="description" maxlength="2000"></textarea>
										@error('description') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div class="row">
										<div class="col-md-6">
											<label>Channel avatar</label>
											<input type="file" wire:model="avatar" accept="image/*">
											@error('avatar') <span style="color:#e0245e;">{{ $message }}</span> @enderror
											@if($avatar)
												<img src="{{ $avatar->temporaryUrl() }}" alt="" style="margin-top:12px;max-width:160px;border-radius:50%;">
											@endif
										</div>
										<div class="col-md-6">
											<label>Channel banner</label>
											<input type="file" wire:model="banner" accept="image/*">
											@error('banner') <span style="color:#e0245e;">{{ $message }}</span> @enderror
											@if($banner)
												<img src="{{ $banner->temporaryUrl() }}" alt="" style="margin-top:12px;max-width:220px;border-radius:4px;">
											@endif
										</div>
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Create Channel</button>
										<a href="{{ route('videos') }}" class="main-btn3">Cancel</a>
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
