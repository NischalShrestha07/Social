<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Messenger extends Component
{
    #[Locked]
    public ?int $conversationId = null;

    #[Locked]
    public ?int $targetUserId = null;

    public string $body = '';

    public function mount(?User $with = null): void
    {
        $me = auth()->user();

        if ($with) {
            $this->conversationId = Conversation::findBetween($me, $with)?->id;
            $this->targetUserId = $this->conversationId ? null : $with->id;

            return;
        }

        $this->conversationId = $me->conversations()
            ->whereNotNull('last_message_at')
            ->orderByDesc('last_message_at')
            ->value('conversations.id');
    }

    public function send(): void
    {
        $this->validate(['body' => 'required|string|max:5000']);

        $body = trim($this->body);
        if ($body === '') {
            return;
        }

        /** @var User $me */
        $me = auth()->user();
        $conversation = $this->activeConversation();

        if (! $conversation && $this->targetUserId) {
            $target = User::findOrFail($this->targetUserId);
            abort_if($target->id === $me->id, 403);
            $conversation = Conversation::between($me, $target);
            $this->conversationId = $conversation->id;
            $this->targetUserId = null;
        }

        abort_unless($conversation !== null, 403);

        $conversation->messages()->create(['user_id' => $me->id, 'body' => $body]);
        $conversation->update(['last_message_at' => now()]);
        $conversation->markReadFor($me);

        $this->reset('body');
        $this->dispatch('message-sent');
    }

    protected function activeConversation(): ?Conversation
    {
        /** @var Conversation|null $conv */
        $conv = $this->conversationId
            ? auth()->user()->conversations()->with('users')->find($this->conversationId)
            : null;

        return $conv;
    }

    public function render(): View
    {
        $me = auth()->user();
        $active = $this->activeConversation();

        // Only write last_read_at when there is actually something unread —
        // this component re-renders on every 3s poll.
        if ($active && $active->unreadCountFor($me) > 0) {
            $active->markReadFor($me);
        }

        $conversations = $me->conversations()
            ->whereNotNull('last_message_at')
            ->with(['users', 'latestMessage'])
            ->orderByDesc('last_message_at')
            ->get();

        return view('livewire.messenger', [
            'conversations' => $conversations,
            'active' => $active,
            'other' => $active?->otherParticipant($me) ?? ($this->targetUserId ? User::find($this->targetUserId) : null),
            // ponytail: last 100 messages only, add "load older" when threads get long.
            'messages' => $active
                ? $active->messages()->with('user')->latest()->latest('id')->take(100)->get()->reverse()
                : collect(),
        ]);
    }
}
