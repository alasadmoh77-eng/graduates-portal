<?php
namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobRejected extends Notification
{
    use Queueable;
    public function __construct(public Job $job, public string $reason = '') {}
    public function via($n): array { return ['database']; }
    public function toArray($n): array
    {
        return [
            'message' => 'تم رفض إعلان وظيفتك "' . $this->job->title . '".' . ($this->reason ? ' السبب: ' . $this->reason : ''),
            'link'    => '/employer/jobs',
        ];
    }
}
