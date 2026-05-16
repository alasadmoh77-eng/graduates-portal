<?php

namespace App\Services;

use App\Models\DocumentRequest;
use App\Models\IssuedDocument;
use App\Enums\RequestStatus;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function processRequest(DocumentRequest $request, string $action, ?string $note = null): void
    {
        if ($action === 'approve') {
            $request->update([
                'status' => RequestStatus::APPROVED->value,
                'admin_note' => $note
            ]);
            
            $this->generateDocument($request);

            AuditLogService::log('approve_document_request', DocumentRequest::class, $request->id);
            
        } elseif ($action === 'reject') {
            $request->update([
                'status' => RequestStatus::REJECTED->value,
                'admin_note' => $note
            ]);
            
            AuditLogService::log('reject_document_request', DocumentRequest::class, $request->id);
        }
    }

    private function generateDocument(DocumentRequest $request): void
    {
        $serialNumber = 'SRU-DOC-' . date('Y') . '-' . str_pad($request->id, 5, '0', STR_PAD_LEFT);
        $qrToken = Str::random(32);
        
        // Ensure directory exists
        if (!Storage::disk('public')->exists('documents')) {
            Storage::disk('public')->makeDirectory('documents');
        }

        $pdfPath = 'documents/' . $serialNumber . '.pdf';
        
        $issuedDoc = IssuedDocument::create([
            'request_id' => $request->id,
            'serial_number' => $serialNumber,
            'qr_token' => $qrToken,
            'issue_date' => now(),
            'pdf_path' => $pdfPath,
            'is_valid' => true,
        ]);

        $request->update(['status' => RequestStatus::READY->value]);

        // Generate QR code (PNG format to embed in PDF)
        $verifyUrl = route('verify.show', ['token' => $qrToken]);
        $qrCode = base64_encode(QrCode::format('png')->size(150)->generate($verifyUrl));

        // Generate PDF Document
        $pdf = Pdf::loadView('documents.template', [
            'document' => $issuedDoc,
            'request' => $request,
            'qrCode' => $qrCode
        ]);

        Storage::disk('public')->put($pdfPath, $pdf->output());
        
        AuditLogService::log('generate_document_pdf', IssuedDocument::class, $issuedDoc->id);
    }
}
