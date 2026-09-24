<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

// Idempotent: skips any page whose slug already exists.
class PageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('username', ['testuser', 'amycarter', 'jackdoe', 'sarahkhan'])->get()->keyBy('username');

        if ($users->count() < 4) {
            return;
        }

        if (! Page::where('slug', 'tech-daily')->exists()) {
            $tech = Page::create([
                'owner_id' => $users['testuser']->id,
                'name' => 'Tech Daily',
                'slug' => 'tech-daily',
                'category' => 'Technology',
                'description' => 'Bite-sized tech news, tools and tutorials every day.',
                'website' => 'https://example.com/tech-daily',
            ]);
            $tech->likers()->attach([$users['amycarter']->id, $users['jackdoe']->id]);

            Post::create(['user_id' => $users['testuser']->id, 'page_id' => $tech->id, 'body' => 'Welcome to Tech Daily! Follow for a new tip every morning.']);
            Post::create(['user_id' => $users['testuser']->id, 'page_id' => $tech->id, 'body' => 'Tip #1: learn your editor\'s multi-cursor shortcuts — it pays off within a week.']);
        }

        if (! Page::where('slug', 'mountain-coffee-co')->exists()) {
            $coffee = Page::create([
                'owner_id' => $users['sarahkhan']->id,
                'name' => 'Mountain Coffee Co.',
                'slug' => 'mountain-coffee-co',
                'category' => 'Business',
                'description' => 'Single-origin beans roasted in small batches.',
            ]);
            $coffee->likers()->attach($users['testuser']->id);

            Post::create(['user_id' => $users['sarahkhan']->id, 'page_id' => $coffee->id, 'body' => 'New harvest from Ilam just landed. Pour-overs are on us this weekend!']);
        }
    }
}
