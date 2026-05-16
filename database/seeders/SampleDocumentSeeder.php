<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\DocumentRequest;
use App\Models\IssuedDocument;
use App\Models\User;
use App\Models\DocumentType;
use App\Services\TrackingCodeService;

class SampleDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $graduate = User::where('email', 'graduate@example.com')->first();
        if (!$graduate) return;

        $type = DocumentType::first();
        $trackingService = new TrackingCodeService();

        $request = DocumentRequest::create([
            'user_id' => $graduate->id,
            'document_type_id' => $type->id,
            'tracking_code' => $trackingService->generateNextCode(),
            'language' => 'AR',
            'purpose' => 'Test Verification',
            'delivery_type' => 'DIGITAL_PDF',
            'status' => 'READY',
        ]);

        IssuedDocument::create([
            'document_request_id' => $request->id,
            'serial_number' => 'SRU-DOC-2023-00001',
            'qr_token' => 'SAMPLE-TOKEN-123',
            'issued_at' => now(),
            'pdf_path' => 'documents/SRU-DOC-2023-00001.pdf',
            'is_valid' => true,
        ]);
    }
}
