<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->limit(4)->get();

        if ($users->isEmpty()) {
            return;
        }

        $categories = collect([
            ['General Discussion', 'Anything and everything about the platform.', 1],
            ['Help & Support', 'Stuck on something? Ask here.', 2],
            ['Show & Tell', 'Share what you have been building.', 3],
        ]);

        foreach ($categories as [$name, $description, $position]) {
            $category = ForumCategory::firstOrCreate(
                ['name' => $name],
                ['slug' => ForumCategory::uniqueSlug($name), 'description' => $description, 'position' => $position],
            );

            $author = $users[$position % $users->count()];

            $topic = ForumTopic::firstOrCreate(
                ['forum_category_id' => $category->id, 'title' => "Welcome to {$name}"],
                [
                    'user_id' => $author->id,
                    'slug' => ForumTopic::uniqueSlug("Welcome to {$name}"),
                    'body' => "This is the start of {$name}. Introduce yourself and say hello.",
                    'is_pinned' => true,
                    'last_posted_at' => now(),
                ],
            );

            $topic->posts()->firstOrCreate(
                ['user_id' => $author->id],
                ['body' => 'Pinned so this stays easy to find.'],
            );
        }
    }
}
