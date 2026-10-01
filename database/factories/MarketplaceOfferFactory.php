<?php

namespace Database\Factories;

use App\Models\MarketplaceListing;
use App\Models\MarketplaceOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceOffer>
 */
class MarketplaceOfferFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'marketplace_listing_id' => MarketplaceListing::factory(),
            'user_id' => User::factory(),
            'offer_price' => fake()->numberBetween(20, 2000),
            'message' => fake()->sentence(),
            'status' => 'pending',
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    public function declined(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'declined',
        ]);
    }
}
