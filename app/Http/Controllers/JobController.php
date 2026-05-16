<?php
namespace App\Http\Controllers;

use App\Http\Requests\ApplyJobRequest;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::where('status', 'active')->latest()->get();
        return view('jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        abort_unless($job->status === 'active', 404);
        $job->load('employer');

        return view('jobs.show', compact('job'));
    }

    public function apply(ApplyJobRequest $request, Job $job)
    {
        abort_unless($job->status === 'active', 404);

        $graduate = Auth::user()->graduate;
        abort_unless($graduate, 403);

        $cvPath = $graduate->cv_path;

        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('cvs', 'public');
        }

        $application = JobApplication::create([
            'job_id' => $job->id,
            'graduate_id' => Auth::id(),
            'cover_letter' => $request->validated('cover_letter'),
            'cv_path' => $cvPath,
            'status' => 'new',
        ]);

        // Notify Employer
        try {
            $job->employer->notify(new \App\Notifications\NewJobApplication($application));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'تم تقديم طلبك بنجاح!');
    }

    public function adminIndex()
    {
        $jobs = Job::with('employer')->latest()->get();
        return view('admin.jobs.index', compact('jobs'));
    }

    public function employerIndex()
    {
        $jobs = Job::where('employer_id', Auth::id())->withCount('applications')->latest()->get();
        return view('employer.jobs.index', compact('jobs'));
    }

    public function applicationsIndex()
    {
        $applications = JobApplication::whereHas('job', function($q) {
            $q->where('employer_id', Auth::id());
        })->with(['job', 'graduate'])->latest()->get();
        
        return view('employer.applications.index', compact('applications'));
    }

    public function create()
    {
        return view('employer.jobs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'deadline' => 'required|date|after:today',
            'location' => 'required|string',
            'job_type' => 'required|string',
        ]);

        Job::create(array_merge($validated, [
            'employer_id' => Auth::id(),
            'status' => 'pending' // Admin must moderate
        ]));

        return redirect()->route('employer.jobs.index')->with('success', __('app.job_posted_success'));
    }

    public function moderate(Request $request, Job $job)
    {
        $job->update(['status' => $request->status]);
        
        // Notify Employer
        try {
            $job->employer->notify(new \App\Notifications\JobModerated($job));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
        }
        
        return back()->with('status', 'تم تحديث حالة الوظيفة وإرسال تنبيه لصاحب العمل.');
    }

    public function downloadCv(JobApplication $application)
    {
        // Security check
        if ($application->job->employer_id !== Auth::id()) {
            abort(403);
        }

        if (!$application->cv_path) {
            return back()->with('error', 'الخريج لم يرفق سيرة ذاتية في ملفه الشخصي.');
        }

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($application->cv_path)) {
            return back()->with('error', 'الملف غير موجود على الخادم.');
        }

        try {
            return \Illuminate\Support\Facades\Storage::disk('public')->download($application->cv_path);
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحميل الملف. قد يكون غير متوفر.');
        }
    }
}
