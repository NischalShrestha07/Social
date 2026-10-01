<?php

use App\Models\JobPosting;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Post a Job'), Layout('layouts::bare')] class extends Component
{
    public string $title = '';

    public string $description = '';

    public string $company = '';

    public string $location = '';

    public string $type = 'full_time';

    public string $workMode = 'onsite';

    public $salaryMin = '';

    public $salaryMax = '';

    public string $expiresAt = '';

    public function save(): void
    {
        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string|max:10000',
            'company' => 'required|string|max:150',
            'location' => 'required|string|max:150',
            'type' => ['required', Rule::in(JobPosting::TYPES)],
            'workMode' => ['required', Rule::in(JobPosting::WORK_MODES)],
            'salaryMin' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'salaryMax' => ['nullable', 'integer', 'min:0', 'max:10000000', 'gte:salaryMin'],
            'expiresAt' => ['nullable', 'date', 'after:today'],
        ]);

        $job = JobPosting::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => JobPosting::uniqueSlug($validated['title']),
            'description' => $validated['description'],
            'company' => $validated['company'],
            'location' => $validated['location'],
            'type' => $validated['type'],
            'work_mode' => $validated['workMode'],
            'salary_min' => $validated['salaryMin'] !== '' ? (int) $validated['salaryMin'] : null,
            'salary_max' => $validated['salaryMax'] !== '' ? (int) $validated['salaryMax'] : null,
            'salary_currency' => 'USD',
            'expires_at' => $validated['expiresAt'] ?: null,
            'is_open' => true,
        ]);

        $this->redirectRoute('jobs.show', $job);
    }
};
?>
@component('layouts::pitnik', ['pageTitle' => 'Post a Job'])
	<section>
		<div class="gap2 gray-bg">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-8">
						<div class="central-meta">
							<div class="editing-info">
								<h5 class="f-title"><i class="ti-briefcase"></i> Post a Job</h5>
								<form wire:submit="save" class="c-form">
									<div>
										<label>Job title</label>
										<input type="text" wire:model="title" required maxlength="150" placeholder="e.g. Senior Laravel Developer">
										@error('title') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Company</label>
										<input type="text" wire:model="company" required maxlength="150" placeholder="e.g. Acme Inc">
										@error('company') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Location</label>
										<input type="text" wire:model="location" required maxlength="150" placeholder="e.g. Kathmandu, Nepal">
										@error('location') <span style="color:#e0245e;">{{ $message }}</span> @enderror
									</div>
									<div>
										<label>Description</label>
										<textarea rows="8" wire:model="description" required maxlength="10000" placeholder="What is the role, and what are you looking for?"></textarea>
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
											<label>Salary min (annual, optional)</label>
											<input type="number" wire:model="salaryMin" min="0" placeholder="40000">
											@error('salaryMin') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-4">
											<label>Salary max (optional)</label>
											<input type="number" wire:model="salaryMax" min="0" placeholder="80000">
											@error('salaryMax') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
										<div class="col-md-4">
											<label>Expires on (optional)</label>
											<input type="date" wire:model="expiresAt">
											@error('expiresAt') <span style="color:#e0245e;">{{ $message }}</span> @enderror
										</div>
									</div>
									<div>
										<button type="submit" class="main-btn" wire:loading.attr="disabled">Publish Job</button>
										<a href="{{ route('jobs') }}" class="main-btn3">Cancel</a>
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