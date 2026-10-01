<?php

namespace Tests\Feature\Marketplace;

use App\Models\MarketplaceListing;
use App\Models\MarketplaceOffer;
use App\Models\User;
use App\Notifications\MarketplaceOfferReceived;
use App\Notifications\MarketplaceOfferStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('marketplace'))->assertRedirect(route('login'));
    }

    public function test_marketplace_renders(): void
    {
        $user = User::factory()->create();
        MarketplaceListing::factory()->create(['title' => 'Vintage Film Camera']);

        Livewire::actingAs($user)
            ->test('pages::marketplace')
            ->assertOk()
            ->assertSee('Vintage Film Camera');
    }

    public function test_sold_listings_are_hidden_from_the_browse_grid(): void
    {
        $user = User::factory()->create();
        MarketplaceListing::factory()->create(['title' => 'Available Desk']);
        MarketplaceListing::factory()->sold()->create(['title' => 'Gone Chair']);

        Livewire::actingAs($user)
            ->test('pages::marketplace')
            ->assertSee('Available Desk')
            ->assertDontSee('Gone Chair');
    }

    public function test_search_filters_by_title_and_location(): void
    {
        $user = User::factory()->create();
        MarketplaceListing::factory()->create(['title' => 'Film Camera', 'location' => 'Kathmandu']);
        MarketplaceListing::factory()->create(['title' => 'Bike Rack', 'location' => 'Pokhara']);

        Livewire::actingAs($user)
            ->test('pages::marketplace')
            ->set('search', 'Camera')
            ->assertSee('Film Camera')
            ->assertDontSee('Bike Rack');

        Livewire::actingAs($user)
            ->test('pages::marketplace')
            ->set('search', 'Pokhara')
            ->assertSee('Bike Rack')
            ->assertDontSee('Film Camera');
    }

    public function test_category_and_condition_filters_combine(): void
    {
        $user = User::factory()->create();
        MarketplaceListing::factory()->category('electronics')->create([
            'title' => 'Camera Body',
            'condition' => 'good',
        ]);
        MarketplaceListing::factory()->category('furniture')->create([
            'title' => 'Oak Table',
            'condition' => 'good',
        ]);

        Livewire::actingAs($user)
            ->test('pages::marketplace')
            ->set('category', 'electronics')
            ->assertSee('Camera Body')
            ->assertDontSee('Oak Table');

        Livewire::actingAs($user)
            ->test('pages::marketplace')
            ->set('category', 'electronics')
            ->set('condition', 'new')
            ->assertDontSee('Camera Body');
    }

    public function test_listings_can_be_sorted_by_price(): void
    {
        $user = User::factory()->create();
        MarketplaceListing::factory()->create(['title' => 'Cheap Item', 'price' => 10]);
        MarketplaceListing::factory()->create(['title' => 'Pricey Item', 'price' => 900]);

        Livewire::actingAs($user)
            ->test('pages::marketplace')
            ->set('sort', 'price_asc')
            ->assertSeeInOrder(['Cheap Item', 'Pricey Item']);

        Livewire::actingAs($user)
            ->test('pages::marketplace')
            ->set('sort', 'price_desc')
            ->assertSeeInOrder(['Pricey Item', 'Cheap Item']);
    }

    public function test_clear_filters_resets_the_query_string(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::marketplace')
            ->set('search', 'Camera')
            ->set('category', 'electronics')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('category', '');
    }

    public function test_a_user_can_list_an_item(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::listing-create')
            ->set('title', 'Walnut Bookshelf')
            ->set('description', 'Solid walnut shelving unit.')
            ->set('price', '120.50')
            ->set('location', 'Kathmandu')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('marketplace_listings', [
            'user_id' => $user->id,
            'title' => 'Walnut Bookshelf',
            'category' => 'other',
        ]);
    }

    public function test_listing_creation_validates_input(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::listing-create')
            ->set('title', '')
            ->set('description', '')
            ->set('price', 'not-a-number')
            ->set('location', '')
            ->call('save')
            ->assertHasErrors(['title', 'description', 'price', 'location']);

        $this->assertDatabaseCount('marketplace_listings', 0);
    }

    public function test_listing_creation_rejects_unknown_enums(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::listing-create')
            ->set('title', 'Mystery Item')
            ->set('description', 'What is it?')
            ->set('price', '10')
            ->set('location', 'Kathmandu')
            ->set('category', 'spaceships')
            ->call('save')
            ->assertHasErrors('category');
    }

    public function test_duplicate_listing_titles_get_unique_slugs(): void
    {
        MarketplaceListing::factory()->create(['title' => 'Film Camera', 'slug' => 'film-camera']);
        $listing = MarketplaceListing::factory()->create(['title' => 'Film Camera', 'slug' => 'film-camera-1']);

        $this->assertSame('film-camera-1', $listing->slug);
    }

    public function test_listing_page_renders(): void
    {
        $user = User::factory()->create();
        $listing = MarketplaceListing::factory()->create(['title' => 'Mountain Bike']);

        Livewire::actingAs($user)
            ->test('pages::listing', ['listing' => $listing])
            ->assertOk()
            ->assertSee('Mountain Bike');
    }

    public function test_sold_listings_are_hidden_from_buyers_but_visible_to_the_seller(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = MarketplaceListing::factory()->sold()->for($seller, 'seller')->create();

        $this->actingAs($buyer)->get(route('marketplace.listing', $listing))->assertForbidden();

        $this->actingAs($seller)
            ->get(route('marketplace.listing', $listing))
            ->assertOk();
    }

    public function test_only_the_seller_can_open_the_edit_page(): void
    {
        $seller = User::factory()->create();
        $stranger = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();

        $this->actingAs($stranger)->get(route('marketplace.listing.edit', $listing))->assertForbidden();

        $this->actingAs($seller)
            ->get(route('marketplace.listing.edit', $listing))
            ->assertOk();
    }

    public function test_a_seller_can_edit_their_listing(): void
    {
        $seller = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create([
            'title' => 'Old Title',
            'slug' => 'old-title',
        ]);

        Livewire::actingAs($seller)
            ->test('pages::listing-edit', ['listing' => $listing])
            ->set('title', 'Brand New Title')
            ->set('price', '99')
            ->set('isSold', true)
            ->call('save')
            ->assertHasNoErrors();

        $listing->refresh();

        $this->assertSame('Brand New Title', $listing->title);
        $this->assertSame('brand-new-title', $listing->slug);
        $this->assertTrue($listing->is_sold);
    }

    public function test_only_the_seller_can_delete_a_listing(): void
    {
        $seller = User::factory()->create();
        $stranger = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();

        Livewire::actingAs($stranger)
            ->test('pages::listing', ['listing' => $listing])
            ->call('deleteListing')
            ->assertForbidden();

        $this->assertDatabaseHas('marketplace_listings', ['id' => $listing->id]);

        Livewire::actingAs($seller)
            ->test('pages::listing', ['listing' => $listing])
            ->call('deleteListing');

        $this->assertDatabaseMissing('marketplace_listings', ['id' => $listing->id]);
    }

    public function test_users_can_save_and_unsave_a_listing(): void
    {
        $buyer = User::factory()->create();
        $listing = MarketplaceListing::factory()->create();

        Livewire::actingAs($buyer)
            ->test('pages::listing', ['listing' => $listing])
            ->call('toggleSave')
            ->assertHasNoErrors();

        $this->assertTrue($listing->fresh()->isSavedBy($buyer));

        Livewire::actingAs($buyer)
            ->test('pages::listing', ['listing' => $listing])
            ->call('toggleSave');

        $this->assertFalse($listing->fresh()->isSavedBy($buyer));
    }

    public function test_sellers_cannot_save_their_own_listing(): void
    {
        $seller = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();

        Livewire::actingAs($seller)
            ->test('pages::listing', ['listing' => $listing])
            ->call('toggleSave');

        $this->assertFalse($listing->fresh()->isSavedBy($seller));
    }

    public function test_saved_only_filter_lists_the_users_saves(): void
    {
        $buyer = User::factory()->create();
        $saved = MarketplaceListing::factory()->create(['title' => 'Saved Item']);
        MarketplaceListing::factory()->create(['title' => 'Unsaved Item']);
        $saved->savers()->attach($buyer->id);

        Livewire::actingAs($buyer)
            ->test('pages::marketplace')
            ->set('savedOnly', true)
            ->assertSee('Saved Item')
            ->assertDontSee('Unsaved Item');
    }

    public function test_a_buyer_can_make_an_offer(): void
    {
        Notification::fake();

        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();

        Livewire::actingAs($buyer)
            ->test('pages::listing', ['listing' => $listing])
            ->set('offerPrice', '150')
            ->set('offerMessage', 'Can you do 150?')
            ->call('makeOffer')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('marketplace_offers', [
            'marketplace_listing_id' => $listing->id,
            'user_id' => $buyer->id,
            'status' => 'pending',
        ]);

        Notification::assertSentTo($seller, MarketplaceOfferReceived::class);
    }

    public function test_buyers_cannot_make_two_offers_on_one_listing(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();
        MarketplaceOffer::factory()->create([
            'marketplace_listing_id' => $listing->id,
            'user_id' => $buyer->id,
        ]);

        Livewire::actingAs($buyer)
            ->test('pages::listing', ['listing' => $listing])
            ->set('offerPrice', '10')
            ->call('makeOffer')
            ->assertHasErrors('offerPrice');

        $this->assertSame(1, $listing->offers()->count());
    }

    public function test_sellers_cannot_offer_on_their_own_listing(): void
    {
        $seller = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();

        Livewire::actingAs($seller)
            ->test('pages::listing', ['listing' => $listing])
            ->set('offerPrice', '10')
            ->call('makeOffer')
            ->assertForbidden();

        $this->assertDatabaseCount('marketplace_offers', 0);
    }

    public function test_sold_listings_take_no_offers(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();

        $this->assertTrue(Gate::forUser($buyer)->allows('makeOffer', $listing));

        $listing->update(['is_sold' => true]);

        $this->assertFalse(Gate::forUser($buyer)->allows('makeOffer', $listing));
    }

    public function test_the_seller_can_accept_an_offer(): void
    {
        Notification::fake();

        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();
        $offer = MarketplaceOffer::factory()->create([
            'marketplace_listing_id' => $listing->id,
            'user_id' => $buyer->id,
        ]);

        Livewire::actingAs($seller)
            ->test('pages::listing', ['listing' => $listing])
            ->call('updateOffer', $offer->id, 'accepted')
            ->assertHasNoErrors();

        $this->assertSame('accepted', $offer->fresh()->status);
        $this->assertTrue($listing->fresh()->is_sold);

        Notification::assertSentTo($buyer, MarketplaceOfferStatusChanged::class);
    }

    public function test_the_seller_can_decline_an_offer(): void
    {
        Notification::fake();

        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();
        $offer = MarketplaceOffer::factory()->create([
            'marketplace_listing_id' => $listing->id,
            'user_id' => $buyer->id,
        ]);

        Livewire::actingAs($seller)
            ->test('pages::listing', ['listing' => $listing])
            ->call('updateOffer', $offer->id, 'declined');

        $this->assertSame('declined', $offer->fresh()->status);
        $this->assertFalse($listing->fresh()->is_sold);
    }

    public function test_strangers_cannot_answer_offers(): void
    {
        $seller = User::factory()->create();
        $stranger = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();
        $offer = MarketplaceOffer::factory()->create([
            'marketplace_listing_id' => $listing->id,
            'user_id' => User::factory()->create(),
        ]);

        Livewire::actingAs($stranger)
            ->test('pages::listing', ['listing' => $listing])
            ->call('updateOffer', $offer->id, 'accepted')
            ->assertForbidden();

        $this->assertSame('pending', $offer->fresh()->status);
    }

    public function test_offers_cannot_be_answered_through_another_listing(): void
    {
        $seller = User::factory()->create();
        $otherSeller = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();
        $offer = MarketplaceOffer::factory()->create([
            'marketplace_listing_id' => $listing->id,
            'user_id' => User::factory()->create(),
        ]);

        // The seller of a different listing owns the page they are on, so authorization
        // passes — the offer id is still unreachable because it is scoped to their listing.
        Livewire::actingAs($otherSeller)
            ->test('pages::listing', ['listing' => MarketplaceListing::factory()->for($otherSeller, 'seller')->create()])
            ->call('updateOffer', $offer->id, 'accepted')
            ->assertNotFound();

        $this->assertSame('pending', $offer->fresh()->status);
    }

    public function test_an_unknown_offer_status_is_ignored(): void
    {
        $seller = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();
        $offer = MarketplaceOffer::factory()->create([
            'marketplace_listing_id' => $listing->id,
            'user_id' => User::factory()->create(),
        ]);

        Livewire::actingAs($seller)
            ->test('pages::listing', ['listing' => $listing])
            ->call('updateOffer', $offer->id, 'hacked');

        $this->assertSame('pending', $offer->fresh()->status);
    }

    public function test_deleting_a_listing_cascades_to_offers_and_saves(): void
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $listing = MarketplaceListing::factory()->for($seller, 'seller')->create();
        MarketplaceOffer::factory()->create([
            'marketplace_listing_id' => $listing->id,
            'user_id' => $buyer->id,
        ]);
        $listing->savers()->attach($buyer->id);

        $listing->delete();

        $this->assertDatabaseCount('marketplace_offers', 0);
        $this->assertDatabaseCount('listing_saves', 0);
    }
}
