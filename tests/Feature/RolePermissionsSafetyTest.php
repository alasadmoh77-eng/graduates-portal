<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Graduate;
use App\Models\Major;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Job;
use App\Models\Employer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionsSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected $academicAdmin;
    protected $financeAdmin;
    protected $employmentOfficer;
    protected $graduateUser;
    protected $major;
    protected $targetGraduate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->major = Major::create(['name_ar' => 'علوم الحاسوب', 'name_en' => 'Computer Science']);

        // Academic Admin
        $this->academicAdmin = User::create([
            'name' => 'Academic Admin',
            'email' => 'academic@example.com',
            'password' => bcrypt('password123'),
            'role' => 'academic_admin',
            'is_active' => true,
        ]);

        // Finance Admin
        $this->financeAdmin = User::create([
            'name' => 'Finance Admin',
            'email' => 'finance@example.com',
            'password' => bcrypt('password123'),
            'role' => 'finance_admin',
            'is_active' => true,
        ]);

        // Employment Officer
        $this->employmentOfficer = User::create([
            'name' => 'Employment Officer',
            'email' => 'employment@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employment_officer',
            'is_active' => true,
        ]);

        // Graduate Owner / Normal User
        $this->graduateUser = User::create([
            'name' => 'Normal Graduate',
            'email' => 'graduate@example.com',
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

        // Target Graduate for Admin updates
        $this->targetGraduate = User::create([
            'name' => 'Target Graduate',
            'email' => 'target@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);
        Graduate::create([
            'user_id' => $this->targetGraduate->id,
            'university_id' => '2023002',
            'major_id' => $this->major->id,
            'graduation_year' => 2023,
        ]);
    }

    /**
     * اختبار: المسؤول الأكاديمي يستطيع حفظ/تعديل السجل الأكاديمي.
     */
    public function test_academic_admin_can_update_academic_record()
    {
        $response = $this->actingAs($this->academicAdmin)
            ->put(route('admin.graduates.academic-record.update', $this->targetGraduate), [
                'student' => [
                    'name' => 'Target Graduate Updated',
                    'id' => '2023002',
                    'degree' => 'Bachelor',
                ],
                'levels' => [
                    [
                        'name' => 'المستوى الأول',
                        'year' => '2020',
                        'avg' => 85.5,
                        'result' => 'ناجح',
                        'semesters' => [
                            ['subjects' => []],
                            ['subjects' => []],
                        ],
                    ]
                ]
            ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    /**
     * اختبار: المسؤول المالي يستطيع مراجعة الدفع (الموافقة).
     */
    public function test_finance_admin_can_approve_payment()
    {
        $docType = DocumentType::create([
            'code' => 'ACADEMIC_RECORD',
            'name_ar' => 'سجل',
            'name_en' => 'Record',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_required' => true,
        ]);

        $request = DocumentRequest::create([
            'user_id' => $this->targetGraduate->id,
            'document_type_id' => $docType->id,
            'tracking_code' => 'DOC-PAY-TEST',
            'status' => 'SUBMITTED',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'pending_review',
            'payment_proof_path' => 'payment-proofs/proof.jpg',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        $response = $this->actingAs($this->financeAdmin)
            ->post(route('admin.payments.approve', $request));

        $response->assertRedirect();
        $this->assertEquals('approved', $request->refresh()->payment_status);
    }

    /**
     * اختبار: مسؤول التوظيف يستطيع مراجعة وقبول الوظائف.
     */
    public function test_employment_officer_can_approve_jobs()
    {
        $employerUser = User::create([
            'name' => 'Test Employer',
            'email' => 'employer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employer',
            'is_active' => true,
        ]);
        Employer::create([
            'user_id' => $employerUser->id,
            'company_name' => 'Test Company',
            'status' => 'approved',
        ]);

        $job = Job::create([
            'employer_id' => $employerUser->id,
            'title' => 'Pending Job Role',
            'description' => 'Description here.',
            'deadline' => now()->addDays(5),
            'location' => 'Marib',
            'job_type' => 'Full-time',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->employmentOfficer)
            ->post(route('admin.employment.jobs.approve', $job));

        $response->assertRedirect();
        $this->assertEquals('active', $job->refresh()->status);
    }

    /**
     * اختبار: خريج عادي لا يستطيع تنفيذ عمليات إدارية ويحصل على 403.
     */
    public function test_normal_graduate_cannot_access_administrative_routes()
    {
        // 1. محاولة تعديل السجل الأكاديمي
        $response1 = $this->actingAs($this->graduateUser)
            ->put(route('admin.graduates.academic-record.update', $this->targetGraduate), [
                'student' => ['name' => 'Hacker Name'],
                'levels' => []
            ]);
        $response1->assertStatus(403);

        // 2. محاولة الموافقة على الدفع
        $docType = DocumentType::create([
            'code' => 'ACADEMIC_RECORD',
            'name_ar' => 'سجل',
            'name_en' => 'Record',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_required' => true,
        ]);
        $request = DocumentRequest::create([
            'user_id' => $this->targetGraduate->id,
            'document_type_id' => $docType->id,
            'tracking_code' => 'DOC-PAY-TEST2',
            'status' => 'SUBMITTED',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'pending_review',
            'payment_proof_path' => 'payment-proofs/proof.jpg',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);
        $response2 = $this->actingAs($this->graduateUser)
            ->post(route('admin.payments.approve', $request));
        $response2->assertStatus(403);

        // 3. محاولة قبول وظيفة معلقة
        $employerUser = User::create([
            'name' => 'Test Employer',
            'email' => 'employer2@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employer',
            'is_active' => true,
        ]);
        Employer::create([
            'user_id' => $employerUser->id,
            'company_name' => 'Test Company',
            'status' => 'approved',
        ]);
        $job = Job::create([
            'employer_id' => $employerUser->id,
            'title' => 'Pending Job Role 2',
            'description' => 'Description here.',
            'deadline' => now()->addDays(5),
            'location' => 'Marib',
            'job_type' => 'Full-time',
            'status' => 'pending',
        ]);
        $response3 = $this->actingAs($this->graduateUser)
            ->post(route('admin.employment.jobs.approve', $job));
        $response3->assertStatus(403);
    }
}
