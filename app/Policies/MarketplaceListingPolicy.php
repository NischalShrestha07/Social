<?php

namespace App\Policies;

use App\Models\MarketplaceListing;
use App\Models\User;

class MarketplaceListingPolicy
{
    /** Sold listings stay visible to their seller and 403 for everyone else. */
    public function view(?User $user, MarketplaceListing $listing): bool
    {
        return $listing->isAvailable() || ($user !== null && $user->id === $listing->user_id);
    }

    public function update(User $user, MarketplaceListing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    public function delete(User $user, MarketplaceListing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    /** Only the seller answers offers. */
    public function manageOffers(User $user, MarketplaceListing $listing): bool
    {
        return $user->id === $listing->user_id;
    }

    /** Sellers don't bid on their own listing, and sold listings take no offers. */
    public function makeOffer(User $user, MarketplaceListing $listing): bool
    {
        return $user->id !== $listing->user_id && $listing->isAvailable();
    }
}
