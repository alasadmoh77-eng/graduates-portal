<?php
namespace App\Http\Controllers\Employment;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Employer;
use App\Models\User;
use App\Models\Graduate;
use Illuminate\Support\Facades\DB;

class EmploymentAnalyticsController extends Controller
{
    public function index()
    {
        // ── Totals ───────────────────────────────────────────────────────────
        $totalGraduates     = Graduate::count();
        $totalHired         = JobApplication::where('status', 'hired')->distinct('graduate_id')->count();
        $employmentRate     = $totalGraduates > 0 ? round($totalHired / $totalGraduates * 100, 1) : 0;

        $pendingEmployers   = Employer::pending()->count();
        $approvedEmployers  = Employer::approved()->count();
        $activeJobs         = Job::active()->count();
        $pendingJobs        = Job::pending()->count();

        // ── Application Funnel ───────────────────────────────────────────────
        $funnel = [
            'total'       => JobApplication::count(),
            'shortlisted' => JobApplication::where('status', 'shortlisted')->count(),
            'interviewed' => JobApplication::where('status', 'interviewed')->count(),
            'hired'       => JobApplication::where('status', 'hired')->count(),
        ];

        // ── Top Hiring Employers (top 5) ────────────────────────────────────
        $topEmployers = JobApplication::where('job_applications.status', 'hired')
            ->join('portal_jobs', 'job_applications.job_id', '=', 'portal_jobs.id')
            ->join('employers', 'portal_jobs.employer_id', '=', 'employers.user_id')
            ->select('employers.company_name', 'portal_jobs.employer_id', DB::raw('count(*) as hire_count'))
            ->groupBy('portal_jobs.employer_id', 'employers.company_name')
            ->orderByDesc('hire_count')
            ->limit(5)
            ->get();

        // ── Employment Rate by Major (top 8) ────────────────────────────────
        $byMajor = Graduate::join('majors', 'graduates.major_id', '=', 'majors.id')
            ->leftJoin('job_applications', function ($join) {
                $join->on('graduates.user_id', '=', 'job_applications.graduate_id')
                     ->where('job_applications.status', 'hired');
            })
            ->select(
                'majors.name_ar',
                DB::raw('count(distinct graduates.user_id) as total'),
                DB::raw('count(distinct job_applications.graduate_id) as hired')
            )
            ->groupBy('majors.id', 'majors.name_ar')
            ->having('total', '>', 0)
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(function ($row) {
                $row->rate = $row->total > 0 ? round($row->hired / $row->total * 100, 1) : 0;
                return $row;
            });

        // ── Monthly Placement Trend (last 6 months) ──────────────────────────
        $monthlyTrend = JobApplication::where('status', 'hired')
            ->where('updated_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw("strftime('%Y-%m', updated_at) as month"),
                DB::raw('count(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('employment.analytics.index', compact(
            'totalGraduates', 'totalHired', 'employmentRate',
            'pendingEmployers', 'approvedEmployers', 'activeJobs', 'pendingJobs',
            'funnel', 'topEmployers', 'byMajor', 'monthlyTrend'
        ));
    }
}
