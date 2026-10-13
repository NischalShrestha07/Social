<?php

use App\Livewire\Concerns\InteractsWithPosts;
use App\Models\Page;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::bare')] class extends Component
{
    use InteractsWithPosts, WithFileUploads;

    public Page $page;

    public function mount(Page $page): void
    {
        $this->page = $page;
    }

    #[Computed]
    public function isOwner(): bool
    {
        return auth()->id() === $this->page->owner_id;
    }

    #[Computed]
    public function liked(): bool
    {
        return $this->page->isLikedBy(auth()->user());
    }

    #[Computed]
    public function likersCount(): int
    {
        return $this->page->likers()->count();
    }

    #[Computed]
    public function likersPreview()
    {
        return $this->page->likers()->latest('page_likes.created_at')->take(8)->get();
    }

    #[Computed]
    public function posts()
    {
        return $this->page->posts()->with([
            'user',
            'page',
            'comments.user',
            'comments.reactions',
            'comments.replies.user',
            'comments.replies.reactions',
            'reactions',
        ])->get();
    }

    protected function postAttributes(): array
    {
        return ['page_id' => $this->page->id];
    }

    protected function authorizeCreatePost(): void
    {
        Gate::authorize('post', $this->page);
    }

    public function like(): void
    {
        $this->page->like(auth()->user());
        unset($this->liked, $this->likersCount, $this->likersPreview);
    }

    public function unlike(): void
    {
        $this->page->unlike(auth()->user());
        unset($this->liked, $this->likersCount, $this->likersPreview);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $page->name])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="row merged20" id="page-contents">
							<div class="user-profile" @if($page->coverUrl()) style="background-image:url('{{ $page->coverUrl() }}')" @endif>
								<figure>
									<img src="{{ $page->avatarUrl() }}" alt="">
								</figure>
								<div class="profile-section">
									<div class="row">
										<div class="col-lg-2 col-md-3">
											<div class="profile-author">
												<div class="profile-author-thumb">
													<img alt="page" src="{{ $page->avatarUrl() }}">
												</div>
												<div class="author-content">
													<a class="h4 author-name" href="{{ route('pages.show', $page) }}">{{ $page->name }}</a>
													<div class="country">{{ $page->category }}</div>
													@if($this->isOwner)
														<a href="{{ route('pages.manage', $page) }}" class="main-btn">Manage Page</a>
													@elseif($this->liked)
														<a href="#" class="main-btn3" wire:click.prevent="unlike">Liked <i class="fa fa-check"></i></a>
													@else
														<a href="#" class="main-btn" wire:click.prevent="like"><i class="fa fa-thumbs-up"></i> Like Page</a>
													@endif
												</div>
											</div>
										</div>
										<div class="col-lg-10 col-md-9">
											<ul class="profile-menu">
												<li><a class="active" href="{{ route('pages.show', $page) }}">Posts</a></li>
												<li><a href="{{ route('pages') }}">All Pages</a></li>
											</ul>
											<ol class="folw-detail">
												<li><span>Likes</span><ins>{{ $this->likersCount }}</ins></li>
												<li><span>Posts</span><ins>{{ $this->posts->count() }}</ins></li>
											</ol>
										</div>
									</div>
								</div>
							</div><!-- page banner -->

							<div class="col-lg-3">
								<aside class="sidebar static left">
									<div class="widget">
										<h4 class="widget-title">About</h4>
										<div class="your-page">
											<p style="padding:0 20px 10px;">{{ $page->description ?: 'No description yet.' }}</p>
											@if($page->website)
												<p style="padding:0 20px 10px;"><i class="fa fa-link"></i> <a href="{{ $page->website }}" target="_blank" rel="noopener nofollow">{{ parse_url($page->website, PHP_URL_HOST) ?: $page->website }}</a></p>
											@endif
											<p style="padding:0 20px 20px;">Created {{ $page->created_at->format('M Y') }}</p>
										</div>
									</div>
									<div class="widget">
										<h4 class="widget-title">Liked by {{ $this->likersCount }}</h4>
										<div class="your-page">
											<div class="users-thumb-list" style="padding:0 20px 20px;">
												@forelse($this->likersPreview as $liker)
													<a href="{{ route('profile.show', $liker) }}" title="{{ $liker->name }}" data-toggle="tooltip" wire:key="liker-{{ $liker->id }}">
														<img src="{{ $liker->avatarUrl() }}" alt="">
													</a>
												@empty
													<span>No likes yet.</span>
												@endforelse
											</div>
										</div>
									</div>
								</aside>
							</div>

							<div class="col-lg-9">
								@if($this->isOwner)
									<x-pitnik.post-composer :image="$image" />
								@endif

								@forelse($this->posts as $post)
									<x-pitnik.post-card :post="$post" wire:key="post-{{ $post->id }}" />
								@empty
									<div class="central-meta"><p>This page hasn't posted anything yet.</p></div>
								@endforelse
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
