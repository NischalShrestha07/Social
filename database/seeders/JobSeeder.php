<?php

namespace Database\Seeders;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::firstOrCreate(
            ['email' => 'hiring@acme.test'],
            ['name' => 'Acme Hiring', 'username' => 'acmehiring', 'password' => 'password', 'email_verified_at' => now()],
        );

        $applicant = User::firstOrCreate(
            ['email' => 'seeker@example.test'],
            ['name' => 'Sam Seeker', 'username' => 'samseeker', 'password' => 'password', 'email_verified_at' => now()],
        );

        $jobs = [
            ['Senior Laravel Developer', 'Acme Inc', 'Remote', 'full_time', 'remote', 90000, 130000],
            ['Frontend Engineer (Vue)', 'Nimbus Labs', 'Kathmandu', 'full_time', 'hybrid', 60000, 85000],
            ['Part-time Support Specialist', 'Acme Inc', 'Kathmandu', 'part_time', 'onsite', 25000, 40000],
            ['Design Intern', 'Pixel Forge', 'Lalitpur', 'internship', 'onsite', null, null],
        ];

        foreach ($jobs as [$title, $company, $location, $type, $mode, $min, $max]) {
            $job = JobPosting::firstOrCreate(
                ['title' => $title],
                [
                    'user_id' => $owner->id,
                    'slug' => JobPosting::uniqueSlug($title),
                    'description' => "We are hiring a {$title} to join our {$company} team. You will work on real product code alongside a small senior team, with a real say in how things get built.",
                    'company' => $company,
                    'location' => $location,
                    'type' => $type,
                    'work_mode' => $mode,
                    'salary_min' => $min,
                    'salary_max' => $max,
                    'salary_currency' => 'USD',
                    'expires_at' => now()->addMonth()->toDateString(),
                    'is_open' => true,
                ],
            );

            JobApplication::firstOrCreate(
                ['job_posting_id' => $job->id, 'user_id' => $applicant->id],
                ['cover_letter' => 'I have been building Laravel apps for four years and would love to work on this.', 'status' => 'pending'],
            );
        }
    }
}
