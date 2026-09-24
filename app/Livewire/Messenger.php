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

        $me = auth()->user();
        $conversation = $this->activeConversation();

        if (! $conversation && $this->targetUserId) {
            $target = User::findOrFail($this->targetUserId);
            abort_if($target->is($me), 403);
            $conversation = Conversation::between($me, $target);
            $this->conversationId = $conversation->id;
            $this->targetUserId = null;
        }

        abort_unless($conversation, 403);

        $conversation->messages()->create(['user_id' => $me->id, 'body' => $body]);
        $conversation->update(['last_message_at' => now()]);
        $conversation->markReadFor($me);

        $this->reset('body');
        $this->dispatch('message-sent');
    }

    // Scoped through the user's own conversations, so a non-participant always gets null.
    protected function activeConversation(): ?Conversation
    {
        return $this->conversationId
            ? auth()->user()->conversations()->with('users')->find($this->conversationId)
            : null;
    }

    public function render(): View
    {
        $me = auth()->user();
        $active = $this->activeConversation();
        $active?->markReadFor($me);

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
