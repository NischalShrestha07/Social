<?php

use App\Models\Video;
use App\Models\VideoComment;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Video'), Layout('layouts::bare')] class extends Component
{
    #[Locked]
    public int $videoId;

    public string $commentBody = '';

    public int $replyTo = 0;

    public function mount(Video $video): void
    {
        Gate::authorize('view', $video);

        $this->videoId = $video->id;
    }

    #[Computed]
    public function video(): Video
    {
        return Video::with(['channel', 'uploader'])->findOrFail($this->videoId);
    }

    #[Computed]
    public function comments()
    {
        return $this->video()
            ->comments()
            ->with(['user', 'replies.user'])
            ->paginate(15);
    }

    public function toggleLike(): void
    {
        $video = $this->video();

        $video->toggleLike(auth()->user());
    }

    public function addComment(): void
    {
        $video = $this->video();
        Gate::authorize('comment', $video);

        $validated = $this->validate([
            'commentBody' => 'required|string|max:5000',
        ]);

        // The parent is always re-fetched from this video, so it can't point elsewhere.
        $parent = $this->replyTo
            ? $video->comments()->whereKey($this->replyTo)->firstOrFail()
            : null;

        $video->comment($validated['commentBody'], auth()->user(), $parent);

        $this->reset('commentBody', 'replyTo');
        unset($this->comments);
    }

    public function deleteComment(int $commentId): void
    {
        // Replies too, so a comment id can only ever address a comment on this video.
        $comment = $this->video()
            ->allComments()
            ->whereKey($commentId)
            ->firstOrFail();

        Gate::authorize('delete', $comment);

        $comment->delete();
        unset($this->comments);
    }

    public function deleteVideo(): void
    {
        $video = $this->video();
        Gate::authorize('delete', $video);

        $video->delete();

        $this->redirectRoute('videos.channel', $video->channel);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $this->video->title])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta padding30">
							<div class="row">
								<div class="col-lg-8">
									@if($this->video->videoUrl())
										<video controls style="width:100%;border-radius:6px;background:#000;">
											<source src="{{ $this->video->videoUrl() }}">
											Your browser cannot play this video.
										</video>
									@else
										<img src="{{ $this->video->thumbnailUrl() }}" alt="" style="width:100%;border-radius:6px;">
									@endif
								</div>
								<div class="col-lg-4">
									<h5 class="f-title">{{ $this->video->title }}</h5>
									<span style="color:#757a95;font-size:13px;">
										{{ number_format($this->video->views_count) }} {{ Str::plural('view', $this->video->views_count) }}
										· {{ $this->video->created_at->diffForHumans() }}
										@if($this->video->durationLabel()) · {{ $this->video->durationLabel() }} @endif
										@if($this->video->visibility !== 'public') · {{ \App\Models\Video::visibilityLabel($this->video->visibility) }} @endif
									</span>

									<div style="display:flex;gap:15px;align-items:center;margin-top:18px;">
										<img src="{{ $this->video->channel->avatarUrl() }}" alt="" style="width:46px;height:46px;border-radius:50%;object-fit:cover;">
										<div>
											<span style="color:#535165;">
												<a href="{{ route('videos.channel', $this->video->channel) }}">{{ $this->video->channel->name }}</a>
											</span>
											<span style="display:block;color:#757a95;font-size:13px;">
												{{ number_format($this->video->channel->subscribers_count) }} {{ Str::plural('subscriber', $this->video->channel->subscribers_count) }}
											</span>
										</div>
									</div>

									<div style="margin-top:18px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
										<button type="button" class="main-btn3" wire:click="toggleLike"
											style="color:{{ $this->video->isLikedBy(auth()->user()) ? '#e0245e' : '#535165' }};">
											<i class="fa-regular fa-thumbs-up"></i>
											{{ $this->video->likesCount() }}
										</button>
										@can('update', $this->video)
											<a href="{{ route('videos.edit', $this->video) }}" class="main-btn3">Edit</a>
										@endcan
										@can('delete', $this->video)
											<button type="button" class="main-btn3" style="background:#e0245e;color:#fff;"
												wire:click="deleteVideo" wire:confirm="Delete this video and its comments?">
												Delete
											</button>
										@endcan
									</div>

									@if($this->video->description)
										<p style="color:#535165;white-space:pre-line;margin-top:18px;">{{ $this->video->description }}</p>
									@endif
								</div>
							</div>
						</div>

						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Comments <span>{{ $this->comments->total() }}</span></h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							@can('comment', $this->video)
								<form wire:submit="addComment" class="c-form" style="margin-bottom:30px;">
									@if($replyTo)
										<div style="margin-bottom:12px;color:#757a95;">
											Replying to a comment
											<button type="button" wire:click="$set('replyTo', 0)" style="background:none;border:0;color:#e0245e;cursor:pointer;font:inherit;">cancel</button>
										</div>
									@endif
									<div>
										<textarea rows="3" wire:model="commentBody" required maxlength="5000" placeholder="Add a comment..."></textarea>
										@error('commentBody') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Post Comment</button>
									</div>
								</form>
							@endcan

							@forelse($this->comments as $comment)
								<div class="widget-shadow" style="padding:18px;margin-bottom:12px;" wire:key="comment-{{ $comment->id }}">
									<div style="display:flex;gap:15px;align-items:flex-start;">
										<img src="{{ $comment->user->avatarUrl() }}" alt="" style="width:42px;height:42px;border-radius:50%;object-fit:cover;">
										<div style="flex:1;">
											<div style="display:flex;justify-content:space-between;">
												<span style="color:#535165;">
													<a href="{{ route('profile.show', $comment->user) }}">{{ $comment->user->name }}</a>
												</span>
												<span style="color:#757a95;font-size:13px;">{{ $comment->created_at->diffForHumans() }}</span>
											</div>
											<p style="color:#535165;margin-top:8px;">{{ $comment->body }}</p>
											<div style="margin-top:10px;">
												@can('comment', $this->video)
													<button type="button" wire:click="$set('replyTo', {{ $comment->id }})"
														style="background:none;border:0;color:#757a95;cursor:pointer;font:inherit;">Reply</button>
												@endcan
												@can('delete', $comment)
													<button type="button" wire:click="deleteComment({{ $comment->id }})"
														style="background:none;border:0;color:#e0245e;cursor:pointer;font:inherit;margin-left:14px;">Delete</button>
												@endcan
											</div>

											@foreach($comment->replies as $reply)
												<div style="margin-top:14px;padding-left:14px;border-left:2px solid #f2f2f7;" wire:key="reply-{{ $reply->id }}">
													<span style="color:#535165;">
														<a href="{{ route('profile.show', $reply->user) }}">{{ $reply->user->name }}</a>
														<span style="color:#757a95;font-size:13px;">{{ $reply->created_at->diffForHumans() }}</span>
													</span>
													<p style="color:#535165;margin-top:6px;">{{ $reply->body }}</p>
													@can('delete', $reply)
														<button type="button" wire:click="deleteComment({{ $reply->id }})"
															style="background:none;border:0;color:#e0245e;cursor:pointer;font:inherit;">Delete</button>
													@endcan
												</div>
											@endforeach
										</div>
									</div>
								</div>
							@empty
								<p>No comments yet.</p>
							@endforelse

							@if($this->comments->hasPages())
								<div class="row">
									<div class="col-lg-12 text-center">{{ $this->comments->links() }}</div>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
