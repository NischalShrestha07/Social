<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\Story;
use App\Notifications\PostCommented;
use App\Notifications\PostLiked;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Newsfeed'), \Livewire\Attributes\Layout('layouts::bare')] class extends Component
{
    use WithFileUploads;

    public string $body = '';

    public $image = null;

    public $storyImage = null;

    public string $storyCaption = '';

    #[Computed]
    public function posts()
    {
        return Post::with([
            'user',
            'comments.user',
            'comments.reactions',
            'comments.replies.user',
            'comments.replies.reactions',
            'reactions',
        ])->latest()->get();
    }

    #[Computed]
    public function activeStoryGroups()
    {
        return Story::active()
            ->where('user_id', '!=', auth()->id())
            ->with('user')
            ->latest()
            ->get()
            ->groupBy('user_id');
    }

    public function createStory(): void
    {
        $this->validate([
            'storyImage' => 'required|image|max:5120',
            'storyCaption' => 'nullable|string|max:255',
        ]);

        auth()->user()->stories()->create([
            'media_path' => $this->storyImage->store('stories', 'public'),
            'caption' => $this->storyCaption ?: null,
            'expires_at' => now()->addDay(),
        ]);

        $this->reset(['storyImage', 'storyCaption']);
        unset($this->activeStoryGroups);
        $this->dispatch('story-created');
    }

    public function createPost(): void
    {
        $validated = $this->validate([
            'body' => 'required_without:image|nullable|string|max:5000',
            'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = $this->image?->store('posts', 'public');

        auth()->user()->posts()->create([
            'body' => $validated['body'] ?? null,
            'image_path' => $imagePath,
        ]);

        $this->reset(['body', 'image']);
        unset($this->posts);
    }

    public function toggleLike(int $postId): void
    {
        $post = Post::findOrFail($postId);

        $existing = $post->reactions()->where('user_id', auth()->id())->where('type', 'like');

        if ($existing->exists()) {
            $existing->delete();
        } else {
            $post->reactions()->create(['user_id' => auth()->id(), 'type' => 'like']);

            if ($post->user_id !== auth()->id()) {
                $post->user->notify(new PostLiked(auth()->user(), $post));
            }
        }

        unset($this->posts);
    }

    public function toggleCommentLike(int $commentId): void
    {
        $comment = Comment::findOrFail($commentId);

        $existing = $comment->reactions()->where('user_id', auth()->id())->where('type', 'like');

        if ($existing->exists()) {
            $existing->delete();
        } else {
            $comment->reactions()->create(['user_id' => auth()->id(), 'type' => 'like']);
        }

        unset($this->posts);
    }

    public function addComment(int $postId, string $body): void
    {
        $body = trim($body);

        if ($body === '') {
            return;
        }

        $comment = Comment::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'body' => $body,
        ]);

        if ($comment->post->user_id !== auth()->id()) {
            $comment->post->user->notify(new PostCommented(auth()->user(), $comment->post));
        }

        unset($this->posts);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Newsfeed'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="row merged20" id="page-contents">
							<x-pitnik.feed-sidebar-left />
							<div class="col-lg-6">

									<x-pitnik.post-composer :image="$image" />

								<div class="central-meta">
									<x-pitnik.story-bar :story-image="$storyImage" :groups="$this->activeStoryGroups" />
								</div><!-- top stories -->

									@foreach($this->posts as $post)
										<x-pitnik.post-card :post="$post" wire:key="post-{{ $post->id }}" />
									@endforeach

							</div><!-- centerl meta -->
							<x-pitnik.feed-sidebar-right />
						</div>	
					</div>
				</div>
			</div>
		</div>	
	</section>
@endcomponent
