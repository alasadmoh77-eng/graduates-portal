<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employer;
use App\Models\Job;
use App\Models\Graduate;
use App\Models\Major;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JobFilledTest extends TestCase
{
    use RefreshDatabase;

    protected $employerUser;
    protected $job;
    protected $major;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Create Major
        $this->major = Major::create(['name_ar' => 'Test Major', 'name_en' => 'Test Major']);

        // Create Employer
        $this->employerUser = User::create([
            'name' => 'Test Employer',
            'email' => 'employer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'employer',
            'is_active' => true,
        ]);
        Employer::create([
            'user_id' => $this->employerUser->id,
            'company_name' => 'Company Alpha',
            'status' => 'approved',
        ]);

        // Create Active Job
        $this->job = Job::create([
            'employer_id' => $this->employerUser->id,
            'title' => 'Software Engineer',
            'description' => 'Write clean code.',
            'deadline' => now()->addDays(5),
            'location' => 'Marib',
            'job_type' => 'Full-time',
            'status' => 'active',
        ]);
    }

    /** Helper to create active graduate user */
    protected function createGraduateUser($email, $universityId)
    {
        $user = User::create([
            'name' => 'Graduate User ' . $universityId,
            'email' => $email,
            'password' => bcrypt('password123'),
            'role' => 'graduate',
            'is_active' => true,
        ]);

        Graduate::create([
            'user_id' => $user->id,
            'university_id' => $universityId,
            'major_id' => $this->major->id,
            'graduation_year' => 2023,
            'cv_path' => 'cvs/test.pdf',
        ]);

        Storage::disk('public')->put('cvs/test.pdf', 'dummy content');

        return $user;
    }

    /**
     * Test: A newly created job is not filled by default.
     */
    public function test_new_job_is_not_filled_by_default()
    {
        $this->assertFalse($this->job->is_filled);
        $this->assertNull($this->job->filled_at);
    }

    /**
     * Test: Employer accepting/hiring an applicant marks job as filled.
     */
    public function test_accepting_applicant_marks_job_as_filled()
    {
        $graduateUser = $this->createGraduateUser('grad1@example.com', '2023001');

        // Apply
        $application = JobApplication::create([
            'job_id' => $this->job->id,
            'graduate_id' => $graduateUser->id,
            'status' => 'new',
            'cover_letter' => 'Test application.',
        ]);

        // Hired status change by employer
        $response = $this->actingAs($this->employerUser)->post(route('employer.applications.status', $application), [
            'status' => 'hired',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'تم تحديث حالة الطلب بنجاح.');

        $this->job->refresh();
        $this->assertTrue($this->job->is_filled);
        $this->assertNotNull($this->job->filled_at);
        $this->assertEquals('hired', $application->refresh()->status);
    }

    /**
     * Test: Graduate cannot apply to filled job.
     */
    public function test_graduate_cannot_apply_to_filled_job()
    {
        // Mark job as filled first
        $this->job->update([
            'is_filled' => true,
            'filled_at' => now(),
        ]);

        $graduateUser = $this->createGraduateUser('grad2@example.com', '2023002');

        $response = $this->actingAs($graduateUser)->post(route('graduate.jobs.apply', $this->job), [
            'cover_letter' => 'Attempting to apply to a filled job.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'عذرًا، تم شغل هذه الوظيفة ولا يمكن التقديم عليها.');

        // Verify application was not created
        $this->assertDatabaseMissing('job_applications', [
            'job_id' => $this->job->id,
            'graduate_id' => $graduateUser->id,
        ]);
    }

    /**
     * Test: Employer cannot hire another applicant if the job is already filled.
     */
    public function test_employer_cannot_hire_another_applicant_if_already_filled()
    {
        $grad1 = $this->createGraduateUser('grad3@example.com', '2023003');
        $grad2 = $this->createGraduateUser('grad4@example.com', '2023004');

        $app1 = JobApplication::create([
            'job_id' => $this->job->id,
            'graduate_id' => $grad1->id,
            'status' => 'new',
        ]);

        $app2 = JobApplication::create([
            'job_id' => $this->job->id,
            'graduate_id' => $grad2->id,
            'status' => 'new',
        ]);

        // Hire the first applicant
        $response1 = $this->actingAs($this->employerUser)->post(route('employer.applications.status', $app1), [
            'status' => 'hired',
        ]);
        $response1->assertRedirect();
        $this->job->refresh();
        $this->assertTrue($this->job->is_filled);

        // Try hiring the second applicant
        $response2 = $this->actingAs($this->employerUser)->post(route('employer.applications.status', $app2), [
            'status' => 'hired',
        ]);
        $response2->assertRedirect();
        $response2->assertSessionHas('error', 'لا يمكن قبول متقدم جديد، لأن هذه الوظيفة تم شغلها بالفعل.');

        // Verify second application is still not hired
        $this->assertNotEquals('hired', $app2->refresh()->status);
    }
}
