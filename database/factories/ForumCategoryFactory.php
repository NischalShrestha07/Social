<?php

namespace Database\Factories;

use App\Models\ForumCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ForumCategory>
 */
class ForumCategoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->word().' '.fake()->unique()->word());

        return [
            'name' => $name,
            'slug' => ForumCategory::uniqueSlug($name),
            'description' => fake()->sentence(),
            'position' => fake()->numberBetween(0, 10),
        ];
    }
}
