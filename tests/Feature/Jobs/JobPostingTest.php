<?php

namespace Tests\Feature\Jobs;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('jobs'))->assertRedirect(route('login'));
    }

    public function test_job_board_renders(): void
    {
        $user = User::factory()->create();
        JobPosting::factory()->create(['title' => 'Laravel Engineer']);

        Livewire::actingAs($user)
            ->test('pages::jobs')
            ->assertOk()
            ->assertSee('Laravel Engineer');
    }

    public function test_only_open_jobs_appear_in_the_listing(): void
    {
        $user = User::factory()->create();
        JobPosting::factory()->create(['title' => 'Open Role']);
        JobPosting::factory()->closed()->create(['title' => 'Closed Role']);
        JobPosting::factory()->expired()->create(['title' => 'Expired Role']);

        Livewire::actingAs($user)
            ->test('pages::jobs')
            ->assertSee('Open Role')
            ->assertDontSee('Closed Role')
            ->assertDontSee('Expired Role');
    }

    public function test_search_filters_by_title_and_company(): void
    {
        $user = User::factory()->create();
        JobPosting::factory()->create(['title' => 'Laravel Engineer', 'company' => 'Acme']);
        JobPosting::factory()->create(['title' => 'Graphic Designer', 'company' => 'Pixel Forge']);

        Livewire::actingAs($user)
            ->test('pages::jobs')
            ->set('search', 'Laravel')
            ->assertSee('Laravel Engineer')
            ->assertDontSee('Graphic Designer');

        Livewire::actingAs($user)
            ->test('pages::jobs')
            ->set('search', 'Pixel')
            ->assertSee('Graphic Designer')
            ->assertDontSee('Laravel Engineer');
    }

    public function test_type_and_work_mode_filters_combine(): void
    {
        $user = User::factory()->create();
        JobPosting::factory()->remote()->create(['title' => 'Remote Full Time', 'type' => 'full_time']);
        JobPosting::factory()->create(['title' => 'Onsite Full Time', 'type' => 'full_time', 'work_mode' => 'onsite']);

        Livewire::actingAs($user)
            ->test('pages::jobs')
            ->set('type', 'full_time')
            ->set('workMode', 'remote')
            ->assertSee('Remote Full Time')
            ->assertDontSee('Onsite Full Time');
    }

    public function test_creator_can_post_a_job(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test('pages::job-create')
            ->set('title', 'Senior Laravel Developer')
            ->set('description', 'Build things.')
            ->set('company', 'Acme Inc')
            ->set('location', 'Remote')
            ->set('type', 'full_time')
            ->set('workMode', 'remote')
            ->set('salaryMin', 90000)
            ->set('salaryMax', 120000)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $job = JobPosting::first();

        $this->assertSame('Senior Laravel Developer', $job->title);
        $this->assertSame('senior-laravel-developer', $job->slug);
        $this->assertSame($user->id, $job->user_id);
        $this->assertSame(90000, $job->salary_min);
    }

    public function test_job_creation_validates_input(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test('pages::job-create')
            ->set('title', '')
            ->set('description', '')
            ->set('company', '')
            ->set('location', '')
            ->call('save')
            ->assertHasErrors(['title', 'description', 'company', 'location']);

        $this->assertSame(0, JobPosting::count());
    }

    public function test_salary_max_must_not_be_below_min(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test('pages::job-create')
            ->set('title', 'Role')
            ->set('description', 'Body')
            ->set('company', 'Acme')
            ->set('location', 'Remote')
            ->set('salaryMin', 90000)
            ->set('salaryMax', 10000)
            ->call('save')
            ->assertHasErrors('salaryMax');
    }

    public function test_duplicate_titles_get_unique_slugs(): void
    {
        $user = User::factory()->create();

        JobPosting::factory()->create(['title' => 'Laravel Engineer', 'slug' => 'laravel-engineer']);

        Livewire::actingAs($user)
            ->test('pages::job-create')
            ->set('title', 'Laravel Engineer')
            ->set('description', 'Body')
            ->set('company', 'Acme')
            ->set('location', 'Remote')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('laravel-engineer-1', JobPosting::latest('id')->first()->slug);
    }

    public function test_closed_job_is_hidden_from_other_users(): void
    {
        $job = JobPosting::factory()->closed()->create();
        $other = User::factory()->create();

        $this->actingAs($other)->get(route('jobs.show', $job))->assertForbidden();

        $this->actingAs($job->user)->get(route('jobs.show', $job))->assertOk();
    }

    public function test_expired_job_is_hidden_from_other_users(): void
    {
        $job = JobPosting::factory()->expired()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('jobs.show', $job))
            ->assertForbidden();
    }

    public function test_only_owner_can_open_the_manage_page(): void
    {
        $job = JobPosting::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('jobs.manage', $job))
            ->assertForbidden();

        $this->actingAs($job->user)
            ->get(route('jobs.manage', $job))
            ->assertOk();
    }

    public function test_owner_can_edit_their_posting(): void
    {
        $job = JobPosting::factory()->create(['title' => 'Old Title', 'slug' => 'old-title']);

        Livewire::actingAs($job->user)
            ->test('pages::job-manage', ['job' => $job])
            ->set('title', 'New Title')
            ->call('save')
            ->assertHasNoErrors();

        $job->refresh();

        $this->assertSame('New Title', $job->title);
        $this->assertSame('new-title', $job->slug);
    }

    public function test_non_owner_cannot_edit_through_the_component(): void
    {
        $job = JobPosting::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('jobs.manage', $job))
            ->assertForbidden();
    }

    public function test_owner_can_toggle_open_state(): void
    {
        $job = JobPosting::factory()->create();

        Livewire::actingAs($job->user)
            ->test('pages::job-show', ['job' => $job])
            ->call('toggleOpen')
            ->assertHasNoErrors();

        $this->assertFalse($job->refresh()->is_open);
    }

    public function test_owner_can_delete_their_posting(): void
    {
        $job = JobPosting::factory()->create();

        Livewire::actingAs($job->user)
            ->test('pages::job-show', ['job' => $job])
            ->call('deleteJob')
            ->assertRedirect(route('jobs'));

        $this->assertDatabaseMissing('job_postings', ['id' => $job->id]);
    }

    public function test_non_owner_cannot_delete_through_the_component(): void
    {
        $job = JobPosting::factory()->create();

        Livewire::actingAs(User::factory()->create())
            ->test('pages::job-show', ['job' => $job])
            ->call('deleteJob')
            ->assertForbidden();

        $this->assertDatabaseHas('job_postings', ['id' => $job->id]);
    }
}
