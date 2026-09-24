<?php

use App\Models\Page;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::bare')] class extends Component
{
    use WithFileUploads;

    public Page $page;

    public string $name = '';

    public string $category = '';

    public string $description = '';

    public string $website = '';

    public $avatar = null;

    public $cover = null;

    public function mount(Page $page): void
    {
        Gate::authorize('update', $page);

        $this->page = $page;
        $this->name = $page->name;
        $this->category = $page->category;
        $this->description = (string) $page->description;
        $this->website = (string) $page->website;
    }

    #[Computed]
    public function likers()
    {
        return $this->page->likers()->latest('page_likes.created_at')->get();
    }

    public function save(): void
    {
        Gate::authorize('update', $this->page);

        $validated = $this->validate([
            'name' => 'required|string|max:100',
            'category' => ['required', Rule::in(Page::CATEGORIES)],
            'description' => 'nullable|string|max:2000',
            'website' => 'nullable|url:http,https|max:255',
            'avatar' => 'nullable|image|max:5120',
            'cover' => 'nullable|image|max:5120',
        ]);

        $attributes = [
            'name' => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?: null,
            'website' => $validated['website'] ?: null,
        ];

        if ($validated['name'] !== $this->page->name) {
            $attributes['slug'] = Page::uniqueSlug($validated['name'], $this->page->id);
        }
        if ($this->avatar) {
            $attributes['avatar_path'] = $this->avatar->store('pages', 'public');
        }
        if ($this->cover) {
            $attributes['cover_path'] = $this->cover->store('pages', 'public');
        }

        $this->page->update($attributes);
        $this->reset(['avatar', 'cover']);

        // Slug may have changed; land on the new URL.
        $this->redirectRoute('pages.manage', $this->page);
    }

    public function deletePage(): void
    {
        Gate::authorize('delete', $this->page);

        $this->page->delete();

        $this->redirectRoute('pages');
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Manage '.$page->name])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-settings"></i> Manage Page — <a href="{{ route('pages.show', $page) }}">{{ $page->name }}</a></h5>
								<form wire:submit="save" class="c-form">
									<div>
										<label>Page name</label>
										<input type="text" wire:model="name" required maxlength="100">
										@error('name') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Category</label>
										<select wire:model="category" required>
											@foreach(\App\Models\Page::CATEGORIES as $cat)
												<option value="{{ $cat }}">{{ $cat }}</option>
											@endforeach
										</select>
										@error('category') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Description</label>
										<textarea rows="4" wire:model="description" maxlength="2000"></textarea>
										@error('description') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Website</label>
										<input type="url" wire:model="website" maxlength="255" placeholder="https://example.com">
										@error('website') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Replace page photo</label>
										<input type="file" wire:model="avatar" accept="image/*">
										@error('avatar') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Replace cover photo</label>
										<input type="file" wire:model="cover" accept="image/*">
										@error('cover') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Save Changes</button>
										<a href="{{ route('pages.show', $page) }}" class="main-btn3">Back to Page</a>
									</div>
								</form>
							</div>
						</div>

						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-trash"></i> Delete Page</h5>
								<p>Permanently deletes this page, all of its posts and its likes. This can't be undone.</p>
								<button type="button" class="main-btn" style="background:#e0245e;" wire:click="deletePage" wire:confirm="Delete {{ $page->name }} and all its posts permanently?">Delete Page</button>
							</div>
						</div>
					</div>

					<div class="col-lg-4">
						<aside class="sidebar static right">
							<div class="widget">
								<h4 class="widget-title">Liked by {{ $this->likers->count() }}</h4>
								<ul class="followers">
									@forelse($this->likers as $liker)
										<li wire:key="liker-{{ $liker->id }}">
											<figure><img src="{{ $liker->avatarUrl() }}" alt=""></figure>
											<div class="friend-meta">
												<h4><a href="{{ route('profile.show', $liker) }}">{{ $liker->name }}</a></h4>
												<span>{{ $liker->pivot->created_at?->diffForHumans() }}</span>
											</div>
										</li>
									@empty
										<li><div class="friend-meta"><span>No likes yet.</span></div></li>
									@endforelse
								</ul>
							</div>
						</aside>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
