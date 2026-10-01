<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('category')->default('other');
            $table->string('condition')->default('good');
            $table->string('location');
            $table->string('image_path')->nullable();
            $table->boolean('is_sold')->default(false);
            $table->timestamps();
            $table->index(['is_sold', 'created_at']);
            $table->index(['category', 'condition']);
        });

        Schema::create('listing_saves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('marketplace_listing_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'marketplace_listing_id']);
        });

        Schema::create('marketplace_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketplace_listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('offer_price', 12, 2);
            $table->text('message')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->unique(['marketplace_listing_id', 'user_id']);
            $table->index(['marketplace_listing_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_offers');
        Schema::dropIfExists('listing_saves');
        Schema::dropIfExists('marketplace_listings');
    }
};
