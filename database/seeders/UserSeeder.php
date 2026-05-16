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
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@sru.edu.ye',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Demo Graduate
        $gradUser = User::create([
            'name' => 'Ali Ahmed',
            'email' => 'graduate@example.com',
            'password' => Hash::make('grad123'),
            'role' => 'graduate',
        ]);
        
        Graduate::create([
            'user_id' => $gradUser->id,
            'university_id' => '2023-1001',
            'phone' => '777123456',
            'major_id' => 1,
            'graduation_year' => 2023,
        ]);

        // Demo Employer
        $empUser = User::create([
            'name' => 'HR Manager',
            'email' => 'employer@example.com',
            'password' => Hash::make('emp123'),
            'role' => 'employer',
        ]);

        \App\Models\Employer::create([
            'user_id' => $empUser->id,
            'company_name' => 'Tech Solutions Ltd',
            'phone' => '770111222',
        ]);
    }
}
