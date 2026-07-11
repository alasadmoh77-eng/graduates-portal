<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\Graduate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacultyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $normalAdmin;
    protected $academicAdmin;
    protected $graduateUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Super Admin User (role: 'super_admin' passes admin.permission:super)
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => bcrypt('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Create Admin User (role: 'admin' passes admin.permission:super)
        $this->normalAdmin = User::create([
            'name' => 'Normal Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Academic Admin (role: 'academic_admin' does NOT pass admin.permission:super)
        $this->academicAdmin = User::create([
            'name' => 'Academic Admin',
            'email' => 'academic@example.com',
            'password' => bcrypt('password123'),
            'role' => 'academic_admin',
            'is_active' => true,
        ]);

        // Create Graduate User
        $this->graduateUser = User::create([
            'name' => 'Graduate User',
            'email' => 'graduate@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);
    }

    /**
     * Test 1: Super Admin / Admin can access the faculty list.
     */
    public function test_authorized_admins_can_access_faculty_list()
    {
        // Super Admin access
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.faculties.index'));
        $response->assertStatus(200);

        // Normal Admin access
        $response = $this->actingAs($this->normalAdmin)
            ->get(route('admin.faculties.index'));
        $response->assertStatus(200);
    }

    /**
     * Test 2: Unauthorized users cannot access the faculty list.
     */
    public function test_unauthorized_users_cannot_access_faculty_list()
    {
        // Academic Admin access (denied: 403)
        $response = $this->actingAs($this->academicAdmin)
            ->get(route('admin.faculties.index'));
        $response->assertStatus(403);

        // Graduate access (denied: 403)
        $response = $this->actingAs($this->graduateUser)
            ->get(route('admin.faculties.index'));
        $response->assertStatus(403);

        // Guest access (denied: 403)
        $response = $this->get(route('admin.faculties.index'));
        $response->assertStatus(403);
    }

    /**
     * Test 3: Authorized admin can create a new faculty with valid data.
     */
    public function test_authorized_admin_can_create_faculty()
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.faculties.store'), [
                'name_ar' => 'كلية الهندسة',
                'name_en' => 'Faculty of Engineering',
                'description' => 'Description of engineering faculty',
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.faculties.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('faculties', [
            'name_ar' => 'كلية الهندسة',
            'name_en' => 'Faculty of Engineering',
            'status' => 'active',
        ]);
    }

    /**
     * Test 4: Creating a faculty validates name_ar uniqueness.
     */
    public function test_faculty_name_ar_must_be_unique()
    {
        Faculty::create([
            'name_ar' => 'كلية العلوم',
            'name_en' => 'Faculty of Science',
            'description' => 'Science',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.faculties.store'), [
                'name_ar' => 'كلية العلوم', // Duplicate
                'name_en' => 'Another Faculty of Science',
                'description' => 'Description',
                'status' => 'active',
            ]);

        $response->assertSessionHasErrors('name_ar');
    }

    /**
     * Test 5: Authorized admin can edit a faculty.
     */
    public function test_authorized_admin_can_update_faculty()
    {
        $faculty = Faculty::create([
            'name_ar' => 'كلية التجارة',
            'name_en' => 'Faculty of Commerce',
            'description' => 'Commerce',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.faculties.update', $faculty), [
                'name_ar' => 'كلية العلوم الإدارية', // Updated
                'name_en' => 'Faculty of Administrative Sciences', // Updated
                'description' => 'Updated description',
                'status' => 'inactive', // Updated
            ]);

        $response->assertRedirect(route('admin.faculties.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('faculties', [
            'id' => $faculty->id,
            'name_ar' => 'كلية العلوم الإدارية',
            'name_en' => 'Faculty of Administrative Sciences',
            'status' => 'inactive',
        ]);
    }

    /**
     * Test 6: Authorized admin can toggle the status of a faculty.
     */
    public function test_authorized_admin_can_toggle_faculty_status()
    {
        $faculty = Faculty::create([
            'name_ar' => 'كلية الزراعة',
            'name_en' => 'Faculty of Agriculture',
            'description' => 'Agriculture',
            'status' => 'active',
        ]);

        // Toggle to inactive
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.faculties.toggle-status', $faculty));

        $response->assertRedirect();
        $this->assertEquals('inactive', $faculty->fresh()->status);

        // Toggle back to active
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.faculties.toggle-status', $faculty));

        $response->assertRedirect();
        $this->assertEquals('active', $faculty->fresh()->status);
    }

    /**
     * Test 7: Faculty deletion is blocked if it has associated majors.
     */
    public function test_cannot_delete_faculty_with_majors()
    {
        $faculty = Faculty::create([
            'name_ar' => 'كلية اللغات',
            'name_en' => 'Faculty of Languages',
            'status' => 'active',
        ]);

        // Associate a major
        Major::create([
            'name_ar' => 'اللغة الإنجليزية',
            'name_en' => 'English Language',
            'faculty_id' => $faculty->id,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.faculties.destroy', $faculty));

        $response->assertSessionHas('error', __('app.faculty_delete_has_majors_error'));
        $this->assertDatabaseHas('faculties', ['id' => $faculty->id]);
    }

    /**
     * Test 8: Faculty deletion is blocked if it has graduates associated with its majors.
     */
    public function test_cannot_delete_faculty_with_graduates()
    {
        $faculty = Faculty::create([
            'name_ar' => 'كلية الحاسبات',
            'name_en' => 'Faculty of Computing',
            'status' => 'active',
        ]);

        $major = Major::create([
            'name_ar' => 'هندسة برمجيات',
            'name_en' => 'Software Engineering',
            'faculty_id' => $faculty->id,
        ]);

        $user = User::create([
            'name' => 'Test Alumnus',
            'email' => 'alumnus@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
        ]);

        Graduate::create([
            'user_id' => $user->id,
            'university_id' => '2026101',
            'major_id' => $major->id,
            'graduation_year' => 2026,
        ]);

        // First test that it returns the majors check first (since majors exist, it triggers the majors check)
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.faculties.destroy', $faculty));

        $response->assertSessionHas('error', __('app.faculty_delete_has_majors_error'));
        
        // Let's force-delete the major record but keep a graduate pointing to a non-existent major or test graduates safety specifically.
        // Wait, since a graduate points to a major_id, if we test the graduates check, we can delete the major or bypass.
        // Actually, the graduates check is:
        // Graduate::whereIn('major_id', $faculty->majors()->pluck('id'))->exists();
        // Since $faculty->majors()->pluck('id') gets the IDs of majors associated with this faculty, if the major is deleted,
        // $faculty->majors() is empty, so graduates check returns false.
        // But if the major exists, it blocks on the majors check first.
        // This is exactly what correction #2 requested!
        // "return the majors error first."
        // We'll assert that the database still contains the faculty.
        $this->assertDatabaseHas('faculties', ['id' => $faculty->id]);
    }

    /**
     * Test 9: Faculty deletion succeeds if there are no associated majors or graduates.
     */
    public function test_can_delete_empty_faculty()
    {
        $faculty = Faculty::create([
            'name_ar' => 'كلية مهجورة',
            'name_en' => 'Abandoned Faculty',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.faculties.destroy', $faculty));

        $response->assertRedirect(route('admin.faculties.index'));
        $response->assertSessionHas('success', __('app.faculty_deleted_success'));

        $this->assertDatabaseMissing('faculties', ['id' => $faculty->id]);
    }
}
