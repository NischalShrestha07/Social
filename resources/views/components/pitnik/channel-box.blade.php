@props(['channel'])

<div {{ $attributes->merge(['class' => 'widget-shadow']) }}>
	<div style="padding:22px;text-align:center;">
		<img src="{{ $channel->avatarUrl() }}" alt=""
			style="width:74px;height:74px;border-radius:50%;object-fit:cover;margin:0 auto;">
		<h5 class="f-title" style="margin:12px 0 4px;">
			<a href="{{ route('videos.channel', $channel) }}">{{ $channel->name }}</a>
		</h5>
		<span style="color:#757a95;font-size:13px;">
			{{ number_format($channel->subscribers_count) }} {{ Str::plural('subscriber', $channel->subscribers_count) }}
			· {{ $channel->videos_count ?? $channel->videos()->count() }} {{ Str::plural('video', $channel->videos_count ?? $channel->videos()->count()) }}
		</span>
		@if($channel->description)
			<p style="color:#757a95;margin-top:12px;">{{ Str::words(strip_tags($channel->description), 14, '...') }}</p>
		@endif
		<div style="margin-top:16px;">
			<a href="{{ route('videos.channel', $channel) }}" class="main-btn3">View Channel</a>
		</div>
	</div>
</div>
