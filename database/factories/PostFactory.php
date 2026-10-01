<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'body' => fake()->sentence(12),
            'image_path' => null,
        ];
    }

    public function group(Group $group): static
    {
        return $this->state(fn (array $attributes) => [
            'group_id' => $group->id,
        ]);
    }

    public function page(Page $page): static
    {
        return $this->state(fn (array $attributes) => [
            'page_id' => $page->id,
        ]);
    }

    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_path' => 'posts/'.fake()->uuid().'.jpg',
        ]);
    }
}
