<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobApplicationReceived extends Notification
{
    use Queueable;

    public function __construct(public JobApplication $application) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $job = $this->application->jobPosting;

        return [
            'actor_name' => $this->application->user->name,
            'actor_avatar' => $this->application->user->avatarUrl(),
            'message' => "{$this->application->user->name} applied for {$job->title}.",
            'url' => route('jobs.show', $job).'?tab=applicants',
        ];
    }
}
