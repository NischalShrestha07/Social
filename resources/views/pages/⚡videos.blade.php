<?php

use App\Models\Video;
use App\Models\VideoChannel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Pittube'), Layout('layouts::bare')] class extends Component
{
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'sort', except: 'newest')]
    public string $sort = 'newest';

    public function updated(): void
    {
        unset($this->videos);
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'sort');
        unset($this->videos);
    }

    #[Computed]
    public function videos()
    {
        return Video::public()
            ->search($this->search)
            ->with('channel')
            ->withCount('comments')
            ->when($this->sort, fn ($q) => match ($this->sort) {
                'views' => $q->orderByDesc('views_count'),
                'oldest' => $q->oldest(),
                default => $q->latest(),
            })
            ->paginate(12);
    }

    #[Computed]
    public function channels()
    {
        return VideoChannel::query()
            ->withCount('videos')
            ->orderByDesc('subscribers_count')
            ->limit(4)
            ->get();
    }

    #[Computed]
    public function subscriptions()
    {
        return auth()->user()->subscriptions()->withCount('videos')->get();
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Pittube'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="title-block">
								<div class="row">
									<div class="col-lg-6">
										<div class="align-left">
											<h5>Pittube <span>{{ $this->videos->total() }}</span></h5>
										</div>
									</div>
									<div class="col-lg-6 text-right">
										<a href="{{ route('videos.channel.create') }}" class="main-btn">Create Channel</a>
									</div>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->channels as $channel)
									<div class="col-lg-3 col-md-6 col-sm-12" wire:key="channel-{{ $channel->id }}">
										<x-pitnik.channel-box :channel="$channel" />
									</div>
								@empty
									<div class="col-lg-12"><p>No channels yet.</p></div>
								@endforelse
							</div>
						</div>

						@if($this->subscriptions->isNotEmpty())
							<div class="central-meta">
								<div class="title-block">
									<div class="align-left">
										<h5>Your Subscriptions <span>{{ $this->subscriptions->count() }}</span></h5>
									</div>
								</div>
							</div>
							<div class="central-meta padding30">
								<div class="row">
									@foreach($this->subscriptions as $channel)
										<div class="col-lg-3 col-md-6 col-sm-12" wire:key="sub-{{ $channel->id }}">
											<x-pitnik.channel-box :channel="$channel" />
										</div>
									@endforeach
								</div>
							</div>
						@endif

						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Browse Videos</h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								<div class="col-lg-12">
									<form wire:submit.prevent class="c-form">
										<div class="row">
											<div class="col-lg-6 col-md-6">
												<label>Search</label>
												<input type="text" wire:model.live.debounce.400ms="search" placeholder="Title or description">
											</div>
											<div class="col-lg-3 col-md-6">
												<label>Sort</label>
												<select wire:model.live="sort">
													<option value="newest">Newest</option>
													<option value="oldest">Oldest</option>
													<option value="views">Most viewed</option>
												</select>
											</div>
											<div class="col-lg-3 col-md-6">
												<label>&nbsp;</label>
												<button type="button" class="main-btn3" wire:click="clearFilters">Reset</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="row">
								@forelse($this->videos as $video)
									<div class="col-lg-4 col-md-6 col-sm-12" wire:key="video-{{ $video->id }}">
										<x-pitnik.video-box :video="$video" />
									</div>
								@empty
									<div class="col-lg-12"><p>No videos match your search.</p></div>
								@endforelse
							</div>
							@if($this->videos->hasPages())
								<div class="row">
									<div class="col-lg-12 text-center">
										{{ $this->videos->links() }}
									</div>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
