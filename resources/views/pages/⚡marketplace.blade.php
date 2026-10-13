<?php

use App\Models\MarketplaceListing;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Marketplace'), Layout('layouts::bare')] class extends Component
{
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'cat', except: '')]
    public string $category = '';

    #[Url(as: 'cond', except: '')]
    public string $condition = '';

    #[Url(as: 'sort', except: 'newest')]
    public string $sort = 'newest';

    #[Url(as: 'saved', except: false)]
    public bool $savedOnly = false;

    public function updated(): void
    {
        unset($this->listings);
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'category', 'condition', 'sort', 'savedOnly');
        unset($this->listings);
    }

    public function toggleSave(int $listingId): void
    {
        $listing = MarketplaceListing::findOrFail($listingId);

        if ($listing->user_id === auth()->id()) {
            return;
        }

        $existing = $listing->savers()->where('users.id', auth()->id())->first();

        if ($existing) {
            $existing->delete();
        } else {
            $listing->savers()->attach(auth()->id());
        }

        unset($this->listings, $this->myListings);
    }

    #[Computed]
    public function listings()
    {
        return MarketplaceListing::available()
            ->search($this->search)
            ->when($this->category, fn ($q) => $q->where('category', $this->category))
            ->when($this->condition, fn ($q) => $q->where('condition', $this->condition))
            ->when($this->savedOnly, fn ($q) => $q->whereHas('savers', fn ($s) => $s->where('users.id', auth()->id())))
            ->with('seller')
            ->withExists(['savers as saved_by_me' => fn ($q) => $q->where('users.id', auth()->id())])
            ->withCount('offers')
            ->when(
                $this->sort,
                fn ($q) => match ($this->sort) {
                    'price_asc' => $q->orderBy('price'),
                    'price_desc' => $q->orderByDesc('price'),
                    default => $q->latest(),
                },
            )
            ->paginate(12);
    }

    #[Computed]
    public function myListings()
    {
        return MarketplaceListing::where('user_id', auth()->id())
            ->withCount('offers')
            ->latest()
            ->get();
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Marketplace'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="title-block">
								<div class="row">
									<div class="col-lg-6">
										<div class="align-left">
											<h5>My Listings <span>{{ $this->myListings->count() }}</span></h5>
										</div>
									</div>
									<div class="col-lg-6 text-right">
										<a href="{{ route('marketplace.create') }}" class="main-btn">Sell an Item</a>
									</div>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->myListings as $listing)
									<div class="col-lg-4 col-md-6 col-sm-12" wire:key="mine-{{ $listing->id }}">
										<div class="widget-shadow">
											<h5 class="f-title">
												<a href="{{ route('marketplace.listing', $listing) }}">{{ $listing->title }}</a>
											</h5>
											<p>{{ $listing->priceLabel() }}</p>
											<span>
												{{ $listing->offers_count }} {{ Str::plural('offer', $listing->offers_count) }}
												· {{ $listing->is_sold ? 'Sold' : 'Available' }}
											</span>
										</div>
									</div>
								@empty
									<div class="col-lg-12"><p>You have not listed anything yet.</p></div>
								@endforelse
							</div>
						</div>

						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Browse Marketplace <span>{{ $this->listings->total() }}</span></h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								<div class="col-lg-12">
									<form wire:submit.prevent class="c-form">
										<div class="row">
											<div class="col-lg-3 col-md-6">
												<label>Search</label>
												<input type="text" wire:model.live.debounce.400ms="search" placeholder="Title, description, location">
											</div>
											<div class="col-lg-2 col-md-6">
												<label>Category</label>
												<select wire:model.live="category">
													<option value="">Any category</option>
													@foreach(\App\Models\MarketplaceListing::CATEGORIES as $c)
														<option value="{{ $c }}">{{ \App\Models\MarketplaceListing::categoryLabel($c) }}</option>
													@endforeach
												</select>
											</div>
											<div class="col-lg-2 col-md-6">
												<label>Condition</label>
												<select wire:model.live="condition">
													<option value="">Any condition</option>
													@foreach(\App\Models\MarketplaceListing::CONDITIONS as $c)
														<option value="{{ $c }}">{{ \App\Models\MarketplaceListing::conditionLabel($c) }}</option>
													@endforeach
												</select>
											</div>
											<div class="col-lg-2 col-md-6">
												<label>Sort</label>
												<select wire:model.live="sort">
													<option value="newest">Newest</option>
													<option value="price_asc">Price: low to high</option>
													<option value="price_desc">Price: high to low</option>
												</select>
											</div>
											<div class="col-lg-2 col-md-6">
												<label>&nbsp;</label>
												<label style="font-weight:400;">
													<input type="checkbox" wire:model.live="savedOnly"> Saved only
												</label>
											</div>
											<div class="col-lg-1 col-md-6">
												<label>&nbsp;</label>
												<button type="button" class="main-btn3" wire:click="clearFilters">Reset</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="row">
								@forelse($this->listings as $listing)
									<div class="col-lg-4 col-md-6 col-sm-12" wire:key="listing-{{ $listing->id }}">
										<x-pitnik.listing-box :listing="$listing" />
										<button type="button" wire:click="toggleSave({{ $listing->id }})"
											style="background:none;border:0;color:{{ $listing->saved_by_me ? '#e0245e' : '#757a95' }};cursor:pointer;font:inherit;padding:10px 0;">
											<i class="fa-regular fa-heart"></i>
											{{ $listing->saved_by_me ? 'Saved' : 'Save' }}
										</button>
									</div>
								@empty
									<div class="col-lg-12"><p>No listings match your search.</p></div>
								@endforelse
							</div>
							@if($this->listings->hasPages())
								<div class="row">
									<div class="col-lg-12 text-center">
										{{ $this->listings->links() }}
									</div>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
