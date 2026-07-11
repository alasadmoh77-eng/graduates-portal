<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Graduate;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. General/Super Admin
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@sru.edu.ye'],
            [
                'name' => 'General Admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Academic Admin
        User::updateOrCreate(
            ['email' => 'academic@sru.edu.ye'],
            [
                'name' => 'Academic Registrar',
                'password' => Hash::make('academic123'),
                'role' => 'academic_admin',
                'is_active' => true,
            ]
        );

        // 3. Finance Admin
        User::updateOrCreate(
            ['email' => 'finance@sru.edu.ye'],
            [
                'name' => 'Finance Director',
                'password' => Hash::make('finance123'),
                'role' => 'finance_admin',
                'is_active' => true,
            ]
        );

        // 4. Employment Officer
        User::updateOrCreate(
            ['email' => 'employment@sru.edu.ye'],
            [
                'name' => 'Career Center Officer',
                'password' => Hash::make('employment123'),
                'role' => 'employment_officer',
                'is_active' => true,
            ]
        );

        // ── 8 Signatory Admins (academic_admin with specific signer_role) ──
        $signers = [
            ['email' => 'dean@sru.edu.ye',           'name' => 'د. عميد الكلية',            'signer_role' => 'عميد الكلية'],
            ['email' => 'registrar@sru.edu.ye',      'name' => 'أ. مسجل الكلية',            'signer_role' => 'مسجل الكلية'],
            ['email' => 'graduates@sru.edu.ye',      'name' => 'أ. مدير شؤون الخريجين',     'signer_role' => 'مدير إدارة شؤون الخريجين'],
            ['email' => 'specialist@sru.edu.ye',     'name' => 'أ. المختص الأكاديمي',       'signer_role' => 'المختص الأكاديمي'],
            ['email' => 'general@sru.edu.ye',        'name' => 'د. المسجل العام',           'signer_role' => 'المسجل العام'],
            ['email' => 'vp@sru.edu.ye',             'name' => 'أ.د. نائب رئيس الجامعة',    'signer_role' => 'نائب رئيس الجامعة لشؤون الطلاب'],
        ];

        foreach ($signers as $signer) {
            User::updateOrCreate(
                ['email' => $signer['email']],
                [
                    'name'        => $signer['name'],
                    'password'    => Hash::make('password123'),
                    'role'        => 'academic_admin',
                    'is_active'   => true,
                    'signer_role' => $signer['signer_role'],
                ]
            );
        }

        // 5. Demo Graduate
        $gradUser = User::updateOrCreate(
            ['email' => 'graduate@example.com'],
            [
                'name' => 'Ali Ahmed',
                'password' => Hash::make('grad123'),
                'role' => 'graduate',
                'is_active' => true,
            ]
        );

        Graduate::updateOrCreate(
            ['user_id' => $gradUser->id],
            [
                'university_id' => '2023-1001',
                'phone' => '777123456',
                'major_id' => 1,
                'graduation_year' => 2023,
            ]
        );

        // Also add approved graduate entry for Ali Ahmed to allow registration/re-auth if needed
        \App\Models\ApprovedGraduate::updateOrCreate(
            ['university_id' => '2023-1001'],
            [
                'name' => 'Ali Ahmed',
                'email' => 'graduate@example.com',
                'major' => 'علوم الحاسوب',
                'graduation_year' => 2023,
            ]
        );

        // 6. Demo Employer
        $empUser = User::updateOrCreate(
            ['email' => 'employer@example.com'],
            [
                'name' => 'HR Manager',
                'password' => Hash::make('emp123'),
                'role' => 'employer',
                'is_active' => true,
            ]
        );

        \App\Models\Employer::updateOrCreate(
            ['user_id' => $empUser->id],
            [
                'company_name' => 'Tech Solutions Ltd',
                'phone' => '770111222',
                'status' => 'approved',
            ]
        );
    }
}
