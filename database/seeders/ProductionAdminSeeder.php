<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProductionAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name = env('ADMIN_NAME', 'مدير النظام');

        if (empty($email) || empty($password)) {
            $this->command?->warn(
                'لم يتم إنشاء المدير لأن ADMIN_EMAIL أو ADMIN_PASSWORD غير موجود.'
            );

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info('تم إنشاء أو تحديث حساب مدير النظام.');
    }
}