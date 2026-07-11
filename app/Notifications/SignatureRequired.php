<?php

namespace App\Notifications;

use App\Models\IssuedDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SignatureRequired extends Notification
{
    use Queueable;

    public function __construct(
        private IssuedDocument $doc,
        private string $roleTitle
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $request = $this->doc->documentRequest;
        $requestNumber = $request->tracking_code ?? $request->id;
        $graduateName = $request->user->name ?? '';

        return [
            'type' => 'signature_required',
            'title' => 'توقيع مطلوب',
            'document_request_id' => $request->id,
            'issued_document_id' => $this->doc->id,
            'tracking_code' => $requestNumber,
            'graduate_name' => $graduateName,
            'current_signer_role' => $this->roleTitle,
            'document_type' => app()->getLocale() == 'ar'
                ? ($request->documentType->name_ar ?? '')
                : ($request->documentType->name_en ?? ''),
            'message' => 'يوجد طلب بانتظار توقيعك، رقم الطلب: ' . $requestNumber,
            'link' => route('admin.pending-signatures'),
        ];
    }
}
