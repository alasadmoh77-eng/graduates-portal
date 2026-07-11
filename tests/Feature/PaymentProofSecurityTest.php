<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Major;
use App\Models\Graduate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentProofSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected $graduateUser;
    protected $anotherGraduate;
    protected $financeAdmin;
    protected $documentType;
    protected $documentRequest;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        // Create a Major
        $major = Major::create(['name_ar' => 'علوم الحاسوب', 'name_en' => 'Computer Science']);

        // Create Graduate Owner
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
            'major_id' => $major->id,
            'graduation_year' => 2023,
        ]);

        // Create Another Graduate
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
            'major_id' => $major->id,
            'graduation_year' => 2023,
        ]);

        // Create Finance Admin
        $this->financeAdmin = User::create([
            'name' => 'Finance Admin',
            'email' => 'finance@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
            'admin_role' => 'finance_admin', // Assuming role check matches finance
        ]);

        // Create Document Type with payment required
        $this->documentType = DocumentType::create([
            'code' => 'ACADEMIC_RECORD',
            'name_ar' => 'سجل أكاديمي',
            'name_en' => 'Academic Record',
            'fee_amount' => 100,
            'currency' => 'YER',
            'payment_required' => true,
        ]);
    }

    /**
     * اختبار: رفع إثبات دفع جديد وحفظه في المجلد الخاص (local) وليس العام (public).
     */
    public function test_graduate_can_upload_payment_proof_to_private_storage()
    {
        $record = \App\Models\GraduateAcademicRecord::create([
            'user_id' => $this->graduateUser->id,
            'student_name_ar' => 'Test Graduate',
        ]);
        $level = $record->levels()->create(['name' => 'الأول', 'sort_order' => 1]);
        $semester = $level->semesters()->create(['sort_order' => 1]);
        $semester->subjects()->create([
            'name' => 'مادة اختبارية',
            'hours' => 3,
            'score' => 95,
        ]);

        $file = UploadedFile::fake()->image('proof.jpg');

        $response = $this->actingAs($this->graduateUser)->post(route('graduate.documents.store'), [
            'document_type_id' => $this->documentType->id,
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'For employment',
            'payment_proof' => $file,
        ]);

        $response->assertRedirect(route('graduate.documents.index'));

        $documentRequest = DocumentRequest::first();
        $this->assertNotNull($documentRequest->payment_proof_path);

        // التأكد من حفظ الملف في القرص الخاص (local)
        Storage::disk('local')->assertExists($documentRequest->payment_proof_path);
        
        // التأكد من عدم حفظه في القرص العام (public)
        Storage::disk('public')->assertMissing($documentRequest->payment_proof_path);
    }

    /**
     * اختبار: الخريج صاحب الطلب يستطيع فتح وتحميل ملف إثبات الدفع الخاص به.
     */
    public function test_owner_graduate_can_view_own_payment_proof()
    {
        $path = 'payment-proofs/test_proof.jpg';
        Storage::disk('local')->put($path, 'fake content');

        $request = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-TEST-1',
            'status' => 'SUBMITTED',
            'fee_amount' => 100,
            'currency' => 'YER',
            'payment_status' => 'pending_review',
            'payment_proof_path' => $path,
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.documents.view-proof', $request));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    /**
     * اختبار: المسؤول المالي يستطيع فتح وتحميل إثبات الدفع.
     */
    public function test_finance_admin_can_view_payment_proof()
    {
        $path = 'payment-proofs/test_proof.jpg';
        Storage::disk('local')->put($path, 'fake content');

        $request = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-TEST-2',
            'status' => 'SUBMITTED',
            'fee_amount' => 100,
            'currency' => 'YER',
            'payment_status' => 'pending_review',
            'payment_proof_path' => $path,
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        $response = $this->actingAs($this->financeAdmin)
            ->get(route('admin.payments.proof', $request));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/jpeg');
    }

    /**
     * اختبار: خريج آخر يحاول الوصول لإثبات الدفع ويجب أن يمنع (403).
     */
    public function test_other_graduate_cannot_view_payment_proof()
    {
        $path = 'payment-proofs/test_proof.jpg';
        Storage::disk('local')->put($path, 'fake content');

        $request = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-TEST-3',
            'status' => 'SUBMITTED',
            'fee_amount' => 100,
            'currency' => 'YER',
            'payment_status' => 'pending_review',
            'payment_proof_path' => $path,
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        // محاولة خريج آخر فتح الإثبات من مسار الخريج
        $response1 = $this->actingAs($this->anotherGraduate)
            ->get(route('graduate.documents.view-proof', $request));
        $response1->assertStatus(403);

        // محاولة خريج آخر فتح الإثبات من مسار الموظف المالي (يجب منعه بواسطة الـ Middleware الخاص بالدور)
        $response2 = $this->actingAs($this->anotherGraduate)
            ->get(route('admin.payments.proof', $request));
        $response2->assertStatus(403);
    }

    /**
     * اختبار: متصفح خفي (غير مسجل دخول) يحاول الوصول للملفات ويجب أن يفشل.
     */
    public function test_guest_cannot_view_payment_proof()
    {
        $path = 'payment-proofs/test_proof.jpg';
        Storage::disk('local')->put($path, 'fake content');

        $request = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-TEST-4',
            'status' => 'SUBMITTED',
            'fee_amount' => 100,
            'currency' => 'YER',
            'payment_status' => 'pending_review',
            'payment_proof_path' => $path,
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        // محاولة ضيف فتح المسار الخاص بالخريج
        $response1 = $this->get(route('graduate.documents.view-proof', $request));
        $response1->assertRedirect(route('login'));

        // محاولة ضيف فتح المسار الخاص بالمسؤول المالي
        $response2 = $this->get(route('admin.payments.proof', $request));
        $response2->assertRedirect(route('login'));
    }
}
