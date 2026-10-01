<?php

use App\Models\VideoChannel;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Manage Channel'), Layout('layouts::bare')] class extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $channelId;

    public string $name = '';

    public string $description = '';

    public $avatar = null;

    public $banner = null;

    public function mount(VideoChannel $channel): void
    {
        Gate::authorize('update', $channel);

        $this->channelId = $channel->id;
        $this->name = $channel->name;
        $this->description = (string) $channel->description;
    }

    public function save(): void
    {
        $channel = VideoChannel::findOrFail($this->channelId);
        Gate::authorize('update', $channel);

        $validated = $this->validate([
            // The channel's own id is excluded so re-saving its current name is fine.
            'name' => ['required', 'string', 'max:150', Rule::unique('video_channels', 'name')->ignore($this->channelId)],
            'description' => 'nullable|string|max:2000',
            'avatar' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:5120',
        ]);

        if ($validated['name'] !== $channel->name) {
            $channel->slug = VideoChannel::uniqueSlug($validated['name'], $channel->id);
        }

        $channel->update([
            'name' => $validated['name'],
            'slug' => $channel->slug,
            'description' => $validated['description'] ?: null,
            'avatar_path' => $this->avatar?->store('channels', 'public') ?? $channel->avatar_path,
            'banner_path' => $this->banner?->store('channels', 'public') ?? $channel->banner_path,
        ]);

        $this->redirectRoute('videos.channel', $channel);
    }

    public function deleteChannel(): void
    {
        $channel = VideoChannel::findOrFail($this->channelId);
        Gate::authorize('delete', $channel);

        $channel->delete();

        $this->redirectRoute('videos');
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Manage Channel'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-pencil-alt"></i> Manage Channel</h5>
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
											<label>Replace avatar</label>
											<input type="file" wire:model="avatar" accept="image/*">
											@error('avatar') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-6">
											<label>Replace banner</label>
											<input type="file" wire:model="banner" accept="image/*">
											@error('banner') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Save Changes</button>
										<a href="{{ route('videos.channel', \App\Models\VideoChannel::findOrFail($this->channelId)) }}" class="main-btn3">Cancel</a>
									</div>
								</form>
								<div style="margin-top:25px;border-top:1px solid #ececf5;padding-top:20px;">
									<button type="button" class="main-btn3" style="background:#e0245e;color:#fff;"
										wire:click="deleteChannel" wire:confirm="Delete this channel and every video on it?">
										Delete Channel
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
