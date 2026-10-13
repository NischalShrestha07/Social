<?php

namespace App\Livewire\Concerns;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Notifications\PostCommented;
use App\Notifications\PostLiked;
use Illuminate\Support\Facades\Gate;

// Host component must also `use WithFileUploads` (the composer's image field).
/** @phpstan-ignore-next-line */
trait InteractsWithPosts
{
    public string $body = '';

    public $image = null;

    // Extra attributes merged into every new post (e.g. group_id on a group page).
    protected function postAttributes(): array
    {
        return [];
    }

    protected function refreshPosts(): void
    {
        unset($this->posts);
    }

    protected function authorizeCreatePost(): void {}

    // Any post id can arrive from the client, so gate by the post's group, if it has one.
    protected function authorizePostInteraction(Post $post): void
    {
        if ($post->group_id) {
            Gate::authorize('view', $post->group);
        }
    }

    public function createPost(): void
    {
        $this->authorizeCreatePost();

        $validated = $this->validate([
            'body' => 'required_without:image|nullable|string|max:5000',
            'image' => 'nullable|image|max:5120',
        ]);

        Post::create([
            ...$this->postAttributes(),
            'user_id' => auth()->id(),
            'body' => $validated['body'] ?? null,
            'image_path' => $this->image?->store('posts', 'public'),
        ]);

        $this->reset(['body', 'image']);
        $this->refreshPosts();
    }

    public function toggleLike(int $postId): void
    {
        $post = Post::findOrFail($postId);
        $this->authorizePostInteraction($post);

        $user = auth()->user();

        if (! $user) {
            return;
        }

        $existing = $post->reactions()->where('user_id', $user->id)->where('type', 'like');

        if ($existing->exists()) {
            $existing->delete();
        } elseif (Reaction::createSafely($post, $user) && $post->user_id !== $user->id) {
            // Only notify when this request actually created the like.
            $post->user->notify(new PostLiked($user, $post));
        }

        $this->refreshPosts();
    }

    public function toggleCommentLike(int $commentId): void
    {
        $comment = Comment::with('post')->findOrFail($commentId);
        $this->authorizePostInteraction($comment->post);

        $user = auth()->user();

        if (! $user) {
            return;
        }

        $existing = $comment->reactions()->where('user_id', $user->id)->where('type', 'like');

        if ($existing->exists()) {
            $existing->delete();
        } else {
            Reaction::createSafely($comment, $user);
        }

        $this->refreshPosts();
    }

    public function addComment(int $postId, string $body): void
    {
        $body = trim($body);

        if ($body === '' || mb_strlen($body) > 5000) {
            return;
        }

        $post = Post::findOrFail($postId);
        $this->authorizePostInteraction($post);

        Comment::create([
            'post_id' => $post->id,
            'user_id' => auth()->id(),
            'body' => $body,
        ]);

        if ($post->user_id !== auth()->id()) {
            $post->user->notify(new PostCommented(auth()->user(), $post));
        }

        $this->refreshPosts();
    }
}
