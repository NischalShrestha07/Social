<?php

namespace App\Notifications;

use App\Models\MarketplaceOffer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class MarketplaceOfferReceived extends Notification
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

        return [
            'actor_name' => $this->offer->buyer->name,
            'actor_avatar' => $this->offer->buyer->avatarUrl(),
            'message' => $this->offer->buyer->name.' made an offer on '.$listing->title.'.',
            'url' => route('marketplace.listing', $listing),
        ];
    }
}
