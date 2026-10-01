<?php

use App\Models\ForumCategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Forum'), Layout('layouts::bare')] class extends Component
{
    #[Computed]
    public function categories()
    {
        return ForumCategory::query()
            ->withCount('topics')
            ->orderBy('position')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function latestTopics()
    {
        return \App\Models\ForumTopic::with(['user', 'category'])
            ->withCount('posts')
            ->orderByDesc('last_posted_at')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Forum'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Forum Categories <span>{{ $this->categories->count() }}</span></h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->categories as $category)
									<div class="col-lg-6 col-md-6 col-sm-12" wire:key="cat-{{ $category->id }}">
										<div class="widget-shadow" style="padding:22px;">
											<h5 class="f-title" style="margin-bottom:8px;">
												<a href="{{ route('forum.category', $category) }}">{{ $category->name }}</a>
											</h5>
											@if($category->description)
												<p style="color:#757a95;">{{ $category->description }}</p>
											@endif
											<span style="color:#757a95;font-size:13px;">
												{{ $category->topics_count }} {{ Str::plural('Topic', $category->topics_count) }}
											</span>
										</div>
									</div>
								@empty
									<div class="col-lg-12"><p>No forum categories yet.</p></div>
								@endforelse
							</div>
						</div>

						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Latest Activity</h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->latestTopics as $topic)
									<div class="col-lg-6 col-md-6 col-sm-12" wire:key="latest-{{ $topic->id }}">
										<div class="widget-shadow" style="padding:18px;">
											@if($topic->is_pinned)
												<span style="font-size:11px;color:#088dcd;font-weight:bold;">PINNED</span>
											@endif
											@if($topic->is_locked)
												<span style="font-size:11px;color:#e0245e;font-weight:bold;">LOCKED</span>
											@endif
											<h5 style="margin:6px 0;font-size:16px;">
												<a href="{{ route('forum.topic', $topic) }}">{{ $topic->title }}</a>
											</h5>
											<span style="color:#757a95;font-size:13px;">
												{{ $topic->category->name }}
												· by <a href="{{ route('profile.show', $topic->user) }}">{{ $topic->user->name }}</a>
												· {{ $topic->last_posted_at?->diffForHumans() }}
												· {{ $topic->posts_count }} {{ Str::plural('reply', $topic->posts_count) }}
											</span>
										</div>
									</div>
								@empty
									<div class="col-lg-12"><p>No topics yet.</p></div>
								@endforelse
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent