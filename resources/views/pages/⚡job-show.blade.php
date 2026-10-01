<?php

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Notifications\JobApplicationReceived;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Job'), Layout('layouts::bare')] class extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $jobId;

    #[Url(as: 'tab', except: 'details')]
    public string $tab = 'details';

    public string $coverLetter = '';

    public $resume = null;

    public function mount(JobPosting $job): void
    {
        Gate::authorize('view', $job);
        $this->jobId = $job->id;
    }

    #[Computed]
    public function job(): JobPosting
    {
        return JobPosting::with('user')->withCount('applications')->findOrFail($this->jobId);
    }

    #[Computed]
    public function myApplication(): ?JobApplication
    {
        return JobApplication::where('job_posting_id', $this->jobId)
            ->where('user_id', auth()->id())
            ->first();
    }

    #[Computed]
    public function canApply(): bool
    {
        return auth()->user()->can('apply', $this->job()) && ! $this->myApplication;
    }

    public function apply(): void
    {
        $job = $this->job();
        Gate::authorize('apply', $job);

        $validated = $this->validate([
            'coverLetter' => 'nullable|string|max:5000',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($job->applications()->where('user_id', auth()->id())->exists()) {
            $this->addError('coverLetter', 'You have already applied to this job.');

            return;
        }

        $application = $job->applications()->create([
            'user_id' => auth()->id(),
            'cover_letter' => $validated['coverLetter'] ?: null,
            'resume_path' => $this->resume?->store('resumes', 'public'),
        ]);

        $job->user->notify(new JobApplicationReceived($application));

        $this->reset('coverLetter', 'resume');
        unset($this->myApplication, $this->job, $this->canApply);
        session()->flash('status', 'Application sent.');
    }

    public function toggleOpen(): void
    {
        $job = $this->job();
        Gate::authorize('update', $job);
        $job->update(['is_open' => ! $job->is_open]);
        unset($this->job, $this->canApply);
    }

    public function deleteJob(): void
    {
        $job = $this->job();
        Gate::authorize('delete', $job);
        $job->delete();
        $this->redirectRoute('jobs');
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => $this->job->title])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="title-block">
								<div class="align-left">
									<h5>{{ $this->job->title }}</h5>
									<span style="color:#757a95;">
										{{ $this->job->company }} · {{ $this->job->location }}
										· Posted by
										<a href="{{ route('profile.show', $this->job->user) }}">{{ $this->job->user->name }}</a>
									</span>
								</div>
							</div>
						</div>
						<div class="central-meta padding30">
							<div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:25px;">
								<span style="background:#f2f2f7;border-radius:4px;padding:6px 12px;font-size:13px;color:#535165;">
									{{ \App\Models\JobPosting::typeLabel($this->job->type) }}
								</span>
								<span style="background:#f2f2f7;border-radius:4px;padding:6px 12px;font-size:13px;color:#535165;">
									{{ ucfirst($this->job->work_mode) }}
								</span>
								@if($this->job->salaryLabel())
									<span style="background:#f2f2f7;border-radius:4px;padding:6px 12px;font-size:13px;color:#535165;">
										{{ $this->job->salaryLabel() }}
									</span>
								@endif
								@if($this->job->expires_at)
									<span style="background:#f2f2f7;border-radius:4px;padding:6px 12px;font-size:13px;color:#535165;">
										Expires {{ $this->job->expires_at->format('M j, Y') }}
									</span>
								@endif
								<span style="background:{{ $this->job->isAcceptingApplications() ? '#e6f7ed' : '#fdeaea' }};border-radius:4px;padding:6px 12px;font-size:13px;color:#535165;">
									{{ $this->job->isAcceptingApplications() ? 'Accepting applications' : 'Closed' }}
								</span>
							</div>

							@if(session('status'))
								<div style="background:#e6f7ed;color:#1b7a45;padding:12px 16px;border-radius:4px;margin-bottom:20px;">
									{{ session('status') }}
								</div>
							@endif

							<p style="color:#535165;white-space:pre-line;">{{ $this->job->description }}</p>

							@if($this->canApply)
								<hr style="border-color:#eaeaea;margin:30px 0;">
								<h5 class="f-title" style="margin-bottom:18px;">Apply for this job</h5>
								<form wire:submit="apply" class="c-form">
									<div>
										<label>Cover letter (optional)</label>
										<textarea rows="5" wire:model="coverLetter" maxlength="5000" placeholder="Tell the employer why you are a good fit"></textarea>
										@error('coverLetter') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Resume (PDF/DOC, max 5MB)</label>
										<input type="file" wire:model="resume" accept=".pdf,.doc,.docx">
										@error('resume') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Submit Application</button>
									</div>
								</form>
							@elseif($this->myApplication)
								<div style="background:#f2f2f7;padding:18px;border-radius:4px;">
									<strong>You applied.</strong>
									<span style="color:#757a95;">
										Status:
										{{ \App\Models\JobPosting::typeLabel($this->myApplication->status) === 'Pending' ? 'Pending' : ucfirst($this->myApplication->status) }}
									</span>
									@if($this->myApplication->resumeUrl())
										<div style="margin-top:10px;">
											<a href="{{ $this->myApplication->resumeUrl() }}" target="_blank">View your uploaded resume</a>
										</div>
									@endif
								</div>
							@elseif($this->job->user_id === auth()->id())
								<p style="color:#757a95;">This is your own posting.</p>
							@else
								<p style="color:#757a95;">This job is no longer accepting applications.</p>
							@endif
						</div>
					</div>

					<div class="col-lg-4">
						<div class="central-meta padding30">
							<h5 class="f-title" style="margin-bottom:18px;">Job details</h5>
							<ul style="list-style:none;padding:0;margin:0;">
								<li style="padding:10px 0;border-bottom:1px solid #f2f2f7;">
									<strong>Type:</strong> {{ \App\Models\JobPosting::typeLabel($this->job->type) }}
								</li>
								<li style="padding:10px 0;border-bottom:1px solid #f2f2f7;">
									<strong>Work mode:</strong> {{ ucfirst($this->job->work_mode) }}
								</li>
								<li style="padding:10px 0;border-bottom:1px solid #f2f2f7;">
									<strong>Location:</strong> {{ $this->job->location }}
								</li>
								<li style="padding:10px 0;border-bottom:1px solid #f2f2f7;">
									<strong>Applicants:</strong> {{ $this->job->applications_count }}
								</li>
								<li style="padding:10px 0;">
									<strong>Posted:</strong> {{ $this->job->created_at->diffForHumans() }}
								</li>
							</ul>

							@if($this->job->user_id === auth()->id())
								<div style="margin-top:25px;">
									<button type="button" class="main-btn" wire:click="toggleOpen">
										{{ $this->job->is_open ? 'Close applications' : 'Reopen applications' }}
									</button>
									<a href="{{ route('jobs.manage', $this->job) }}" class="main-btn3">Edit</a>
									<a href="{{ route('jobs.applicants', $this->job) }}" class="main-btn3">
										Applicants ({{ $this->job->applications_count }})
									</a>
									<button type="button" class="main-btn3" style="background:#e0245e;color:#fff;" wire:click="deleteJob"
										wire:confirm="Delete this job posting?">
										Delete
									</button>
								</div>
							@else
								<div style="margin-top:25px;">
									<a href="{{ route('jobs') }}" class="main-btn3">Back to jobs</a>
								</div>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent