<?php

use App\Models\Comment;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Newsfeed')] class extends Component
{
    use WithFileUploads;

    public string $body = '';

    public $image = null;

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

        Comment::create([
            'post_id' => $postId,
            'user_id' => auth()->id(),
            'body' => $body,
        ]);

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
									<span class="create-post">Reels<a href="#" title="">See All</a></span>
									<div class="story-postbox">
										<div class="row">
											<div class="col-lg-3 col-md-3 col-sm-3">
												<div class="story-box">
													<figure>
														<img src="/vendor/pitnik/images/resources/story-1.jpg" alt="">
														<span>Add Your Story</span>
													</figure>
													<div class="story-thumb" data-toggle="tooltip" title="Add Your Story">
														<i class="fa fa-plus"></i>
													</div>
												</div>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3">
												<div class="story-box">
													<figure>
														<img src="/vendor/pitnik/images/resources/story-2.jpg" alt="">
														<span>Adam James</span>
													</figure>
													<div class="story-thumb" data-toggle="tooltip" title="Adam James">
														<img src="/vendor/pitnik/images/resources/thumb-2.jpg" alt="">
													</div>
												</div>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3">
												<div class="story-box">
													<figure>
														<img src="/vendor/pitnik/images/resources/story-3.jpg" alt="">
														<span>Emily grey</span>
													</figure>
													<div class="story-thumb" data-toggle="tooltip" title="Emily grey">
														<img src="/vendor/pitnik/images/resources/thumb-3.jpg" alt="">
													</div>
												</div>
											</div>
											<div class="col-lg-3 col-md-3 col-sm-3">
												<div class="story-box">
													<figure>
														<img src="/vendor/pitnik/images/resources/story-4.jpg" alt="">
														<span>Jhon Deo</span>
													</figure>
													<div class="story-thumb" data-toggle="tooltip" title="Jhon Deo">
														<img src="/vendor/pitnik/images/resources/thumb-4.jpg" alt="">
													</div>
												</div>
											</div>
										</div>
										<div class="stories-wraper">
											<div class="status-story">
												<span class="close-story"><i class="ti-close"></i></span>
												<div class="row">
													<div class="col-lg-4">
														<div class="stories-users">
															<h5>Stories</h5>
															<div class="my-status">
																<figure><img src="/vendor/pitnik/images/resources/friend-avatar10.jpg" alt=""></figure>
																<a href="#" title="" class="main-btn">Add New</a>
																<div class="story-user">
																	<span>My Story</span>
																	<ins>No updates</ins>
																</div>
															</div>
															<ul class="frnds-stories">
																<li>
																	<figure><img src="/vendor/pitnik/images/resources/friend-avatar11.jpg" alt=""></figure>
																	<div class="story-user">
																		<span>Emily doll</span>
																		<ins>yesterday</ins>
																	</div>
																</li>
																<li>
																	<figure><img src="/vendor/pitnik/images/resources/friend-avatar15.jpg" alt=""></figure>
																	<div class="story-user">
																		<span>Jhon Borny</span>
																		<ins>3 days ago</ins>
																	</div>
																</li>
																<li>
																	<figure><img src="/vendor/pitnik/images/resources/friend-avatar12.jpg" alt=""></figure>
																	<div class="story-user">
																		<span>Sarah Khan</span>
																		<ins>2 days ago</ins>
																	</div>
																</li>
																<li>
																	<figure><img src="/vendor/pitnik/images/resources/friend-avatar13.jpg" alt=""></figure>
																	<div class="story-user">
																		<span>Zara Hayat</span>
																		<ins>1 day ago</ins>
																	</div>
																</li>
																<li>
																	<figure><img src="/vendor/pitnik/images/resources/friend-avatar14.jpg" alt=""></figure>
																	<div class="story-user">
																		<span>Zing Jang</span>
																		<ins>2 days ago</ins>
																	</div>
																</li>
																
																<li>
																	<figure><img src="/vendor/pitnik/images/resources/friend-avatar16.jpg" alt=""></figure>
																	<div class="story-user">
																		<span>Emma watson</span>
																		<ins>jan,12 2020</ins>
																	</div>
																</li>
															</ul>
														</div>
													</div>
													<div class="col-lg-8">
														<div id="btns-wrapper"></div>
														<div id="slideshow">
															<img class="slide" src="http://placehold.it/856x380/FF6347/FFFFFF&text=1" alt="11"/>
															<img class="slide" src="http://placehold.it/856x380/6FC6F7/FFFFFF&text=2" alt="2"/>
															<img class="slide" src="http://placehold.it/856x380/f3e686/FFFFFF&text=3" alt="3"/>
															<img class="slide" src="http://placehold.it/856x380/71eea3/FFFFFF&text=4" alt="4"/>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
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
