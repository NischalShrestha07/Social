<?php

use App\Models\MarketplaceListing;
use App\Models\MarketplaceOffer;
use App\Notifications\MarketplaceOfferReceived;
use App\Notifications\MarketplaceOfferStatusChanged;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Listing'), Layout('layouts::bare')] class extends Component
{
    #[Locked]
    public int $listingId;

    public $offerPrice = '';

    public string $offerMessage = '';

    public function mount(MarketplaceListing $listing): void
    {
        Gate::authorize('view', $listing);

        $this->listingId = $listing->id;
    }

    #[Computed]
    public function listing(): MarketplaceListing
    {
        return MarketplaceListing::with('seller')->findOrFail($this->listingId);
    }

    /** Offers are always scoped through the listing, never trusted from the client. */
    #[Computed]
    public function offers()
    {
        return $this->listing()->offers()->with('buyer')->latest()->get();
    }

    #[Computed]
    public function myOffer(): ?MarketplaceOffer
    {
        return $this->listing()
            ->offers()
            ->where('user_id', auth()->id())
            ->first();
    }

    #[Computed]
    public function isSaved(): bool
    {
        return $this->listing()->isSavedBy(auth()->user());
    }

    public function toggleSave(): void
    {
        $listing = $this->listing();

        if ($listing->user_id === auth()->id()) {
            return;
        }

        $existing = $listing->savers()->where('users.id', auth()->id())->first();

        if ($existing) {
            $existing->delete();
        } else {
            $listing->savers()->attach(auth()->id());
        }

        unset($this->isSaved);
    }

    public function makeOffer(): void
    {
        $listing = $this->listing();
        Gate::authorize('makeOffer', $listing);

        if ($listing->offers()->where('user_id', auth()->id())->exists()) {
            $this->addError('offerPrice', 'You already made an offer on this listing.');

            return;
        }

        $validated = $this->validate([
            'offerPrice' => 'required|numeric|min:0|max:99999999',
            'offerMessage' => 'nullable|string|max:2000',
        ]);

        $offer = $listing->offers()->create([
            'user_id' => auth()->id(),
            'offer_price' => $validated['offerPrice'],
            'message' => $validated['offerMessage'] ?: null,
            'status' => 'pending',
        ]);

        Notification::send($listing->seller, new MarketplaceOfferReceived($offer));

        $this->reset('offerPrice', 'offerMessage');
        unset($this->myOffer, $this->offers);
        session()->flash('status', 'Offer sent to the seller.');
    }

    public function updateOffer(int $offerId, string $status): void
    {
        $listing = $this->listing();
        Gate::authorize('manageOffers', $listing);

        $offer = $listing->offers()->whereKey($offerId)->firstOrFail();

        if (! in_array($status, ['accepted', 'declined'], true)) {
            return;
        }

        $offer->update(['status' => $status]);

        // Accepting an offer takes the listing off the market.
        if ($status === 'accepted') {
            $listing->update(['is_sold' => true]);
        }

        Notification::send($offer->buyer, new MarketplaceOfferStatusChanged($offer));

        unset($this->offers);
    }

    public function deleteListing(): void
    {
        $listing = $this->listing();
        Gate::authorize('delete', $listing);

        $listing->delete();

        $this->redirectRoute('marketplace');
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $this->listing->title])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>{{ $this->listing->title }}</h5>
									<span style="color:#757a95;">
										{{ \App\Models\MarketplaceListing::categoryLabel($this->listing->category) }}
										· {{ \App\Models\MarketplaceListing::conditionLabel($this->listing->condition) }}
										· {{ $this->listing->location }}
										· posted {{ $this->listing->created_at->diffForHumans() }}
										@if($this->listing->is_sold) · <strong style="color:#e0245e;">Sold</strong> @endif
									</span>
								</div>
								<div class="align-right">
									<a href="{{ route('marketplace') }}" class="main-btn3">Back</a>
								</div>
							</div>
						</div>

						<div class="central-meta padding30">
							<div class="row">
								<div class="col-lg-5">
									<img src="{{ $this->listing->imageUrl() }}" alt=""
										style="width:100%;border-radius:4px;object-fit:cover;">
								</div>
								<div class="col-lg-7">
									<h5 class="f-title">{{ $this->listing->priceLabel() }}</h5>
									<p style="color:#535165;white-space:pre-line;margin-top:15px;">{{ $this->listing->description }}</p>
									<div style="display:flex;gap:15px;align-items:center;margin-top:20px;">
										<figure style="margin:0;">
											<img src="{{ $this->listing->seller->avatarUrl() }}" alt="" style="width:42px;height:42px;border-radius:50%;object-fit:cover;">
										</figure>
										<div>
											<span style="color:#535165;">
												sold by <a href="{{ route('profile.show', $this->listing->seller) }}">{{ $this->listing->seller->name }}</a>
											</span>
										</div>
									</div>
									<div style="margin-top:20px;display:flex;gap:12px;flex-wrap:wrap;">
										@can('update', $this->listing)
											<a href="{{ route('marketplace.listing.edit', $this->listing) }}" class="main-btn3">Edit</a>
										@endcan
										@if(! $this->listing->is_sold)
											<button type="button" wire:click="toggleSave"
												style="background:none;border:0;color:{{ $this->isSaved ? '#e0245e' : '#757a95' }};cursor:pointer;font:inherit;">
												<i class="fa-regular fa-heart"></i>
												{{ $this->isSaved ? 'Saved' : 'Save' }}
											</button>
										@endif
										@can('delete', $this->listing)
											<button type="button" class="main-btn3" style="background:#e0245e;color:#fff;"
												wire:click="deleteListing" wire:confirm="Delete this listing?">
												Delete Listing
											</button>
										@endcan
									</div>
								</div>
							</div>
						</div>

						@if(session('status'))
							<div class="central-meta padding30">
								<div style="background:#e6f7ed;color:#1b7a45;padding:12px 16px;border-radius:4px;">
									{{ session('status') }}
								</div>
							</div>
						@endif

						@if($this->listing->user_id === auth()->id())
							<div class="central-meta">
								<div class="title-block">
									<div class="align-left">
										<h5>Offers <span>{{ $this->offers->count() }}</span></h5>
									</div>
								</div>
							</div>
							<div class="central-meta padding30">
								@forelse($this->offers as $offer)
									<div class="widget-shadow" style="padding:18px;margin-bottom:12px;" wire:key="offer-{{ $offer->id }}">
										<div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;">
											<span style="color:#535165;">
												<a href="{{ route('profile.show', $offer->buyer) }}">{{ $offer->buyer->name }}</a>
												offered <strong>{{ $this->listing->currency }} {{ number_format((float) $offer->offer_price, 2) }}</strong>
											</span>
											<span style="color:#757a95;font-size:13px;">
												{{ \App\Models\MarketplaceOffer::statusLabel($offer->status) }}
											</span>
										</div>
										@if($offer->message)
											<p style="color:#535165;margin-top:10px;">{{ $offer->message }}</p>
										@endif
										@if($offer->status === 'pending')
											<div style="margin-top:14px;display:flex;gap:12px;">
												<button type="button" class="main-btn3" wire:click="updateOffer({{ $offer->id }}, 'accepted')">
													Accept
												</button>
												<button type="button" class="main-btn3" wire:click="updateOffer({{ $offer->id }}, 'declined')">
													Decline
												</button>
											</div>
										@endif
									</div>
								@empty
									<p>No offers yet.</p>
								@endforelse
							</div>
						@elseif($this->listing->isAvailable())
							<div class="central-meta">
								<div class="title-block">
									<div class="align-left">
										<h5>Make an Offer</h5>
									</div>
								</div>
							</div>
							<div class="central-meta padding30">
								@if($this->myOffer)
									<div class="widget-shadow" style="padding:18px;">
										<span style="color:#535165;">
											Your offer of
											<strong>{{ $this->listing->currency }} {{ number_format((float) $this->myOffer->offer_price, 2) }}</strong>
											is {{ strtolower(\App\Models\MarketplaceOffer::statusLabel($this->myOffer->status)) }}.
										</span>
									</div>
								@else
									<form wire:submit="makeOffer" class="c-form">
										<div class="row">
											<div class="col-md-4">
												<label>Your offer</label>
												<input type="number" step="0.01" wire:model="offerPrice" required min="0">
												@error('offerPrice') <span style="color:#e0245e;">{{ $message }}</span> @enderror
											</div>
											<div class="col-md-8">
												<label>Message (optional)</label>
												<input type="text" wire:model="offerMessage" maxlength="2000">
												@error('offerMessage') <span style="color:#e0245e;">{{ $message }}</span> @enderror
											</div>
										</div>
										<div>
											<button type="submit" class="main-btn" wire:loading.attr="disabled">Send Offer</button>
										</div>
									</form>
								@endif
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
