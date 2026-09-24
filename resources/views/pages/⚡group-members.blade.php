<?php

use App\Models\Group;
use App\Models\User;
use App\Notifications\GroupJoinApproved;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::bare')] class extends Component
{
    public Group $group;

    public function mount(Group $group): void
    {
        $this->group = $group;
    }

    #[Computed]
    public function canManage(): bool
    {
        return Gate::allows('manageMembers', $this->group);
    }

    #[Computed]
    public function members()
    {
        return Gate::allows('view', $this->group)
            ? $this->group->approvedMembers()->orderByPivot('role')->orderBy('name')->get()
            : collect();
    }

    #[Computed]
    public function pending()
    {
        return $this->canManage ? $this->group->pendingMembers()->oldest('group_members.created_at')->get() : collect();
    }

    // Target must already be in this group with the given status; ids from the client are untrusted.
    protected function memberOrFail(int $userId, string $status): User
    {
        Gate::authorize('manageMembers', $this->group);

        return $this->group->members()->wherePivot('status', $status)->where('users.id', $userId)->firstOrFail();
    }

    public function approve(int $userId): void
    {
        $user = $this->memberOrFail($userId, 'pending');
        $this->group->members()->updateExistingPivot($user->id, ['status' => 'approved']);
        $user->notify(new GroupJoinApproved(auth()->user(), $this->group));
        $this->reload();
    }

    public function decline(int $userId): void
    {
        $user = $this->memberOrFail($userId, 'pending');
        $this->group->members()->detach($user->id);
        $this->reload();
    }

    public function remove(int $userId): void
    {
        $user = $this->memberOrFail($userId, 'approved');
        abort_if($user->id === $this->group->owner_id, 403);
        $this->group->members()->detach($user->id);
        $this->reload();
    }

    public function promote(int $userId): void
    {
        $user = $this->memberOrFail($userId, 'approved');
        $this->group->members()->updateExistingPivot($user->id, ['role' => 'admin']);
        $this->reload();
    }

    protected function reload(): void
    {
        unset($this->members, $this->pending);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $group->name.' — Members'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5><a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a> · Members <span>{{ $this->members->count() }}</span></h5>
								</div>
							</div>
						</div>

						@if($this->canManage && $this->pending->isNotEmpty())
							<div class="central-meta">
								<h5 class="f-title">Pending requests <span>{{ $this->pending->count() }}</span></h5>
								<div class="row">
									@foreach($this->pending as $member)
										<x-pitnik.friend-box :user="$member" mode="custom" wire:key="pending-{{ $member->id }}">
											<a class="send-mesg" href="#" wire:click.prevent="approve({{ $member->id }})">Approve</a>
											<a class="send-mesg" href="#" wire:click.prevent="decline({{ $member->id }})" style="margin-left:6px;">Decline</a>
										</x-pitnik.friend-box>
									@endforeach
								</div>
							</div>
						@endif

						<div class="central-meta">
							@can('view', $group)
								<div class="row">
									@foreach($this->members as $member)
										<x-pitnik.friend-box :user="$member" mode="custom" wire:key="member-{{ $member->id }}">
											@if($member->id === $group->owner_id)
												<span class="send-mesg">Owner</span>
											@elseif($member->pivot->role === 'admin')
												<span class="send-mesg">Admin</span>
											@endif
											@if($this->canManage && $member->id !== $group->owner_id)
												@if($member->pivot->role !== 'admin')
													<a class="send-mesg" href="#" wire:click.prevent="promote({{ $member->id }})" style="margin-left:6px;">Make admin</a>
												@endif
												<a class="send-mesg" href="#" wire:click.prevent="remove({{ $member->id }})" wire:confirm="Remove {{ $member->name }} from the group?" style="margin-left:6px;">Remove</a>
											@endif
										</x-pitnik.friend-box>
									@endforeach
								</div>
							@else
								<h5><i class="fa fa-lock"></i> This group is private</h5>
								<p>Only members can see who's in this group.</p>
							@endcan
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
