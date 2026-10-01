<?php

namespace Database\Factories;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ForumTopic>
 */
class ForumTopicFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'forum_category_id' => ForumCategory::factory(),
            'user_id' => User::factory(),
            'title' => rtrim($title, '.'),
            'slug' => ForumTopic::uniqueSlug($title),
            'body' => fake()->paragraphs(2, true),
            'is_pinned' => false,
            'is_locked' => false,
            'last_posted_at' => now(),
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
        ]);
    }

    public function lockedBy(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'is_locked' => true,
            'locked_by' => $user->id,
            'locked_at' => now(),
        ]);
    }
}
