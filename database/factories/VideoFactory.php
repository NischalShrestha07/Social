<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Video>
 */
class VideoFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucfirst(fake()->unique()->word().' '.fake()->unique()->word().' '.fake()->unique()->word());

        return [
            'video_channel_id' => VideoChannel::factory(),
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Video::uniqueSlug($title),
            'description' => fake()->sentence(),
            'duration_seconds' => fake()->numberBetween(30, 3600),
            'visibility' => 'public',
            'views_count' => 0,
        ];
    }

    public function unlisted(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'unlisted',
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'visibility' => 'private',
        ]);
    }
}
