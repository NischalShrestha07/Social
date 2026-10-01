@props(['job'])

<div {{ $attributes->merge(['class' => 'widget-shadow']) }}>
	<div class="career-thumb" style="height:4px;background:#088dcd;border-radius:4px 4px 0 0;"></div>
	<div style="padding:25px 25px 20px;">
		<h5 class="f-title" style="margin-bottom:8px;">
			<a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a>
		</h5>
		<span style="color:#535165;font-size:14px;">{{ $job->company }} · {{ $job->location }}</span>
		<p style="margin-top:15px;color:#757a95;">
			{{ Str::words(strip_tags($job->description), 18, '...') }}
		</p>
		<div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:16px;">
			<span style="background:#f2f2f7;border-radius:4px;padding:4px 10px;font-size:12px;color:#535165;">
				{{ \App\Models\JobPosting::typeLabel($job->type) }}
			</span>
			<span style="background:#f2f2f7;border-radius:4px;padding:4px 10px;font-size:12px;color:#535165;">
				{{ ucfirst($job->work_mode) }}
			</span>
			@if($job->salaryLabel())
				<span style="background:#f2f2f7;border-radius:4px;padding:4px 10px;font-size:12px;color:#535165;">
					{{ $job->salaryLabel() }}
				</span>
			@endif
			<span style="background:#f2f2f7;border-radius:4px;padding:4px 10px;font-size:12px;color:#535165;">
				{{ $job->applications_count }} {{ Str::plural('applicant', $job->applications_count) }}
			</span>
		</div>
		<div style="margin-top:20px;">
			<a href="{{ route('jobs.show', $job) }}" class="main-btn">View Job</a>
		</div>
	</div>
</div>