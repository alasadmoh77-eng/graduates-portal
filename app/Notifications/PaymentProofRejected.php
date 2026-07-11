<?php

namespace App\Notifications;

use App\Models\DocumentRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentProofRejected extends Notification
{
    use Queueable;

    protected $documentRequest;
    protected $reason;

    public function __construct(DocumentRequest $documentRequest, string $reason)
    {
        $this->documentRequest = $documentRequest;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'payment_rejected',
            'document_request_id' => $this->documentRequest->id,
            'tracking_code' => $this->documentRequest->tracking_code,
            'reason' => $this->reason,
            'message' => __('app.payment_proof_rejected_msg', ['code' => $this->documentRequest->tracking_code, 'reason' => $this->reason]),
            'link' => route('graduate.documents.show', $this->documentRequest->id),
        ];
    }
}
