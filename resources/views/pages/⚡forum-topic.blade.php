<?php

use App\Models\ForumTopic;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Forum Topic'), Layout('layouts::bare')] class extends Component
{
    #[Locked]
    public int $topicId;

    public string $reply = '';

    public function mount(ForumTopic $topic): void
    {
        $this->topicId = $topic->id;
    }

    #[Computed]
    public function topic(): ForumTopic
    {
        return ForumTopic::with(['user', 'category'])->findOrFail($this->topicId);
    }

    #[Computed]
    public function replies()
    {
        return $this->topic()
            ->posts()
            ->with('user')
            ->oldest()
            ->paginate(20);
    }

    #[Computed]
    public function canReply(): bool
    {
        return auth()->user()->can('reply', $this->topic());
    }

    public function addReply(): void
    {
        $topic = $this->topic();
        Gate::authorize('reply', $topic);

        $validated = $this->validate([
            'reply' => 'required|string|max:20000',
        ]);

        // Post ids never come from the client; the relation scopes to this topic.
        $topic->reply($validated['reply']);

        $this->reset('reply');
        unset($this->replies, $this->topic);
        session()->flash('status', 'Reply posted.');
    }

    public function togglePostLike(int $postId): void
    {
        $post = $this->topic()
            ->posts()
            ->whereKey($postId)
            ->firstOrFail();

        $post->toggleLike(auth()->user());
        unset($this->replies);
    }

    public function deletePost(int $postId): void
    {
        $post = $this->topic()
            ->posts()
            ->whereKey($postId)
            ->firstOrFail();

        Gate::authorize('delete', $post);

        $post->delete();
        unset($this->replies);
    }

    public function toggleLock(): void
    {
        $topic = $this->topic();
        Gate::authorize('toggleLock', $topic);

        if ($topic->is_locked) {
            $topic->update(['is_locked' => false, 'locked_by' => null, 'locked_at' => null]);
        } else {
            $topic->update(['is_locked' => true, 'locked_by' => auth()->id(), 'locked_at' => now()]);
        }

        unset($this->topic, $this->canReply);
    }

    public function togglePin(): void
    {
        $topic = $this->topic();
        Gate::authorize('pin', $topic);

        $topic->update(['is_pinned' => ! $topic->is_pinned]);
        unset($this->topic);
    }

    public function deleteTopic(): void
    {
        $topic = $this->topic();
        Gate::authorize('delete', $topic);

        // The category is fetched before the delete so the slug survives the topic row.
        $category = $topic->category;

        $topic->delete();

        $this->redirectRoute('forum.category', $category);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $this->topic->title])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>{{ $this->topic->title }}</h5>
									<span style="color:#757a95;">
										in <a href="{{ route('forum.category', $this->topic->category) }}">{{ $this->topic->category->name }}</a>
										· by <a href="{{ route('profile.show', $this->topic->user) }}">{{ $this->topic->user->name }}</a>
										· {{ $this->topic->created_at->diffForHumans() }}
										@if($this->topic->is_pinned) · <strong style="color:#088dcd;">Pinned</strong> @endif
										@if($this->topic->is_locked) · <strong style="color:#e0245e;">Locked</strong> @endif
									</span>
								</div>
								<div class="align-right">
									<a href="{{ route('forum.category', $this->topic->category) }}" class="main-btn3">Back</a>
								</div>
							</div>
						</div>

						<div class="central-meta padding30">
							<div class="row">
								<div class="col-lg-12">
									<div style="display:flex;gap:15px;align-items:flex-start;">
										<figure style="margin:0;flex-shrink:0;">
											<img src="{{ $this->topic->user->avatarUrl() }}" alt="" style="width:52px;height:52px;border-radius:50%;object-fit:cover;">
										</figure>
										<div style="flex:1;">
											<p style="color:#535165;white-space:pre-line;">{{ $this->topic->body }}</p>
											<div style="margin-top:18px;display:flex;gap:10px;">
												@can('pin', $this->topic)
													<button type="button" class="main-btn3" wire:click="togglePin">
														{{ $this->topic->is_pinned ? 'Unpin' : 'Pin' }}
													</button>
												@endcan
												@can('toggleLock', $this->topic)
													<button type="button" class="main-btn3" wire:click="toggleLock">
														{{ $this->topic->is_locked ? 'Unlock' : 'Lock' }}
													</button>
												@endcan
												@can('delete', $this->topic)
													<button type="button" class="main-btn3" style="background:#e0245e;color:#fff;"
														wire:click="deleteTopic" wire:confirm="Delete this topic and all its replies?">
														Delete Topic
													</button>
												@endcan
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>{{ $this->replies->total() }} {{ Str::plural('Reply', $this->replies->total()) }}</h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							@if(session('status'))
								<div style="background:#e6f7ed;color:#1b7a45;padding:12px 16px;border-radius:4px;margin-bottom:20px;">
									{{ session('status') }}
								</div>
							@endif

							@if($this->canReply)
								<form wire:submit="addReply" class="c-form" style="margin-bottom:30px;">
									<div>
										<label>Your reply</label>
										<textarea rows="4" wire:model="reply" required maxlength="20000" placeholder="Write a reply..."></textarea>
										@error('reply') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Post Reply</button>
									</div>
								</form>
							@else
								<div style="background:#f2f2f7;padding:15px;border-radius:4px;margin-bottom:30px;color:#757a95;">
									This topic is locked. No new replies can be added.
								</div>
							@endif

							<div class="row">
								@forelse($this->replies as $post)
									<div class="col-lg-12" wire:key="post-{{ $post->id }}">
										<div class="widget-shadow" style="padding:18px;margin-bottom:12px;">
											<div style="display:flex;gap:15px;align-items:flex-start;">
												<figure style="margin:0;flex-shrink:0;">
													<img src="{{ $post->user->avatarUrl() }}" alt="" style="width:44px;height:44px;border-radius:50%;object-fit:cover;">
												</figure>
												<div style="flex:1;">
													<div style="display:flex;justify-content:space-between;align-items:center;">
														<h5 style="margin:0;font-size:15px;">
															<a href="{{ route('profile.show', $post->user) }}">{{ $post->user->name }}</a>
														</h5>
														<span style="color:#757a95;font-size:13px;">{{ $post->created_at->diffForHumans() }}</span>
													</div>
													<p style="color:#535165;white-space:pre-line;margin-top:10px;">{{ $post->body }}</p>
													<div style="margin-top:14px;display:flex;gap:12px;align-items:center;">
														<button type="button" wire:click="togglePostLike({{ $post->id }})"
															style="background:none;border:0;color:{{ $post->isLikedBy(auth()->user()) ? '#e0245e' : '#757a95' }};cursor:pointer;font:inherit;">
															<i class="fa-regular fa-thumbs-up"></i>
															{{ $post->likesCount() }}
														</button>
														@can('delete', $post)
															<button type="button" class="main-btn3" style="background:#e0245e;color:#fff;"
																wire:click="deletePost({{ $post->id }})" wire:confirm="Delete this reply?">
																Delete
															</button>
														@endcan
													</div>
												</div>
											</div>
										</div>
									</div>
								@empty
									<div class="col-lg-12"><p>No replies yet. Start the conversation.</p></div>
								@endforelse
							</div>

							@if($this->replies->hasPages())
								<div class="row">
									<div class="col-lg-12 text-center">{{ $this->replies->links() }}</div>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent