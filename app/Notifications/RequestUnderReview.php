<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RequestUnderReview extends Notification
{
    use Queueable;

    public $documentRequest;

    public function __construct(DocumentRequest $documentRequest)
    {
        $this->documentRequest = $documentRequest;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $graduateName = $this->documentRequest->user->name ?? __('app.unknown_graduate');
        $trackingCode = $this->documentRequest->tracking_code;

        return [
            'type' => 'academic_review',
            'title' => 'طلب وثيقة جديد للمراجعة الأكاديمية',
            'message' => 'طلب جديد للمراجعة الأكاديمية من الخريج: ' . $graduateName,
            'document_request_id' => $this->documentRequest->id,
            'graduate_id' => $this->documentRequest->user_id,
            'graduate_name' => $graduateName,
            'document_type' => app()->getLocale() == 'ar'
                ? ($this->documentRequest->documentType->name_ar ?? '')
                : ($this->documentRequest->documentType->name_en ?? ''),
            'link' => route('admin.requests.show', $this->documentRequest->id),
        ];
    }
}
