<?php
namespace App\Http\Controllers\Employment;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;

class EmploymentDashboardController extends Controller
{
    public function index()
    {
        $pendingEmployers = Employer::pending()->count();
        $pendingJobs      = Job::pending()->count();
        $newApplications  = JobApplication::where('status', 'new')->count();
        $hiredThisMonth   = JobApplication::where('status', 'hired')
            ->where('updated_at', '>=', now()->startOfMonth())
            ->count();

        $recentPendingEmployers = Employer::pending()->with('user')->latest('created_at')->take(5)->get();
        $recentPendingJobs      = Job::pending()->with('company')->latest()->take(5)->get();

        return view('employment.dashboard', compact(
            'pendingEmployers', 'pendingJobs', 'newApplications', 'hiredThisMonth',
            'recentPendingEmployers', 'recentPendingJobs'
        ));
    }
}
