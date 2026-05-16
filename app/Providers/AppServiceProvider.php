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
        //
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

        Route::bind('graduate', function (string $value) {
            return User::query()
                ->where('id', $value)
                ->where('role', 'graduate')
                ->firstOrFail();
        });
    }
}
