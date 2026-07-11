<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmployerSuspended extends Notification
{
    use Queueable;
    public function via($n): array { return ['database']; }
    public function toArray($n): array
    {
        return [
            'message' => 'تم إيقاف حساب شركتك مؤقتاً. يرجى التواصل مع الجامعة لمزيد من المعلومات.',
            'link'    => '/login',
        ];
    }
}
