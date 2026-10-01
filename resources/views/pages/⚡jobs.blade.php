<?php

use App\Models\JobPosting;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Jobs'), Layout('layouts::bare')] class extends Component
{
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'type', except: '')]
    public string $type = '';

    #[Url(as: 'mode', except: '')]
    public string $workMode = '';

    public function updated(): void
    {
        unset($this->listings);
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'type', 'workMode');
        unset($this->listings);
    }

    #[Computed]
    public function listings()
    {
        return JobPosting::open()
            ->search($this->search)
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->when($this->workMode, fn ($q) => $q->where('work_mode', $this->workMode))
            ->with('user')
            ->withCount('applications')
            ->latest()
            ->paginate(12);
    }

    #[Computed]
    public function myPostings()
    {
        return JobPosting::where('user_id', auth()->id())
            ->withCount('applications')
            ->latest()
            ->get();
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Jobs'])
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
											<h5>My Job Postings <span>{{ $this->myPostings->count() }}</span></h5>
										</div>
									</div>
									<div class="col-lg-6 text-right">
										<a href="{{ route('jobs.create') }}" class="main-btn">Post a Job</a>
									</div>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->myPostings as $job)
									<div class="col-lg-6 col-md-6 col-sm-12" wire:key="mine-{{ $job->id }}">
										<div class="widget-shadow">
											<h5 class="f-title">
												<a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a>
											</h5>
											<p>{{ $job->company }} · {{ $job->location }}</p>
											<span>
												{{ $job->applications_count }} {{ Str::plural('applicant', $job->applications_count) }}
												· {{ $job->isAcceptingApplications() ? 'Open' : 'Closed' }}
											</span>
										</div>
									</div>
								@empty
									<div class="col-lg-12"><p>You have not posted any jobs yet.</p></div>
								@endforelse
							</div>
						</div>

						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Browse Jobs <span>{{ $this->listings->total() }}</span></h5>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								<div class="col-lg-12">
									<form wire:submit.prevent class="c-form">
										<div class="row">
											<div class="col-lg-5 col-md-6">
												<label>Search</label>
												<input type="text" wire:model.live.debounce.400ms="search" placeholder="Title, company, location">
											</div>
											<div class="col-lg-3 col-md-6">
												<label>Job type</label>
												<select wire:model.live="type">
													<option value="">Any type</option>
													@foreach(\App\Models\JobPosting::TYPES as $t)
														<option value="{{ $t }}">{{ \App\Models\JobPosting::typeLabel($t) }}</option>
													@endforeach
												</select>
											</div>
											<div class="col-lg-3 col-md-6">
												<label>Work mode</label>
												<select wire:model.live="workMode">
													<option value="">Any mode</option>
													@foreach(\App\Models\JobPosting::WORK_MODES as $m)
														<option value="{{ $m }}">{{ ucfirst($m) }}</option>
													@endforeach
												</select>
											</div>
											<div class="col-lg-1 col-md-6">
												<label>&nbsp;</label>
												<button type="button" class="main-btn3" wire:click="clearFilters">Reset</button>
											</div>
										</div>
									</form>
								</div>
							</div>
							<div class="row">
								@forelse($this->listings as $job)
									<div class="col-lg-6 col-md-6 col-sm-12" wire:key="job-{{ $job->id }}">
										<x-pitnik.job-box :job="$job" />
									</div>
								@empty
									<div class="col-lg-12"><p>No jobs match your search.</p></div>
								@endforelse
							</div>
							@if($this->listings->hasPages())
								<div class="row">
									<div class="col-lg-12 text-center">
										{{ $this->listings->links() }}
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