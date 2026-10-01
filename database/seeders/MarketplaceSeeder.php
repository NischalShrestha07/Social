<?php

namespace Database\Seeders;

use App\Models\MarketplaceListing;
use App\Models\MarketplaceOffer;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $seller = User::firstOrCreate(
            ['email' => 'seller@example.test'],
            ['name' => 'Sally Seller', 'username' => 'sallyseller', 'password' => 'password', 'email_verified_at' => now()],
        );

        $buyer = User::firstOrCreate(
            ['email' => 'buyer@example.test'],
            ['name' => 'Ben Buyer', 'username' => 'benbuyer', 'password' => 'password', 'email_verified_at' => now()],
        );

        $items = [
            ['Vintage Film Camera', 'electronics', 'good', 180.00, 'A well-loved 35mm film camera with a 50mm lens. Shutter fires cleanly, light seals replaced last year.'],
            ['Walnut Bookshelf', 'furniture', 'like_new', 120.00, 'Solid walnut shelving unit, 180cm tall. Minor scuff on the back panel, otherwise perfect.'],
            ['Mountain Bike', 'sports', 'good', 340.00, 'Hardtail mountain bike, recently tuned. New chain and cables, brakes bite well.'],
            ['Hardcover Cookbook Set', 'books', 'new', 45.50, 'Set of three hardcover cookbooks, brand new and unread.'],
        ];

        foreach ($items as [$title, $category, $condition, $price, $description]) {
            $listing = MarketplaceListing::firstOrCreate(
                ['title' => $title],
                [
                    'user_id' => $seller->id,
                    'slug' => MarketplaceListing::uniqueSlug($title),
                    'description' => $description,
                    'price' => $price,
                    'currency' => 'USD',
                    'category' => $category,
                    'condition' => $condition,
                    'location' => 'Kathmandu',
                    'is_sold' => false,
                ],
            );

            MarketplaceOffer::firstOrCreate(
                ['marketplace_listing_id' => $listing->id, 'user_id' => $buyer->id],
                ['offer_price' => (float) $price * 0.85, 'message' => 'Would you take a lower offer for this?', 'status' => 'pending'],
            );

            $listing->savers()->syncWithoutDetaching([$buyer->id]);
        }
    }
}
