<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create faculties table
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // 2. Insert the 7 faculties
        $faculties = [
            [
                'name_ar' => 'كلية الآداب والعلوم الإنسانية',
                'name_en' => 'Faculty of Arts and Humanities',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'كلية التربية والعلوم الإنسانية والتطبيقية - الجوف',
                'name_en' => 'Faculty of Education, Humanities and Applied Sciences – Al-Jawf',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'كلية التربية والعلوم',
                'name_en' => 'Faculty of Education and Sciences',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'كلية الشريعة والقانون',
                'name_en' => 'Faculty of Sharia and Law',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'كلية العلوم الإدارية والمالية',
                'name_en' => 'Faculty of Administrative and Financial Sciences',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'كلية تكنولوجيا المعلومات وعلوم الحاسوب',
                'name_en' => 'Faculty of Information Technology and Computer Science',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_ar' => 'كلية الطب والعلوم الصحية',
                'name_en' => 'Faculty of Medicine and Health Sciences',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('faculties')->insert($faculties);

        // 3. Modify majors table - add faculty_id
        Schema::table('majors', function (Blueprint $table) {
            $table->unsignedBigInteger('faculty_id')->nullable()->after('id');
        });

        // 4. Map existing seeded majors in database to their corresponding faculties
        $artsId = DB::table('faculties')->where('name_en', 'Faculty of Arts and Humanities')->value('id');
        $jawfId = DB::table('faculties')->where('name_en', 'Faculty of Education, Humanities and Applied Sciences – Al-Jawf')->value('id');
        $eduSciId = DB::table('faculties')->where('name_en', 'Faculty of Education and Sciences')->value('id');
        $shariaId = DB::table('faculties')->where('name_en', 'Faculty of Sharia and Law')->value('id');
        $adminId = DB::table('faculties')->where('name_en', 'Faculty of Administrative and Financial Sciences')->value('id');
        $itId = DB::table('faculties')->where('name_en', 'Faculty of Information Technology and Computer Science')->value('id');
        $medId = DB::table('faculties')->where('name_en', 'Faculty of Medicine and Health Sciences')->value('id');

        // Map Computer Science, Software Engineering, Information Systems to IT faculty
        DB::table('majors')->whereIn('name_en', ['Computer Science', 'Software Engineering', 'Information Systems'])
            ->update(['faculty_id' => $itId]);

        // Map Business Administration, Accounting to Administrative faculty
        DB::table('majors')->whereIn('name_en', ['Business Administration', 'Accounting'])
            ->update(['faculty_id' => $adminId]);

        // Map Medicine, Pharmacy to Medicine faculty
        DB::table('majors')->whereIn('name_en', ['Medicine', 'Pharmacy'])
            ->update(['faculty_id' => $medId]);

        // Map English Language to Arts faculty
        DB::table('majors')->where('name_en', 'English Language')
            ->update(['faculty_id' => $artsId]);

        // Map Mathematics, Physics to Education & Sciences
        DB::table('majors')->whereIn('name_en', ['Mathematics', 'Physics'])
            ->update(['faculty_id' => $eduSciId]);

        // Add Sharia and Law (الشريعة والقانون) major if it doesn't exist yet
        $hasLaw = DB::table('majors')->where('name_en', 'Sharia and Law')->exists();
        if (!$hasLaw) {
            DB::table('majors')->insert([
                'name_ar' => 'الشريعة والقانون',
                'name_en' => 'Sharia and Law',
                'faculty_id' => $shariaId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            DB::table('majors')->where('name_en', 'Sharia and Law')->update(['faculty_id' => $shariaId]);
        }

        // Set up foreign key constraint (if supported)
        Schema::table('majors', function (Blueprint $table) {
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('majors', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->dropColumn('faculty_id');
        });
        Schema::dropIfExists('faculties');
    }
};
