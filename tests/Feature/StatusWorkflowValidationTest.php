<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Graduate;
use App\Models\Major;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Job;
use App\Models\Employer;
use App\Models\IssuedDocument;
use App\Services\RequestStatusService;
use App\Services\DocumentIssuanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusWorkflowValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $academicAdmin;
    protected $financeAdmin;
    protected $employmentOfficer;
    protected $graduateUser;
    protected $major;
    protected $documentType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->major = Major::create(['name_ar' => 'علوم الحاسوب', 'name_en' => 'Computer Science']);

        // Admin User
        $this->adminUser = User::create([
            'name' => 'General Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

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

        // Graduate
        $this->graduateUser = User::create([
            'name' => 'Graduate User',
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

        $this->documentType = DocumentType::create([
            'code' => 'ACADEMIC_RECORD',
            'name_ar' => 'سجل',
            'name_en' => 'Record',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_required' => true,
        ]);
    }

    /**
     * اختبار: لا يمكن تمرير status غير مسموح عند تحديث حالة الطلب.
     */
    public function test_cannot_update_document_request_status_to_invalid_value()
    {
        $request = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-TEST-101',
            'status' => 'SUBMITTED',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'pending_review',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        $response = $this->actingAs($this->academicAdmin)
            ->post(route('admin.requests.status', $request), [
                'status' => 'INVALID_STATUS', // قيمة غير مسموحة
                'note' => 'Some note',
            ]);

        $response->assertSessionHasErrors('status');
    }

    /**
     * اختبار: لا يمكن تمرير status غير مسموح عند إدارة الوظائف.
     */
    public function test_cannot_moderate_job_status_to_invalid_value()
    {
        $employerUser = User::create([
            'name' => 'Employer',
            'email' => 'employer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employer',
            'is_active' => true,
        ]);
        Employer::create(['user_id' => $employerUser->id, 'company_name' => 'Company', 'status' => 'approved']);

        $job = Job::create([
            'employer_id' => $employerUser->id,
            'title' => 'Job Title',
            'description' => 'Desc.',
            'deadline' => now()->addDays(5),
            'location' => 'Marib',
            'job_type' => 'Full-time',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.jobs.moderate', $job), [
                'status' => 'EXPIRED', // قيمة غير مسموحة
            ]);

        $response->assertSessionHasErrors('status');
    }

    /**
     * اختبار: إحصائيات لوحة التحكم تعرض أرقامًا صحيحة.
     */
    public function test_admin_dashboard_statistics_are_correct()
    {
        // إنشاء طلب بانتظار الدفع (payment_status = pending_review)
        DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-TEST-102',
            'status' => 'SUBMITTED',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'pending_review',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        // طلب آخر مكتمل الدفع ومسجل كـ approved
        DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-TEST-103',
            'status' => 'UNDER_REVIEW',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'approved',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        // إنشاء خريج معتمد للتحقق من إجمالي الخريجين المعتمدين
        \App\Models\ApprovedGraduate::create([
            'university_id' => '112233',
            'name' => 'Approved Graduate 1',
            'email' => 'approved1@example.com',
            'college' => 'Engineering',
            'major' => 'CS',
            'graduation_year' => 2024
        ]);

        $req103 = DocumentRequest::where('tracking_code', 'DOC-TEST-103')->first();
        \App\Models\IssuedDocument::create([
            'document_request_id' => $req103->id,
            'serial_number' => 'SR-TEST-99999',
            'qr_token' => 'QR-TOKEN-999',
            'pdf_path' => 'test2.pdf',
            'issued_at' => now(),
            'is_valid' => true,
        ]);

        // إنشاء كلية وتخصص للتحقق من الإحصائيات
        $faculty = \App\Models\Faculty::create([
            'name_ar' => 'كلية الهندسة',
            'name_en' => 'Faculty of Engineering',
            'status' => 'active'
        ]);
        \App\Models\Major::create([
            'name_ar' => 'هندسة البرمجيات',
            'name_en' => 'Software Engineering',
            'faculty_id' => $faculty->id,
            'degree_name_ar' => 'بكالوريوس',
            'degree_name_en' => 'Bachelor'
        ]);

        // إنشاء جهة عمل ووظائف للتحقق من إحصائيات التوظيف
        $employerUser = User::create([
            'name' => 'Dashboard Employer',
            'email' => 'dash_employer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employer',
            'is_active' => true,
        ]);
        \App\Models\Employer::create([
            'user_id' => $employerUser->id,
            'company_name' => 'Dashboard Employer Co',
            'status' => 'approved'
        ]);

        \App\Models\Job::create([
            'employer_id' => $employerUser->id,
            'title' => 'Dashboard active job',
            'description' => 'Desc',
            'deadline' => now()->addDays(10),
            'location' => 'Marib',
            'job_type' => 'Full-time',
            'status' => 'active',
        ]);

        \App\Models\Job::create([
            'employer_id' => $employerUser->id,
            'title' => 'Dashboard pending job',
            'description' => 'Desc',
            'deadline' => now()->addDays(10),
            'location' => 'Marib',
            'job_type' => 'Full-time',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);

        // التحقق من الإحصائيات الممررة إلى view
        $stats = $response->viewData('stats');
        $this->assertEquals(1, $stats['total_pending_payments']); // السجل الأول فقط pending_review
        $this->assertEquals(2, $stats['pending_requests']); // كلاهما SUBMITTED أو UNDER_REVIEW
        $this->assertEquals(\App\Models\ApprovedGraduate::count(), $stats['total_approved_graduates']);
        $this->assertEquals(\App\Models\User::where('role', 'graduate')->count(), $stats['total_graduates']);
        $this->assertEquals(\App\Models\DocumentRequest::count(), $stats['total_document_requests']);
        $this->assertEquals(\App\Models\IssuedDocument::count(), $stats['total_issued_documents']);
        $this->assertEquals(\Illuminate\Support\Facades\DB::table('approved_graduates')->whereNotNull('college')->distinct()->count('college'), $stats['total_faculties']);
        $this->assertEquals(\Illuminate\Support\Facades\DB::table('approved_graduates')->distinct()->count('major'), $stats['total_majors']);
        $this->assertEquals(\App\Models\Employer::count(), $stats['total_employers']);
        $this->assertEquals(\App\Models\Job::count(), $stats['total_jobs']);
        $this->assertEquals(\App\Models\Job::where('status', 'active')->count(), $stats['total_active_jobs']);

        // التحقق من بيانات الرسوم البيانية لتوزيع الحالات وأكثر الوثائق طلباً
        $topTypes = $response->viewData('topTypes');
        $statusBreakdown = $response->viewData('statusBreakdown');

        $this->assertNotEmpty($topTypes);
        $this->assertNotEmpty($statusBreakdown);
        $this->assertEquals(2, $topTypes->sum('value'));
        $this->assertEquals(2, $statusBreakdown->sum('value'));

        $months = $response->viewData('months');
        $requestCounts = $response->viewData('requestCounts');
        $majorStats = $response->viewData('majorStats');

        $this->assertCount(6, $months);
        $this->assertCount(6, $requestCounts);
        $this->assertEquals(2, array_sum($requestCounts));
        $this->assertNotEmpty($majorStats);
        $this->assertEquals(2, $majorStats->sum('value'));
    }

    /**
     * اختبار: مسار طلب الوثيقة ينتقل بين الحالات الصحيحة فقط.
     */
    public function test_document_request_transitions_properly()
    {
        $request = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-TEST-104',
            'status' => 'SUBMITTED',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'approved', // Approved to allow later stages
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        $statusService = app(RequestStatusService::class);

        // انتقال غير صالح: من SUBMITTED مباشرة إلى APPROVED (يجب أن يمر بـ UNDER_REVIEW)
        $this->expectException(\Exception::class);
        $statusService->transition($request, 'APPROVED', 'Approved Directly', $this->academicAdmin->id);
    }

    /**
     * اختبار: لا يتم إصدار وثيقة إلا بعد اكتمال الشروط المطلوبة (الدفع أولاً).
     */
    public function test_document_cannot_be_issued_without_approved_payment()
    {
        $request = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $this->documentType->id,
            'tracking_code' => 'DOC-TEST-105',
            'status' => 'APPROVED',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'pending_review', // الدفع لم يعتمد بعد
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        // إدخال السجل الأكاديمي للخريج حتى لا يفشل بسبب نقص البيانات الأكاديمية
        \App\Models\GraduateAcademicRecord::create([
            'user_id' => $this->graduateUser->id,
        ]);

        $issuanceService = app(DocumentIssuanceService::class);

        // يجب أن يرمي استثناءً لأن حالة الدفع ليست approved
        $this->expectException(\Exception::class);
        $issuanceService->issue($request, $this->academicAdmin->id);
    }

    public function test_public_stats_service_queries_correct_numbers()
    {
        // Setup initial counts
        $initialGrads = \App\Models\ApprovedGraduate::count();
        $initialEmployers = \App\Models\Employer::count();
        $initialJobs = \App\Models\Job::count();
        $initialIssuedDocs = \App\Models\IssuedDocument::count();

        // Add 1 of each
        \App\Models\ApprovedGraduate::create([
            'university_id' => '999999',
            'name' => 'Test approved graduate',
            'email' => 'testapproved@example.com',
            'college' => 'Science',
            'major' => 'CS',
            'graduation_year' => 2026
        ]);

        $employerUser = User::create([
            'name' => 'New Employer',
            'email' => 'newemployer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employer',
            'is_active' => true,
        ]);
        \App\Models\Employer::create([
            'user_id' => $employerUser->id,
            'company_name' => 'New Employer Co',
            'status' => 'approved'
        ]);

        \App\Models\Job::create([
            'employer_id' => $employerUser->id,
            'title' => 'New job opportunity',
            'description' => 'Desc',
            'deadline' => now()->addDays(10),
            'location' => 'Marib',
            'job_type' => 'Full-time',
            'status' => 'active',
        ]);

        $type = DocumentType::create([
            'code' => 'TEST_DOC',
            'name_ar' => 'Test document',
            'name_en' => 'Test document',
            'fee_mock' => 10,
            'eta_days' => 1
        ]);
        $request = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $type->id,
            'tracking_code' => 'DOC-TEST-106',
            'status' => 'ISSUED',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'approved',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);
        \App\Models\IssuedDocument::create([
            'document_request_id' => $request->id,
            'serial_number' => 'SR-TEST-12345',
            'qr_token' => 'QR-TOKEN-123',
            'pdf_path' => 'test.pdf',
            'issued_at' => now(),
            'is_valid' => true,
        ]);

        $service = app(\App\Services\PublicPortalStatsService::class);
        $stats = $service->getStats();

        $this->assertEquals($initialGrads + 1, $stats['approved_graduates_count']);
        $this->assertEquals($initialEmployers + 1, $stats['employers_count']);
        $this->assertEquals($initialJobs + 1, $stats['jobs_count']);
        $this->assertEquals($initialIssuedDocs + 1, $stats['issued_documents_count']);
    }

    public function test_homepage_has_dynamic_stats_and_no_plus_sign()
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        // Verify it passes the $publicStats array to the view
        $publicStats = $response->viewData('publicStats');
        $this->assertNotNull($publicStats);
        $this->assertArrayHasKey('approved_graduates_count', $publicStats);
        $this->assertArrayHasKey('issued_documents_count', $publicStats);
        $this->assertArrayHasKey('employers_count', $publicStats);
        $this->assertArrayHasKey('jobs_count', $publicStats);
        
        // Assert we do not have '+5,400' or similar static strings on the homepage anymore
        $content = $response->getContent();
        $this->assertStringNotContainsString('5,400+', $content);
        $this->assertStringNotContainsString('12,600+', $content);
        $this->assertStringNotContainsString('320+', $content);
        $this->assertStringNotContainsString('850+', $content);
    }

    public function test_date_inputs_are_text_type_and_have_placeholders()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.requests.index'));
        $response->assertStatus(200);

        $content = $response->getContent();
        
        // Assert date_from and date_to do not use type="date"
        $this->assertStringNotContainsString('type="date" name="date_from"', $content);
        $this->assertStringNotContainsString('type="date" name="date_to"', $content);

        // Assert they use date-picker-input class and readonly
        $this->assertStringContainsString('class="form-control custom-input date-picker-input"', $content);
        $this->assertStringContainsString('readonly', $content);

        // Assert they are text and have placeholder YYYY-MM-DD and dir ltr
        $this->assertStringContainsString('name="date_from"', $content);
        $this->assertStringContainsString('placeholder="YYYY-MM-DD"', $content);
        $this->assertStringContainsString('dir="ltr"', $content);
    }
}
