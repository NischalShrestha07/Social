<?php

use App\Models\ForumCategory;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Forum'), Layout('layouts::bare')] class extends Component
{
    public string $search = '';

    public function mount(ForumCategory $category): void
    {
        $this->category = $category;
    }

    public ForumCategory $category;

    public function updated(): void
    {
        unset($this->topics);
    }

    #[Computed]
    public function topics()
    {
        return $this->category->topics()
            ->with('user')
            ->withCount('posts')
            ->search($this->search)
            ->orderByDesc('is_pinned')
            ->orderByDesc('last_posted_at')
            ->orderByDesc('updated_at')
            ->paginate(20);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $this->category->name])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>{{ $this->category->name }}</h5>
									@if($this->category->description)
										<span style="color:#757a95;">{{ $this->category->description }}</span>
									@endif
								</div>
								<div class="align-right">
									<a href="{{ route('forum.topic.create', $this->category) }}" class="main-btn">New Topic</a>
									<a href="{{ route('forum') }}" class="main-btn3">All Categories</a>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								<div class="col-lg-12">
									<form wire:submit.prevent class="c-form">
										<label>Search in this category</label>
										<input type="text" wire:model.live.debounce.400ms="search" placeholder="Search topics...">
									</form>
								</div>
							</div>
							<div class="row">
								@forelse($this->topics as $topic)
									<div class="col-lg-12" wire:key="topic-{{ $topic->id }}">
										<div class="widget-shadow" style="padding:18px;margin-bottom:12px;">
											<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:15px;">
												<div style="flex:1;">
													<div style="margin-bottom:6px;">
														@if($topic->is_pinned)
															<span style="font-size:11px;color:#088dcd;font-weight:bold;">PINNED</span>
														@endif
														@if($topic->is_locked)
															<span style="font-size:11px;color:#e0245e;font-weight:bold;">LOCKED</span>
														@endif
													</div>
													<h5 style="margin:0 0 8px 0;font-size:17px;">
														<a href="{{ route('forum.topic', $topic) }}">{{ $topic->title }}</a>
													</h5>
													<span style="color:#757a95;font-size:13px;">
														by <a href="{{ route('profile.show', $topic->user) }}">{{ $topic->user->name }}</a>
														· {{ $topic->created_at->diffForHumans() }}
														· {{ $topic->posts_count }} {{ Str::plural('reply', $topic->posts_count) }}
														· last activity {{ $topic->last_posted_at?->diffForHumans() ?? 'never' }}
													</span>
												</div>
												<a href="{{ route('forum.topic', $topic) }}" class="main-btn3">Open</a>
											</div>
										</div>
									</div>
								@empty
									<div class="col-lg-12"><p>No topics in this category yet.</p></div>
								@endforelse
							</div>
							@if($this->topics->hasPages())
								<div class="row">
									<div class="col-lg-12 text-center">{{ $this->topics->links() }}</div>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent