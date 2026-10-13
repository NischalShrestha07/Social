<?php

use App\Livewire\Concerns\InteractsWithPosts;
use App\Models\Friendship;
use App\Models\User;
use App\Notifications\FriendRequestAccepted;
use App\Notifications\FriendRequestReceived;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[\Livewire\Attributes\Layout('layouts::bare')] class extends Component
{
    use InteractsWithPosts, WithFileUploads;

    public User $user;

    public $avatar = null;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->loadPosts();
    }

    public function friendshipStatus(): string
    {
        return auth()->user()->friendshipStatusWith($this->user);
    }

    public function sendFriendRequest(): void
    {
        if (auth()->id() === $this->user->id) {
            return;
        }

        Friendship::firstOrCreate([
            'user_id' => auth()->id(),
            'friend_id' => $this->user->id,
        ], ['status' => 'pending']);

        $this->user->notify(new FriendRequestReceived(auth()->user()));
    }

    public function cancelFriendRequest(): void
    {
        Friendship::where('user_id', auth()->id())
            ->where('friend_id', $this->user->id)
            ->where('status', 'pending')
            ->delete();
    }

    public function acceptFriendRequest(): void
    {
        $friendship = Friendship::where('user_id', $this->user->id)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->update(['status' => 'accepted']);

        $this->user->notify(new FriendRequestAccepted(auth()->user()));
    }

    public function declineFriendRequest(): void
    {
        Friendship::where('user_id', $this->user->id)
            ->where('friend_id', auth()->id())
            ->where('status', 'pending')
            ->delete();
    }

    public function unfriend(): void
    {
        Friendship::where('status', 'accepted')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('user_id', auth()->id())->where('friend_id', $this->user->id);
                })->orWhere(function ($query) {
                    $query->where('user_id', $this->user->id)->where('friend_id', auth()->id());
                });
            })->delete();
    }

    public function title(): string
    {
        return $this->user->name;
    }

    protected function loadPosts(): void
    {
        $this->user->load([
            'posts.user',
            'posts.comments.user',
            'posts.comments.reactions',
            'posts.comments.replies.user',
            'posts.comments.replies.reactions',
            'posts.reactions',
        ]);
    }

    public function updatedAvatar(): void
    {
        if (auth()->id() !== $this->user->id) {
            return;
        }

        $this->validate(['avatar' => 'image|max:5120']);

        $this->user->update(['avatar_path' => $this->avatar->store('avatars', 'public')]);
        $this->avatar = null;
    }

    protected function refreshPosts(): void
    {
        $this->loadPosts();
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $user->name])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="row merged20" id="page-contents">
							<div class="user-profile" @if($user->cover_path) style="background-image:url('{{ asset('storage/'.$user->cover_path) }}')" @endif>
								<figure>
									@if(auth()->id() === $user->id)
										<div class="edit-pp">
											<label class="fileContainer">
												<i class="fa fa-camera"></i>
												<input type="file" wire:model="avatar">
											</label>
										</div>
									@endif
									<img src="{{ $user->avatarUrl() }}" alt="">
									@if(auth()->id() === $user->id)
										<ul class="profile-controls">
											<li>
												<a href="{{ route('profile.edit') }}" title="Account Settings"><i class="fa fa-sliders"></i></a>
											</li>
										</ul>
									@endif
								</figure>
								
								<div class="profile-section">
									<div class="row">
										<div class="col-lg-2 col-md-3">
											<div class="profile-author">
												<div class="profile-author-thumb">
													<img alt="author" src="{{ $user->avatarUrl() }}">
												</div>

												<div class="author-content">
													<a class="h4 author-name" href="{{ route('profile.show', $user) }}">{{ $user->name }}</a>

													@if(auth()->id() !== $user->id)
														@switch($this->friendshipStatus())
															@case('friends')
																<a href="#" title="" class="main-btn3" wire:click.prevent="unfriend">Unfriend</a>
																@break
															@case('pending_sent')
																<a href="#" title="" class="main-btn3" wire:click.prevent="cancelFriendRequest">Cancel Request</a>
																@break
															@case('pending_received')
																<a href="#" title="" class="main-btn" wire:click.prevent="acceptFriendRequest">Accept</a>
																<a href="#" title="" class="main-btn3" wire:click.prevent="declineFriendRequest">Decline</a>
																@break
															@default
																<a href="#" title="" class="main-btn" wire:click.prevent="sendFriendRequest">Add Friend</a>
														@endswitch
														<a href="{{ route('messages', $user) }}" title="" class="main-btn">Message</a>
													@endif
												</div>
											</div>
										</div>
										<div class="col-lg-10 col-md-9">
											<ul class="profile-menu">
												<li>
													<a class="active" href="{{ route('profile.show', $user) }}">Timeline</a>
												</li>
												<li>
													<a class="" href="about.html">About</a>
												</li>
												<li>
													<a href="{{ route('profile.friends', $user) }}">Friends</a>
												</li>
												<li>
													<a class="" href="timeline-photos.html">Photos</a>
												</li>
												<li>
													<a class="" href="timeline-videos.html">Videos</a>
												</li>
												<li>
													<div class="more">
														<i class="fa fa-ellipsis-h"></i>
														<ul class="more-dropdown">
															<li>
																<a href="{{ route('groups') }}">Groups</a>
															</li>
															<li>
																<a href="statistics.html">Profile Analytics</a>
															</li>
														</ul>
													</div>
												</li>
											</ul>
											<ol class="folw-detail">
												<li><span>Posts</span><ins>{{ $user->posts->count() }}</ins></li>
												<li><a href="{{ route('profile.friends', $user) }}"><span>Friends</span><ins>{{ $user->acceptedFriends()->count() }}</ins></a></li>
											</ol>
										</div>
									</div>
								</div>	
							</div><!-- user profile banner  -->
							<x-pitnik.timeline-sidebar-left />
							<div class="col-lg-6">

									@if(auth()->id() === $user->id)
										<x-pitnik.post-composer :image="$image" />
									@endif

									@foreach($user->posts as $post)
										<x-pitnik.post-card :post="$post" wire:key="post-{{ $post->id }}" />
									@endforeach

							</div><!-- centerl meta -->
							<x-pitnik.timeline-sidebar-right :user="$user" />
						</div>	
					</div>
				</div>
			</div>
		</div>	
	</section><!-- content -->
@endcomponent
