<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Major;
use App\Models\Graduate;
use App\Models\GraduateAcademicRecord;
use App\Models\GradesCertificate;
use App\Models\DocumentType;
use App\Models\DocumentRequest;
use App\Services\DocumentIssuanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradesCertificateSeparationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $graduateUser;
    protected Major $major;
    protected Graduate $graduate;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->major = Major::create(['name_ar' => 'علوم الحاسوب', 'name_en' => 'Computer Science']);
        $this->graduateUser = User::factory()->create(['role' => 'graduate']);
        $this->graduate = Graduate::create([
            'user_id' => $this->graduateUser->id,
            'university_id' => '2023001',
            'major_id' => $this->major->id,
            'graduation_year' => 2023,
        ]);
    }

    /**
     * Test accessing the edit pages
     */
    public function test_admin_can_access_edit_pages_separately()
    {
        $response1 = $this->actingAs($this->admin)
            ->get(route('admin.graduates.academic-record.edit', $this->graduateUser));
        $response1->assertStatus(200);
        $response1->assertSee('سجل الطالب الأكاديمي');

        $response2 = $this->actingAs($this->admin)
            ->get(route('admin.graduates.grades-certificate.edit', $this->graduateUser));
        $response2->assertStatus(200);
        $response2->assertSee('شهادة الدرجات والتقديرات');
    }

    /**
     * Test updating Academic Record and verifying auto-calculation of result
     */
    public function test_academic_record_update_calculates_result_automatically()
    {
        $payload = [
            'student' => [
                'name' => 'Graduate Ar Name',
                'name_en' => 'Graduate En Name',
                'id' => '2023001',
                'degree' => 'بكالوريوس',
                'degree_en' => 'BSc',
                'total' => '',
                'gpa' => '',
                'rating' => '',
                'honors' => '',
                'gradYear' => '2023',
                'enrollmentYear' => '2019',
                'dora' => 'يونيو',
            ],
            'levels' => [
                [
                    'name' => 'الأول',
                    'year' => '2019/2020',
                    'avg' => '85.00',
                    'semesters' => [
                        [
                            'subjects' => [
                                ['catalog_key' => '', 'name' => 'مادة ناجحة 1', 'hours' => '3', 'score' => '85', 'rating' => ''],
                                ['catalog_key' => '', 'name' => 'مادة ناجحة 2', 'hours' => '3', 'score' => '90', 'rating' => ''],
                            ]
                        ],
                        [
                            'subjects' => [
                                ['catalog_key' => '', 'name' => 'مادة ناجحة 3', 'hours' => '3', 'score' => '70', 'rating' => ''],
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'الثاني',
                    'year' => '2020/2021',
                    'avg' => '55.00',
                    'semesters' => [
                        [
                            'subjects' => [
                                ['catalog_key' => '', 'name' => 'مادة راسبة', 'hours' => '3', 'score' => '50', 'rating' => ''],
                            ]
                        ],
                        [
                            'subjects' => []
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.graduates.academic-record.update', $this->graduateUser), $payload);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('graduate_academic_records', [
            'user_id' => $this->graduateUser->id,
            'student_name_ar' => 'Graduate Ar Name',
        ]);

        $record = GraduateAcademicRecord::where('user_id', $this->graduateUser->id)->first();
        $this->assertNotNull($record);
        
        $level1 = $record->levels()->where('name', 'الأول')->first();
        $level2 = $record->levels()->where('name', 'الثاني')->first();

        $this->assertNotNull($level1);
        $this->assertNotNull($level2);

        // Level 1: All subjects >= 60 -> ناجح
        $this->assertEquals('ناجح', $level1->final_result);
        
        // Level 2: Subject score is 50 (< 60) -> راسب
        $this->assertEquals('راسب', $level2->final_result);
    }

    /**
     * Test updating Grades Certificate and verifying it is saved to academic record
     */
    public function test_grades_certificate_update_saves_to_academic_record()
    {
        $payload = [
            'student' => [
                'name' => 'Grades Graduate',
                'name_en' => 'Grades Graduate En',
                'id' => '2023001',
                'degree' => 'بكالوريوس',
                'degree_en' => 'BSc',
                'total' => '',
                'gpa' => '',
                'rating' => '',
                'honors' => '',
                'gradYear' => '2023',
                'enrollmentYear' => '2019',
                'dora' => 'يونيو',
            ],
            'levels' => [
                [
                    'name' => 'الأول',
                    'year' => '2019/2020',
                    'avg' => '92.00',
                    'semesters' => [
                        [
                            'subjects' => [
                                ['catalog_key' => '', 'name' => 'مادة ممتازة', 'hours' => '3', 'score' => '95', 'rating' => ''],
                            ]
                        ],
                        [
                            'subjects' => []
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.graduates.grades-certificate.update', $this->graduateUser), $payload);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('graduate_academic_records', [
            'user_id' => $this->graduateUser->id,
            'student_name_ar' => 'Grades Graduate',
        ]);

        $record = GraduateAcademicRecord::where('user_id', $this->graduateUser->id)->first();
        $this->assertNotNull($record);
        
        $level = $record->levels()->first();
        $this->assertNotNull($level);
        $this->assertEquals('ناجح', $level->final_result);
    }

    /**
     * Test issuing correct PDFs based on request type using single academic record
     */
    public function test_issuance_service_uses_academic_record_for_both()
    {
        // 1. Setup request templates and types
        $academicType = DocumentType::create([
            'name_ar' => 'سجل أكاديمي',
            'name_en' => 'Academic Record',
            'code' => 'ACADEMIC_RECORD',
            'fee_mock' => 10.0,
            'eta_days' => 1,
            'fee_amount' => 10,
            'currency' => 'YER',
            'payment_required' => false
        ]);

        $gradesType = DocumentType::create([
            'name_ar' => 'شهادة درجات',
            'name_en' => 'Grades Certificate',
            'code' => 'GRADES_CERTIFICATE',
            'fee_mock' => 10.0,
            'eta_days' => 1,
            'fee_amount' => 10,
            'currency' => 'YER',
            'payment_required' => false
        ]);

        // Create academic record
        $academicRecord = GraduateAcademicRecord::create([
            'user_id' => $this->graduateUser->id,
            'student_name_ar' => 'Academic Record Name',
        ]);

        // Create document requests
        $req1 = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $academicType->id,
            'tracking_code' => 'ACAD-001',
            'status' => 'APPROVED',
            'language' => 'AR',
            'purpose' => 'Test',
            'delivery_type' => 'DIGITAL'
        ]);

        $req2 = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $gradesType->id,
            'tracking_code' => 'GRAD-001',
            'status' => 'APPROVED',
            'language' => 'AR',
            'purpose' => 'Test',
            'delivery_type' => 'DIGITAL'
        ]);

        $service = app(DocumentIssuanceService::class);
        
        $issued1 = $service->issue($req1, $this->admin->id);
        $issued2 = $service->issue($req2, $this->admin->id);

        $this->assertNotNull($issued1);
        $this->assertNotNull($issued2);
        
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists($issued1->pdf_path));
        $this->assertTrue(\Illuminate\Support\Facades\Storage::disk('local')->exists($issued2->pdf_path));
    }

    /**
     * Test that issuing grades certificate fails if academic record is missing
     */
    public function test_issuance_fails_if_academic_record_is_missing_for_grades_certificate()
    {
        $gradesType = DocumentType::create([
            'name_ar' => 'شهادة درجات',
            'name_en' => 'Grades Certificate',
            'code' => 'GRADES_CERTIFICATE',
            'fee_mock' => 10.0,
            'eta_days' => 1,
            'fee_amount' => 10,
            'currency' => 'YER',
            'payment_required' => false
        ]);

        $req = DocumentRequest::create([
            'user_id' => $this->graduateUser->id,
            'document_type_id' => $gradesType->id,
            'tracking_code' => 'GRAD-002',
            'status' => 'APPROVED',
            'language' => 'AR',
            'purpose' => 'Test',
            'delivery_type' => 'DIGITAL'
        ]);

        $service = app(DocumentIssuanceService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('لا يمكن إصدار شهادة الدرجات والتقديرات لأن السجل الأكاديمي غير مدخل لهذا الطالب.');

        $service->issue($req, $this->admin->id);
    }

    /**
     * Test honors rank disqualification logic and Dora formatting/translation helpers
     */
    public function test_honors_disqualification_and_dora_formatting()
    {
        // 1. Create a record with a subject score of 64 (disqualified)
        $record1 = GraduateAcademicRecord::create([
            'user_id' => $this->graduateUser->id,
            'student_name_ar' => 'Test student',
            'university_number' => '111111',
            'honors_rank' => 'مع مرتبة الشرف',
            'exam_session' => 'الدور الأول'
        ]);
        $level1 = $record1->levels()->create(['sort_order' => 0, 'name' => 'الأول']);
        $sem1 = $level1->semesters()->create(['sort_order' => 0]);
        $sem1->subjects()->create(['sort_order' => 0, 'name' => 'Math', 'score' => 64, 'credit_hours' => 3]);

        $this->assertTrue(\App\Helpers\AcademicHelper::hasHonorDisqualifyingGrade($record1));

        // 2. Create another record with all subject scores > 64 (not disqualified)
        $record2 = GradesCertificate::create([
            'user_id' => $this->graduateUser->id,
            'student_name_ar' => 'Test student 2',
            'university_number' => '222222',
            'honors_rank' => 'مع مرتبة الشرف',
            'exam_session' => 'يونيو'
        ]);
        $level2 = $record2->levels()->create(['sort_order' => 0, 'name' => 'الأول']);
        $sem2 = $level2->semesters()->create(['sort_order' => 0]);
        $sem2->subjects()->create(['sort_order' => 0, 'name' => 'Math', 'score' => 65, 'credit_hours' => 3]);

        $this->assertFalse(\App\Helpers\AcademicHelper::hasHonorDisqualifyingGrade($record2));

        // 3. Test Arabic Dora Session formatting
        $this->assertEquals('دور يونيو', \App\Helpers\AcademicHelper::formatArabicSession('يونيو'));
        $this->assertEquals('الدور الأول', \App\Helpers\AcademicHelper::formatArabicSession('الدور الأول'));
        $this->assertEquals('دور سبتمبر', \App\Helpers\AcademicHelper::formatArabicSession('دور سبتمبر'));

        // 4. Test English Dora Session translation
        $this->assertEquals('June session', \App\Support\AcademicRecordEnglishPdf::examSession('يونيو'));
        $this->assertEquals('First session', \App\Support\AcademicRecordEnglishPdf::examSession('الدور الأول'));
        $this->assertEquals('Summer session', \App\Support\AcademicRecordEnglishPdf::examSession('Summer'));
        $this->assertEquals('First Session', \App\Support\AcademicRecordEnglishPdf::examSession('First Session'));

        // 5. Test HTTP PUT update payload mapping 'مستحق' to 'مع مرتبة الشرف'
        $payload = [
            'student' => [
                'name' => 'Honors Graduate',
                'name_en' => 'Honors Graduate En',
                'id' => '2023001',
                'degree' => 'بكالوريوس',
                'degree_en' => 'BSc',
                'total' => '',
                'gpa' => '85.00',
                'rating' => 'جيد جداً',
                'honors' => 'مستحق',
                'gradYear' => '2023',
                'enrollmentYear' => '2019',
                'dora' => 'يونيو',
            ],
            'levels' => [
                [
                    'name' => 'الأول',
                    'year' => '2019/2020',
                    'avg' => '85.00',
                    'semesters' => [
                        [
                            'subjects' => [
                                ['catalog_key' => '', 'name' => 'مادة ممتازة', 'hours' => '3', 'score' => '85', 'rating' => ''],
                            ]
                        ],
                        [
                            'subjects' => []
                        ]
                    ]
                ]
            ]
        ];

        // Delete existing records to avoid conflicts
        GraduateAcademicRecord::where('user_id', $this->graduateUser->id)->delete();

        $response = $this->actingAs($this->admin)
            ->put(route('admin.graduates.academic-record.update', $this->graduateUser), $payload);

        $response->assertRedirect();
        
        $updatedRecord = GraduateAcademicRecord::where('user_id', $this->graduateUser->id)->first();
        $this->assertEquals('مع مرتبة الشرف', $updatedRecord->honors_rank);
    }
}
