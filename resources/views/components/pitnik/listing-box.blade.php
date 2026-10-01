@props(['listing'])

<div {{ $attributes->merge(['class' => 'widget-shadow']) }}>
	<div style="padding:25px 25px 20px;">
		<a href="{{ route('marketplace.listing', $listing) }}">
			<img src="{{ $listing->imageUrl() }}" alt=""
				style="width:100%;height:180px;object-fit:cover;border-radius:4px;">
		</a>
		<h5 class="f-title" style="margin:15px 0 6px;">
			<a href="{{ route('marketplace.listing', $listing) }}">{{ $listing->title }}</a>
		</h5>
		<span style="color:#535165;font-size:14px;font-weight:bold;">
			{{ $listing->priceLabel() }}
			@if($listing->is_sold)
				<span style="color:#e0245e;">· SOLD</span>
			@endif
		</span>
		<p style="margin-top:12px;color:#757a95;">
			{{ Str::words(strip_tags($listing->description), 18, '...') }}
		</p>
		<div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:16px;">
			<span style="background:#f2f2f7;border-radius:4px;padding:4px 10px;font-size:12px;color:#535165;">
				{{ \App\Models\MarketplaceListing::categoryLabel($listing->category) }}
			</span>
			<span style="background:#f2f2f7;border-radius:4px;padding:4px 10px;font-size:12px;color:#535165;">
				{{ \App\Models\MarketplaceListing::conditionLabel($listing->condition) }}
			</span>
			<span style="background:#f2f2f7;border-radius:4px;padding:4px 10px;font-size:12px;color:#535165;">
				{{ $listing->location }}
			</span>
			@if(isset($listing->offers_count))
				<span style="background:#f2f2f7;border-radius:4px;padding:4px 10px;font-size:12px;color:#535165;">
					{{ $listing->offers_count }} {{ Str::plural('offer', $listing->offers_count) }}
				</span>
			@endif
		</div>
		<div style="margin-top:20px;">
			<a href="{{ route('marketplace.listing', $listing) }}" class="main-btn">View Listing</a>
		</div>
	</div>
</div>
