@props(['video'])

<div {{ $attributes->merge(['class' => 'widget-shadow']) }}>
	<div style="padding:20px;">
		<a href="{{ route('videos.show', $video) }}">
			<img src="{{ $video->thumbnailUrl() }}" alt=""
				style="width:100%;height:170px;object-fit:cover;border-radius:4px;position:relative;">
		</a>
		@if($video->durationLabel())
			<span style="display:inline-block;background:rgba(0,0,0,0.75);color:#fff;border-radius:3px;padding:2px 6px;font-size:12px;margin-top:-28px;position:relative;">
				{{ $video->durationLabel() }}
			</span>
		@endif
		<h5 class="f-title" style="margin:14px 0 6px;">
			<a href="{{ route('videos.show', $video) }}">{{ $video->title }}</a>
		</h5>
		<span style="color:#757a95;font-size:13px;">
			<a href="{{ route('videos.channel', $video->channel) }}">{{ $video->channel->name }}</a>
			· {{ number_format($video->views_count) }} {{ Str::plural('view', $video->views_count) }}
			· {{ $video->created_at->diffForHumans() }}
		</span>
		@if($video->visibility !== 'public')
			<span style="color:#e0245e;font-size:12px;">· {{ \App\Models\Video::visibilityLabel($video->visibility) }}</span>
		@endif
	</div>
</div>
