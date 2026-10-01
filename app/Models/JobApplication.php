<?php

namespace App\Models;

use Database\Factories\JobApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $job_posting_id
 * @property int $user_id
 * @property string|null $cover_letter
 * @property string|null $resume_path
 * @property string $status pending|accepted|rejected
 */
#[Fillable(['job_posting_id', 'user_id', 'cover_letter', 'resume_path', 'status'])]
class JobApplication extends Model
{
    /** @use HasFactory<JobApplicationFactory> */
    use HasFactory;

    public const STATUSES = ['pending', 'accepted', 'rejected'];

    /** @return BelongsTo<JobPosting, $this> */
    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resumeUrl(): ?string
    {
        return $this->resume_path ? asset('storage/'.$this->resume_path) : null;
    }
}
