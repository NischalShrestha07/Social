<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

// Idempotent: skips any group whose slug already exists.
class GroupSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('username', ['testuser', 'amycarter', 'jackdoe'])->get()->keyBy('username');

        if ($users->count() < 3) {
            return;
        }

        if (! Group::where('slug', 'weekend-hikers')->exists()) {
            $hikers = Group::create([
                'owner_id' => $users['testuser']->id,
                'name' => 'Weekend Hikers',
                'slug' => 'weekend-hikers',
                'description' => 'Plan trips, share trail photos and gear tips.',
                'privacy' => 'public',
            ]);
            $hikers->members()->attach($users['testuser']->id, ['role' => 'admin', 'status' => 'approved']);
            $hikers->members()->attach($users['amycarter']->id, ['role' => 'member', 'status' => 'approved']);

            Post::create(['user_id' => $users['testuser']->id, 'group_id' => $hikers->id, 'body' => 'Welcome to Weekend Hikers! Introduce yourself and your favourite trail.']);
            Post::create(['user_id' => $users['amycarter']->id, 'group_id' => $hikers->id, 'body' => 'Anyone up for Nagarkot this Saturday?']);
        }

        if (! Group::where('slug', 'book-club-insiders')->exists()) {
            $books = Group::create([
                'owner_id' => $users['amycarter']->id,
                'name' => 'Book Club Insiders',
                'slug' => 'book-club-insiders',
                'description' => 'Monthly reads and spoiler-full discussions. Members only.',
                'privacy' => 'private',
            ]);
            $books->members()->attach($users['amycarter']->id, ['role' => 'admin', 'status' => 'approved']);
            $books->members()->attach($users['jackdoe']->id, ['role' => 'member', 'status' => 'pending']);

            Post::create(['user_id' => $users['amycarter']->id, 'group_id' => $books->id, 'body' => 'This month: "The Remains of the Day". Discussion thread opens Friday.']);
        }
    }
}
