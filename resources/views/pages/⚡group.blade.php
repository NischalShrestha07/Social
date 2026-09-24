<?php

use App\Livewire\Concerns\InteractsWithPosts;
use App\Models\Group;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts::bare')] class extends Component
{
    use InteractsWithPosts, WithFileUploads;

    public Group $group;

    public function mount(Group $group): void
    {
        $this->group = $group;
    }

    #[Computed]
    public function status(): string
    {
        return $this->group->membershipStatus(auth()->user());
    }

    #[Computed]
    public function canView(): bool
    {
        return Gate::allows('view', $this->group);
    }

    #[Computed]
    public function posts()
    {
        if (! $this->canView) {
            return collect();
        }

        return $this->group->posts()->with([
            'user',
            'comments.user',
            'comments.reactions',
            'comments.replies.user',
            'comments.replies.reactions',
            'reactions',
        ])->get();
    }

    protected function postAttributes(): array
    {
        return ['group_id' => $this->group->id];
    }

    protected function authorizeCreatePost(): void
    {
        Gate::authorize('post', $this->group);
    }

    public function join(): void
    {
        $this->group->join(auth()->user());
        unset($this->status, $this->canView, $this->posts);
    }

    public function leave(): void
    {
        $this->group->leave(auth()->user());
        unset($this->status, $this->canView, $this->posts);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $group->name])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="row merged20" id="page-contents">
							<div class="user-profile" @if($group->coverUrl()) style="background-image:url('{{ $group->coverUrl() }}')" @endif>
								<figure>
									<img src="{{ $group->avatarUrl() }}" alt="">
								</figure>
								<div class="profile-section">
									<div class="row">
										<div class="col-lg-2 col-md-3">
											<div class="profile-author">
												<div class="profile-author-thumb">
													<img alt="group" src="{{ $group->avatarUrl() }}">
												</div>
												<div class="author-content">
													<a class="h4 author-name" href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>
													@switch($this->status)
														@case('none')
															<a href="#" class="main-btn" wire:click.prevent="join">{{ $group->isPrivate() ? 'Request to Join' : 'Join Group' }}</a>
															@break
														@case('pending')
															<a href="#" class="main-btn3" wire:click.prevent="leave">Cancel Request</a>
															@break
														@default
															@if(auth()->id() !== $group->owner_id)
																<a href="#" class="main-btn3" wire:click.prevent="leave" wire:confirm="Leave {{ $group->name }}?">Leave Group</a>
															@endif
													@endswitch
												</div>
											</div>
										</div>
										<div class="col-lg-10 col-md-9">
											<ul class="profile-menu">
												<li><a class="active" href="{{ route('groups.show', $group) }}">Discussion</a></li>
												<li><a href="{{ route('groups.members', $group) }}">Members</a></li>
												<li><a href="{{ route('groups') }}">All Groups</a></li>
											</ul>
											<ol class="folw-detail">
												<li><span>{{ $group->isPrivate() ? 'Private' : 'Public' }}</span><ins><i class="fa {{ $group->isPrivate() ? 'fa-lock' : 'fa-globe' }}"></i></ins></li>
												<li><a href="{{ route('groups.members', $group) }}"><span>Members</span><ins>{{ $group->approvedMembers()->count() }}</ins></a></li>
											</ol>
										</div>
									</div>
								</div>
							</div><!-- group banner -->

							<div class="col-lg-3">
								<aside class="sidebar static left">
									<div class="widget">
										<h4 class="widget-title">About</h4>
										<div class="your-page">
											<p style="padding:0 20px 20px;">{{ $group->description ?: 'No description yet.' }}</p>
											<p style="padding:0 20px 20px;">Created by <a href="{{ route('profile.show', $group->owner) }}">{{ $group->owner->name }}</a> · {{ $group->created_at->format('M Y') }}</p>
										</div>
									</div>
								</aside>
							</div>

							<div class="col-lg-9">
								@if($this->canView)
									@can('post', $group)
										<x-pitnik.post-composer :image="$image" />
									@endcan

									@forelse($this->posts as $post)
										<x-pitnik.post-card :post="$post" wire:key="post-{{ $post->id }}" />
									@empty
										<div class="central-meta"><p>No posts in this group yet.</p></div>
									@endforelse
								@else
									<div class="central-meta">
										<h5><i class="fa fa-lock"></i> This group is private</h5>
										<p>Join the group to see its posts and members.</p>
									</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
