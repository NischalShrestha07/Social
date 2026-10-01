<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Video;
use App\Models\VideoChannel;
use App\Models\VideoComment;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'creator@example.test'],
            ['name' => 'Casey Creator', 'username' => 'caseycreator', 'password' => 'password', 'email_verified_at' => now()],
        );

        $viewer = User::firstOrCreate(
            ['email' => 'viewer@example.test'],
            ['name' => 'Vic Viewer', 'username' => 'vicviewer', 'password' => 'password', 'email_verified_at' => now()],
        );

        $channels = [
            ['Laravel Lens', 'Weekly deep dives into Laravel and PHP, no fluff.', [['Getting started with Livewire', 'public'], ['Queues in production', 'public'], ['Roadmap walkthrough', 'unlisted']]],
            ['Kitchen Chronicles', 'Recipes and the mess behind them.', [['Sourdough for the impatient', 'public'], ['One-pan dinner', 'public']]],
        ];

        foreach ($channels as [$name, $description, $videos]) {
            $channel = VideoChannel::firstOrCreate(
                ['name' => $name],
                ['user_id' => $owner->id, 'slug' => VideoChannel::uniqueSlug($name), 'description' => $description],
            );

            foreach ($videos as [$title, $visibility]) {
                $video = Video::firstOrCreate(
                    ['title' => $title],
                    [
                        'video_channel_id' => $channel->id,
                        'user_id' => $owner->id,
                        'slug' => Video::uniqueSlug($title),
                        'description' => "A full walkthrough of {$title}. Recorded in one take, captions coming soon.",
                        'duration_seconds' => 180 + (mb_strlen($title) * 7),
                        'visibility' => $visibility,
                        'views_count' => random_int(120, 9000),
                    ],
                );

                VideoComment::firstOrCreate(
                    ['video_id' => $video->id, 'user_id' => $viewer->id],
                    ['body' => 'This finally made it click for me. Thanks for the clear explanation.'],
                );
            }

            $channel->subscribe($viewer);
        }
    }
}
