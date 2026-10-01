<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = collect([
            ['name' => 'Test User', 'username' => 'testuser', 'email' => 'test@example.com', 'bio' => 'Just here testing the feed.'],
            ['name' => 'Amy Carter', 'username' => 'amycarter', 'email' => 'amy@example.com', 'bio' => 'Coffee, code, and cats.'],
            ['name' => 'Jack Doe', 'username' => 'jackdoe', 'email' => 'jack@example.com', 'bio' => 'Photographer and traveler.'],
            ['name' => 'Sarah Khan', 'username' => 'sarahkhan', 'email' => 'sarah@example.com', 'bio' => 'Building things for the web.'],
        ])->map(fn (array $attrs) => User::create([
            ...$attrs,
            'password' => 'password',
            'email_verified_at' => now(),
        ]));

        $posts = collect([
            [0, 'Excited to try out this new platform!'],
            [1, 'Just shipped a new feature at work. Feeling good.'],
            [1, 'Anyone got recommendations for a good coffee grinder?'],
            [2, 'Sunset from last night\'s hike.'],
            [3, 'Refactored the whole auth flow today, much cleaner now.'],
        ])->map(fn (array $p) => Post::create([
            'user_id' => $users[$p[0]]->id,
            'body' => $p[1],
        ]));

        $comments = collect([
            [0, 0, 'Welcome aboard!'],
            [0, 1, 'Glad to have you here.'],
            [3, 2, 'That view is incredible.'],
            [4, 3, 'Nice, refactors like that always feel great.'],
        ])->map(fn (array $c) => Comment::create([
            'post_id' => $posts[$c[0]]->id,
            'user_id' => $users[$c[1]]->id,
            'body' => $c[2],
        ]));

        Comment::create([
            'post_id' => $comments[2]->post_id,
            'user_id' => $users[2]->id,
            'parent_id' => $comments[2]->id,
            'body' => 'Thanks! It was worth the climb.',
        ]);

        foreach ([[0, 1], [0, 2], [1, 0], [3, 0], [3, 1]] as [$postIndex, $userIndex]) {
            Reaction::create([
                'user_id' => $users[$userIndex]->id,
                'reactable_type' => Post::class,
                'reactable_id' => $posts[$postIndex]->id,
                'type' => 'like',
            ]);
        }

        $this->call([MessageSeeder::class, GroupSeeder::class, PageSeeder::class, JobSeeder::class, ForumSeeder::class, MarketplaceSeeder::class, VideoSeeder::class]);
    }
}
