<?php

namespace App\Services;

use App\Models\DocumentRequest;
use App\Models\GraduateAcademicRecord;
use App\Models\IssuedDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Exception;

class DocumentIssuanceService
{
    protected $statusService;
    protected $studentInfoProvider;

    public function __construct(
        RequestStatusService $statusService,
        \App\Contracts\StudentInformationProvider $studentInfoProvider
    ) {
        $this->statusService = $statusService;
        $this->studentInfoProvider = $studentInfoProvider;
    }

    /**
     * Issue a document
     */
    public function issue(DocumentRequest $request, int $adminId): IssuedDocument
    {
        // 1. Validate request status
        if (!in_array($request->status, ['PENDING_SIGNATURES', 'READY', 'ISSUED'])) {
            throw new Exception("Document can only be issued if status is PENDING_SIGNATURES, READY or ISSUED.");
        }

        // 2. Payment check
        if ($request->documentType->payment_required && $request->payment_status !== 'approved') {
            throw new Exception(__('app.payment_must_be_approved'));
        }

        // 3. Check for existing issued document to preserve serial number and qr token
        $existingDoc = $request->issuedDocument;

        if ($existingDoc) {
            $serialNumber = $existingDoc->serial_number;
            $qrToken = $existingDoc->qr_token;
            $pdfPath = $existingDoc->pdf_path;

            if ($existingDoc->pdf_path && Storage::disk('local')->exists($existingDoc->pdf_path)) {
                Storage::disk('local')->delete($existingDoc->pdf_path);
            }
        } else {
            $year = now()->year;
            $prefix = "SRU-DOC-{$year}-";
            $lastDoc = IssuedDocument::where('serial_number', 'like', "{$prefix}%")
                ->orderBy('serial_number', 'desc')
                ->first();

            $number = $lastDoc ? ((int) substr($lastDoc->serial_number, -5)) + 1 : 1;
            $serialNumber = $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);

            $qrToken = Str::random(64);
        }

        $pdfFileName = "{$serialNumber}.pdf";
        $pdfPath = "documents/{$pdfFileName}";

        // 4. Generate QR Code Image (SVG Base64)
        $verifyUrl = route('verify.show', ['token' => $qrToken], true);
        $qrCodeData = QrCode::format('svg')->size(200)->margin(1)->generate($verifyUrl);
        $qrCodeBase64 = base64_encode($qrCodeData);

        // 5. Render PDF
        $pdf = $this->renderPdf($request, $serialNumber, $qrCodeBase64, $qrToken, $existingDoc);

        // 6. Save to Storage
        if (!Storage::disk('local')->exists('documents')) {
            Storage::disk('local')->makeDirectory('documents');
        }
        Storage::disk('local')->put($pdfPath, $pdf->output());

        // 7. Create/Update IssuedDocument record
        $issuedDoc = IssuedDocument::updateOrCreate(
            ['document_request_id' => $request->id],
            [
                'serial_number' => $serialNumber,
                'qr_token' => $qrToken,
                'pdf_path' => $pdfPath,
                'issued_at' => now(),
                'is_valid' => true,
                'revoked_at' => null
            ]
        );

        // 8. Update Request Status to READY via StatusService if not already
        if (!in_array($request->status, ['READY', 'ISSUED'])) {
            $this->statusService->transition($request, 'READY', 'تم إنشاء المستند بنجاح.', $adminId);
        }

        return $issuedDoc;
    }

    /**
     * Initiate a draft document for signature workflow.
     * Creates the IssuedDocument record without generating the PDF.
     */
    public function initiateDraft(DocumentRequest $request, int $adminId): IssuedDocument
    {
        if ($request->status !== 'APPROVED') {
            throw new Exception("Document must be in APPROVED status to start signing workflow.");
        }

        if ($request->documentType->payment_required && $request->payment_status !== 'approved') {
            throw new Exception(__('app.payment_must_be_approved'));
        }

        $existingDoc = $request->issuedDocument;

        if (!$existingDoc) {
            $year = now()->year;
            $prefix = "SRU-DOC-{$year}-";
            $lastDoc = IssuedDocument::where('serial_number', 'like', "{$prefix}%")
                ->orderBy('serial_number', 'desc')
                ->first();

            $number = $lastDoc ? ((int) substr($lastDoc->serial_number, -5)) + 1 : 1;
            $serialNumber = $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
            $qrToken = Str::random(64);

            $existingDoc = IssuedDocument::create([
                'document_request_id' => $request->id,
                'serial_number' => $serialNumber,
                'qr_token' => $qrToken,
                'pdf_path' => '',
                'issued_at' => null,
                'is_valid' => true,
            ]);
        }

        $this->statusService->transition($request, 'PENDING_SIGNATURES', 'تم إرسال الوثيقة للتوقيعات.', $adminId);

        app(DocumentSigningService::class)->notifyCurrentSigner($existingDoc);

        return $existingDoc;
    }

    /**
     * Finalize PDF generation after all signatures are collected.
     */
    public function finalizePdf(IssuedDocument $doc): void
    {
        $request = $doc->documentRequest;

        $pdfPath = "documents/{$doc->serial_number}.pdf";

        $verifyUrl = route('verify.show', ['token' => $doc->qr_token], true);
        $qrCodeData = QrCode::format('svg')->size(200)->margin(1)->generate($verifyUrl);
        $qrCodeBase64 = base64_encode($qrCodeData);

        $pdf = $this->renderPdf($request, $doc->serial_number, $qrCodeBase64, $doc->qr_token, $doc);

        if (!Storage::disk('local')->exists('documents')) {
            Storage::disk('local')->makeDirectory('documents');
        }

        if ($doc->pdf_path && Storage::disk('local')->exists($doc->pdf_path)) {
            Storage::disk('local')->delete($doc->pdf_path);
        }

        Storage::disk('local')->put($pdfPath, $pdf->output());

        $doc->update([
            'pdf_path' => $pdfPath,
            'issued_at' => now(),
        ]);
    }

    /**
     * Render the PDF from the Blade template.
     */
    private function renderPdf(DocumentRequest $request, string $serialNumber, string $qrCodeBase64, string $qrToken, ?IssuedDocument $issuedDoc = null): \Barryvdh\DomPDF\PDF
    {
        $templateMap = [
            'academic_record' => 'academic_record',
            'grades_certificate' => 'grades_certificate',
            'grade_certificate' => 'grades_certificate',
            'grades' => 'grades_certificate',
            'certificate_grades' => 'grades_certificate',
        ];

        $typeCode = strtolower($request->documentType->code);
        $typeCode = $templateMap[$typeCode] ?? $typeCode;

        $lang = strtolower($request->language);
        $specificTemplate = "pdf.documents.{$typeCode}.{$lang}";

        $academicRecord = $this->studentInfoProvider->getAcademicRecordWithDetails($request->user);

        if (!$academicRecord) {
            if ($request->documentType->code === 'ACADEMIC_RECORD') {
                throw new Exception(__('app.academic_record_missing'));
            } elseif ($request->documentType->code === 'GRADES_CERTIFICATE') {
                throw new Exception('لا يمكن إصدار شهادة الدرجات والتقديرات لأن السجل الأكاديمي غير مدخل لهذا الطالب.');
            } else {
                throw new Exception("بيانات السجل الأكاديمي غير متوفرة لهذا الطالب.");
            }
        }

        $template = view()->exists($specificTemplate) ? $specificTemplate : "pdf.documents.{$lang}";

        $signatures = $issuedDoc
            ? $issuedDoc->signatures()->with('user')->get()->map(function ($sig) {
                if ($sig->user && $sig->user->signature_image) {
                    $path = Storage::disk('public')->path($sig->user->signature_image);
                    if (file_exists($path)) {
                        $sig->user->signature_base64 = base64_encode(file_get_contents($path));
                    }
                }
                return $sig;
            })->keyBy('role_title')
            : collect();

        return Pdf::loadView($template, [
            'request' => $request->load(['user.graduate.major', 'documentType']),
            'serial_number' => $serialNumber,
            'qr_code' => $qrCodeBase64,
            'qr_token' => $qrToken,
            'issue_date' => now()->format('Y-m-d'),
            'academic_record' => $academicRecord,
            'signatures' => $signatures,
        ]);
    }

    /**
     * Revoke a document
     */
    public function revoke(IssuedDocument $doc, int $adminId, ?string $reason): void
    {
        $doc->update([
            'is_valid' => false,
            'revoked_at' => now()
        ]);

        // Optionally update the request status or log the revocation
    }

    /**
     * Reset all signatures and prepare the document for re-signing.
     * Preserves serial_number, qr_token, and is_valid.
     * Deletes old PDF so it gets regenerated after new signatures complete.
     */
    public function resetForReissue(IssuedDocument $doc): void
    {
        $doc->signatures()->delete();

        $doc->update(['all_signed_at' => null]);

        if ($doc->pdf_path && Storage::disk('local')->exists($doc->pdf_path)) {
            Storage::disk('local')->delete($doc->pdf_path);
        }
    }
}
