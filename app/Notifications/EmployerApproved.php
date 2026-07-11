<?php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmployerApproved extends Notification
{
    use Queueable;
    public function via($n): array { return ['database']; }
    public function toArray($n): array
    {
        return [
            'message' => 'تهانينا! تم قبول تسجيل شركتك في البوابة. يمكنك الآن تسجيل الدخول ونشر فرص العمل.',
            'link'    => '/login',
        ];
    }
}
