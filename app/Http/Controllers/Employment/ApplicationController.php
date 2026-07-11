<?php
namespace App\Http\Controllers\Employment;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Notifications\ApplicationStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ApplicationController extends Controller
{
    /** Employment Officer: system-wide applications with filters */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query  = JobApplication::with(['job.company', 'graduate'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $applications = $query->paginate(20)->withQueryString();

        $counts = [
            'all'         => JobApplication::count(),
            'new'         => JobApplication::where('status', 'new')->count(),
            'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
            'interviewed' => JobApplication::where('status', 'interviewed')->count(),
            'hired'       => JobApplication::where('status', 'hired')->count(),
            'rejected'    => JobApplication::where('status', 'rejected')->count(),
        ];

        return view('employment.applications.index', compact('applications', 'status', 'counts'));
    }

    /** Full application detail */
    public function show(JobApplication $application)
    {
        $application->load(['job.company', 'graduate.graduate']);
        return view('employment.applications.show', compact('application'));
    }

    /** Move application through pipeline (officer view) */
    public function updateStatus(Request $request, JobApplication $application)
    {
        $allowed = ['new', 'shortlisted', 'interviewed', 'hired', 'rejected'];
        $request->validate([
            'status'         => 'required|in:' . implode(',', $allowed),
            'employer_notes' => 'nullable|string|max:1000',
        ]);

        if ($request->status === 'hired' && $application->status !== 'hired') {
            if ($application->job->is_filled) {
                return back()->with('error', app()->getLocale() == 'ar' ? 'لا يمكن قبول متقدم جديد، لأن هذه الوظيفة تم شغلها بالفعل.' : 'Cannot accept a new applicant because this job is already filled.');
            }
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $application) {
                $application->update([
                    'status'         => $request->status,
                    'employer_notes' => $request->employer_notes,
                ]);

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

    /** Schedule an interview */
    public function scheduleInterview(Request $request, JobApplication $application)
    {
        $request->validate([
            'interview_date'  => 'required|date|after:now',
            'interview_notes' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'interview_date'  => $request->interview_date,
            'interview_notes' => $request->interview_notes,
            'status'          => 'interviewed',
        ]);

        try {
            $application->graduate->notify(new ApplicationStatusChanged($application));
        } catch (\Exception $e) {
            Log::error('Notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'تم جدولة المقابلة وإشعار الخريج.');
    }
}
