<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentRequestController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\GraduateProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Employment\EmploymentDashboardController;
use App\Http\Controllers\Employment\EmployerManagementController;
use App\Http\Controllers\Employment\ApplicationController as EmploymentApplicationController;
use App\Http\Controllers\Employment\EmploymentAnalyticsController;
use App\Http\Controllers\Graduate\ApplicationTrackingController;

Route::get('/', function(\App\Services\PublicPortalStatsService $statsService) {
    $latestJobs = \App\Models\Job::where('status', 'active')
        ->with(['company', 'employer'])
        ->latest()
        ->limit(3)
        ->get();
    $publicStats = $statsService->getStats();
    return view('welcome', compact('latestJobs', 'publicStats'));
});

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
    
    Route::get('employers/register', [AuthController::class, 'showEmployerRegister'])->name('employer.register');
    Route::post('employers/register', [AuthController::class, 'registerEmployer']);
    Route::post('register/employer', [AuthController::class, 'registerEmployer']);
    Route::get('employers/login', [AuthController::class, 'showEmployerLogin'])->name('employer.login');
    Route::post('employers/login', [AuthController::class, 'employerLogin']);
    
    // حماية مسار API التحقق من الخريج بالتخمين (Rate Limiting: 10 طلب في الدقيقة)
    Route::middleware('throttle:10,1')
        ->get('api/check-graduate/{university_id}', [AuthController::class, 'checkGraduate']);
});

// Employer pending approval page (accessible without auth)
Route::get('employers/pending', function() {
    return view('employer.pending');
})->name('employer.pending');

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Public forgot-password informational page
Route::view('password/forgot', 'auth.forgot-password')->name('password.forgot');

// Public verification - محمية بحد معدل الطلبات (30 طلب/دقيقة للتحقق العام)
Route::middleware('throttle:30,1')->group(function () {
    Route::get('verify/{token}', [VerificationController::class, 'show'])->name('verify.show');
    Route::get('verify', [VerificationController::class, 'show'])->name('verify.search');
    Route::post('verify', [VerificationController::class, 'verify'])->name('verify.process');
});

// Public Alumni Pages
Route::prefix('alumni')->name('alumni.')->group(function () {
    Route::view('about', 'alumni.about')->name('about');
    Route::get('contact', [ContactController::class, 'show'])->name('contact');
    Route::post('contact', [ContactController::class, 'store'])->name('contact.store');
});

// Public Events & Training
Route::get('events', [\App\Http\Controllers\EventController::class, 'index'])->name('events.public');

// Authenticated Routes
Route::middleware(['auth'])->group(function() {
    
    // Graduate Routes
    Route::middleware('role:graduate')->prefix('graduate')->name('graduate.')->group(function() {
        Route::get('dashboard', [\App\Http\Controllers\GraduateDashboardController::class, 'index'])->name('dashboard');

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
        Route::post('documents/{document}/payment-proof', [DocumentRequestController::class, 'uploadPaymentProof'])->name('documents.payment-proof');
        // مسار محمي لعرض إثبات الدفع لصاحب الطلب فقط (الملف مخزن في قرص خاص ليس عام)
        Route::get('documents/{document}/view-proof', [DocumentRequestController::class, 'viewPaymentProof'])->name('documents.view-proof');

        // Jobs for graduates
        Route::get('jobs', [\App\Http\Controllers\JobController::class, 'index'])->name('jobs.index');
        Route::get('jobs/{job}', [\App\Http\Controllers\JobController::class, 'show'])->name('jobs.show');
        Route::post('jobs/{job}/apply', [\App\Http\Controllers\JobController::class, 'apply'])
            ->name('jobs.apply');

        // Employers directory for graduates
        Route::get('employers', [\App\Http\Controllers\Graduate\EmployerDirectoryController::class, 'index'])->name('employers.index');
        Route::get('employers/{employer}', [\App\Http\Controllers\Graduate\EmployerDirectoryController::class, 'show'])->name('employers.show');

        // Graduate Application Tracking
        Route::get('applications', [ApplicationTrackingController::class, 'index'])->name('applications.index');
        Route::get('applications/{application}', [ApplicationTrackingController::class, 'show'])->name('applications.show');

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

        // ── Admin Management (super_admin + admin only) ──────────────────────
        Route::middleware('admin.permission:super')->group(function () {
            Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
            Route::get('admins/create', [AdminController::class, 'create'])->name('admins.create');
            Route::post('admins', [AdminController::class, 'store'])->name('admins.store');
            Route::get('admins/{admin}/edit', [AdminController::class, 'edit'])->name('admins.edit');
            Route::put('admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
            Route::delete('admins/{admin}', [AdminController::class, 'destroy'])->name('admins.destroy');
            Route::patch('admins/{admin}/toggle-status', [AdminController::class, 'toggleStatus'])->name('admins.toggleStatus');
            Route::patch('admins/{admin}/signature/approve', [AdminController::class, 'approveSignature'])->name('admins.signature.approve');
            Route::patch('admins/{admin}/signature/revoke', [AdminController::class, 'revokeSignature'])->name('admins.signature.revoke');

            // Faculty management (admin + super_admin only)
            Route::get('faculties', [FacultyController::class, 'index'])->name('faculties.index');
            Route::get('faculties/create', [FacultyController::class, 'create'])->name('faculties.create');
            Route::post('faculties', [FacultyController::class, 'store'])->name('faculties.store');
            Route::get('faculties/{faculty}/edit', [FacultyController::class, 'edit'])->name('faculties.edit');
            Route::put('faculties/{faculty}', [FacultyController::class, 'update'])->name('faculties.update');
            Route::delete('faculties/{faculty}', [FacultyController::class, 'destroy'])->name('faculties.destroy');
            Route::post('faculties/{faculty}/toggle-status', [FacultyController::class, 'toggleStatus'])->name('faculties.toggle-status');

            // Admin Job management (admin + super_admin only)
            Route::get('jobs', [\App\Http\Controllers\JobController::class, 'adminIndex'])->name('jobs.index');
            Route::post('jobs/{job}/moderate', [\App\Http\Controllers\JobController::class, 'moderate'])->name('jobs.moderate');

            // Admin Event management (admin + super_admin only)
            Route::get('events/create', [\App\Http\Controllers\EventController::class, 'adminCreate'])->name('events.create');
            Route::post('events', [\App\Http\Controllers\EventController::class, 'adminStore'])->name('events.store');
            Route::get('events', [\App\Http\Controllers\EventController::class, 'adminIndex'])->name('events.index');
            Route::get('events/{event}/registrations', [\App\Http\Controllers\EventController::class, 'adminRegistrations'])->name('events.registrations');
            Route::get('events/{event}/edit', [\App\Http\Controllers\EventController::class, 'adminEdit'])->name('events.edit');
            Route::put('events/{event}', [\App\Http\Controllers\EventController::class, 'adminUpdate'])->name('events.update');
            Route::post('events/{event}/cancel', [\App\Http\Controllers\EventController::class, 'adminCancel'])->name('events.cancel');
            Route::delete('events/{event}', [\App\Http\Controllers\EventController::class, 'adminDestroy'])->name('events.destroy');

            // Freeze / Unfreeze / Password Reset / Clear Test Data for Graduate Registry
            Route::patch('graduate-registry/{approvedGraduate}/freeze-account', [\App\Http\Controllers\Admin\ApprovedGraduateController::class, 'freezeAccount'])->name('graduate-registry.freeze-account');
            Route::patch('graduate-registry/{approvedGraduate}/password', [\App\Http\Controllers\Admin\GraduatePasswordController::class, 'update'])->name('graduate-registry.password');
            Route::patch('graduate-registry/{approvedGraduate}/unfreeze-account', [\App\Http\Controllers\Admin\ApprovedGraduateController::class, 'unfreezeAccount'])->name('graduate-registry.unfreeze-account');
            Route::delete('graduate-registry/clear-test-data', [\App\Http\Controllers\Admin\ApprovedGraduateController::class, 'clearTestData'])->name('graduate-registry.clear-test-data');
        });
        
            // Admin Contact Messages
            Route::get('contact-messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
            Route::patch('contact-messages/{contactMessage}/read', [\App\Http\Controllers\Admin\ContactMessageController::class, 'markRead'])->name('contact-messages.read');

            // Document Fees Management
            Route::get('document-fees', [\App\Http\Controllers\Admin\DocumentFeeController::class, 'index'])->name('document-fees.index');
            Route::post('document-fees/{documentType}', [\App\Http\Controllers\Admin\DocumentFeeController::class, 'update'])->name('document-fees.update');

            // Admin Academic management (admin + super_admin + academic_admin)
        Route::middleware('admin.permission:academic')->group(function() {
            // Admin Request management
            Route::get('requests', [\App\Http\Controllers\Admin\RequestController::class, 'index'])->name('requests.index');
            Route::get('requests/{documentRequest}', [\App\Http\Controllers\Admin\RequestController::class, 'show'])->name('requests.show');
            Route::post('requests/{documentRequest}/status', [\App\Http\Controllers\Admin\RequestController::class, 'updateStatus'])->name('requests.status');
            Route::post('requests/{documentRequest}/generate-pdf', [\App\Http\Controllers\Admin\RequestController::class, 'generatePdf'])->name('requests.generate-pdf');
            Route::post('requests/{documentRequest}/send-for-signatures', [\App\Http\Controllers\Admin\RequestController::class, 'sendForSignatures'])->name('requests.send-for-signatures');
            Route::get('requests/{documentRequest}/download-pdf', [\App\Http\Controllers\Admin\RequestController::class, 'downloadPdf'])->name('requests.download-pdf');

            // Reports & Analytics (admin + super_admin + academic_admin)
            Route::prefix('reports')->name('reports.')->controller(\App\Http\Controllers\Admin\ReportController::class)->group(function() {
                Route::get('/requests', 'requests')->name('requests');
                Route::get('/requests/export', 'exportRequests')->name('requests.export');
                Route::get('/graduates', 'graduates')->name('graduates');
                Route::get('/graduates/export', 'exportGraduates')->name('graduates.export');
            });

            Route::get('graduates/{graduate}', [\App\Http\Controllers\Admin\GraduateController::class, 'show'])
                ->name('graduates.show');

            Route::get('graduates/{graduate}/academic-record', [\App\Http\Controllers\Admin\GraduateAcademicRecordController::class, 'edit'])
                ->name('graduates.academic-record.edit');
            Route::put('graduates/{graduate}/academic-record', [\App\Http\Controllers\Admin\GraduateAcademicRecordController::class, 'update'])
                ->name('graduates.academic-record.update');

            Route::get('graduates/{graduate}/grades-certificate', [\App\Http\Controllers\Admin\GradesCertificateController::class, 'edit'])
                ->name('graduates.grades-certificate.edit');
            Route::put('graduates/{graduate}/grades-certificate', [\App\Http\Controllers\Admin\GradesCertificateController::class, 'update'])
                ->name('graduates.grades-certificate.update');

            Route::get('graduates/{graduate}/academic-record/download', function(\App\Models\User $graduate) {
                abort_unless($graduate->role === 'graduate' && $graduate->graduate !== null, 404);
                $provider = app(\App\Contracts\StudentInformationProvider::class);
                $academicRecord = $provider->getAcademicRecordWithDetails($graduate);
                if (!$academicRecord) {
                    return back()->with('error', 'السجل الأكاديمي غير متوفر لهذا الطالب.');
                }
                $pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.documents.academic_record.ar', [
                    'request' => (object)[
                        'user' => $graduate,
                        'tracking_code' => 'DOC-' . strtoupper(Illuminate\Support\Str::random(8)),
                    ],
                    'serial_number' => 'SRU-DOC-' . date('Y') . '-00001',
                    'qr_code' => base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->margin(1)->generate(rtrim(config('app.url'), '/') . '/verify/' . Illuminate\Support\Str::random(40))),
                    'qr_token' => Illuminate\Support\Str::random(40),
                    'issue_date' => now()->format('Y-m-d'),
                    'academic_record' => $academicRecord,
                ]);
                return $pdf->download("Academic-Record-{$graduate->graduate->university_id}.pdf");
            })->name('graduates.academic-record.download');

            Route::get('graduates/{graduate}/grades-certificate/download', function(\App\Models\User $graduate) {
                abort_unless($graduate->role === 'graduate' && $graduate->graduate !== null, 404);
                $provider = app(\App\Contracts\StudentInformationProvider::class);
                $academicRecord = $provider->getAcademicRecordWithDetails($graduate);
                if (!$academicRecord) {
                    return back()->with('error', 'السجل الأكاديمي غير متوفر لهذا الطالب.');
                }
                $pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.documents.grades_certificate.ar', [
                    'request' => (object)[
                        'user' => $graduate,
                        'tracking_code' => 'DOC-' . strtoupper(Illuminate\Support\Str::random(8)),
                    ],
                    'serial_number' => 'SRU-DOC-' . date('Y') . '-00001',
                    'qr_code' => base64_encode(SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(200)->margin(1)->generate(rtrim(config('app.url'), '/') . '/verify/' . Illuminate\Support\Str::random(40))),
                    'qr_token' => Illuminate\Support\Str::random(40),
                    'issue_date' => now()->format('Y-m-d'),
                    'academic_record' => $academicRecord,
                ]);
                return $pdf->download("Grades-Certificate-{$graduate->graduate->university_id}.pdf");
            })->name('graduates.grades-certificate.download');

            // Graduate Registry Management
            Route::get('graduate-registry', [\App\Http\Controllers\Admin\ApprovedGraduateController::class, 'index'])->name('graduate-registry.index');
            Route::post('graduate-registry/import', [\App\Http\Controllers\Admin\ApprovedGraduateController::class, 'import'])->name('graduate-registry.import');
            Route::get('graduate-registry/template', [\App\Http\Controllers\Admin\ApprovedGraduateController::class, 'downloadTemplate'])->name('graduate-registry.template');

             // Excel Academic Record Import
            Route::get('academic-record-import', [\App\Http\Controllers\Admin\AcademicRecordImportController::class, 'showImportForm'])->name('academic-records.import-form');
            Route::post('academic-record-import', [\App\Http\Controllers\Admin\AcademicRecordImportController::class, 'import'])->name('academic-records.import');
            Route::get('academic-record-template', [\App\Http\Controllers\Admin\AcademicRecordImportController::class, 'downloadTemplate'])->name('academic-records.template');

            // Digital Signatures
            Route::get('pending-signatures', [\App\Http\Controllers\Admin\SignatureController::class, 'pendingSignatures'])->name('pending-signatures');
            Route::get('ready-signatures', [\App\Http\Controllers\Admin\SignatureController::class, 'readySignatures'])->name('ready-signatures');
            Route::get('ready-signatures/export', [\App\Http\Controllers\Admin\SignatureController::class, 'exportSignatures'])->name('ready-signatures.export');
            Route::post('documents/{issuedDocument}/sign', [\App\Http\Controllers\Admin\SignatureController::class, 'signDocument'])->name('documents.sign');
            Route::post('documents/{issuedDocument}/approve-issue', [\App\Http\Controllers\Admin\SignatureController::class, 'approveAndIssue'])->name('documents.approve-issue');
            Route::post('documents/{issuedDocument}/reissue', [\App\Http\Controllers\Admin\SignatureController::class, 'reissue'])->name('documents.reissue');
        });

        // Signature image upload (available to all admin roles)
        Route::post('profile/signature', [\App\Http\Controllers\Admin\SignatureController::class, 'uploadSignature'])->name('profile.signature.upload');

        // Payment Review (finance admin + super admin)
        Route::middleware('admin.permission:finance')->group(function() {
            Route::get('payments', [\App\Http\Controllers\Admin\PaymentReviewController::class, 'index'])->name('payments.index');
            Route::get('payments/{documentRequest}/proof', [\App\Http\Controllers\Admin\PaymentReviewController::class, 'showProof'])->name('payments.proof');
            Route::post('payments/{documentRequest}/approve', [\App\Http\Controllers\Admin\PaymentReviewController::class, 'approve'])->name('payments.approve');
            Route::post('payments/{documentRequest}/reject', [\App\Http\Controllers\Admin\PaymentReviewController::class, 'reject'])->name('payments.reject');
        });

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
        Route::get('applications/{application}', [\App\Http\Controllers\JobController::class, 'showApplication'])->name('applications.show');
        Route::get('applications/{application}/cv', [\App\Http\Controllers\JobController::class, 'downloadCv'])->name('applications.cv');
        Route::post('applications/{application}/status', [\App\Http\Controllers\JobController::class, 'updateApplicationStatus'])->name('applications.status');
    });

    // Employment Officer Routes (under /admin prefix, admin middleware already applied above)
    Route::middleware('admin.permission:employment')->prefix('admin')->name('admin.')->group(function() {
        // Dashboard
        Route::get('employment/dashboard', [EmploymentDashboardController::class, 'index'])->name('employment.dashboard');

        // Employer management
        Route::get('employers', [EmployerManagementController::class, 'index'])->name('employers.index');
        Route::get('employers/{employer}', [EmployerManagementController::class, 'show'])->name('employers.show');
        Route::post('employers/{employer}/approve', [EmployerManagementController::class, 'approve'])->name('employers.approve');
        Route::post('employers/{employer}/reject', [EmployerManagementController::class, 'reject'])->name('employers.reject');
        Route::post('employers/{employer}/suspend', [EmployerManagementController::class, 'suspend'])->name('employers.suspend');
        Route::post('employers/{employer}/reactivate', [EmployerManagementController::class, 'reactivate'])->name('employers.reactivate');

        // Job moderation
        Route::get('employment/jobs', [\App\Http\Controllers\JobController::class, 'employmentOfficerIndex'])->name('employment.jobs.index');
        Route::post('employment/jobs/{job}/approve', [\App\Http\Controllers\JobController::class, 'approveJob'])->name('employment.jobs.approve');
        Route::post('employment/jobs/{job}/reject', [\App\Http\Controllers\JobController::class, 'rejectJob'])->name('employment.jobs.reject');
        Route::post('employment/jobs/{job}/close', [\App\Http\Controllers\JobController::class, 'closeJob'])->name('employment.jobs.close');

        // Application oversight
        Route::get('employment/applications', [EmploymentApplicationController::class, 'index'])->name('employment.applications.index');
        Route::get('employment/applications/{application}', [EmploymentApplicationController::class, 'show'])->name('employment.applications.show');
        Route::post('employment/applications/{application}/status', [EmploymentApplicationController::class, 'updateStatus'])->name('employment.applications.status');
        Route::post('employment/applications/{application}/interview', [EmploymentApplicationController::class, 'scheduleInterview'])->name('employment.applications.interview');

        // Analytics
        Route::get('employment/analytics', [EmploymentAnalyticsController::class, 'index'])->name('employment.analytics');
    });
});

// Employment Officer root redirect (outside the admin block above)
Route::middleware('auth')->get('employment/dashboard', function() {
    return redirect()->route('admin.employment.dashboard');
})->name('employment.dashboard');


