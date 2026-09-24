<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public User $user;

    public string $body = '';

    public $image = null;

    public $avatar = null;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->loadPosts();
    }

    public function title(): string
    {
        return $this->user->name;
    }

    protected function loadPosts(): void
    {
        $this->user->load([
            'posts.user',
            'posts.comments.user',
            'posts.comments.reactions',
            'posts.comments.replies.user',
            'posts.comments.replies.reactions',
            'posts.reactions',
        ]);
    }

    public function updatedAvatar(): void
    {
        if (auth()->id() !== $this->user->id) {
            return;
        }

        $this->validate(['avatar' => 'image|max:5120']);

        $this->user->update(['avatar_path' => $this->avatar->store('avatars', 'public')]);
        $this->avatar = null;
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
        $this->loadPosts();
    }

    public function toggleLike(int $postId): void
    {
        $post = Post::findOrFail($postId);

        $existing = $post->reactions()->where('user_id', auth()->id())->where('type', 'like');

        if ($existing->exists()) {
            $existing->delete();
        } else {
            $post->reactions()->create(['user_id' => auth()->id(), 'type' => 'like']);
        }

        $this->loadPosts();
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

        $this->loadPosts();
    }

    public function addComment(int $postId, string $body): void
    {
        $body = trim($body);

        if ($body === '') {
            return;
        }

        Comment::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'body' => $body,
        ]);

        $this->loadPosts();
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $user->name])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="row merged20" id="page-contents">
							<div class="user-profile" @if($user->cover_path) style="background-image:url('{{ asset('storage/'.$user->cover_path) }}')" @endif>
								<figure>
									@if(auth()->id() === $user->id)
										<div class="edit-pp">
											<label class="fileContainer">
												<i class="fa fa-camera"></i>
												<input type="file" wire:model="avatar">
											</label>
										</div>
									@endif
									<img src="{{ $user->avatarUrl() }}" alt="">
									@if(auth()->id() === $user->id)
										<ul class="profile-controls">
											<li>
												<a href="{{ route('profile.edit') }}" title="Account Settings"><i class="fa fa-sliders"></i></a>
											</li>
										</ul>
									@endif
								</figure>
								
								<div class="profile-section">
									<div class="row">
										<div class="col-lg-2 col-md-3">
											<div class="profile-author">
												<div class="profile-author-thumb">
													<img alt="author" src="{{ $user->avatarUrl() }}">
												</div>

												<div class="author-content">
													<a class="h4 author-name" href="{{ route('profile.show', $user) }}">{{ $user->name }}</a>
												</div>
											</div>
										</div>
										<div class="col-lg-10 col-md-9">
											<ul class="profile-menu">
												<li>
													<a class="active" href="{{ route('profile.show', $user) }}">Timeline</a>
												</li>
												<li>
													<a class="" href="about.html">About</a>
												</li>
												<li>
													<a class="" href="timeline-friends.html">Friends</a>
												</li>
												<li>
													<a class="" href="timeline-photos.html">Photos</a>
												</li>
												<li>
													<a class="" href="timeline-videos.html">Videos</a>
												</li>
												<li>
													<div class="more">
														<i class="fa fa-ellipsis-h"></i>
														<ul class="more-dropdown">
															<li>
																<a href="timeline-groups.html">Profile Groups</a>
															</li>
															<li>
																<a href="statistics.html">Profile Analytics</a>
															</li>
														</ul>
													</div>
												</li>
											</ul>
											<ol class="folw-detail">
												<li><span>Posts</span><ins>{{ $user->posts->count() }}</ins></li>
											</ol>
										</div>
									</div>
								</div>	
							</div><!-- user profile banner  -->
							<x-pitnik.timeline-sidebar-left />
							<div class="col-lg-6">

									@if(auth()->id() === $user->id)
										<x-pitnik.post-composer :image="$image" />
									@endif

									@foreach($user->posts as $post)
										<x-pitnik.post-card :post="$post" wire:key="post-{{ $post->id }}" />
									@endforeach

							</div><!-- centerl meta -->
							<x-pitnik.timeline-sidebar-right :user="$user" />
						</div>	
					</div>
				</div>
			</div>
		</div>	
	</section><!-- content -->
@endcomponent
