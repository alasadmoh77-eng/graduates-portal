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

    public function __construct(RequestStatusService $statusService)
    {
        $this->statusService = $statusService;
    }

    /**
     * Issue a document
     */
    public function issue(DocumentRequest $request, int $adminId): IssuedDocument
    {
        // 1. Validate request status
        if (!in_array($request->status, ['APPROVED', 'READY'])) {
            throw new Exception("Document can only be issued if status is APPROVED or READY.");
        }

        // 2. Generate Serial Number
        $year = now()->year;
        $prefix = "SRU-DOC-{$year}-";
        $lastDoc = IssuedDocument::where('serial_number', 'like', "{$prefix}%")
            ->orderBy('serial_number', 'desc')
            ->first();

        $number = $lastDoc ? ((int) substr($lastDoc->serial_number, -5)) + 1 : 1;
        $serialNumber = $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);

        // 3. Generate Secure QR Token
        $qrToken = Str::random(64);

        // 4. Prepare Storage Path
        $pdfFileName = "{$serialNumber}.pdf";
        $pdfPath = "documents/{$pdfFileName}";

        // 5. Generate QR Code Image (SVG Base64)
        $verifyUrl = route('verify.show', ['token' => $qrToken]);
        $qrCodeData = QrCode::format('svg')->size(200)->margin(1)->generate($verifyUrl);
        $qrCodeBase64 = base64_encode($qrCodeData);

        // 6. Render PDF
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

        $academicRecord = null;
        if (in_array($request->documentType->code, ['ACADEMIC_RECORD', 'GRADES_CERTIFICATE'])) {
            $academicRecord = GraduateAcademicRecord::query()
                ->where('user_id', $request->user_id)
                ->with(['levels.semesters.subjects'])
                ->first();
            if (! $academicRecord) {
                throw new Exception(__('app.academic_record_missing'));
            }
        }

        $template = view()->exists($specificTemplate) ? $specificTemplate : "pdf.documents.{$lang}";
        $pdf = Pdf::loadView($template, [
            'request' => $request->load(['user.graduate.major', 'documentType']),
            'serial_number' => $serialNumber,
            'qr_code' => $qrCodeBase64,
            'qr_token' => $qrToken,
            'issue_date' => now()->format('Y-m-d'),
            'academic_record' => $academicRecord,
        ]);

        // 7. Save to Storage
        if (!Storage::disk('public')->exists('documents')) {
            Storage::disk('public')->makeDirectory('documents');
        }
        Storage::disk('public')->put($pdfPath, $pdf->output());

        // 8. Create/Update IssuedDocument record
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

        // 9. Update Request Status to READY via StatusService if not already
        if ($request->status !== 'READY') {
            $this->statusService->transition($request, 'READY', 'تم إنشاء المستند بنجاح.', $adminId);
        }

        return $issuedDoc;
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
}
