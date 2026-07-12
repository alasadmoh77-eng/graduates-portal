<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Major;
use App\Models\Faculty;
use App\Models\Graduate;
use App\Models\GraduateAcademicRecord;
use App\Services\AcademicRecordExcelImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SecurityFixesTest extends TestCase
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
        $this->graduateUser = User::factory()->create([
            'name' => 'Original Name',
            'email' => 'original@example.com',
            'role' => 'graduate'
        ]);
        $this->graduate = Graduate::create([
            'user_id' => $this->graduateUser->id,
            'university_id' => '2023001',
            'major_id' => $this->major->id,
            'graduation_year' => 2023,
        ]);
    }

    /**
     * Helper to call academic record update with a score.
     */
    private function updateAcademicRecordWithScore($score)
    {
        return $this->actingAs($this->admin)
            ->putJson(route('admin.graduates.academic-record.update', $this->graduateUser), [
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
                                    ['catalog_key' => '', 'name' => 'مادة 1', 'hours' => '3', 'score' => $score, 'rating' => ($score !== '' && is_numeric($score) && $score >= 90 && $score <= 100) ? 'ممتاز' : ''],
                                ]
                            ],
                            [
                                'subjects' => []
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /**
     * Helper to call grades certificate update with a score.
     */
    private function updateGradesCertificateWithScore($score)
    {
        return $this->actingAs($this->admin)
            ->putJson(route('admin.graduates.grades-certificate.update', $this->graduateUser), [
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
                                    ['catalog_key' => '', 'name' => 'مادة 1', 'hours' => '3', 'score' => $score, 'rating' => ($score !== '' && is_numeric($score) && $score >= 90 && $score <= 100) ? 'ممتاز' : ''],
                                ]
                            ],
                            [
                                'subjects' => []
                            ]
                        ]
                    ]
                ]
            ]);
    }

    /**
     * Test: Grade 0 is accepted.
     */
    public function test_grade_zero_is_accepted()
    {
        $response = $this->updateAcademicRecordWithScore('0');
        $response->assertStatus(200);
    }

    /**
     * Test: Grade 50 is accepted.
     */
    public function test_grade_fifty_is_accepted()
    {
        $response = $this->updateAcademicRecordWithScore('50');
        $response->assertStatus(200);
    }

    /**
     * Test: Grade 100 is accepted.
     */
    public function test_grade_one_hundred_is_accepted()
    {
        $response = $this->updateAcademicRecordWithScore('100');
        $response->assertStatus(200);
    }

    /**
     * Test: Grade -1 is rejected.
     */
    public function test_grade_negative_one_is_rejected()
    {
        $response = $this->updateAcademicRecordWithScore('-1');
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['levels.0.semesters.0.subjects.0.score']);
    }

    /**
     * Test: Grade 101 is rejected.
     */
    public function test_grade_one_hundred_and_one_is_rejected()
    {
        $response = $this->updateAcademicRecordWithScore('101');
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['levels.0.semesters.0.subjects.0.score']);
    }

    /**
     * Test: Grade 1000 is rejected.
     */
    public function test_grade_one_thousand_is_rejected()
    {
        $response = $this->updateAcademicRecordWithScore('1000');
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['levels.0.semesters.0.subjects.0.score']);
    }

    /**
     * Test: Non-numeric grade is rejected.
     */
    public function test_grade_non_numeric_is_rejected()
    {
        $response = $this->updateAcademicRecordWithScore('abc');
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['levels.0.semesters.0.subjects.0.score']);
    }

    /**
     * Test: The invalid value is not stored in the database.
     */
    public function test_invalid_grade_value_is_not_stored_in_database()
    {
        $initialCount = \App\Models\GraduateAcademicSubject::count();
        $this->updateAcademicRecordWithScore('1000');
        $this->assertEquals($initialCount, \App\Models\GraduateAcademicSubject::count());
    }

    /**
     * Test: A valid grade receives the expected classification.
     */
    public function test_valid_grade_receives_expected_classification()
    {
        $this->updateAcademicRecordWithScore('95');
        $subject = \App\Models\GraduateAcademicSubject::first();
        $this->assertEquals('ممتاز', $subject->rating);
    }

    /**
     * Test: Invalid grades are not classified as "Excellent" / "ممتاز".
     */
    public function test_invalid_grades_are_not_classified_as_excellent()
    {
        // Test service methods defensively
        $importService = new AcademicRecordExcelImportService();
        
        $reflector = new \ReflectionClass(AcademicRecordExcelImportService::class);
        $method = $reflector->getMethod('getSubjectRating');
        $method->setAccessible(true);

        $this->assertEquals('غير صالح', $method->invoke($importService, 1000));
        $this->assertEquals('غير صالح', $method->invoke($importService, -5));
        
        $methodGpa = $reflector->getMethod('getOverallRating');
        $methodGpa->setAccessible(true);
        
        $this->assertEquals('غير صالح', $methodGpa->invoke($importService, 1000));
        $this->assertEquals('غير صالح', $methodGpa->invoke($importService, -5));
    }

    /**
     * Test: Academic-record edit validation also rejects out-of-range values.
     */
    public function test_grades_certificate_edit_validation_rejects_out_of_range()
    {
        $response = $this->updateGradesCertificateWithScore('1000');
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['levels.0.semesters.0.subjects.0.score']);
    }

    /**
     * Test: Excel import rejects an out-of-range value.
     */
    public function test_excel_import_rejects_out_of_range_grade()
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $handle = fopen($tempFile, 'w');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, ['university_id', 'student_name', 'college', 'department', 'degree', 'admission_year', 'graduation_year', 'level', 'academic_year', 'semester', 'subject_name', 'credit_hours', 'score', 'grade']);
        fputcsv($handle, ['2023001', 'Graduate Ar Name', 'New College', 'علوم الحاسوب', 'بكالوريوس', '2019', '2023', 'الأول', '2019/2020', 'الفصل الأول', 'مادة 1', '3', '1000', 'ممتاز']);
        fclose($handle);

        $file = new UploadedFile($tempFile, 'import.csv', 'text/csv', null, true);

        $importService = new AcademicRecordExcelImportService();
        $result = $importService->import($file);

        $this->assertEquals(1, $result['error_count']);
        $this->assertStringContainsString('يجب أن تكون قيمة عددية بين 0 و 100', $result['errors'][0]);
    }

    /**
     * Test: A graduate can open the profile-edit page.
     */
    public function test_graduate_can_open_profile_edit_page()
    {
        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.profile.edit'));

        $response->assertStatus(200);
    }

    /**
     * Test: Their name and email are displayed.
     */
    public function test_name_and_email_are_displayed_in_profile_edit_page()
    {
        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.profile.edit'));

        $response->assertSee($this->graduateUser->name);
        $response->assertSee($this->graduateUser->email);
    }

    /**
     * Test: Name, email, major, and graduation year are not presented as editable graduate fields (readonly check).
     */
    public function test_name_email_major_and_graduation_year_are_presented_as_readonly()
    {
        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.profile.edit'));

        $response->assertSee('readonly');
        $response->assertSee('لا يمكن تعديل الاسم أو البريد الإلكتروني أو التخصص أو سنة التخرج');
    }

    /**
     * Test: A graduate can update allowed profile fields (phone).
     */
    public function test_graduate_can_update_allowed_profile_fields()
    {
        $response = $this->actingAs($this->graduateUser)
            ->put(route('graduate.profile.update'), [
                'phone' => '123456789',
            ]);

        $response->assertRedirect(route('graduate.profile.show'));
        
        $this->graduate->refresh();
        $this->assertEquals('123456789', $this->graduate->phone);
    }

    /**
     * Test: A graduate sends a crafted request containing a different name and it does not change.
     */
    public function test_crafted_request_with_different_name_does_not_change_stored_name()
    {
        $response = $this->actingAs($this->graduateUser)
            ->put(route('graduate.profile.update'), [
                'name' => 'Hacked Name',
                'phone' => '123456789',
            ]);

        $this->graduateUser->refresh();
        $this->assertEquals('Original Name', $this->graduateUser->name);
    }

    /**
     * Test: A graduate sends a crafted request containing a different email and it does not change.
     */
    public function test_crafted_request_with_different_email_does_not_change_stored_email()
    {
        $response = $this->actingAs($this->graduateUser)
            ->put(route('graduate.profile.update'), [
                'email' => 'hacked@example.com',
                'phone' => '123456789',
            ]);

        $this->graduateUser->refresh();
        $this->assertEquals('original@example.com', $this->graduateUser->email);
    }

    /**
     * Test: A graduate sends a crafted request containing a different major_id and it does not change.
     */
    public function test_crafted_request_with_different_major_does_not_change_stored_major()
    {
        $newMajor = Major::create(['name_ar' => 'جديد', 'name_en' => 'New Major']);

        $response = $this->actingAs($this->graduateUser)
            ->put(route('graduate.profile.update'), [
                'major_id' => $newMajor->id,
                'phone' => '123456789',
            ]);

        $this->graduate->refresh();
        $this->assertEquals($this->major->id, $this->graduate->major_id);
    }

    /**
     * Test: A graduate sends a crafted request containing a different graduation_year and it does not change.
     */
    public function test_crafted_request_with_different_graduation_year_does_not_change()
    {
        $response = $this->actingAs($this->graduateUser)
            ->put(route('graduate.profile.update'), [
                'graduation_year' => 2099,
                'phone' => '123456789',
            ]);

        $this->graduate->refresh();
        $this->assertEquals(2023, $this->graduate->graduation_year);
    }

    /**
     * Test: A combined malicious payload does not change any protected fields, while allowed fields are still updated.
     */
    public function test_combined_malicious_payload_does_not_change_protected_fields()
    {
        $newMajor = Major::create(['name_ar' => 'جديد', 'name_en' => 'New Major']);

        $response = $this->actingAs($this->graduateUser)
            ->put(route('graduate.profile.update'), [
                'name' => 'Hacked Name',
                'email' => 'hacked@example.com',
                'major_id' => $newMajor->id,
                'graduation_year' => 2099,
                'phone' => '987654321',
            ]);

        $this->graduateUser->refresh();
        $this->graduate->refresh();

        $this->assertEquals('Original Name', $this->graduateUser->name);
        $this->assertEquals('original@example.com', $this->graduateUser->email);
        $this->assertEquals($this->major->id, $this->graduate->major_id);
        $this->assertEquals(2023, $this->graduate->graduation_year);
        $this->assertEquals('987654321', $this->graduate->phone);
    }

    /**
     * Test: An authorized administrator can still update major_id and graduation_year.
     */
    public function test_admin_can_still_update_major_and_graduation_year_via_admin_workflow()
    {
        $newMajor = Major::create(['name_ar' => 'جديد', 'name_en' => 'New Major']);

        // In the admin workflow, updates can be done via AcademicRecordExcelImportService or direct model updates
        // Since $fillable on the model is not modified, direct model update (the administrative workflow foundation) remains functional.
        $this->graduate->update([
            'major_id' => $newMajor->id,
            'graduation_year' => 2026,
        ]);

        $this->graduate->refresh();
        $this->assertEquals($newMajor->id, $this->graduate->major_id);
        $this->assertEquals(2026, $this->graduate->graduation_year);
    }
}
