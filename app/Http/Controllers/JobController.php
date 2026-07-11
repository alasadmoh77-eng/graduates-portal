<?php
namespace App\Http\Controllers;

use App\Http\Requests\ApplyJobRequest;
use App\Models\Job;
use App\Models\JobApplication;
use App\Notifications\JobApproved;
use App\Notifications\JobRejected;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::where('status', 'active')->with(['company', 'employer'])->latest()->get();
        return view('jobs.index', compact('jobs'));
    }

    public function show(Job $job)
    {
        abort_unless($job->status === 'active', 404);
        $job->load(['company', 'employer']);

        return view('jobs.show', compact('job'));
    }

    public function apply(ApplyJobRequest $request, Job $job)
    {
        abort_unless($job->status === 'active', 404);

        if ($job->is_filled) {
            return back()->with('error', app()->getLocale() == 'ar' ? 'عذرًا، تم شغل هذه الوظيفة ولا يمكن التقديم عليها.' : 'Sorry, this job has been filled and is no longer available for application.');
        }

        $graduate = Auth::user()->graduate;
        abort_unless($graduate, 403);

        $existingApplication = JobApplication::where('job_id', $job->id)
            ->where('graduate_id', Auth::id())
            ->exists();

        if ($existingApplication) {
            return back()->with('error', app()->getLocale() == 'ar' ? 'لقد تقدمت لهذه الوظيفة مسبقًا.' : 'You have already applied for this job.');
        }

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
        $jobs = Job::with(['company', 'employer'])->latest()->get();
        return view('admin.jobs.index', compact('jobs'));
    }

    /** Employment Officer: all jobs with status filters */
    public function employmentOfficerIndex()
    {
        $status = request()->get('status', 'all');
        $query  = Job::with(['company', 'employer']);
        if ($status !== 'all') { $query->where('status', $status); }
        $jobs = $query->latest()->paginate(20)->withQueryString();
        $counts = [
            'all'      => Job::count(),
            'pending'  => Job::pending()->count(),
            'active'   => Job::active()->count(),
            'closed'   => Job::closed()->count(),
            'rejected' => Job::rejected()->count(),
        ];
        return view('employment.jobs.index', compact('jobs', 'status', 'counts'));
    }

    public function employerIndex()
    {
        $jobs = Job::where('employer_id', Auth::id())->with(['company', 'employer'])->withCount('applications')->latest()->get();
        return view('employer.jobs.index', compact('jobs'));
    }

    public function applicationsIndex()
    {
        $applications = JobApplication::whereHas('job', function($q) {
            $q->where('employer_id', Auth::id());
        })->with(['job', 'graduate'])->latest()->get();
        
        return view('employer.applications.index', compact('applications'));
    }

    /** Employer: application detail */
    public function showApplication(JobApplication $application)
    {
        abort_unless($application->job->employer_id === Auth::id(), 403);
        $application->load(['job', 'graduate.graduate']);
        return view('employer.applications.show', compact('application'));
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
        $request->validate([
            'status' => 'required|string|in:pending,active,closed,rejected',
        ]);

        $job->update(['status' => $request->status]);
        
        // Notify Employer
        try {
            $job->employer->notify(new \App\Notifications\JobModerated($job));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Notification failed: ' . $e->getMessage());
        }
        
        return back()->with('status', 'تم تحديث حالة الوظيفة وإرسال تنبيه لصاحب العمل.');
    }

    /** Employment Officer: approve a pending job */
    public function approveJob(Job $job)
    {
        $job->update(['status' => 'active', 'rejection_reason' => null]);
        try {
            $job->employer->notify(new JobApproved($job));
        } catch (\Exception $e) {
            Log::error('JobApproved notification failed: ' . $e->getMessage());
        }
        return back()->with('success', 'تم قبول الوظيفة ونشرها للخريجين.');
    }

    /** Employment Officer: reject a pending job with reason */
    public function rejectJob(Request $request, Job $job)
    {
        $request->validate(['reason' => 'required|string|max:500']);
        $job->update(['status' => 'rejected', 'rejection_reason' => $request->reason]);
        try {
            $job->employer->notify(new JobRejected($job, $request->reason));
        } catch (\Exception $e) {
            Log::error('JobRejected notification failed: ' . $e->getMessage());
        }
        return back()->with('success', 'تم رفض الوظيفة وإشعار جهة التوظيف.');
    }

    /** Employment Officer: close an active job */
    public function closeJob(Job $job)
    {
        $job->update(['status' => 'closed', 'closed_at' => now()]);
        return back()->with('success', 'تم إغلاق الوظيفة.');
    }

    /** Employer: update application status through pipeline */
    public function updateApplicationStatus(Request $request, JobApplication $application)
    {
        // Security: only the job's employer can update
        abort_unless($application->job->employer_id === Auth::id(), 403);

        $allowed = ['shortlisted', 'interviewed', 'hired', 'rejected'];
        $request->validate([
            'status'         => 'required|in:' . implode(',', $allowed),
            'employer_notes' => 'nullable|string|max:1000',
            'interview_date' => 'nullable|date|after:now',
            'interview_notes'=> 'nullable|string|max:1000',
        ]);

        if ($request->status === 'hired' && $application->status !== 'hired') {
            if ($application->job->is_filled) {
                return back()->with('error', app()->getLocale() == 'ar' ? 'لا يمكن قبول متقدم جديد، لأن هذه الوظيفة تم شغلها بالفعل.' : 'Cannot accept a new applicant because this job is already filled.');
            }
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $application) {
                $application->update(array_filter([
                    'status'          => $request->status,
                    'employer_notes'  => $request->employer_notes,
                    'interview_date'  => $request->interview_date,
                    'interview_notes' => $request->interview_notes,
                ], fn($v) => $v !== null));

                if ($request->status === 'hired') {
                    $application->job->update([
                        'is_filled' => true,
                        'filled_at' => now(),
                    ]);
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to update application status: ' . $e->getMessage());
            return back()->with('error', app()->getLocale() == 'ar' ? 'حدث خطأ أثناء تحديث حالة الطلب.' : 'An error occurred while updating application status.');
        }

        try {
            $application->graduate->notify(new ApplicationStatusChanged($application));
        } catch (\Exception $e) {
            Log::error('ApplicationStatusChanged notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
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
