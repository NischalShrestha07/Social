<?php

use App\Models\Page;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Create Page'), Layout('layouts::bare')] class extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $category = '';

    public string $description = '';

    public string $website = '';

    public $avatar = null;

    public $cover = null;

    public function save(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|max:100',
            'category' => ['required', Rule::in(Page::CATEGORIES)],
            'description' => 'nullable|string|max:2000',
            'website' => 'nullable|url:http,https|max:255',
            'avatar' => 'nullable|image|max:5120',
            'cover' => 'nullable|image|max:5120',
        ]);

        $page = Page::create([
            'owner_id' => auth()->id(),
            'name' => $validated['name'],
            'slug' => Page::uniqueSlug($validated['name']),
            'category' => $validated['category'],
            'description' => $validated['description'] ?: null,
            'website' => $validated['website'] ?: null,
            'avatar_path' => $this->avatar?->store('pages', 'public'),
            'cover_path' => $this->cover?->store('pages', 'public'),
        ]);

        $this->redirectRoute('pages.show', $page);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Create Page'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-plus"></i> Create Page</h5>
								<form wire:submit="save" class="c-form">
									<div>
										<label>Page name</label>
										<input type="text" wire:model="name" required maxlength="100" placeholder="e.g. Himalayan Tea House">
										@error('name') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Category</label>
										<select wire:model="category" required>
											<option value="">Choose a category</option>
											@foreach(\App\Models\Page::CATEGORIES as $cat)
												<option value="{{ $cat }}">{{ $cat }}</option>
											@endforeach
										</select>
										@error('category') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Description</label>
										<textarea rows="4" wire:model="description" maxlength="2000" placeholder="Tell people what your page is about"></textarea>
										@error('description') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Website (optional)</label>
										<input type="url" wire:model="website" maxlength="255" placeholder="https://example.com">
										@error('website') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Page photo (optional)</label>
										<input type="file" wire:model="avatar" accept="image/*">
										@error('avatar') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Cover photo (optional)</label>
										<input type="file" wire:model="cover" accept="image/*">
										@error('cover') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Create Page</button>
										<a href="{{ route('pages') }}" class="main-btn3">Cancel</a>
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
