<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employer;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected $graduateUser;
    protected $employerUser;
    protected $employer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a graduate
        $this->graduateUser = User::create([
            'name' => 'Graduate User',
            'email' => 'graduate@example.com',
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);

        // Create an approved employer
        $this->employerUser = User::create([
            'name' => 'Employer User',
            'email' => 'employer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employer',
            'is_active' => true,
        ]);

        $this->employer = Employer::create([
            'user_id' => $this->employerUser->id,
            'company_name' => 'Tech Solutions Corp',
            'company_email' => 'contact@techsolutions.com',
            'phone' => '123456789',
            'address' => 'Marib, Yemen',
            'website' => 'https://techsolutions.com',
            'status' => 'approved',
            'industry' => 'Technology',
            'description' => 'A leading tech company.',
        ]);
    }

    /**
     * 1. Graduate can access the employers list and view the name, industry, and location.
     */
    public function test_graduate_can_access_employers_list()
    {
        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.employers.index'));

        $response->assertStatus(200);
        $response->assertSee('Tech Solutions Corp');
        $response->assertSee('Technology');
        $response->assertSee('Marib, Yemen');
    }

    /**
     * 2. Search works correctly.
     */
    public function test_graduate_can_search_employers_by_name()
    {
        // Create another approved employer
        $otherUser = User::create([
            'name' => 'Other Employer',
            'email' => 'other@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employer',
            'is_active' => true,
        ]);
        Employer::create([
            'user_id' => $otherUser->id,
            'company_name' => 'Al-Amal Services',
            'company_email' => 'contact@alamal.com',
            'status' => 'approved',
            'industry' => 'Finance',
        ]);

        // Search for "Tech"
        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.employers.index', ['search' => 'Tech']));

        $response->assertStatus(200);
        $response->assertSee('Tech Solutions Corp');
        $response->assertDontSee('Al-Amal Services');
    }

    /**
     * 3. Unapproved (pending, rejected, suspended) employers are hidden.
     */
    public function test_unapproved_employers_are_hidden()
    {
        $pendingUser = User::create([
            'name' => 'Pending Employer',
            'email' => 'pending@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employer',
            'is_active' => true,
        ]);
        $pendingEmployer = Employer::create([
            'user_id' => $pendingUser->id,
            'company_name' => 'Pending Solutions',
            'status' => 'pending',
        ]);

        // Access index page
        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.employers.index'));

        $response->assertStatus(200);
        $response->assertDontSee('Pending Solutions');

        // Access show page directly of pending employer -> returns 404
        $responseDetail = $this->actingAs($this->graduateUser)
            ->get(route('graduate.employers.show', $pendingEmployer->user_id));

        $responseDetail->assertStatus(404);
    }

    /**
     * 4. Graduate can view approved employer details and private/sensitive data is hidden.
     */
    public function test_graduate_can_access_employer_details_and_hides_private_data()
    {
        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.employers.show', $this->employer->user_id));

        $response->assertStatus(200);
        $response->assertSee('Tech Solutions Corp');
        $response->assertSee('Technology');
        $response->assertSee('Marib, Yemen');
        $response->assertSee('https://techsolutions.com');

        // Assert links are formatted correctly
        $response->assertSee('href="mailto:contact@techsolutions.com"', false);
        $response->assertSee('href="tel:123456789"', false);
        $response->assertSee('href="https://techsolutions.com" target="_blank"', false);
        
        // Assert no editing or deletion buttons/links are visible
        $response->assertDontSee('edit');
        $response->assertDontSee('delete');
        $response->assertDontSee('destroy');
        $response->assertDontSee('تعديل');
        $response->assertDontSee('حذف');
    }

    /**
     * Test fallback text displays if email, phone, or website are null.
     */
    public function test_employer_details_shows_fallback_when_fields_are_null()
    {
        $bareUser = User::create([
            'name' => 'Bare Employer',
            'email' => 'bare@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employer',
            'is_active' => true,
        ]);
        $bareEmployer = Employer::create([
            'user_id' => $bareUser->id,
            'company_name' => 'Bare Solutions Corp',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.employers.show', $bareEmployer->user_id));

        $response->assertStatus(200);
        // It should show "غير متاح" (Not available) for missing fields
        $response->assertSee('غير متاح');
    }

    /**
     * 5. Active jobs are displayed, but unapproved/inactive jobs are hidden.
     */
    public function test_employer_details_shows_active_jobs_only()
    {
        // Create active job
        $activeJob = Job::create([
            'employer_id' => $this->employerUser->id,
            'title' => 'Senior Developer Active',
            'description' => 'Description of active job',
            'deadline' => now()->addDays(30),
            'location' => 'Marib',
            'job_type' => 'Full-time',
            'status' => 'active',
        ]);

        // Create pending job
        $pendingJob = Job::create([
            'employer_id' => $this->employerUser->id,
            'title' => 'Junior Developer Pending',
            'description' => 'Description of pending job',
            'deadline' => now()->addDays(30),
            'location' => 'Marib',
            'job_type' => 'Part-time',
            'status' => 'pending',
        ]);

        // Access detail page
        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.employers.show', $this->employer->user_id));

        $response->assertStatus(200);
        $response->assertSee('Senior Developer Active');
        $response->assertDontSee('Junior Developer Pending');
    }

    /**
     * 6. Friendly message displays when no active jobs exist.
     */
    public function test_shows_no_jobs_message_when_no_active_jobs()
    {
        $response = $this->actingAs($this->graduateUser)
            ->get(route('graduate.employers.show', $this->employer->user_id));

        $response->assertStatus(200);
        $response->assertSee('لا توجد وظائف نشطة معلنة حالياً');
    }
}
