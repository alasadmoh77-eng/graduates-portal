<?php
namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApplicationStatusChanged extends Notification
{
    use Queueable;
    public function __construct(public JobApplication $application) {}
    public function via($n): array { return ['database']; }
    public function toArray($n): array
    {
        $statusLabel = $this->application->statusLabel();
        $jobTitle    = $this->application->job?->title ?? 'الوظيفة';
        return [
            'message' => 'تم تحديث حالة طلبك لوظيفة "' . $jobTitle . '" إلى: ' . $statusLabel,
            'link'    => '/graduate/applications/' . $this->application->id,
        ];
    }
}
