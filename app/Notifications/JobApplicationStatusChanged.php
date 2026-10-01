<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobApplicationStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public JobApplication $application, public string $status) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        $job = $this->application->jobPosting;
        $label = ucfirst($this->status);

        return [
            'actor_name' => $job->company,
            'actor_avatar' => asset('vendor/pitnik/images/logo2.png'),
            'message' => "Your application for {$job->title} was {$label}.",
            'url' => route('jobs.show', $job),
        ];
    }
}
