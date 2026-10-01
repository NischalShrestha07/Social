<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\VideoChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VideoChannel>
 */
class VideoChannelFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => VideoChannel::uniqueSlug($name),
            'description' => fake()->sentence(),
            'subscribers_count' => 0,
        ];
    }
}
