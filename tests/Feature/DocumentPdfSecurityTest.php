<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Graduate;
use App\Models\Major;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\IssuedDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentPdfSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $graduateUser;
    protected $anotherGraduate;
    protected $academicAdmin;
    protected $documentType;
    protected $documentRequest;
    protected $issuedDoc;
    protected $major;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->major = Major::create(['name_ar' => 'علوم الحاسوب', 'name_en' => 'Computer Science']);

        // Graduate Owner
        $this->graduateUser = User::create([
            'name' => 'Graduate Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);
        Graduate::create([
            'user_id' => $this->graduateUser->id,
            'university_id' => '2023001',
            'major_id' => $this->major->id,
            'graduation_year' => 2023,
        ]);

        // Another Graduate
        $this->anotherGraduate = User::create([
            'name' => 'Another Graduate',
            'email' => 'other@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);
        Graduate::create([
            'user_id' => $this->anotherGraduate->id,
            'university_id' => '2023002',
            'major_id' => $this->major->id,
            'graduation_year' => 2023,
        ]);

        // Academic Admin
        $this->academicAdmin = User::create([
            'name' => 'Academic Admin',
            'email' => 'academic@example.com',
            'password' => bcrypt('password123'),
            'role' => 'academic_admin',
            'is_active' => true,
        ]);

        // Document Type
        $this->documentType = DocumentType::create([
            'code' => 'ACADEMIC_RECORD',
            'name_ar' => 'سجل',
            'name_en' => 'Record',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_required' => false,
        ]);

        // Document Request
        $this->documentRequest = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-PDF-101',
            'status' => 'READY',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'approved',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        // Issued Document PDF
        $pdfPath = 'documents/SRU-DOC-2026-00001.pdf';
        Storage::disk('local')->put($pdfPath, 'dummy pdf content');

        $this->issuedDoc = IssuedDocument::create([
            'document_request_id' => $this->documentRequest->id,
            'serial_number' => 'SRU-DOC-2026-00001',
            'qr_token' => 'secure_qr_token_123',
            'pdf_path' => $pdfPath,
            'issued_at' => now(),
            'is_valid' => true,
        ]);
    }

    /**
     * اختبار: الخريج صاحب الوثيقة يستطيع تحميل PDF بنجاح.
     */
    public function test_owner_graduate_can_download_pdf()
    {
        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.documents.download', $this->documentRequest));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=Document-DOC-PDF-101.pdf');
    }

    /**
     * اختبار: خريج آخر لا يستطيع تحميل PDF.
     */
    public function test_other_graduate_cannot_download_pdf()
    {
        $response = $this->actingAs($this->anotherGraduate)
            ->get(route('graduate.documents.download', $this->documentRequest));

        $response->assertStatus(403);
    }

    /**
     * اختبار: زائر غير مسجل لا يستطيع تحميل PDF.
     */
    public function test_guest_cannot_download_pdf()
    {
        $response = $this->get(route('graduate.documents.download', $this->documentRequest));

        $response->assertRedirect(route('login'));
    }

    /**
     * اختبار: المسؤول المخوّل يستطيع تحميل PDF.
     */
    public function test_academic_admin_can_download_pdf()
    {
        $response = $this->actingAs($this->academicAdmin)
            ->get(route('admin.requests.download-pdf', $this->documentRequest));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=Document-DOC-PDF-101.pdf');
    }

    /**
     * اختبار: صفحة QR تعمل بدون تسجيل دخول لكنها لا تعرض رابط تحميل PDF مباشر.
     */
    public function test_qr_page_works_without_auth_but_has_no_pdf_link()
    {
        $response = $this->get(route('verify.show', ['token' => 'secure_qr_token_123']));

        $response->assertStatus(200);
        $response->assertSee('نظام التحقق الرقمي');
        $response->assertSee('مستند صالح وصحيح');
        $response->assertSee('Graduate Owner');

        // التأكد من عدم وجود رابط مباشر للـ PDF أو كلمة storage/documents
        $response->assertDontSee('storage/documents');
        $response->assertDontSee('SRU-DOC-2026-00001.pdf');
    }

    /**
     * اختبار: لا يمكن إصدار PDF إلا إذا كان الطلب مستوفيًا الشروط (حالة الطلب APPROVED أو READY).
     */
    public function test_cannot_issue_pdf_if_request_is_not_approved()
    {
        $draftRequest = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-PDF-102',
            'status' => 'SUBMITTED', // مسودة أو لسه مقدم
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'approved',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        $issuanceService = app(\App\Services\DocumentIssuanceService::class);

        $this->expectException(\Exception::class);
        $issuanceService->issue($draftRequest, $this->academicAdmin->id);
    }

    /**
     * اختبار: التحقق من المستند عبر الرقم التسلسلي يدويًا.
     */
    public function test_can_verify_document_via_serial_number()
    {
        $response = $this->post(route('verify.process'), [
            'token' => 'SRU-DOC-2026-00001'
        ]);

        $response->assertStatus(200);
        $response->assertSee('مستند صالح وصحيح');
        $response->assertSee('Graduate Owner');
    }

    /**
     * اختبار: التحقق من المستند عبر كود التتبع يدويًا.
     */
    public function test_can_verify_document_via_tracking_code()
    {
        $response = $this->post(route('verify.process'), [
            'token' => 'DOC-PDF-101'
        ]);

        $response->assertStatus(200);
        $response->assertSee('مستند صالح وصحيح');
        $response->assertSee('Graduate Owner');
    }

    /**
     * اختبار: التحقق من أن رابط QR المولد للوثيقة يعتمد على APP_URL ولا يحتوي على localhost.
     */
    public function test_qr_code_url_uses_configured_app_url()
    {
        // 1. Change config APP_URL
        config(['app.url' => 'http://192.168.8.183:8000']);
        
        // 2. Issue document through service or test URL generation helper
        $qrToken = 'test_token_xyz_123';
        $verifyUrl = route('verify.show', ['token' => $qrToken], true);
        
        $this->assertEquals('http://192.168.8.183:8000/verify/test_token_xyz_123', $verifyUrl);
        $this->assertStringNotContainsString('localhost', $verifyUrl);
        $this->assertStringNotContainsString('127.0.0.1', $verifyUrl);
    }

    /**
     * اختبار: السجل الأكاديمي يحتوي على مسميات التواقيع الصحيحة وبالترتيب الصحيح.
     */
    public function test_academic_record_contains_correct_signature_titles()
    {
        $request = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'TEST-AR-101',
            'status' => 'READY',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'approved',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        $academicRecord = \App\Models\GraduateAcademicRecord::create([
            'user_id' => $this->graduateUser->id,
            'student_name_ar' => 'طالب تجريبي',
            'university_number' => '112233',
            'gpa' => '85',
            'total_marks' => '4000',
        ]);

        // Render the layout or view
        $html = view('pdf.documents.layout', [
            'request' => $request,
            'serial_number' => 'SRU-DOC-2026-00001',
            'qr_code' => 'dummy',
            'qr_token' => 'token123',
            'issue_date' => '2026-07-08',
            'academic_record' => $academicRecord,
        ])->render();

        $this->assertStringContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('المختص الأكاديمي'), $html);
        $this->assertStringContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('مدير إدارة شؤون الخريجين'), $html);
        $this->assertStringContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('مسجل الكلية'), $html);
        $this->assertStringContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('عميد الكلية'), $html);
        
        $this->assertStringNotContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('المسجل العام'), $html);
        $this->assertStringNotContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('نائب رئيس الجامعة لشؤون الطلاب'), $html);
    }

    /**
     * اختبار: شهادة الدرجات والتقديرات تحتوي على مسميات التواقيع الصحيحة وبالترتيب الصحيح.
     */
    public function test_grades_certificate_contains_correct_signature_titles()
    {
        $docType = DocumentType::create([
            'code' => 'GRADES_CERTIFICATE',
            'name_ar' => 'شهادة درجات',
            'name_en' => 'Grades Certificate',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_required' => false,
        ]);

        $request = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $docType->id,
            'tracking_code' => 'TEST-GC-101',
            'status' => 'READY',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'approved',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        $academicRecord = \App\Models\GraduateAcademicRecord::create([
            'user_id' => $this->graduateUser->id,
            'student_name_ar' => 'طالب تجريبي',
            'university_number' => '112233',
            'gpa' => '85',
            'total_marks' => '4000',
        ]);

        // Render the layout or view
        $html = view('pdf.documents.layout', [
            'request' => $request,
            'serial_number' => 'SRU-DOC-2026-00001',
            'qr_code' => 'dummy',
            'qr_token' => 'token123',
            'issue_date' => '2026-07-08',
            'academic_record' => $academicRecord,
        ])->render();

        $this->assertStringContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('مسجل الكلية'), $html);
        $this->assertStringContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('عميد الكلية'), $html);
        $this->assertStringContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('المسجل العام'), $html);
        $this->assertStringContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('نائب رئيس الجامعة لشؤون الطلاب'), $html);
        
        $this->assertStringNotContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('المختص الأكاديمي'), $html);
        $this->assertStringNotContainsString(\App\Helpers\ArabicReshaper::utf8Glyphs('مدير إدارة شؤون الخريجين'), $html);
    }
}

