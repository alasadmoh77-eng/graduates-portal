<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\GraduateProfileController;

Route::get('/', function() { return view('welcome'); });

// Language Switch
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) { session()->put('locale', $locale); }
    return back();
})->name('lang.switch');

// Auth Routes (Rate limited by default in Laravel 11 for login if configured)
Route::middleware('guest')->group(function() {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'registerGraduate']);
    
    Route::get('register/employer', [AuthController::class, 'showEmployerRegister'])->name('employer.register');
    Route::post('register/employer', [AuthController::class, 'registerEmployer']);
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public verification
Route::get('verify/{token}', [VerificationController::class, 'show'])->name('verify.show');
Route::get('verify', [VerificationController::class, 'show'])->name('verify.search');
Route::post('verify', [VerificationController::class, 'verify'])->name('verify.process');

// Authenticated Routes
Route::middleware(['auth'])->group(function() {
    
    // Graduate Routes
    Route::middleware('role:graduate')->prefix('graduate')->name('graduate.')->group(function() {
        Route::view('dashboard', 'graduate.dashboard')->name('dashboard');

        Route::get('profile', [GraduateProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/photo', [GraduateProfileController::class, 'showPhoto'])->name('profile.photo');
        Route::get('profile/edit', [GraduateProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [GraduateProfileController::class, 'update'])->name('profile.update');
        Route::get('profile/cv', [GraduateProfileController::class, 'downloadCv'])->name('profile.cv');
        
        Route::get('documents', [DocumentRequestController::class, 'index'])->name('documents.index');
        Route::get('documents/create', [DocumentRequestController::class, 'create'])->name('documents.create');
        Route::post('documents', [DocumentRequestController::class, 'store'])->name('documents.store');
        Route::get('documents/{document}', [DocumentRequestController::class, 'show'])->name('documents.show');
        Route::get('documents/{document}/download', [DocumentRequestController::class, 'download'])->name('documents.download');

        // Jobs for graduates
        Route::get('jobs', [\App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
        Route::get('jobs/{job}', [\App\Http\Controllers\JobController::class, 'show'])->name('jobs.show');
        Route::post('jobs/{job}/apply', [\App\Http\Controllers\JobController::class, 'apply'])
            ->name('jobs.apply');

        // Events for graduates
        Route::get('events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.index');
        Route::post('events/{event}/register', [\App\Http\Controllers\EventController::class, 'register'])->name('events.register');
    });

    // Short URLs for graduates: /jobs → /graduate/jobs (avoids 404 when visiting /jobs directly)
    Route::middleware('role:graduate')->group(function () {
        Route::redirect('/jobs', '/graduate/jobs', 302);
        Route::get('/jobs/{job}', function (\App\Models\Job $job) {
            return redirect()->route('graduate.jobs.show', $job);
        });
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->controller(\App\Http\Controllers\NotificationController::class)->group(function() {
        Route::get('/', 'index')->name('index');
        Route::post('/{id}/mark-read', 'markAsRead')->name('markRead');
        Route::get('/mark-all-read', 'markAllAsRead')->name('markAllRead');
    });

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function() {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Admin Request management
        Route::get('requests', [\App\Http\Controllers\Admin\RequestController::class, 'index'])->name('requests.index');
        Route::get('requests/{documentRequest}', [\App\Http\Controllers\Admin\RequestController::class, 'show'])->name('requests.show');
        Route::post('requests/{documentRequest}/status', [\App\Http\Controllers\Admin\RequestController::class, 'updateStatus'])->name('requests.status');
        Route::post('requests/{documentRequest}/generate-pdf', [\App\Http\Controllers\Admin\RequestController::class, 'generatePdf'])->name('requests.generate-pdf');

        // Reports & Analytics
        Route::prefix('reports')->name('reports.')->controller(\App\Http\Controllers\Admin\ReportController::class)->group(function() {
            Route::get('/requests', 'requests')->name('requests');
            Route::get('/requests/export', 'exportRequests')->name('requests.export');
            Route::get('/graduates', 'graduates')->name('graduates');
            Route::get('/graduates/export', 'exportGraduates')->name('graduates.export');
        });

        // Admin Job management
        Route::get('jobs', [\App\Http\Controllers\JobController::class, 'adminIndex'])->name('jobs.index');
        Route::post('jobs/{job}/moderate', [\App\Http\Controllers\JobController::class, 'moderate'])->name('jobs.moderate');

        // Admin Event management
        Route::get('events/create', [\App\Http\Controllers\EventController::class, 'adminCreate'])->name('events.create');
        Route::post('events', [\App\Http\Controllers\EventController::class, 'adminStore'])->name('events.store');
        Route::get('events', [\App\Http\Controllers\EventController::class, 'adminIndex'])->name('events.index');
        Route::get('events/{event}/registrations', [\App\Http\Controllers\EventController::class, 'adminRegistrations'])->name('events.registrations');
        Route::get('events/{event}/edit', [\App\Http\Controllers\EventController::class, 'adminEdit'])->name('events.edit');
        Route::put('events/{event}', [\App\Http\Controllers\EventController::class, 'adminUpdate'])->name('events.update');
        Route::post('events/{event}/cancel', [\App\Http\Controllers\EventController::class, 'adminCancel'])->name('events.cancel');
        Route::delete('events/{event}', [\App\Http\Controllers\EventController::class, 'adminDestroy'])->name('events.destroy');

        Route::get('graduates/{graduate}/academic-record', [\App\Http\Controllers\Admin\GraduateAcademicRecordController::class, 'edit'])
            ->name('graduates.academic-record.edit');
        Route::put('graduates/{graduate}/academic-record', [\App\Http\Controllers\Admin\GraduateAcademicRecordController::class, 'update'])
            ->name('graduates.academic-record.update');

        Route::redirect('academic-record-entry', '/admin/reports/graduates', 302)->name('academic-record.preview');

        Route::view('grades-certificate-entry', 'admin.grades-certificate.create')->name('grades-certificate.preview');
    });

    // Employer Routes
    Route::middleware('role:employer')->prefix('employer')->name('employer.')->group(function() {
        Route::view('dashboard', 'employer.dashboard')->name('dashboard');
        
        // Job management for employers
        Route::get('jobs', [\App\Http\Controllers\JobController::class, 'employerIndex'])->name('jobs.index');
        Route::get('jobs/create', [\App\Http\Controllers\JobController::class, 'create'])->name('jobs.create');
        Route::post('jobs', [\App\Http\Controllers\JobController::class, 'store'])->name('jobs.store');
        Route::get('applications', [\App\Http\Controllers\JobController::class, 'applicationsIndex'])->name('applications.index');
        Route::get('applications/{application}/cv', [\App\Http\Controllers\JobController::class, 'downloadCv'])->name('applications.cv');
    });
});
