<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\Graduate;
use App\Models\Major;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicRecordImportTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $graduateUser;
    private $graduate;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create a graduate user and profile
        $this->graduateUser = User::factory()->create([
            'name' => 'Old Name',
            'role' => 'graduate',
        ]);

        $faculty = Faculty::create([
            'name_ar' => 'Old College',
            'name_en' => 'Old College',
        ]);

        $major = Major::create([
            'name_ar' => 'Old Department',
            'name_en' => 'Old Department',
            'faculty_id' => $faculty->id,
        ]);

        $this->graduate = Graduate::create([
            'user_id' => $this->graduateUser->id,
            'university_id' => '2023001',
            'major_id' => $major->id,
            'graduation_year' => 2022,
        ]);
    }

    /**
     * Helper to create a fake CSV file.
     */
    private function createCsvFile(array $rows): \Illuminate\Http\UploadedFile
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'excel');
        $handle = fopen($tempFile, 'w');
        
        // Write UTF-8 BOM
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return new \Illuminate\Http\UploadedFile($tempFile, 'import.csv', 'text/csv', null, true);
    }

    /**
     * Test uploading a correct file succeeds.
     */
    public function test_uploading_correct_file_succeeds()
    {
        $csv = $this->createCsvFile([
            ['university_id', 'student_name', 'college', 'department', 'degree', 'admission_year', 'graduation_year', 'level', 'academic_year', 'semester', 'subject_name', 'credit_hours', 'score', 'grade'],
            ['2023001', 'New Name', 'New College', 'New Department', 'BSc', '2019', '2023', 'الأول', '2019/2020', 'الفصل الأول', 'برمجة حاسوب 1', '3', '95', 'ممتاز'],
            ['2023001', 'New Name', 'New College', 'New Department', 'BSc', '2019', '2023', 'الأول', '2019/2020', 'الفصل الثاني', 'برمجة حاسوب 2', '3', '85', 'جيد جداً'],
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.academic-records.import'), [
                'excel_file' => $csv,
                'update_student_profile' => 1
            ]);

        $response->assertStatus(200);
        $response->assertViewHas('result');

        // Check if student profile was updated
        $this->graduateUser->refresh();
        $this->graduate->refresh();
        $this->assertEquals('New Name', $this->graduateUser->name);
        $this->assertEquals('New Department', $this->graduate->major->name_ar);
        $this->assertEquals('New College', $this->graduate->major->faculty->name_ar);

        // Check if academic record was created with correct level average & total score
        $record = $this->graduateUser->academicRecord;
        $this->assertNotNull($record);
        $this->assertEquals(90, $record->gpa); // (95*3 + 85*3)/6 = 90
        $this->assertCount(1, $record->levels);
        $this->assertEquals(90, $record->levels->first()->level_avg);
        $this->assertCount(2, $record->levels->first()->semesters->flatMap->subjects);
    }

    /**
     * Test uploading file with missing required columns fails.
     */
    public function test_uploading_file_with_missing_columns_fails()
    {
        $csv = $this->createCsvFile([
            ['university_id', 'student_name', 'college', 'level', 'academic_year', 'semester', 'subject_name', 'credit_hours', 'score'],
            ['2023001', 'New Name', 'New College', 'الأول', '2019/2020', 'الفصل الأول', 'برمجة حاسوب 1', '3', '95'],
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.academic-records.import'), [
                'excel_file' => $csv,
            ]);

        $response->assertStatus(200);
        $response->assertViewHas('error');
    }

    /**
     * Test row without university ID is reported as an error.
     */
    public function test_row_without_university_id_fails()
    {
        $csv = $this->createCsvFile([
            ['university_id', 'student_name', 'college', 'department', 'degree', 'admission_year', 'graduation_year', 'level', 'academic_year', 'semester', 'subject_name', 'credit_hours', 'score', 'grade'],
            ['', 'New Name', 'New College', 'New Department', 'BSc', '2019', '2023', 'الأول', '2019/2020', 'الفصل الأول', 'برمجة حاسوب 1', '3', '95', 'ممتاز'],
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.academic-records.import'), [
                'excel_file' => $csv,
            ]);

        $response->assertStatus(200);
        $response->assertViewHas('result');
        $result = $response->viewData('result');
        $this->assertEquals(1, $result['error_count']);
        $this->assertStringContainsString('الرقم الجامعي مطلوب', $result['errors'][0]);
    }

    /**
     * Test non-existent student logs error and does not create user.
     */
    public function test_non_existent_student_logs_error()
    {
        $csv = $this->createCsvFile([
            ['university_id', 'student_name', 'college', 'department', 'degree', 'admission_year', 'graduation_year', 'level', 'academic_year', 'semester', 'subject_name', 'credit_hours', 'score', 'grade'],
            ['9999999', 'Ghost Student', 'New College', 'New Department', 'BSc', '2019', '2023', 'الأول', '2019/2020', 'الفصل الأول', 'برمجة حاسوب 1', '3', '95', 'ممتاز'],
        ]);

        $userCountBefore = User::count();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.academic-records.import'), [
                'excel_file' => $csv,
            ]);

        $response->assertStatus(200);
        $response->assertViewHas('result');
        $result = $response->viewData('result');
        $this->assertEquals(1, $result['error_count']);
        $this->assertStringContainsString('الطالب غير موجود في النظام', $result['errors'][0]);
        $this->assertEquals($userCountBefore, User::count()); // No new user created
    }

    /**
     * Test uploading the same file twice does not create duplicate subjects.
     */
    public function test_importing_twice_does_not_duplicate()
    {
        $csv = $this->createCsvFile([
            ['university_id', 'student_name', 'college', 'department', 'degree', 'admission_year', 'graduation_year', 'level', 'academic_year', 'semester', 'subject_name', 'credit_hours', 'score', 'grade'],
            ['2023001', 'New Name', 'New College', 'New Department', 'BSc', '2019', '2023', 'الأول', '2019/2020', 'الفصل الأول', 'برمجة حاسوب 1', '3', '95', 'ممتاز'],
        ]);

        // First import
        $this->actingAs($this->admin)
            ->post(route('admin.academic-records.import'), [
                'excel_file' => $csv,
            ]);

        // Second import of the same file
        $response = $this->actingAs($this->admin)
            ->post(route('admin.academic-records.import'), [
                'excel_file' => $csv,
            ]);

        $response->assertStatus(200);
        
        $this->graduateUser->refresh();
        $record = $this->graduateUser->academicRecord;
        
        $this->assertCount(1, $record->levels);
        $this->assertCount(1, $record->levels->first()->semesters->flatMap->subjects); // Exactly 1 subject, not 2
    }
}
