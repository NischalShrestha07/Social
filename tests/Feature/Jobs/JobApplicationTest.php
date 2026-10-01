<?php

namespace Tests\Feature\Jobs;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\JobApplicationReceived;
use App\Notifications\JobApplicationStatusChanged;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_apply_to_an_open_job(): void
    {
        Notification::fake();

        $job = JobPosting::factory()->create();
        $applicant = User::factory()->create();

        Livewire::actingAs($applicant)
            ->test('pages::job-show', ['job' => $job])
            ->set('coverLetter', 'I would love to work here.')
            ->call('apply')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('job_applications', [
            'job_posting_id' => $job->id,
            'user_id' => $applicant->id,
            'status' => 'pending',
        ]);

        Notification::assertSentTo($job->user, JobApplicationReceived::class);
    }

    public function test_applying_uploads_a_resume(): void
    {
        Storage::fake('public');

        $job = JobPosting::factory()->create();
        $applicant = User::factory()->create();

        Livewire::actingAs($applicant)
            ->test('pages::job-show', ['job' => $job])
            ->set('coverLetter', 'See attached.')
            ->set('resume', UploadedFile::fake()->create('cv.pdf', 120, 'application/pdf'))
            ->call('apply')
            ->assertHasNoErrors();

        $application = JobApplication::firstOrFail();

        $this->assertNotNull($application->resume_path);
        Storage::disk('public')->assertExists($application->resume_path);
    }

    public function test_a_user_cannot_apply_twice(): void
    {
        $job = JobPosting::factory()->create();
        $applicant = User::factory()->create();

        JobApplication::factory()->create([
            'job_posting_id' => $job->id,
            'user_id' => $applicant->id,
        ]);

        Livewire::actingAs($applicant)
            ->test('pages::job-show', ['job' => $job])
            ->call('apply')
            ->assertHasErrors('coverLetter');

        $this->assertSame(1, JobApplication::count());
    }

    public function test_the_poster_cannot_apply_to_their_own_job(): void
    {
        $job = JobPosting::factory()->create();

        Livewire::actingAs($job->user)
            ->test('pages::job-show', ['job' => $job])
            ->call('apply')
            ->assertForbidden();

        $this->assertSame(0, JobApplication::count());
    }

    /** The mount-time `view` authorization is what stops these, so assert on the HTTP route. */
    public function test_nobody_can_apply_to_a_closed_job(): void
    {
        $job = JobPosting::factory()->closed()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('jobs.show', $job))
            ->assertForbidden();

        $this->assertSame(0, JobApplication::count());
    }

    public function test_nobody_can_apply_to_an_expired_job(): void
    {
        $job = JobPosting::factory()->expired()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('jobs.show', $job))
            ->assertForbidden();

        $this->assertSame(0, JobApplication::count());
    }

    public function test_the_poster_can_reopen_a_closed_job_and_then_applications_work(): void
    {
        $job = JobPosting::factory()->closed()->create();

        $this->actingAs($job->user)
            ->get(route('jobs.show', $job))
            ->assertOk();

        $job->update(['is_open' => true]);

        $this->actingAs($job->user)
            ->get(route('jobs.show', $job))
            ->assertOk();
    }

    public function test_only_the_poster_can_view_the_applicants_page(): void
    {
        $job = JobPosting::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('jobs.applicants', $job))
            ->assertForbidden();

        $this->actingAs($job->user)
            ->get(route('jobs.applicants', $job))
            ->assertOk();
    }

    public function test_poster_can_accept_an_applicant(): void
    {
        Notification::fake();

        $job = JobPosting::factory()->create();
        $application = JobApplication::factory()->create(['job_posting_id' => $job->id]);

        Livewire::actingAs($job->user)
            ->test('pages::job-applicants', ['job' => $job])
            ->call('setStatus', $application->id, 'accepted')
            ->assertHasNoErrors();

        $this->assertSame('accepted', $application->refresh()->status);
        Notification::assertSentTo($application->user, JobApplicationStatusChanged::class);
    }

    public function test_poster_can_reject_an_applicant(): void
    {
        $job = JobPosting::factory()->create();
        $application = JobApplication::factory()->create(['job_posting_id' => $job->id]);

        Livewire::actingAs($job->user)
            ->test('pages::job-applicants', ['job' => $job])
            ->call('setStatus', $application->id, 'rejected')
            ->assertHasNoErrors();

        $this->assertSame('rejected', $application->refresh()->status);
    }

    public function test_an_invalid_status_is_rejected(): void
    {
        $job = JobPosting::factory()->create();
        $application = JobApplication::factory()->create(['job_posting_id' => $job->id]);

        Livewire::actingAs($job->user)
            ->test('pages::job-applicants', ['job' => $job])
            ->call('setStatus', $application->id, 'hacked')
            ->assertStatus(422);

        $this->assertSame('pending', $application->refresh()->status);
    }

    public function test_poster_cannot_touch_an_application_from_another_job(): void
    {
        $job = JobPosting::factory()->create();
        $otherJob = JobPosting::factory()->create();
        $foreignApplication = JobApplication::factory()->create(['job_posting_id' => $otherJob->id]);

        Livewire::actingAs($job->user)
            ->test('pages::job-applicants', ['job' => $job])
            ->call('setStatus', $foreignApplication->id, 'accepted')
            ->assertNotFound();

        $this->assertSame('pending', $foreignApplication->refresh()->status);
    }

    public function test_a_non_poster_cannot_change_status_through_the_component(): void
    {
        $job = JobPosting::factory()->create();
        $application = JobApplication::factory()->create(['job_posting_id' => $job->id]);

        $this->actingAs(User::factory()->create())
            ->get(route('jobs.applicants', $job))
            ->assertForbidden();

        $this->assertSame('pending', $application->refresh()->status);
    }

    public function test_poster_can_remove_an_application(): void
    {
        $job = JobPosting::factory()->create();
        $application = JobApplication::factory()->create(['job_posting_id' => $job->id]);

        Livewire::actingAs($job->user)
            ->test('pages::job-applicants', ['job' => $job])
            ->call('deleteApplication', $application->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('job_applications', ['id' => $application->id]);
    }

    public function test_deleting_a_job_cascades_to_its_applications(): void
    {
        $job = JobPosting::factory()->create();
        JobApplication::factory()->count(3)->create(['job_posting_id' => $job->id]);

        $job->delete();

        $this->assertSame(0, JobApplication::count());
    }
}
