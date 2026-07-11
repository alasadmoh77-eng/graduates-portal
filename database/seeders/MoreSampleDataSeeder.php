<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Job;
use App\Models\Event;
use App\Models\User;

class MoreSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $employer = User::where('role', 'employer')->first();
        if ($employer) {
            Job::updateOrCreate(
                ['title' => 'Software Developer', 'employer_id' => $employer->id],
                [
                    'description' => 'We are looking for a fullstack developer to join our team.',
                    'requirements' => 'Laravel, Vue.js, MySQL',
                    'deadline' => now()->addDays(30),
                    'location' => 'Marib',
                    'job_type' => 'Full-time',
                    'status' => 'active',
                ]
            );
        }

        Event::updateOrCreate(
            ['title_en' => 'Career Path Workshop'],
            [
                'title_ar' => 'ورشة عمل المسار الوظيفي',
                'description_ar' => 'ورشة عمل لتمكين الخريجين من تحديد مساراتهم المهنية.',
                'description_en' => 'Workshop to empower graduates to define their career paths.',
                'start_at' => now()->addDays(10),
                'location' => 'Main Hall',
                'seats' => 50,
                'status' => 'upcoming',
            ]
        );
    }
}
