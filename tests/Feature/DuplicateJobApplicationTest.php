<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employer;
use App\Models\Job;
use App\Models\Graduate;
use App\Models\Major;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DuplicateJobApplicationTest extends TestCase
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
            'cv_path' => 'cvs/test.pdf', // default cv path
        ]);

        Storage::disk('public')->put('cvs/test.pdf', 'dummy content');

        return $user;
    }

    /**
     * اختبار: الخريج يستطيع التقديم لأول مرة بنجاح.
     */
    public function test_graduate_can_apply_for_first_time()
    {
        $graduateUser = $this->createGraduateUser('grad1@example.com', '2023001');

        $response = $this->actingAs($graduateUser)->post(route('graduate.jobs.apply', $this->job), [
            'cover_letter' => 'I would like to apply for this job.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'تم تقديم طلبك بنجاح!');

        $this->assertDatabaseHas('job_applications', [
            'job_id' => $this->job->id,
            'graduate_id' => $graduateUser->id,
        ]);
    }

    /**
     * اختبار: الخريج لا يستطيع التقديم مرتين على نفس الوظيفة.
     */
    public function test_graduate_cannot_apply_twice_for_same_job()
    {
        $graduateUser = $this->createGraduateUser('grad2@example.com', '2023002');

        // التقديم الأول
        $response1 = $this->actingAs($graduateUser)->post(route('graduate.jobs.apply', $this->job), [
            'cover_letter' => 'First application.',
        ]);
        $response1->assertRedirect();

        // التقديم الثاني
        $response2 = $this->actingAs($graduateUser)->post(route('graduate.jobs.apply', $this->job), [
            'cover_letter' => 'Second application.',
        ]);
        $response2->assertRedirect();
        $response2->assertSessionHas('error', 'لقد تقدمت لهذه الوظيفة مسبقًا.');

        // التأكد من أن قاعدة البيانات تحتوي على سجل تطبيق واحد فقط لهذا المستخدم
        $count = JobApplication::where('job_id', $this->job->id)
            ->where('graduate_id', $graduateUser->id)
            ->count();
        $this->assertEquals(1, $count);
    }

    /**
     * اختبار: خريجان مختلفان يستطيعان التقديم على نفس الوظيفة بنجاح.
     */
    public function test_different_graduates_can_apply_to_same_job()
    {
        $grad1 = $this->createGraduateUser('grad3@example.com', '2023003');
        $grad2 = $this->createGraduateUser('grad4@example.com', '2023004');

        // تقديم الخريج الأول
        $response1 = $this->actingAs($grad1)->post(route('graduate.jobs.apply', $this->job), [
            'cover_letter' => 'Grad 1 application.',
        ]);
        $response1->assertRedirect();

        // تقديم الخريج الثاني
        $response2 = $this->actingAs($grad2)->post(route('graduate.jobs.apply', $this->job), [
            'cover_letter' => 'Grad 2 application.',
        ]);
        $response2->assertRedirect();

        // التأكد من وجود كلا التطبيقين في قاعدة البيانات
        $this->assertDatabaseHas('job_applications', [
            'job_id' => $this->job->id,
            'graduate_id' => $grad1->id,
        ]);
        $this->assertDatabaseHas('job_applications', [
            'job_id' => $this->job->id,
            'graduate_id' => $grad2->id,
        ]);
    }
}
