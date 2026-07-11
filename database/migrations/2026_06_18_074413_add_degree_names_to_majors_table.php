<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('majors', function (Blueprint $table) {
            $table->string('degree_name_ar')->nullable()->after('name_en');
            $table->string('degree_name_en')->nullable()->after('degree_name_ar');
        });

        // Seed/Update existing majors with correct degrees
        $degrees = [
            'Computer Science' => [
                'degree_name_ar' => 'بكالوريوس علوم الحاسوب',
                'degree_name_en' => 'Bachelor of Computer Science',
            ],
            'Software Engineering' => [
                'degree_name_ar' => 'بكالوريوس هندسة البرمجيات',
                'degree_name_en' => 'Bachelor of Software Engineering',
            ],
            'Information Systems' => [
                'degree_name_ar' => 'بكالوريوس نظم المعلومات',
                'degree_name_en' => 'Bachelor of Information Systems',
            ],
            'Business Administration' => [
                'degree_name_ar' => 'بكالوريوس إدارة الأعمال',
                'degree_name_en' => 'Bachelor of Business Administration',
            ],
            'Accounting' => [
                'degree_name_ar' => 'بكالوريوس المحاسبة',
                'degree_name_en' => 'Bachelor of Accounting',
            ],
            'Civil Engineering' => [
                'degree_name_ar' => 'بكالوريوس الهندسة المدنية',
                'degree_name_en' => 'Bachelor of Civil Engineering',
            ],
            'Electrical Engineering' => [
                'degree_name_ar' => 'بكالوريوس الهندسة الكهربائية',
                'degree_name_en' => 'Bachelor of Engineering in Electrical Engineering',
            ],
            'Medicine' => [
                'degree_name_ar' => 'بكالوريوس الطب البشري والجراحة',
                'degree_name_en' => 'Bachelor of Medicine and Bachelor of Surgery',
            ],
            'Pharmacy' => [
                'degree_name_ar' => 'بكالوريوس الصيدلة',
                'degree_name_en' => 'Bachelor of Pharmacy',
            ],
            'English Language' => [
                'degree_name_ar' => 'بكالوريوس آداب لغة إنجليزية',
                'degree_name_en' => 'Bachelor of Arts in English Language',
            ],
            'Mathematics' => [
                'degree_name_ar' => 'بكالوريوس التربية والعلوم في الرياضيات',
                'degree_name_en' => 'Bachelor of Education in Mathematics',
            ],
            'Physics' => [
                'degree_name_ar' => 'بكالوريوس التربية والعلوم في الفيزياء',
                'degree_name_en' => 'Bachelor of Education in Physics',
            ],
            'Sharia and Law' => [
                'degree_name_ar' => 'بكالوريوس الشريعة والقانون',
                'degree_name_en' => 'Bachelor of Sharia and Law',
            ],
        ];

        foreach ($degrees as $nameEn => $degreeData) {
            DB::table('majors')
                ->where('name_en', $nameEn)
                ->update($degreeData);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('majors', function (Blueprint $table) {
            $table->dropColumn(['degree_name_ar', 'degree_name_en']);
        });
    }
};
