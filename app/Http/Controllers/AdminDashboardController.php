<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\IssuedDocument;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_graduates' => User::where('role', 'graduate')->count(),
            'pending_requests' => DocumentRequest::whereIn('status', ['SUBMITTED', 'UNDER_REVIEW'])->count(),
            'approved_requests' => DocumentRequest::where('status', 'APPROVED')->count(),
            'ready_requests' => DocumentRequest::where('status', 'READY')->count(),
            'issued_requests' => DocumentRequest::where('status', 'ISSUED')->count(),
            'revoked_documents' => IssuedDocument::where('is_valid', false)->count(),
            'total_jobs' => Job::count(),
            'total_applications' => JobApplication::count(),
        ];

        // Chart 1: Requests per month (Last 6 Months)
        $months = [];
        $requestCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->translatedFormat('F');
            $requestCounts[] = DocumentRequest::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        // Chart 2: Top requested document types
        $topTypes = DB::table('document_requests')
            ->join('document_types', 'document_requests.document_type_id', '=', 'document_types.id')
            ->select('document_types.name_ar as label', DB::raw('count(*) as value'))
            ->groupBy('document_types.id', 'document_types.name_ar')
            ->orderBy('value', 'desc')
            ->limit(5)
            ->get();

        // Chart 3: Requests by major
        $majorStats = DB::table('document_requests')
            ->join('users', 'document_requests.user_id', '=', 'users.id')
            ->join('graduates', 'users.id', '=', 'graduates.user_id')
            ->join('majors', 'graduates.major_id', '=', 'majors.id')
            ->select('majors.name_ar as label', DB::raw('count(*) as value'))
            ->groupBy('majors.name_ar')
            ->get();
        $statusBreakdown = DB::table('document_requests')
            ->select('status as label', DB::raw('count(*) as value'))
            ->groupBy('status')
            ->get();

        // Recent Activity
        $recentRequests = DocumentRequest::with(['user', 'documentType'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'months', 
            'requestCounts', 
            'topTypes', 
            'majorStats',
            'statusBreakdown',
            'recentRequests'
        ));
    }

    public function exportCsv(Request $request)
    {
        // To be implemented in ReportController but keeping this for backward compatibility if needed
        return redirect()->route('admin.reports.graduates.export');
    }
}
