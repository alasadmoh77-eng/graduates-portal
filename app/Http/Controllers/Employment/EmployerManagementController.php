<?php
namespace App\Http\Controllers\Employment;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employer;
use App\Notifications\EmployerApproved;
use App\Notifications\EmployerRejected;
use App\Notifications\EmployerSuspended;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmployerManagementController extends Controller
{
    /** List all employers with optional status filter */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query  = Employer::with('user')->latest('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $employers = $query->paginate(20)->withQueryString();

        $counts = [
            'all'       => Employer::count(),
            'pending'   => Employer::pending()->count(),
            'approved'  => Employer::approved()->count(),
            'rejected'  => Employer::rejected()->count(),
            'suspended' => Employer::suspended()->count(),
        ];

        return view('employment.employers.index', compact('employers', 'status', 'counts'));
    }

    /** Show employer profile with their jobs and application summary */
    public function show(User $employer)
    {
        abort_unless($employer->role === 'employer', 404);
        $employerProfile = $employer->employer;
        abort_unless($employerProfile, 404);

        $jobs = $employer->jobs()->withCount('applications')->latest()->get();
        $applicationStats = [
            'total'       => $jobs->sum('applications_count'),
            'hired'       => \App\Models\JobApplication::whereHas('job', fn($q) => $q->where('employer_id', $employer->id))->where('status', 'hired')->count(),
            'shortlisted' => \App\Models\JobApplication::whereHas('job', fn($q) => $q->where('employer_id', $employer->id))->where('status', 'shortlisted')->count(),
        ];

        return view('employment.employers.show', compact('employer', 'employerProfile', 'jobs', 'applicationStats'));
    }

    /** Approve an employer */
    public function approve(User $employer)
    {
        abort_unless($employer->role === 'employer', 404);
        $employer->employer->update(['status' => 'approved', 'rejection_reason' => null]);
        try {
            $employer->notify(new EmployerApproved());
        } catch (\Exception $e) {
            Log::error('EmployerApproved notification failed: ' . $e->getMessage());
        }
        return back()->with('success', 'تم قبول جهة التوظيف بنجاح.');
    }

    /** Reject an employer with a reason */
    public function reject(Request $request, User $employer)
    {
        abort_unless($employer->role === 'employer', 404);
        $request->validate(['reason' => 'required|string|max:500']);
        $employer->employer->update(['status' => 'rejected', 'rejection_reason' => $request->reason]);
        try {
            $employer->notify(new EmployerRejected($request->reason));
        } catch (\Exception $e) {
            Log::error('EmployerRejected notification failed: ' . $e->getMessage());
        }
        return back()->with('success', 'تم رفض جهة التوظيف.');
    }

    /** Suspend an approved employer */
    public function suspend(User $employer)
    {
        abort_unless($employer->role === 'employer', 404);
        $employer->employer->update(['status' => 'suspended']);
        try {
            $employer->notify(new EmployerSuspended());
        } catch (\Exception $e) {
            Log::error('EmployerSuspended notification failed: ' . $e->getMessage());
        }
        return back()->with('success', 'تم إيقاف جهة التوظيف مؤقتاً.');
    }

    /** Reactivate a suspended/rejected employer */
    public function reactivate(User $employer)
    {
        abort_unless($employer->role === 'employer', 404);
        $employer->employer->update(['status' => 'approved', 'rejection_reason' => null]);
        try {
            $employer->notify(new EmployerApproved());
        } catch (\Exception $e) {
            Log::error('EmployerApproved notification failed: ' . $e->getMessage());
        }
        return back()->with('success', 'تم إعادة تفعيل حساب جهة التوظيف.');
    }
}
