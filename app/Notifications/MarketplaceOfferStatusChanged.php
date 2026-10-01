<?php

namespace App\Notifications;

use App\Models\MarketplaceOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MarketplaceOfferStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public MarketplaceOffer $offer) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $listing = $this->offer->listing;
        $status = strtolower(MarketplaceOffer::statusLabel($this->offer->status));

        return [
            'actor_name' => $listing->seller->name,
            'actor_avatar' => $listing->seller->avatarUrl(),
            'message' => 'Your offer on '.$listing->title.' was '.$status.'.',
            'url' => route('marketplace.listing', $listing),
        ];
    }
}
