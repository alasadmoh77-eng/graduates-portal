<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RequestStatusChanged extends Notification
{
    use Queueable;

    protected $request;
    protected $oldStatus;
    protected $newStatus;

    public function __construct(DocumentRequest $request, $oldStatus, $newStatus)
    {
        $this->request = $request;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable): array
    {
        $channels = ['database'];
        if (config('app.email_notifications_enabled', false)) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تحديث حالة طلب المستند - ' . $this->request->tracking_code)
            ->line('تم تغيير حالة طلبك رقم: ' . $this->request->tracking_code)
            ->line('الحالة الجديدة: ' . $this->newStatus)
            ->action('عرض الطلب', route('graduate.documents.show', $this->request->id))
            ->line('شكراً لاستخدامك بوابتنا الإلكترونية.');
    }

    public function toArray($notifiable): array
    {
        return [
            'document_request_id' => $this->request->id,
            'tracking_code' => $this->request->tracking_code,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'document_type' => $this->request->documentType->name_ar,
            'note' => $this->request->admin_note,
            'link' => route('graduate.documents.show', $this->request->id),
        ];
    }
}
