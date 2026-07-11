<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobModerated extends Notification
{
    use Queueable;

    protected $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $status_ar = $this->job->status == 'active' ? 'مقبول ونشط' : 'مرفوض/مغلق';
        return [
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'status' => $this->job->status,
            'message' => 'تم تحديث حالة إعلان الوظيفة (' . $this->job->title . ') إلى: ' . $status_ar,
            'link' => route('employer.jobs.index'),
        ];
    }
}
