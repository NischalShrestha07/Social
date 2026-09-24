<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('username', ['testuser', 'amycarter', 'jackdoe'])->get()->keyBy('username');

        if ($users->count() < 3) {
            return;
        }

        $threads = [
            // [peer, [[fromTestUser?, body, minutesAgo], ...], testuser has read it?]
            ['amycarter', [
                [false, 'Hey! Saw your post about the new platform, how are you finding it?', 50],
                [true, 'Pretty good so far, still exploring everything.', 45],
                [false, 'Nice. Let me know if you want a tour of the features.', 40],
                [true, 'Will do, thanks Amy!', 38],
            ], true],
            ['jackdoe', [
                [false, 'Those sunset photos were from the ridge trail btw.', 12],
                [false, 'We should go hike it sometime.', 10],
            ], false],
        ];

        $me = $users['testuser'];

        foreach ($threads as [$peerName, $messages, $read]) {
            $peer = $users[$peerName];

            if (Conversation::findBetween($me, $peer)) {
                continue;
            }

            $conversation = Conversation::between($me, $peer);

            foreach ($messages as [$mine, $body, $minutesAgo]) {
                $message = $conversation->messages()->create([
                    'user_id' => $mine ? $me->id : $peer->id,
                    'body' => $body,
                ]);
                $message->forceFill(['created_at' => now()->subMinutes($minutesAgo), 'updated_at' => now()->subMinutes($minutesAgo)])->save();
            }

            $conversation->update(['last_message_at' => now()->subMinutes(end($messages)[2])]);
            $conversation->users()->updateExistingPivot($peer->id, ['last_read_at' => now()]);

            if ($read) {
                $conversation->users()->updateExistingPivot($me->id, ['last_read_at' => now()]);
            }
        }
    }
}
