<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Pagination\Paginator;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\StudentInformation\StudentInformationService::class, function ($app) {
            return new \App\Services\StudentInformation\StudentInformationService($app);
        });

        $this->app->bind(
            \App\Contracts\StudentInformationProvider::class,
            \App\Services\StudentInformation\StudentInformationService::class
        );
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Global Gates for fast role checking
        Gate::define('isAdmin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('isEmployer', function (User $user) {
            return $user->role === 'employer';
        });

        Gate::define('isGraduate', function (User $user) {
            return $user->role === 'graduate';
        });

        Gate::define('edit-academic-record', function (User $user) {
            if (in_array($user->role, ['admin', 'super_admin'])) {
                return true;
            }
            if ($user->role === 'academic_admin' && in_array($user->signer_role, [
                'المختص الأكاديمي',
                'المسجل العام',
                'مسجل الكلية',
                'مدير إدارة شؤون الخريجين',
            ])) {
                return true;
            }

            if ($user->role === 'academic_admin' && empty($user->signer_role)) {
                return true;
            }
            return false;
        });

        Route::bind('graduate', function (string $value) {
            return User::query()
                ->where('id', $value)
                ->where('role', 'graduate')
                ->firstOrFail();
        });
    }
}
