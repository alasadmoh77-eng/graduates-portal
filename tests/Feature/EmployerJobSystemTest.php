<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employer;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerJobSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test registering multiple employers successfully.
     */
    public function test_multiple_employers_can_be_added_independently()
    {
        $this->post('/register/employer', [
            'name' => 'Employer One',
            'email' => 'emp1@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Company Alpha',
        ])->assertRedirect(route('employer.pending'));

        $this->post('/register/employer', [
            'name' => 'Employer Two',
            'email' => 'emp2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Company Beta',
        ])->assertRedirect(route('employer.pending'));

        $this->assertDatabaseHas('users', ['email' => 'emp1@example.com', 'role' => 'employer']);
        $this->assertDatabaseHas('users', ['email' => 'emp2@example.com', 'role' => 'employer']);
        $this->assertDatabaseHas('employers', ['company_name' => 'Company Alpha']);
        $this->assertDatabaseHas('employers', ['company_name' => 'Company Beta']);
    }

    /**
     * Test adding multiple jobs per employer and ensuring they do not overwrite each other.
     */
    public function test_employers_can_add_multiple_jobs_without_affecting_previous_listings()
    {
        // Register employer
        $user = User::factory()->create(['role' => 'employer']);
        Employer::create([
            'user_id' => $user->id,
            'company_name' => 'Company Alpha',
            'status' => 'approved',
        ]);

        $this->actingAs($user);

        // Post job 1
        $this->post('/employer/jobs', [
            'title' => 'Job Number One',
            'description' => 'First description',
            'requirements' => 'Some requirements',
            'deadline' => now()->addDays(5)->format('Y-m-d'),
            'location' => 'Marib',
            'job_type' => 'Full-time',
        ])->assertRedirect(route('employer.jobs.index'));

        // Post job 2
        $this->post('/employer/jobs', [
            'title' => 'Job Number Two',
            'description' => 'Second description',
            'requirements' => 'Other requirements',
            'deadline' => now()->addDays(10)->format('Y-m-d'),
            'location' => 'Remote',
            'job_type' => 'Part-time',
        ])->assertRedirect(route('employer.jobs.index'));

        // Verify both jobs exist under this employer
        $jobs = Job::where('employer_id', $user->id)->get();
        $this->assertCount(2, $jobs);
        $this->assertEquals('Job Number One', $jobs[0]->title);
        $this->assertEquals('Job Number Two', $jobs[1]->title);
    }

    /**
     * Test that active job offers display correctly to graduates.
     */
    public function test_all_active_jobs_display_correctly_to_graduates()
    {
        // Set up employer and active job
        $employerUser = User::factory()->create(['role' => 'employer']);
        $employer = Employer::create([
            'user_id' => $employerUser->id,
            'company_name' => 'Dynamic Tech Corp',
            'status' => 'approved',
        ]);

        $job = Job::create([
            'employer_id' => $employerUser->id,
            'title' => 'Dynamic Developer Role',
            'description' => 'Dynamic job description',
            'deadline' => now()->addDays(30),
            'location' => 'Marib',
            'job_type' => 'Full-time',
            'status' => 'active',
        ]);

        // Graduate login
        $graduateUser = User::factory()->create(['role' => 'graduate']);
        
        $response = $this->actingAs($graduateUser)->get('/graduate/jobs');
        
        $response->assertStatus(200);
        $response->assertSee('Dynamic Developer Role');
        $response->assertSee('Dynamic Tech Corp'); // Confirms the view renders the company name, not personal name
    }

    /**
     * Test that jobs are isolated so employers only see their own listings in their dashboard.
     */
    public function test_employers_only_see_their_own_jobs_in_dashboard()
    {
        // Employer 1
        $emp1User = User::factory()->create(['role' => 'employer']);
        Employer::create(['user_id' => $emp1User->id, 'company_name' => 'Company Alpha', 'status' => 'approved']);
        Job::create([
            'employer_id' => $emp1User->id,
            'title' => 'Alpha Job',
            'description' => 'Alpha desc',
            'deadline' => now()->addDays(10),
            'location' => 'Remote',
            'job_type' => 'Full-time',
            'status' => 'active',
        ]);

        // Employer 2
        $emp2User = User::factory()->create(['role' => 'employer']);
        Employer::create(['user_id' => $emp2User->id, 'company_name' => 'Company Beta', 'status' => 'approved']);
        Job::create([
            'employer_id' => $emp2User->id,
            'title' => 'Beta Job',
            'description' => 'Beta desc',
            'deadline' => now()->addDays(10),
            'location' => 'Remote',
            'job_type' => 'Full-time',
            'status' => 'active',
        ]);

        // Verify Employer 1 list
        $response1 = $this->actingAs($emp1User)->get('/employer/jobs');
        $response1->assertStatus(200);
        $response1->assertSee('Alpha Job');
        $response1->assertDontSee('Beta Job');

        // Verify Employer 2 list
        $response2 = $this->actingAs($emp2User)->get('/employer/jobs');
        $response2->assertStatus(200);
        $response2->assertSee('Beta Job');
        $response2->assertDontSee('Alpha Job');
    }
}
