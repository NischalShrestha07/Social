@php($requests = auth()->user()->pendingRequestsReceived()->take(5))
<li>
	<a href="{{ route('friend-requests') }}" title="Friend Requests" data-ripple="">
		@if($requests->count())
			<em class="bg-red">{{ $requests->count() }}</em>
		@endif
		<i><svg id="Layer_2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 499.3 512" height="16px" fill="#30305b">
			<g>
				<path d="M196.3 239.5c30.4 0 58.1-11.4 79.3-30.1 7.2 12.8 17.4 23.7 30 32 15.5 10.1 33.1 15.4 51.2 15.4 6.4 0 12.8-.7 19.2-2 24.5-5.1 45.6-19.4 59.3-40.3 13.7-20.9 18.5-45.9 13.4-70.4-7.7-36.9-36.5-65.5-73.5-72.9-10.8-2.1-21.4 4.9-23.5 15.7-2.1 10.8 4.9 21.4 15.7 23.5 21.2 4.2 37.8 20.6 42.1 41.8 2.9 14 .2 28.4-7.7 40.3-7.9 12-19.9 20.2-34 23.1-14 2.9-28.4.2-40.3-7.7-12-7.9-20.2-19.9-23.1-34-.1-.6-.3-1.3-.5-1.9 7.7-15.8 12.1-33.6 12.1-52.4C316 53.7 262.3 0 196.3 0S76.5 53.7 76.5 119.8s53.7 119.8 119.8 119.8zm0-199.5c44 0 79.8 35.8 79.8 79.8s-35.8 79.8-79.8 79.8-79.8-35.8-79.8-79.8S152.3 40 196.3 40zM498.4 415.2c-15.6-51.9-57.9-92.1-110.5-105.1-10.7-2.6-21.6 3.9-24.2 14.6-2.6 10.7 3.9 21.6 14.6 24.2 38.9 9.6 70.2 39.3 81.7 77.7 2.6 8.7 10.5 14.3 19.1 14.3s3.8-.3 5.8-.8c10.6-3.2 16.6-14.3 13.4-24.9zM323.4 365.5c-7.8-7.8-20.5-7.8-28.3 0-7.8 7.8-7.8 20.5 0 28.3l25.7 25.7c-24.5 3.1-64.3 7.5-106.6 9.4-115.7 5-154.1-12.4-166.7-23.2-6.8-5.8-7.6-11.2-7.6-15.3 0-9.5 3.7-17.3 11.4-24.5C88.6 331 197 329.1 235.6 331.3c11 .6 20.5-7.8 21.1-18.8.6-11-7.8-20.5-18.8-21.1-1.6 0-38.8-2.2-83.1 1.9-62 5.6-106 20.3-130.8 43.5C8.5 351.3.2 369.7 0 390c-.2 17.8 7.3 33.7 21.5 46 26.3 22.7 76.7 33.8 153.5 33.8s26.8-.3 41.4-1c36.6-1.6 70.8-5 95.7-7.9l-16.9 16.9c-7.8 7.8-7.8 20.5 0 28.3 3.9 3.9 9 5.9 14.1 5.9s10.2-2 14.1-5.9l56.2-56.2c3.8-3.8 5.9-8.8 5.9-14.1s-2.1-10.4-5.9-14.1l-56.2-56.2z"></path>
			</g>
		</svg></i>
	</a>
	<div class="dropdowns">
		<span>{{ $requests->count() }} Friend Requests <a href="{{ route('friend-requests') }}" title="">View all Requests</a></span>
		<ul class="drops-menu">
			@forelse($requests as $friendship)
				<li wire:key="req-bell-{{ $friendship->id }}">
					<div>
						<figure>
							<img src="{{ $friendship->user->avatarUrl() }}" alt="">
						</figure>
						<div class="mesg-meta">
							<h6><a href="{{ route('profile.show', $friendship->user) }}" title="">{{ $friendship->user->name }}</a></h6>
							<i>{{ $friendship->created_at->diffForHumans() }}</i>
						</div>
						<div class="add-del-friends">
							<a href="#" title="Accept" wire:click.prevent="acceptRequest({{ $friendship->id }})"><i class="ti-heart"></i></a>
							<a href="#" title="Decline" wire:click.prevent="declineRequest({{ $friendship->id }})"><i class="ti-trash"></i></a>
						</div>
					</div>
				</li>
			@empty
				<li><div class="mesg-meta"><span>No pending requests</span></div></li>
			@endforelse
		</ul>
		<a href="{{ route('friend-requests') }}" title="" class="more-mesg">View All</a>
	</div>
</li>
