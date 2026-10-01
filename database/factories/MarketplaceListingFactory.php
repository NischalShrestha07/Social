<?php

namespace Database\Factories;

use App\Models\MarketplaceListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceListing>
 */
class MarketplaceListingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Faker's `words()` is typed array|string even with asArray off, so build it by hand.
        $title = ucfirst(fake()->unique()->word().' '.fake()->unique()->word().' '.fake()->unique()->word());

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => MarketplaceListing::uniqueSlug($title),
            'description' => fake()->paragraphs(2, true),
            'price' => fake()->numberBetween(20, 2000),
            'currency' => 'USD',
            'category' => fake()->randomElement(MarketplaceListing::CATEGORIES),
            'condition' => fake()->randomElement(MarketplaceListing::CONDITIONS),
            'location' => fake()->city(),
            'is_sold' => false,
        ];
    }

    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sold' => true,
        ]);
    }

    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }
}
