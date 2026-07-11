<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmployerRejected extends Notification
{
    use Queueable;
    public function __construct(public string $reason = '') {}
    public function via($n): array { return ['database']; }
    public function toArray($n): array
    {
        return [
            'message' => 'عذراً، تم رفض طلب تسجيل شركتك.' . ($this->reason ? ' السبب: ' . $this->reason : ''),
            'link'    => '/login',
        ];
    }
}
