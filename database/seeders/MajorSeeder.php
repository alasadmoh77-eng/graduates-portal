<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Major;

class MajorSeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            [
                'name_ar' => 'كلية الآداب والعلوم الإنسانية',
                'name_en' => 'Faculty of Arts and Humanities',
            ],
            [
                'name_ar' => 'كلية التربية والعلوم الإنسانية والتطبيقية - الجوف',
                'name_en' => 'Faculty of Education, Humanities and Applied Sciences – Al-Jawf',
            ],
            [
                'name_ar' => 'كلية التربية والعلوم',
                'name_en' => 'Faculty of Education and Sciences',
            ],
            [
                'name_ar' => 'كلية الشريعة والقانون',
                'name_en' => 'Faculty of Sharia and Law',
            ],
            [
                'name_ar' => 'كلية العلوم الإدارية والمالية',
                'name_en' => 'Faculty of Administrative and Financial Sciences',
            ],
            [
                'name_ar' => 'كلية تكنولوجيا المعلومات وعلوم الحاسوب',
                'name_en' => 'Faculty of Information Technology and Computer Science',
            ],
            [
                'name_ar' => 'كلية الطب والعلوم الصحية',
                'name_en' => 'Faculty of Medicine and Health Sciences',
            ],
        ];

        $facultyIds = [];
        foreach ($faculties as $f) {
            $faculty = Faculty::updateOrCreate(['name_en' => $f['name_en']], $f);
            $facultyIds[$f['name_en']] = $faculty->id;
        }

        $majors = [
            [
                'name_ar' => 'علوم الحاسوب',
                'name_en' => 'Computer Science',
                'faculty_id' => $facultyIds['Faculty of Information Technology and Computer Science'],
                'degree_name_ar' => 'بكالوريوس علوم الحاسوب',
                'degree_name_en' => 'Bachelor of Computer Science',
            ],
            [
                'name_ar' => 'هندسة البرمجيات',
                'name_en' => 'Software Engineering',
                'faculty_id' => $facultyIds['Faculty of Information Technology and Computer Science'],
                'degree_name_ar' => 'بكالوريوس هندسة البرمجيات',
                'degree_name_en' => 'Bachelor of Software Engineering',
            ],
            [
                'name_ar' => 'نظم المعلومات',
                'name_en' => 'Information Systems',
                'faculty_id' => $facultyIds['Faculty of Information Technology and Computer Science'],
                'degree_name_ar' => 'بكالوريوس نظم المعلومات',
                'degree_name_en' => 'Bachelor of Information Systems',
            ],
            [
                'name_ar' => 'إدارة أعمال',
                'name_en' => 'Business Administration',
                'faculty_id' => $facultyIds['Faculty of Administrative and Financial Sciences'],
                'degree_name_ar' => 'بكالوريوس إدارة الأعمال',
                'degree_name_en' => 'Bachelor of Business Administration',
            ],
            [
                'name_ar' => 'المحاسبة',
                'name_en' => 'Accounting',
                'faculty_id' => $facultyIds['Faculty of Administrative and Financial Sciences'],
                'degree_name_ar' => 'بكالوريوس المحاسبة',
                'degree_name_en' => 'Bachelor of Accounting',
            ],
            [
                'name_ar' => 'هندسة مدنية',
                'name_en' => 'Civil Engineering',
                'faculty_id' => $facultyIds['Faculty of Education, Humanities and Applied Sciences – Al-Jawf'],
                'degree_name_ar' => 'بكالوريوس الهندسة المدنية',
                'degree_name_en' => 'Bachelor of Civil Engineering',
            ],
            [
                'name_ar' => 'هندسة كهربائية',
                'name_en' => 'Electrical Engineering',
                'faculty_id' => $facultyIds['Faculty of Education, Humanities and Applied Sciences – Al-Jawf'],
                'degree_name_ar' => 'بكالوريوس الهندسة الكهربائية',
                'degree_name_en' => 'Bachelor of Engineering in Electrical Engineering',
            ],
            [
                'name_ar' => 'الطب البشري',
                'name_en' => 'Medicine',
                'faculty_id' => $facultyIds['Faculty of Medicine and Health Sciences'],
                'degree_name_ar' => 'بكالوريوس الطب البشري والجراحة',
                'degree_name_en' => 'Bachelor of Medicine and Bachelor of Surgery',
            ],
            [
                'name_ar' => 'الصيدلة',
                'name_en' => 'Pharmacy',
                'faculty_id' => $facultyIds['Faculty of Medicine and Health Sciences'],
                'degree_name_ar' => 'بكالوريوس الصيدلة',
                'degree_name_en' => 'Bachelor of Pharmacy',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية',
                'name_en' => 'English Language',
                'faculty_id' => $facultyIds['Faculty of Arts and Humanities'],
                'degree_name_ar' => 'بكالوريوس آداب لغة إنجليزية',
                'degree_name_en' => 'Bachelor of Arts in English Language',
            ],
            [
                'name_ar' => 'الرياضيات',
                'name_en' => 'Mathematics',
                'faculty_id' => $facultyIds['Faculty of Education and Sciences'],
                'degree_name_ar' => 'بكالوريوس التربية والعلوم في الرياضيات',
                'degree_name_en' => 'Bachelor of Education in Mathematics',
            ],
            [
                'name_ar' => 'الفيزياء',
                'name_en' => 'Physics',
                'faculty_id' => $facultyIds['Faculty of Education and Sciences'],
                'degree_name_ar' => 'بكالوريوس التربية والعلوم في الفيزياء',
                'degree_name_en' => 'Bachelor of Education in Physics',
            ],
            [
                'name_ar' => 'الشريعة والقانون',
                'name_en' => 'Sharia and Law',
                'faculty_id' => $facultyIds['Faculty of Sharia and Law'],
                'degree_name_ar' => 'بكالوريوس الشريعة والقانون',
                'degree_name_en' => 'Bachelor of Sharia and Law',
            ],
        ];

        foreach ($majors as $major) { 
            Major::updateOrCreate(['name_en' => $major['name_en']], $major); 
        }
    }
}
