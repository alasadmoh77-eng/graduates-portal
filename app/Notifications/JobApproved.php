<?php
namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobApproved extends Notification
{
    use Queueable;
    public function __construct(public Job $job) {}
    public function via($n): array { return ['database']; }
    public function toArray($n): array
    {
        return [
            'message' => 'تمت الموافقة على إعلان وظيفتك "' . $this->job->title . '" وأصبح متاحاً للخريجين.',
            'link'    => '/employer/jobs',
        ];
    }
}
