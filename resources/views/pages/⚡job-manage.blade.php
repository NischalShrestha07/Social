<?php

use App\Models\JobPosting;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Edit Job'), Layout('layouts::bare')] class extends Component
{
    #[Locked]
    public int $jobId;

    public string $title = '';

    public string $description = '';

    public string $company = '';

    public string $location = '';

    public string $type = 'full_time';

    public string $workMode = 'onsite';

    public $salaryMin = '';

    public $salaryMax = '';

    public string $expiresAt = '';

    public function mount(JobPosting $job): void
    {
        Gate::authorize('update', $job);
        $this->jobId = $job->id;
        $this->title = $job->title;
        $this->description = $job->description;
        $this->company = $job->company;
        $this->location = $job->location;
        $this->type = $job->type;
        $this->workMode = $job->work_mode;
        $this->salaryMin = $job->salary_min;
        $this->salaryMax = $job->salary_max;
        $this->expiresAt = $job->expires_at?->format('Y-m-d') ?? '';
    }

    public function save(): void
    {
        $job = JobPosting::findOrFail($this->jobId);
        Gate::authorize('update', $job);

        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string|max:10000',
            'company' => 'required|string|max:150',
            'location' => 'required|string|max:150',
            'type' => ['required', Rule::in(JobPosting::TYPES)],
            'workMode' => ['required', Rule::in(JobPosting::WORK_MODES)],
            'salaryMin' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'salaryMax' => ['nullable', 'integer', 'min:0', 'max:10000000', 'gte:salaryMin'],
            'expiresAt' => ['nullable', 'date'],
        ]);

        // Retitling regenerates the slug, so redirect to the new URL.
        if ($validated['title'] !== $job->title) {
            $job->slug = JobPosting::uniqueSlug($validated['title'], $job->id);
        }

        $job->update([
            'title' => $validated['title'],
            'slug' => $job->slug,
            'description' => $validated['description'],
            'company' => $validated['company'],
            'location' => $validated['location'],
            'type' => $validated['type'],
            'work_mode' => $validated['workMode'],
            'salary_min' => $validated['salaryMin'] !== '' ? (int) $validated['salaryMin'] : null,
            'salary_max' => $validated['salaryMax'] !== '' ? (int) $validated['salaryMax'] : null,
            'expires_at' => $validated['expiresAt'] ?: null,
        ]);

        $this->redirectRoute('jobs.show', $job);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Edit Job'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-pencil-alt"></i> Edit Job</h5>
								<form wire:submit="save" class="c-form">
									<div>
										<label>Job title</label>
										<input type="text" wire:model="title" required maxlength="150">
										@error('title') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Company</label>
										<input type="text" wire:model="company" required maxlength="150">
										@error('company') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Location</label>
										<input type="text" wire:model="location" required maxlength="150">
										@error('location') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Description</label>
										<textarea rows="8" wire:model="description" required maxlength="10000"></textarea>
										@error('description') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div class="row">
										<div class="col-md-6">
											<label>Job type</label>
											<select wire:model="type" required>
												@foreach(\App\Models\JobPosting::TYPES as $t)
													<option value="{{ $t }}">{{ \App\Models\JobPosting::typeLabel($t) }}</option>
												@endforeach
											</select>
											@error('type') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-6">
											<label>Work mode</label>
											<select wire:model="workMode" required>
												@foreach(\App\Models\JobPosting::WORK_MODES as $m)
													<option value="{{ $m }}">{{ ucfirst($m) }}</option>
												@endforeach
											</select>
											@error('workMode') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<label>Salary min (optional)</label>
											<input type="number" wire:model="salaryMin" min="0">
											@error('salaryMin') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-4">
											<label>Salary max (optional)</label>
											<input type="number" wire:model="salaryMax" min="0">
											@error('salaryMax') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-4">
											<label>Expires on (optional)</label>
											<input type="date" wire:model="expiresAt">
											@error('expiresAt') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Save Changes</button>
										<a href="{{ route('jobs.show', \App\Models\JobPosting::findOrFail($this->jobId)) }}" class="main-btn3">Cancel</a>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endcomponent