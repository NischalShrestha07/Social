<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoComment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VideoComment>
 */
class VideoCommentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'video_id' => Video::factory(),
            'user_id' => User::factory(),
            'body' => fake()->sentence(),
        ];
    }
}
