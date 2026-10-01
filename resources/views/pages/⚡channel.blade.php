<?php

use App\Models\Video;
use App\Models\VideoChannel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Channel'), Layout('layouts::bare')] class extends Component
{
    public int $channelId;

    public function mount(VideoChannel $channel): void
    {
        $this->channelId = $channel->id;
    }

    #[Computed]
    public function channel(): VideoChannel
    {
        return VideoChannel::with('owner')->findOrFail($this->channelId);
    }

    /** Everyone but a stranger sees the owner's videos; strangers see public ones. */
    #[Computed]
    public function videos()
    {
        return Video::withCount('comments')
            ->where('video_channel_id', $this->channelId)
            ->when(
                $this->channel()->user_id !== auth()->id(),
                fn ($q) => $q->whereIn('visibility', ['public', 'unlisted']),
            )
            ->latest()
            ->get();
    }

    #[Computed]
    public function isSubscribed(): bool
    {
        return $this->channel()->isSubscribedBy(auth()->user());
    }

    public function toggleSubscribe(): void
    {
        $channel = $this->channel();

        if ($channel->isSubscribedBy(auth()->user())) {
            $channel->unsubscribe(auth()->user());
        } else {
            $channel->subscribe(auth()->user());
        }

        unset($this->isSubscribed, $this->channel);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $this->channel->name])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						@if($this->channel->bannerUrl())
							<img src="{{ $this->channel->bannerUrl() }}" alt="" style="width:100%;height:170px;object-fit:cover;border-radius:6px;">
						@endif
						<div class="central-meta">
							<div class="title-block">
								<div style="display:flex;gap:18px;align-items:center;">
									<img src="{{ $this->channel->avatarUrl() }}" alt="" style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
									<div>
										<h5 style="margin-bottom:4px;">{{ $this->channel->name }}</h5>
										<span style="color:#757a95;">
											{{ number_format($this->channel->subscribers_count) }} {{ Str::plural('subscriber', $this->channel->subscribers_count) }}
											· {{ $this->videos->count() }} {{ Str::plural('video', $this->videos->count()) }}
										</span>
									</div>
								</div>
								<div class="align-right">
									@can('update', $this->channel)
										<a href="{{ route('videos.channel.manage', $this->channel) }}" class="main-btn3">Manage</a>
										<a href="{{ route('videos.upload', $this->channel) }}" class="main-btn">Upload Video</a>
									@else
										<button type="button" class="main-btn" wire:click="toggleSubscribe">
											{{ $this->isSubscribed ? 'Unsubscribe' : 'Subscribe' }}
										</button>
									@endcan
								</div>
							</div>
							@if($this->channel->description)
								<p style="color:#535165;margin-top:14px;">{{ $this->channel->description }}</p>
							@endif
						</div>

						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Videos</h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->videos as $video)
									<div class="col-lg-4 col-md-6 col-sm-12" wire:key="video-{{ $video->id }}">
										<x-pitnik.video-box :video="$video" />
									</div>
								@empty
									<div class="col-lg-12"><p>This channel has no videos yet.</p></div>
								@endforelse
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
