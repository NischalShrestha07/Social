<?php

namespace App\Models;

use Database\Factories\MarketplaceOfferFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $marketplace_listing_id
 * @property int $user_id
 * @property string $offer_price
 * @property string|null $message
 * @property string $status pending|accepted|declined
 */
#[Fillable(['marketplace_listing_id', 'user_id', 'offer_price', 'message', 'status'])]
class MarketplaceOffer extends Model
{
    /** @use HasFactory<MarketplaceOfferFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'offer_price' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<MarketplaceListing, $this> */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(MarketplaceListing::class, 'marketplace_listing_id');
    }

    /** @return BelongsTo<User, $this> */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            'accepted' => 'Accepted',
            'declined' => 'Declined',
            default => 'Pending',
        };
    }
}
