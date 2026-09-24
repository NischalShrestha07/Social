<?php

use App\Models\Group;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Groups'), Layout('layouts::bare')] class extends Component
{
    #[Computed]
    public function myGroups()
    {
        return auth()->user()->groups()->withCount('approvedMembers')->orderBy('name')->get();
    }

    #[Computed]
    public function discover()
    {
        return Group::withCount('approvedMembers')
            ->whereDoesntHave('members', fn ($q) => $q->where('users.id', auth()->id())->where('group_members.status', 'approved'))
            ->latest()
            ->get();
    }

    #[Computed]
    public function pendingIds(): array
    {
        return auth()->user()->belongsToMany(Group::class, 'group_members')
            ->wherePivot('status', 'pending')
            ->pluck('groups.id')
            ->all();
    }

    public function join(int $groupId): void
    {
        Group::findOrFail($groupId)->join(auth()->user());
        unset($this->myGroups, $this->discover, $this->pendingIds);
    }

    public function leave(int $groupId): void
    {
        Group::findOrFail($groupId)->leave(auth()->user());
        unset($this->myGroups, $this->discover, $this->pendingIds);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Groups'])
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
											<h5>My Groups <span>{{ $this->myGroups->count() }}</span></h5>
										</div>
									</div>
								</div>
							</div>
						</div><!-- title block -->
						<div class="central-meta padding30">
							<div class="row">
								<div class="col-lg-3 col-md-4 col-sm-6">
									<a href="{{ route('groups.create') }}">
										<div class="addgroup">
											<div class="item-upload">
												<i class="fa fa-plus-circle"></i>
												<div class="upload-meta">
													<h5>Create Group</h5>
													<span>its only take a few minutes!</span>
												</div>
											</div>
										</div>
									</a>
								</div>
								@foreach($this->myGroups as $group)
									<x-pitnik.group-box :group="$group" status="member" wire:key="my-{{ $group->id }}" />
								@endforeach
							</div>
						</div>

						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Discover Groups <span>{{ $this->discover->count() }}</span></h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->discover as $group)
									<x-pitnik.group-box
										:group="$group"
										:status="in_array($group->id, $this->pendingIds, true) ? 'pending' : 'none'"
										wire:key="discover-{{ $group->id }}"
									/>
								@empty
									<div class="col-lg-12"><p>No other groups yet — create the first one.</p></div>
								@endforelse
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent
