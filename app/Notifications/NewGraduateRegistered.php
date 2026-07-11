<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewGraduateRegistered extends Notification
{
    use Queueable;

    public $graduate;

    public function __construct(User $graduate)
    {
        $this->graduate = $graduate;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_graduate_registered',
            'title' => 'تسجيل خريج جديد',
            'message' => 'تم تسجيل خريج جديد باسم: ' . $this->graduate->name,
            'graduate_id' => $this->graduate->id,
            'graduate_name' => $this->graduate->name,
            'graduate_email' => $this->graduate->email,
            'registration_date' => $this->graduate->created_at ? $this->graduate->created_at->format('Y-m-d') : now()->format('Y-m-d'),
            'link' => route('admin.graduates.show', $this->graduate->id),
        ];
    }
}
