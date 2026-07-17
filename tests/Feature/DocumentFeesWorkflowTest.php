<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\DocumentType;
use App\Models\DocumentRequest;
use App\Models\Graduate;
use App\Models\Faculty;
use App\Models\Major;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RequestUnderReview;
use Tests\TestCase;

class DocumentFeesWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $academicAdmin;
    protected $financeAdmin;
    protected $graduateUser;
    protected $graduateProfile;
    protected $academicRecordType;
    protected $gradesCertType;
    protected $faculty;
    protected $major;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create academic admin
        $this->academicAdmin = User::create([
            'name' => 'Academic Admin',
            'email' => 'academic@example.com',
            'password' => bcrypt('password123'),
            'role' => 'academic_admin',
            'is_active' => true,
        ]);

        // 2. Create finance admin
        $this->financeAdmin = User::create([
            'name' => 'Finance Admin',
            'email' => 'finance@example.com',
            'password' => bcrypt('password123'),
            'role' => 'finance_admin',
            'is_active' => true,
        ]);

        // 3. Create graduate user
        $this->graduateUser = User::create([
            'name' => 'Graduate User',
            'email' => 'graduate@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);

        // 4. Create faculty & major
        $this->faculty = Faculty::create([
            'name_ar' => 'كلية الهندسة',
            'name_en' => 'Faculty of Engineering',
        ]);

        $this->major = Major::create([
            'faculty_id' => $this->faculty->id,
            'name_ar' => 'هندسة حاسوب',
            'name_en' => 'Computer Engineering',
        ]);

        // 5. Create graduate profile (matching the database constraints)
        $this->graduateProfile = Graduate::create([
            'user_id' => $this->graduateUser->id,
            'university_id' => '123456',
            'major_id' => $this->major->id,
            'phone' => '777777777',
            'graduation_year' => 2025,
        ]);

        // Create Academic Record Excel Registry for the student
        \App\Models\ApprovedGraduate::create([
            'university_id' => '123456',
            'name' => 'Graduate User',
            'email' => 'graduate@example.com',
            'college' => 'Engineering',
            'major' => 'CS',
            'graduation_year' => 2025,
            'academic_record_text' => 'Some courses and grades text details'
        ]);

        // Populate Database Driver Academic Record details so hasAcademicRecord returns true
        $record = \App\Models\GraduateAcademicRecord::create([
            'user_id' => $this->graduateUser->id,
            'student_name_ar' => 'Graduate User',
            'university_number' => '123456',
        ]);
        $level = \App\Models\GraduateAcademicLevel::create([
            'graduate_academic_record_id' => $record->id,
            'name' => 'المستوى الأول',
            'sort_order' => 1,
        ]);
        $semester = \App\Models\GraduateAcademicSemester::create([
            'graduate_academic_level_id' => $level->id,
            'sort_order' => 1,
        ]);
        \App\Models\GraduateAcademicSubject::create([
            'graduate_academic_semester_id' => $semester->id,
            'sort_order' => 1,
            'catalog_key' => 'math101',
            'name' => 'رياضيات',
            'credit_hours' => 3,
            'score' => 90,
            'rating' => 'A',
        ]);

        // 6. Create document types
        $this->academicRecordType = DocumentType::create([
            'name_ar' => 'السجل الأكاديمي',
            'name_en' => 'Academic Record',
            'code' => 'ACADEMIC_RECORD',
            'fee_amount' => 5000,
            'currency' => 'YER',
            'payment_required' => true,
        ]);

        $this->gradesCertType = DocumentType::create([
            'name_ar' => 'شهادة التقديرات',
            'name_en' => 'Grades Certificate',
            'code' => 'GRADES_CERTIFICATE',
            'fee_amount' => 3000,
            'currency' => 'YER',
            'payment_required' => true,
        ]);
    }

    /** @test */
    public function academic_admin_can_access_fees_management_page()
    {
        $response = $this->actingAs($this->academicAdmin)
            ->get(route('admin.document-fees.index'));

        $response->assertStatus(200);
        $response->assertSee('إدارة رسوم الوثائق');
    }

    /** @test */
    public function finance_admin_can_access_fees_management_page()
    {
        $response = $this->actingAs($this->financeAdmin)
            ->get(route('admin.document-fees.index'));

        $response->assertStatus(200);
        $response->assertSee('إدارة رسوم الوثائق');
    }

    /** @test */
    public function graduate_cannot_access_fees_management_page()
    {
        $response = $this->actingAs($this->graduateUser)
            ->get(route('admin.document-fees.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function academic_admin_can_update_document_fees()
    {
        // 1. Change Grades Certificate to free
        $response = $this->actingAs($this->academicAdmin)
            ->post(route('admin.document-fees.update', $this->gradesCertType->id), [
                'payment_required' => 0,
                'fee_amount' => 0,
            ]);

        $response->assertRedirect(route('admin.document-fees.index'));
        $this->gradesCertType->refresh();
        $this->assertFalse($this->gradesCertType->payment_required);
        $this->assertEquals(0, (int)$this->gradesCertType->fee_amount);

        // 2. Change Academic Record fee to 6000 YER
        $response = $this->actingAs($this->academicAdmin)
            ->post(route('admin.document-fees.update', $this->academicRecordType->id), [
                'payment_required' => 1,
                'fee_amount' => 6000,
            ]);

        $response->assertRedirect(route('admin.document-fees.index'));
        $this->academicRecordType->refresh();
        $this->assertTrue($this->academicRecordType->payment_required);
        $this->assertEquals(6000, (int)$this->academicRecordType->fee_amount);
    }

    /** @test */
    public function finance_admin_can_update_document_fees()
    {
        // 1. Change Grades Certificate to free
        $response = $this->actingAs($this->financeAdmin)
            ->post(route('admin.document-fees.update', $this->gradesCertType->id), [
                'payment_required' => 0,
                'fee_amount' => 0,
            ]);

        $response->assertRedirect(route('admin.document-fees.index'));
        $this->gradesCertType->refresh();
        $this->assertFalse($this->gradesCertType->payment_required);
        $this->assertEquals(0, (int)$this->gradesCertType->fee_amount);
    }

    /** @test */
    public function validation_blocks_negative_fees_and_invalid_payment_status()
    {
        // Negative amount
        $response = $this->actingAs($this->academicAdmin)
            ->post(route('admin.document-fees.update', $this->academicRecordType->id), [
                'payment_required' => 1,
                'fee_amount' => -100,
            ]);

        $response->assertSessionHasErrors(['fee_amount']);

        // Paid document cannot have 0 fee amount
        $response = $this->actingAs($this->academicAdmin)
            ->post(route('admin.document-fees.update', $this->academicRecordType->id), [
                'payment_required' => 1,
                'fee_amount' => 0,
            ]);

        $response->assertSessionHas('error');
    }

    /** @test */
    public function free_requests_bypass_payment_verification_and_transition_to_under_review_immediately()
    {
        Notification::fake();

        // 1. Make Grades Certificate free
        $this->gradesCertType->update([
            'payment_required' => false,
            'fee_amount' => 0,
        ]);

        // 2. Submit free request (should not require payment proof)
        $response = $this->actingAs($this->graduateUser)
            ->post(route('graduate.documents.store'), [
                'document_type_id' => $this->gradesCertType->id,
                'language' => 'AR',
                'purpose' => 'للعمل',
                'delivery_type' => 'PICKUP',
            ]);

        $response->assertRedirect(route('graduate.documents.index'));

        // 3. Verify database state
        $request = DocumentRequest::first();
        $this->assertNotNull($request);
        $this->assertEquals('UNDER_REVIEW', $request->status);
        $this->assertEquals('not_required', $request->payment_status);
        $this->assertFalse($request->payment_required);
        $this->assertEquals(0, (int)$request->fee_amount);

        // 4. Verify Academic Admin notified
        Notification::assertSentTo(
            [$this->academicAdmin],
            RequestUnderReview::class
        );
    }

    /** @test */
    public function paid_requests_require_payment_proof_and_stay_submitted_until_approved()
    {
        Notification::fake();

        // 1. Submit request without payment proof (should fail validation)
        $response = $this->actingAs($this->graduateUser)
            ->post(route('graduate.documents.store'), [
                'document_type_id' => $this->academicRecordType->id,
                'language' => 'AR',
                'purpose' => 'للعمل',
                'delivery_type' => 'PICKUP',
            ]);

        $response->assertSessionHasErrors(['payment_proof']);

        // 2. Submit request with payment proof
        $file = UploadedFile::fake()->create('proof.png', 100);
        $response = $this->actingAs($this->graduateUser)
            ->post(route('graduate.documents.store'), [
                'document_type_id' => $this->academicRecordType->id,
                'language' => 'AR',
                'purpose' => 'للعمل',
                'delivery_type' => 'PICKUP',
                'payment_proof' => $file,
            ]);

        $response->assertRedirect(route('graduate.documents.index'));

        // 3. Verify database state (should be SUBMITTED)
        $request = DocumentRequest::first();
        $this->assertNotNull($request);
        $this->assertEquals('SUBMITTED', $request->status);
        $this->assertEquals('pending_review', $request->payment_status);
        $this->assertTrue($request->payment_required);
        $this->assertEquals(5000, (int)$request->fee_amount);

        // 4. Verify Academic Admin was NOT notified (still in payment approval stage)
        Notification::assertNotSentTo(
            [$this->academicAdmin],
            RequestUnderReview::class
        );
    }

    /** @test */
    public function request_preserves_original_fee_snapshot_when_document_type_fees_change()
    {
        Notification::fake();

        // 1. Submit free request (snapshot: payment_required = false, fee = 0)
        $this->gradesCertType->update([
            'payment_required' => false,
            'fee_amount' => 0,
        ]);

        $this->actingAs($this->graduateUser)
            ->post(route('graduate.documents.store'), [
                'document_type_id' => $this->gradesCertType->id,
                'language' => 'AR',
                'purpose' => 'للعمل',
                'delivery_type' => 'PICKUP',
            ]);

        $request = DocumentRequest::first();
        $this->assertNotNull($request);
        $this->assertFalse($request->payment_required);
        $this->assertEquals(0, (int)$request->fee_amount);

        // 2. Change document type fees to 4000 YER, paid
        $this->gradesCertType->update([
            'payment_required' => true,
            'fee_amount' => 4000,
        ]);

        // 3. Refresh and check that existing request still preserves original snapshot
        $request->refresh();
        $this->assertFalse($request->payment_required);
        $this->assertEquals(0, (int)$request->fee_amount);
    }
}
