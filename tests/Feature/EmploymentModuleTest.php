<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmploymentModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test employer registration sets status to pending and redirects.
     */
    public function test_employer_registration_sets_pending_status()
    {
        Notification::fake();

        $response = $this->post('/register/employer', [
            'name' => 'John Employer',
            'email' => 'john@employer.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Employer Corp',
        ]);

        $response->assertRedirect(route('employer.pending'));

        $this->assertDatabaseHas('employers', [
            'company_name' => 'Employer Corp',
            'status' => 'pending',
        ]);
    }

    /**
     * Test pending employer cannot access dashboard.
     */
    public function test_pending_employer_cannot_access_dashboard()
    {
        $user = User::factory()->create(['role' => 'employer']);
        $employer = Employer::create([
            'user_id' => $user->id,
            'company_name' => 'Pending Corp',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('employer.dashboard'));
        $response->assertRedirect(route('employer.pending'));
    }

    /**
     * Test approved employer can access dashboard.
     */
    public function test_approved_employer_can_access_dashboard()
    {
        $user = User::factory()->create(['role' => 'employer']);
        $employer = Employer::create([
            'user_id' => $user->id,
            'company_name' => 'Approved Corp',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->get(route('employer.dashboard'));
        $response->assertStatus(200);
    }

    /**
     * Test employment officer can approve employer.
     */
    public function test_employment_officer_can_approve_employer()
    {
        $officer = User::factory()->create(['role' => 'employment_officer']);
        $employerUser = User::factory()->create(['role' => 'employer']);
        $employer = Employer::create([
            'user_id' => $employerUser->id,
            'company_name' => 'Test Corp',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($officer)->post(route('admin.employers.approve', $employerUser->id));

        $response->assertRedirect();
        $this->assertEquals('approved', $employer->fresh()->status);
    }

    /**
     * Test job posted by approved employer defaults to pending.
     */
    public function test_job_posted_defaults_to_pending()
    {
        $user = User::factory()->create(['role' => 'employer']);
        $employer = Employer::create([
            'user_id' => $user->id,
            'company_name' => 'Test Corp',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->post('/employer/jobs', [
            'title' => 'Software Engineer',
            'description' => 'Great job role',
            'requirements' => 'Laravel experience',
            'deadline' => now()->addDays(10)->format('Y-m-d'),
            'location' => 'Remote',
            'job_type' => 'Full-time',
        ]);

        $response->assertRedirect(route('employer.jobs.index'));
        $this->assertDatabaseHas('portal_jobs', [
            'title' => 'Software Engineer',
            'status' => 'pending',
        ]);
    }

    /**
     * Test employment officer can approve a job.
     */
    public function test_employment_officer_can_approve_job()
    {
        $officer = User::factory()->create(['role' => 'employment_officer']);
        $employerUser = User::factory()->create(['role' => 'employer']);
        
        $job = Job::create([
            'employer_id' => $employerUser->id,
            'title' => 'Laravel Developer',
            'description' => 'Build stuff',
            'deadline' => now()->addDays(10),
            'location' => 'Marib',
            'job_type' => 'Full-time',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($officer)->post(route('admin.employment.jobs.approve', $job->id));

        $response->assertRedirect();
        $this->assertEquals('active', $job->fresh()->status);
    }

    /**
     * Test employer can manage application pipeline.
     */
    public function test_employer_can_update_application_status()
    {
        $employerUser = User::factory()->create(['role' => 'employer']);
        $employer = Employer::create([
            'user_id' => $employerUser->id,
            'company_name' => 'Test Corp',
            'status' => 'approved',
        ]);

        $job = Job::create([
            'employer_id' => $employerUser->id,
            'title' => 'PHP Developer',
            'description' => 'Build PHP apps',
            'deadline' => now()->addDays(10),
            'location' => 'Remote',
            'job_type' => 'Full-time',
            'status' => 'active',
        ]);

        $graduate = User::factory()->create(['role' => 'graduate']);

        $application = JobApplication::create([
            'job_id' => $job->id,
            'graduate_id' => $graduate->id,
            'cover_letter' => 'I love PHP',
            'status' => 'new',
        ]);

        // Employer updates status to shortlisted
        $response = $this->actingAs($employerUser)->post(route('employer.applications.status', $application->id), [
            'status' => 'shortlisted',
            'employer_notes' => 'Strong candidate profile',
        ]);

        $response->assertRedirect();
        $this->assertEquals('shortlisted', $application->fresh()->status);
        $this->assertEquals('Strong candidate profile', $application->fresh()->employer_notes);
    }
}
