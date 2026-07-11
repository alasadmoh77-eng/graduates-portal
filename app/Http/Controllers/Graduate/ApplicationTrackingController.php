<?php
namespace App\Http\Controllers\Graduate;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

class ApplicationTrackingController extends Controller
{
    public function index()
    {
        $applications = JobApplication::where('graduate_id', Auth::id())
            ->with(['job.company'])
            ->latest()
            ->paginate(15);

        $counts = [
            'total'       => JobApplication::where('graduate_id', Auth::id())->count(),
            'new'         => JobApplication::where('graduate_id', Auth::id())->where('status', 'new')->count(),
            'shortlisted' => JobApplication::where('graduate_id', Auth::id())->where('status', 'shortlisted')->count(),
            'interviewed' => JobApplication::where('graduate_id', Auth::id())->where('status', 'interviewed')->count(),
            'hired'       => JobApplication::where('graduate_id', Auth::id())->where('status', 'hired')->count(),
            'rejected'    => JobApplication::where('graduate_id', Auth::id())->where('status', 'rejected')->count(),
        ];

        return view('graduate.applications.index', compact('applications', 'counts'));
    }

    public function show(JobApplication $application)
    {
        // Security: only the applicant can view their own application
        abort_unless($application->graduate_id === Auth::id(), 403);
        $application->load(['job.company', 'job.employer']);
        return view('graduate.applications.show', compact('application'));
    }
}
