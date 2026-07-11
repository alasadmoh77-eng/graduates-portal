<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewContactMessage extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $message)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'رسالة جديدة من: ' . $this->message->name,
            'subject' => $this->message->subject,
            'email' => $this->message->email,
            'link' => route('admin.contact-messages.index'),
        ];
    }
}
