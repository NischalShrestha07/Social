<?php

use App\Models\Group;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Create Group'), Layout('layouts::bare')] class extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $description = '';

    public string $privacy = 'public';

    public $avatar = null;

    public $cover = null;

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:2000',
            'privacy' => 'required|in:public,private',
            'avatar' => 'nullable|image|max:5120',
            'cover' => 'nullable|image|max:5120',
        ]);

        $group = Group::create([
            'owner_id' => auth()->id(),
            'name' => $validated['name'],
            'slug' => Group::uniqueSlug($validated['name']),
            'description' => $validated['description'] ?: null,
            'privacy' => $validated['privacy'],
            'avatar_path' => $this->avatar?->store('groups', 'public'),
            'cover_path' => $this->cover?->store('groups', 'public'),
        ]);

        $group->members()->attach(auth()->id(), ['role' => 'admin', 'status' => 'approved']);

        $this->redirectRoute('groups.show', $group);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Create Group'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-plus"></i> Create Group</h5>
								<form wire:submit="save" class="c-form">
									<div>
										<label>Group name</label>
										<input type="text" wire:model="name" required maxlength="100" placeholder="e.g. Kathmandu Photographers">
										@error('name') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Description</label>
										<textarea rows="4" wire:model="description" maxlength="2000" placeholder="What is this group about?"></textarea>
										@error('description') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Privacy</label>
										<select wire:model="privacy">
											<option value="public">Public — anyone can see posts and join</option>
											<option value="private">Private — only members see posts, joining needs approval</option>
										</select>
										@error('privacy') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Group photo (optional)</label>
										<input type="file" wire:model="avatar" accept="image/*">
										@error('avatar') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Cover photo (optional)</label>
										<input type="file" wire:model="cover" accept="image/*">
										@error('cover') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Create Group</button>
										<a href="{{ route('groups') }}" class="main-btn3">Cancel</a>
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
