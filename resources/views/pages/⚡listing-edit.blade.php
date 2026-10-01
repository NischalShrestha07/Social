<?php

use App\Models\MarketplaceListing;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Edit Listing'), Layout('layouts::bare')] class extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $listingId;

    public string $title = '';

    public string $description = '';

    public $price = '';

    public string $currency = 'USD';

    public string $category = 'other';

    public string $condition = 'good';

    public string $location = '';

    public bool $isSold = false;

    public $image = null;

    public function mount(MarketplaceListing $listing): void
    {
        Gate::authorize('update', $listing);

        $this->listingId = $listing->id;
        $this->title = $listing->title;
        $this->description = $listing->description;
        $this->price = (float) $listing->price;
        $this->currency = $listing->currency;
        $this->category = $listing->category;
        $this->condition = $listing->condition;
        $this->location = $listing->location;
        $this->isSold = $listing->is_sold;
    }

    public function save(): void
    {
        $listing = MarketplaceListing::findOrFail($this->listingId);
        Gate::authorize('update', $listing);

        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string|max:10000',
            'price' => 'required|numeric|min:0|max:99999999',
            'currency' => ['required', Rule::in(MarketplaceListing::CURRENCIES)],
            'category' => ['required', Rule::in(MarketplaceListing::CATEGORIES)],
            'condition' => ['required', Rule::in(MarketplaceListing::CONDITIONS)],
            'location' => 'required|string|max:150',
            'image' => 'nullable|image|max:5120',
        ]);

        // Retitling regenerates the slug, so redirect to the new URL.
        if ($validated['title'] !== $listing->title) {
            $listing->slug = MarketplaceListing::uniqueSlug($validated['title'], $listing->id);
        }

        $listing->update([
            'title' => $validated['title'],
            'slug' => $listing->slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'currency' => $validated['currency'],
            'category' => $validated['category'],
            'condition' => $validated['condition'],
            'location' => $validated['location'],
            'is_sold' => $this->isSold,
            'image_path' => $this->image?->store('listings', 'public') ?? $listing->image_path,
        ]);

        $this->redirectRoute('marketplace.listing', $listing);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Edit Listing'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-pencil-alt"></i> Edit Listing</h5>
								<form wire:submit="save" class="c-form">
									<div>
										<label>Title</label>
										<input type="text" wire:model="title" required maxlength="150">
										@error('title') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Description</label>
										<textarea rows="8" wire:model="description" required maxlength="10000"></textarea>
										@error('description') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div class="row">
										<div class="col-md-4">
											<label>Price</label>
											<input type="number" step="0.01" wire:model="price" required min="0">
											@error('price') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-4">
											<label>Currency</label>
											<select wire:model="currency" required>
												@foreach(\App\Models\MarketplaceListing::CURRENCIES as $c)
													<option value="{{ $c }}">{{ $c }}</option>
												@endforeach
											</select>
											@error('currency') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-4">
											<label>Location</label>
											<input type="text" wire:model="location" required maxlength="150">
											@error('location') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<label>Category</label>
											<select wire:model="category" required>
												@foreach(\App\Models\MarketplaceListing::CATEGORIES as $c)
													<option value="{{ $c }}">{{ \App\Models\MarketplaceListing::categoryLabel($c) }}</option>
												@endforeach
											</select>
											@error('category') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-6">
											<label>Condition</label>
											<select wire:model="condition" required>
												@foreach(\App\Models\MarketplaceListing::CONDITIONS as $c)
													<option value="{{ $c }}">{{ \App\Models\MarketplaceListing::conditionLabel($c) }}</option>
												@endforeach
											</select>
											@error('condition') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
									</div>
									<div>
										<label style="font-weight:400;">
											<input type="checkbox" wire:model="isSold"> Mark as sold
										</label>
									</div>
									<div>
										<label>Replace photo (optional)</label>
										<input type="file" wire:model="image" accept="image/*">
										@error('image') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										@if($image)
											<img src="{{ $image->temporaryUrl() }}" alt="" style="margin-top:12px;max-width:220px;border-radius:4px;">
										@endif
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Save Changes</button>
										<a href="{{ route('marketplace.listing', \App\Models\MarketplaceListing::findOrFail($this->listingId)) }}" class="main-btn3">Cancel</a>
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
