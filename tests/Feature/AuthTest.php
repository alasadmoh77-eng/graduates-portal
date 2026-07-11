<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use App\Models\Major;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_graduate_can_register()
    {
        $major = Major::create(['name_ar' => 'Test', 'name_en' => 'Test']);

        \App\Models\ApprovedGraduate::create([
            'university_id' => '2023001',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'major' => 'Test',
            'graduation_year' => 2023,
        ]);
        
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'university_id' => '2023001',
            'major_id' => $major->id,
            'graduation_year' => 2023,
        ]);

        $response->assertRedirect('/graduate/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_graduate_registration_creates_major_dynamically()
    {
        // major "New Nonexistent Major" does NOT exist in majors table
        $this->assertDatabaseMissing('majors', ['name_ar' => 'New Nonexistent Major']);

        \App\Models\ApprovedGraduate::create([
            'university_id' => '2023002',
            'name' => 'Another Test User',
            'email' => 'another@example.com',
            'major' => 'New Nonexistent Major',
            'graduation_year' => 2024,
        ]);

        $response = $this->post('/register', [
            'email' => 'another@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'university_id' => '2023002',
        ]);

        $response->assertRedirect('/graduate/dashboard');
        
        // Assert that user was created
        $this->assertDatabaseHas('users', ['email' => 'another@example.com']);
        
        // Assert that major was dynamically created
        $this->assertDatabaseHas('majors', [
            'name_ar' => 'New Nonexistent Major',
            'name_en' => 'New Nonexistent Major'
        ]);

        // Assert that graduate was created and linked to the new major
        $newMajor = Major::where('name_ar', 'New Nonexistent Major')->first();
        $this->assertDatabaseHas('graduates', [
            'university_id' => '2023002',
            'major_id' => $newMajor->id,
            'graduation_year' => 2024
        ]);
    }

    public function test_excel_import_syncs_graduates_and_creates_majors()
    {
        // Assert major does not exist
        $this->assertDatabaseMissing('majors', ['name_ar' => 'Imported Major']);

        // Create a temporary Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'university_id');
        $sheet->setCellValue('B1', 'name');
        $sheet->setCellValue('C1', 'email');
        $sheet->setCellValue('D1', 'major');
        $sheet->setCellValue('E1', 'graduation_year');
        
        // Data Row 1 (Create new)
        $sheet->setCellValue('A2', '2023999');
        $sheet->setCellValue('B2', 'Imported Student');
        $sheet->setCellValue('C2', 'imported@example.com');
        $sheet->setCellValue('D2', 'Imported Major');
        $sheet->setCellValue('E2', '2023');

        // Write to a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_import_test');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempFile);

        // Run the import directly
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ApprovedGraduatesImport, $tempFile);

        // Cleanup temp file
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        // Assert database has approved graduate
        $this->assertDatabaseHas('approved_graduates', [
            'university_id' => '2023999',
            'name' => 'Imported Student',
            'email' => 'imported@example.com',
            'major' => 'Imported Major',
            'graduation_year' => 2023,
        ]);

        // Assert database dynamically created the major
        $this->assertDatabaseHas('majors', [
            'name_ar' => 'Imported Major',
            'name_en' => 'Imported Major',
        ]);
    }

    public function test_excel_import_syncs_college_and_creates_faculty()
    {
        // Assert faculty does not exist
        $this->assertDatabaseMissing('faculties', ['name_ar' => 'Imported College']);

        // Create a temporary Excel file using PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'university_id');
        $sheet->setCellValue('B1', 'name');
        $sheet->setCellValue('C1', 'email');
        $sheet->setCellValue('D1', 'college');
        $sheet->setCellValue('E1', 'major');
        $sheet->setCellValue('F1', 'graduation_year');
        
        // Data Row 1 (Create new)
        $sheet->setCellValue('A2', '2023999');
        $sheet->setCellValue('B2', 'Imported Student');
        $sheet->setCellValue('C2', 'imported@example.com');
        $sheet->setCellValue('D2', 'Imported College');
        $sheet->setCellValue('E2', 'Imported Major');
        $sheet->setCellValue('F2', '2023');

        // Write to a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'excel_import_test');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($tempFile);

        // Run the import directly
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ApprovedGraduatesImport, $tempFile);

        // Cleanup temp file
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        // Assert database has approved graduate with college
        $this->assertDatabaseHas('approved_graduates', [
            'university_id' => '2023999',
            'name' => 'Imported Student',
            'email' => 'imported@example.com',
            'college' => 'Imported College',
            'major' => 'Imported Major',
            'graduation_year' => 2023,
        ]);

        // Assert database dynamically created the faculty and linked it to the major
        $this->assertDatabaseHas('faculties', [
            'name_ar' => 'Imported College',
        ]);
        
        $faculty = \App\Models\Faculty::where('name_ar', 'Imported College')->first();
        $this->assertDatabaseHas('majors', [
            'name_ar' => 'Imported Major',
            'faculty_id' => $faculty->id,
        ]);
    }

    public function test_check_graduate_returns_college()
    {
        \App\Models\ApprovedGraduate::create([
            'university_id' => '2023998',
            'name' => 'College Test Student',
            'email' => 'collegetest@example.com',
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $response = $this->get('/api/check-graduate/2023998');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'graduate' => [
                'university_id' => '2023998',
                'name' => 'College Test Student',
                'email' => 'collegetest@example.com',
                'college' => 'Science College',
                'major' => 'Physics',
                'graduation_year' => '2023',
            ]
        ]);
    }

    public function test_freeze_approved_graduate_no_associated_user_fails()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $graduate = \App\Models\ApprovedGraduate::create([
            'university_id' => '2023997',
            'name' => 'Delete Test Student',
            'email' => 'deletetest@example.com',
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $this->assertDatabaseHas('approved_graduates', ['university_id' => '2023997']);

        // Send freeze request as admin
        $response = $this->actingAs($admin)
            ->patch("/admin/graduate-registry/{$graduate->id}/freeze-account");

        $response->assertRedirect('/admin/graduate-registry');
        $response->assertSessionHas('error');
        // Assert approved_graduates record is not deleted
        $this->assertDatabaseHas('approved_graduates', ['university_id' => '2023997']);
    }

    public function test_freeze_approved_graduate_deactivates_user_and_preserves_other_data()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $major = Major::create(['name_ar' => 'Physics', 'name_en' => 'Physics']);

        $approvedGraduate = \App\Models\ApprovedGraduate::create([
            'university_id' => '2023996',
            'name' => 'Deactivate Test Student',
            'email' => 'deactivate_test@example.com',
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $user = User::create([
            'name' => 'Deactivate Test Student',
            'email' => 'deactivate_test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);

        $graduate = \App\Models\Graduate::create([
            'user_id' => $user->id,
            'university_id' => '2023996',
            'phone' => '123456789',
            'major_id' => $major->id,
            'graduation_year' => 2023,
        ]);

        $documentType = \App\Models\DocumentType::create([
            'code' => 'ACADEMIC_RECORD',
            'name_ar' => 'سجل',
            'name_en' => 'Record',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_required' => false,
        ]);

        $documentRequest = \App\Models\DocumentRequest::create([
            'user_id' => $user->id,
            'document_type_id' => $documentType->id,
            'tracking_code' => 'DOC-DEACT-101',
            'status' => 'READY',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'approved',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
            'payment_proof_path' => 'proofs/test.jpg',
        ]);

        $issuedDocument = \App\Models\IssuedDocument::create([
            'document_request_id' => $documentRequest->id,
            'serial_number' => 'SRU-DOC-2026-00002',
            'qr_token' => 'qr_token_test_123',
            'pdf_path' => 'documents/SRU-DOC-2026-00002.pdf',
            'issued_at' => now(),
            'is_valid' => true,
        ]);

        $academicRecord = \App\Models\GraduateAcademicRecord::create([
            'user_id' => $user->id,
            'student_name_ar' => 'طالب تجريبي',
            'university_number' => '2023996',
            'degree_ar' => 'بكالوريوس',
            'total_marks' => 80,
            'gpa' => 3.5,
        ]);

        $this->assertDatabaseHas('approved_graduates', ['university_id' => '2023996']);
        $this->assertTrue($user->fresh()->is_active);

        // Freeze approved graduate
        $response = $this->actingAs($admin)
            ->patch("/admin/graduate-registry/{$approvedGraduate->id}/freeze-account");

        $response->assertRedirect('/admin/graduate-registry');

        // 1. Assert approved_graduate was NOT deleted
        $this->assertDatabaseHas('approved_graduates', ['university_id' => '2023996']);

        // 2. Assert user is deactivated
        $this->assertFalse($user->fresh()->is_active);

        // 3. Assert user is NOT deleted
        $this->assertDatabaseHas('users', ['id' => $user->id]);

        // 4. Assert graduate is NOT deleted
        $this->assertDatabaseHas('graduates', ['user_id' => $user->id]);

        // 5. Assert document request and payment proof path are NOT deleted
        $this->assertDatabaseHas('document_requests', [
            'id' => $documentRequest->id,
            'payment_proof_path' => 'proofs/test.jpg'
        ]);

        // 6. Assert issued document is NOT deleted
        $this->assertDatabaseHas('issued_documents', ['id' => $issuedDocument->id]);

        // 7. Assert academic record is NOT deleted
        $this->assertDatabaseHas('graduate_academic_records', ['id' => $academicRecord->id]);

        // 8. Assert deactivated user cannot log in
        \Illuminate\Support\Facades\Auth::logout();
        $loginResponse = $this->post('/login', [
            'email' => 'deactivate_test@example.com',
            'password' => 'password123',
        ]);
        $loginResponse->assertSessionHasErrors('email');
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_unfreeze_approved_graduate_no_associated_user_fails()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        $approvedGraduate = \App\Models\ApprovedGraduate::create([
            'university_id' => '2023995',
            'name' => 'No Graduate Student',
            'email' => 'no_graduate@example.com',
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $this->assertDatabaseHas('approved_graduates', ['university_id' => '2023995']);

        // Send unfreeze request as admin
        $response = $this->actingAs($admin)
            ->patch("/admin/graduate-registry/{$approvedGraduate->id}/unfreeze-account");

        $response->assertRedirect('/admin/graduate-registry');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('approved_graduates', ['university_id' => '2023995']);
    }

    public function test_check_graduate_api_returns_expected_structure_with_email()
    {
        \App\Models\ApprovedGraduate::create([
            'university_id' => '2023994',
            'name' => 'API Test Student',
            'email' => 'api_test@example.com',
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $response = $this->get('/api/check-graduate/2023994');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'graduate' => [
                'university_id' => '2023994',
                'name' => 'API Test Student',
                'email' => 'api_test@example.com',
                'college' => 'Science College',
                'major' => 'Physics',
                'graduation_year' => '2023',
            ]
        ]);
    }

    public function test_register_graduate_succeeds_with_approved_email()
    {
        $major = Major::create(['name_ar' => 'Physics', 'name_en' => 'Physics']);

        \App\Models\ApprovedGraduate::create([
            'university_id' => '2023993',
            'name' => 'Matching Email Graduate',
            'email' => 'matching@example.com',
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $response = $this->post('/register', [
            'name' => 'Matching Email Graduate',
            'email' => 'matching@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'university_id' => '2023993',
            'major_id' => $major->id,
            'graduation_year' => 2023,
        ]);

        $response->assertRedirect('/graduate/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'matching@example.com']);
    }

    public function test_register_graduate_fails_with_different_email_when_approved_email_exists()
    {
        $major = Major::create(['name_ar' => 'Physics', 'name_en' => 'Physics']);

        \App\Models\ApprovedGraduate::create([
            'university_id' => '2023992',
            'name' => 'Nonmatching Email Graduate',
            'email' => 'approved_email@example.com',
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $response = $this->post('/register', [
            'name' => 'Nonmatching Email Graduate',
            'email' => 'different_email@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'university_id' => '2023992',
            'major_id' => $major->id,
            'graduation_year' => 2023,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'different_email@example.com']);
    }

    public function test_register_graduate_allows_custom_email_when_approved_email_is_null()
    {
        $major = Major::create(['name_ar' => 'Physics', 'name_en' => 'Physics']);

        \App\Models\ApprovedGraduate::create([
            'university_id' => '2023991',
            'name' => 'No Email Graduate',
            'email' => null,
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $response = $this->post('/register', [
            'name' => 'No Email Graduate',
            'email' => 'custom_email@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'university_id' => '2023991',
            'major_id' => $major->id,
            'graduation_year' => 2023,
        ]);

        $response->assertRedirect('/graduate/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'custom_email@example.com']);
    }

    public function test_active_user_can_access_dashboard()
    {
        $user = User::create([
            'name' => 'Active Graduate',
            'email' => 'active@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);
        \App\Models\Graduate::create([
            'user_id' => $user->id,
            'university_id' => '2023005',
            'major_id' => Major::create(['name_ar' => 'Test', 'name_en' => 'Test'])->id,
            'graduation_year' => 2023,
        ]);

        $response = $this->actingAs($user)->get('/graduate/dashboard');
        $response->assertStatus(200);
    }

    public function test_inactive_user_cannot_log_in()
    {
        User::create([
            'name' => 'Inactive Graduate',
            'email' => 'inactive@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_user_logged_in_becomes_inactive_gets_evicted()
    {
        $user = User::create([
            'name' => 'Eviction Graduate',
            'email' => 'evict@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);
        \App\Models\Graduate::create([
            'user_id' => $user->id,
            'university_id' => '2023006',
            'major_id' => Major::create(['name_ar' => 'Test2', 'name_en' => 'Test2'])->id,
            'graduation_year' => 2023,
        ]);

        // Simulating logged in state
        $this->actingAs($user);

        // Turn them to inactive in DB
        $user->is_active = false;
        $user->save();

        // Access protected page
        $response = $this->get('/graduate/dashboard');

        // Should redirect to login with correct error message and have logged out
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_inactive_graduate_cannot_access_document_request_page()
    {
        $user = User::create([
            'name' => 'Inactive Graduate Docs',
            'email' => 'inactive_docs@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => false,
        ]);
        \App\Models\Graduate::create([
            'user_id' => $user->id,
            'university_id' => '2023007',
            'major_id' => Major::create(['name_ar' => 'Test3', 'name_en' => 'Test3'])->id,
            'graduation_year' => 2023,
        ]);

        $response = $this->actingAs($user)->get(route('graduate.documents.create'));

        $response->assertRedirect('/login');
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_inactive_graduate_cannot_send_document_request_post()
    {
        $user = User::create([
            'name' => 'Inactive Graduate Post',
            'email' => 'inactive_post@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => false,
        ]);
        \App\Models\Graduate::create([
            'user_id' => $user->id,
            'university_id' => '2023008',
            'major_id' => Major::create(['name_ar' => 'Test4', 'name_en' => 'Test4'])->id,
            'graduation_year' => 2023,
        ]);

        $response = $this->actingAs($user)->post(route('graduate.documents.store'), [
            'document_type_id' => 1,
            'purpose' => 'Job Application',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF'
        ]);

        $response->assertRedirect('/login');
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_inactive_graduate_cannot_download_document()
    {
        $user = User::create([
            'name' => 'Inactive Graduate Download',
            'email' => 'inactive_download@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => false,
        ]);
        \App\Models\Graduate::create([
            'user_id' => $user->id,
            'university_id' => '2023009',
            'major_id' => Major::create(['name_ar' => 'Test5', 'name_en' => 'Test5'])->id,
            'graduation_year' => 2023,
        ]);

        $docType = \App\Models\DocumentType::create([
            'code' => 'TEST_RECORD_2',
            'name_ar' => 'سجل2',
            'name_en' => 'Record2',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_required' => false,
        ]);
        $docRequest = \App\Models\DocumentRequest::create([
            'user_id' => $user->id,
            'document_type_id' => $docType->id,
            'tracking_code' => 'DOC-PDF-TEST-INACTIVE-2',
            'status' => 'READY',
            'fee_amount' => 50,
            'currency' => 'YER',
            'payment_status' => 'approved',
            'language' => 'AR',
            'delivery_type' => 'DIGITAL_PDF',
            'purpose' => 'Test',
        ]);

        $response = $this->actingAs($user)->get(route('graduate.documents.download', $docRequest));

        $response->assertRedirect('/login');
        $this->assertFalse(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_active_admin_and_active_graduate_are_not_affected()
    {
        $admin = User::create([
            'name' => 'Active Admin',
            'email' => 'active_admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $graduate = User::create([
            'name' => 'Active Graduate OK',
            'email' => 'active_graduate_ok@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);
        \App\Models\Graduate::create([
            'user_id' => $graduate->id,
            'university_id' => '2023010',
            'major_id' => Major::create(['name_ar' => 'Test6', 'name_en' => 'Test6'])->id,
            'graduation_year' => 2023,
        ]);

        $responseAdmin = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseAdmin->assertStatus(200);

        $responseGrad = $this->actingAs($graduate)->get(route('graduate.dashboard'));
        $responseGrad->assertStatus(200);
    }

    public function test_public_user_can_access_events_and_training_page()
    {
        $response = $this->get('/events');
        $response->assertStatus(200);
    }

    public function test_unfreeze_approved_graduate_reactivates_user_and_allows_login()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $major = Major::create(['name_ar' => 'Physics', 'name_en' => 'Physics']);

        $approvedGraduate = \App\Models\ApprovedGraduate::create([
            'university_id' => '2023991',
            'name' => 'Unfreeze Test Student',
            'email' => 'unfreeze_test@example.com',
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $user = User::create([
            'name' => 'Unfreeze Test Student',
            'email' => 'unfreeze_test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => false,
        ]);

        $graduate = \App\Models\Graduate::create([
            'user_id' => $user->id,
            'university_id' => '2023991',
            'phone' => '123456789',
            'major_id' => $major->id,
            'graduation_year' => 2023,
        ]);

        $this->assertFalse($user->fresh()->is_active);

        // Unfreeze approved graduate
        $response = $this->actingAs($admin)
            ->patch("/admin/graduate-registry/{$approvedGraduate->id}/unfreeze-account");

        $response->assertRedirect('/admin/graduate-registry');
        $this->assertTrue($user->fresh()->is_active);

        // Assert user can log in now
        \Illuminate\Support\Facades\Auth::logout();
        $loginResponse = $this->post('/login', [
            'email' => 'unfreeze_test@example.com',
            'password' => 'password123',
        ]);
        $loginResponse->assertRedirect();
        $this->assertTrue(\Illuminate\Support\Facades\Auth::check());
    }

    public function test_clear_test_data_route_works_in_local_testing()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

        \App\Models\ApprovedGraduate::create([
            'university_id' => '2023992',
            'name' => 'Purge Test Student',
            'email' => 'purge_test@example.com',
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $this->assertDatabaseHas('approved_graduates', ['university_id' => '2023992']);

        // Send clear request
        $response = $this->actingAs($admin)
            ->delete("/admin/graduate-registry/clear-test-data");

        $response->assertRedirect('/admin/graduate-registry');
        $this->assertDatabaseMissing('approved_graduates', ['university_id' => '2023992']);
    }

    public function test_clear_test_data_route_fails_in_production()
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->app->detectEnvironment(function () {
            return 'production';
        });

        $response = $this->actingAs($admin)
            ->delete("/admin/graduate-registry/clear-test-data");

        $response->assertStatus(403);
    }

    public function test_restricted_roles_cannot_execute_freeze_unfreeze_clear()
    {
        $academicAdmin = User::factory()->create(['role' => 'academic_admin', 'is_active' => true]);
        $financeAdmin = User::factory()->create(['role' => 'finance_admin', 'is_active' => true]);
        $graduateRole = User::factory()->create(['role' => 'graduate', 'is_active' => true]);
        $employer = User::factory()->create(['role' => 'employer', 'is_active' => true]);

        $approvedGraduate = \App\Models\ApprovedGraduate::create([
            'university_id' => '2023993',
            'name' => 'Restricted Role Student',
            'email' => 'restricted@example.com',
            'college' => 'Science College',
            'major' => 'Physics',
            'graduation_year' => 2023,
        ]);

        $restrictedUsers = [$academicAdmin, $financeAdmin, $graduateRole, $employer];

        foreach ($restrictedUsers as $user) {
            // Freeze
            $response = $this->actingAs($user)
                ->patch("/admin/graduate-registry/{$approvedGraduate->id}/freeze-account");
            $response->assertStatus(403);

            // Unfreeze
            $response = $this->actingAs($user)
                ->patch("/admin/graduate-registry/{$approvedGraduate->id}/unfreeze-account");
            $response->assertStatus(403);

            // Clear
            $response = $this->actingAs($user)
                ->delete("/admin/graduate-registry/clear-test-data");
            $response->assertStatus(403);
        }
    }
}
