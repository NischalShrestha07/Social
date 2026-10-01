<?php

namespace App\Models;

use Database\Factories\MarketplaceListingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'title', 'slug', 'description', 'price', 'currency', 'category', 'condition', 'location', 'image_path', 'is_sold'])]
class MarketplaceListing extends Model
{
    /** @use HasFactory<MarketplaceListingFactory> */
    use HasFactory;

    public const CATEGORIES = ['electronics', 'furniture', 'vehicles', 'clothing', 'books', 'sports', 'home', 'other'];

    public const CONDITIONS = ['new', 'like_new', 'good', 'fair'];

    public const CURRENCIES = ['USD', 'EUR', 'GBP', 'NPR', 'INR'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_sold' => 'boolean',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return HasMany<MarketplaceOffer, $this> */
    public function offers(): HasMany
    {
        return $this->hasMany(MarketplaceOffer::class);
    }

    /** @return BelongsToMany<User, $this> */
    public function savers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'listing_saves')->withTimestamps();
    }

    /** Sold listings drop out of the browse grid; the seller still sees them. */
    public function isAvailable(): bool
    {
        return ! $this->is_sold;
    }

    public function imageUrl(): string
    {
        return $this->image_path
            ? asset('storage/'.$this->image_path)
            : 'https://placehold.it/600x400/e8e9f2/9aa0b5?text='.urlencode($this->title);
    }

    public function isSavedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->savers()->where('users.id', $user->id)->exists();
    }

    /** @param Builder<MarketplaceListing> $query */
    public function scopeAvailable(Builder $query): void
    {
        $query->where('is_sold', false);
    }

    /** @param Builder<MarketplaceListing> $query */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        $term = trim((string) $term);

        if ($term === '') {
            return;
        }

        $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%");
        });
    }

    public function priceLabel(): string
    {
        return $this->currency.' '.number_format((float) $this->price, 2);
    }

    public static function categoryLabel(?string $category): string
    {
        return match ($category) {
            'electronics' => 'Electronics',
            'furniture' => 'Furniture',
            'vehicles' => 'Vehicles',
            'clothing' => 'Clothing',
            'books' => 'Books',
            'sports' => 'Sports',
            'home' => 'Home & Garden',
            default => 'Other',
        };
    }

    public static function conditionLabel(?string $condition): string
    {
        return match ($condition) {
            'new' => 'New',
            'like_new' => 'Like New',
            'good' => 'Good',
            default => 'Fair',
        };
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'listing';
        $slug = $base;
        $suffix = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
