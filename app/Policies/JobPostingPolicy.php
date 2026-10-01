<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    /** Closed postings are only visible to the poster; everyone else sees 404. */
    public function view(?User $user, JobPosting $job): bool
    {
        return $job->isAcceptingApplications() || ($user !== null && $user->id === $job->user_id);
    }

    public function update(User $user, JobPosting $job): bool
    {
        return $user->id === $job->user_id;
    }

    public function delete(User $user, JobPosting $job): bool
    {
        return $user->id === $job->user_id;
    }

    /** Only the poster reviews applicants. */
    public function reviewApplications(User $user, JobPosting $job): bool
    {
        return $user->id === $job->user_id;
    }

    /** The poster can't apply to their own listing, and closed jobs take no applications. */
    public function apply(User $user, JobPosting $job): bool
    {
        return $user->id !== $job->user_id && $job->isAcceptingApplications();
    }
}
