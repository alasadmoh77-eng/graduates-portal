<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentProofApproved extends Notification
{
    use Queueable;

    protected $documentRequest;

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
        return [
            'type' => 'payment_approved',
            'document_request_id' => $this->documentRequest->id,
            'tracking_code' => $this->documentRequest->tracking_code,
            'message' => __('app.payment_proof_approved_msg', ['code' => $this->documentRequest->tracking_code]),
            'link' => route('graduate.documents.show', $this->documentRequest->id),
        ];
    }
}
