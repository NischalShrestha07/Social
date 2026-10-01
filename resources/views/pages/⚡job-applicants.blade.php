<?php

use App\Models\JobPosting;
use App\Notifications\JobApplicationStatusChanged;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Applicants'), Layout('layouts::bare')] class extends Component
{
    #[Locked]
    public int $jobId;

    public function mount(JobPosting $job): void
    {
        Gate::authorize('reviewApplications', $job);
        $this->jobId = $job->id;
    }

    #[Computed]
    public function job(): JobPosting
    {
        return JobPosting::findOrFail($this->jobId);
    }

    /** Applicant ids come from the client, so always re-resolve through the job's relation. */
    #[Computed]
    public function applicants()
    {
        return $this->job()
            ->applications()
            ->with('user')
            ->latest()
            ->get();
    }

    public function setStatus(int $applicationId, string $status): void
    {
        Gate::authorize('reviewApplications', $this->job());
        abort_unless(in_array($status, ['accepted', 'rejected'], true), 422);

        $application = $this->job()
            ->applications()
            ->with('user')
            ->whereKey($applicationId)
            ->firstOrFail();

        $application->update(['status' => $status]);
        $application->user->notify(new App\Notifications\JobApplicationStatusChanged($application, $status));

        unset($this->applicants);
    }

    public function deleteApplication(int $applicationId): void
    {
        Gate::authorize('reviewApplications', $this->job());

        $this->job()
            ->applications()
            ->whereKey($applicationId)
            ->firstOrFail()
            ->delete();

        unset($this->applicants);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Applicants'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>Applicants <span>{{ $this->applicants->count() }}</span></h5>
									<span style="color:#757a95;">
										<a href="{{ route('jobs.show', $this->job) }}">{{ $this->job->title }}</a>
										· {{ $this->job->company }}
									</span>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div class="row">
								@forelse($this->applicants as $application)
									<div class="col-lg-6 col-md-6 col-sm-12" wire:key="app-{{ $application->id }}">
										<div class="widget-shadow" style="padding:20px;">
											<div style="display:flex;align-items:center;gap:15px;">
												<figure style="margin:0;">
													<img src="{{ $application->user->avatarUrl() }}" alt="" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
												</figure>
												<div style="flex:1;">
													<h5 style="margin:0 0 4px 0;font-size:16px;">
														<a href="{{ route('profile.show', $application->user) }}">{{ $application->user->name }}</a>
													</h5>
													<span style="color:#757a95;font-size:13px;">
														{{ '@'.$application->user->username }} · {{ $application->created_at->diffForHumans() }}
													</span>
												</div>
												<span style="background:{{ $application->status === 'accepted' ? '#e6f7ed' : ($application->status === 'rejected' ? '#fdeaea' : '#f2f2f7') }};border-radius:4px;padding:4px 10px;font-size:12px;color:#535165;">
													{{ ucfirst($application->status) }}
												</span>
											</div>
											@if($application->cover_letter)
												<p style="margin-top:15px;color:#535165;">{{ Str::words($application->cover_letter, 40, '...') }}</p>
											@endif
											@if($application->resumeUrl())
												<div style="margin-top:12px;">
													<a href="{{ $application->resumeUrl() }}" target="_blank">Download resume</a>
												</div>
											@endif
											<div style="margin-top:18px;display:flex;gap:10px;">
												<button type="button" class="main-btn" wire:click="setStatus({{ $application->id }}, 'accepted')">Accept</button>
												<button type="button" class="main-btn3" wire:click="setStatus({{ $application->id }}, 'rejected')">Reject</button>
												<button type="button" class="main-btn3" style="background:#e0245e;color:#fff;"
													wire:click="deleteApplication({{ $application->id }})"
													wire:confirm="Remove this application?">
													Remove
												</button>
											</div>
										</div>
									</div>
								@empty
									<div class="col-lg-12"><p>No applications yet.</p></div>
								@endforelse
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent