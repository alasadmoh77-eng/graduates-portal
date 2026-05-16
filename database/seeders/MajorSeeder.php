<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Major;

class MajorSeeder extends Seeder
{
    public function run(): void
    {
        $majors = [
            ['name_ar' => 'علوم الحاسوب', 'name_en' => 'Computer Science'],
            ['name_ar' => 'هندسة البرمجيات', 'name_en' => 'Software Engineering'],
            ['name_ar' => 'نظم المعلومات', 'name_en' => 'Information Systems'],
            ['name_ar' => 'إدارة أعمال', 'name_en' => 'Business Administration'],
            ['name_ar' => 'المحاسبة', 'name_en' => 'Accounting'],
            ['name_ar' => 'هندسة مدنية', 'name_en' => 'Civil Engineering'],
            ['name_ar' => 'هندسة كهربائية', 'name_en' => 'Electrical Engineering'],
            ['name_ar' => 'الطب البشري', 'name_en' => 'Medicine'],
            ['name_ar' => 'الصيدلة', 'name_en' => 'Pharmacy'],
            ['name_ar' => 'اللغة الإنجليزية', 'name_en' => 'English Language'],
            ['name_ar' => 'الرياضيات', 'name_en' => 'Mathematics'],
            ['name_ar' => 'الفيزياء', 'name_en' => 'Physics'],
        ];
        foreach ($majors as $major) { 
            Major::updateOrCreate(['name_en' => $major['name_en']], $major); 
        }
    }
}
