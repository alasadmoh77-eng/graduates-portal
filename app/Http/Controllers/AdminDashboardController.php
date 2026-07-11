<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\IssuedDocument;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Major;
use App\Models\Faculty;
use App\Models\Event;
use App\Models\ApprovedGraduate;
use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_graduates' => User::where('role', 'graduate')->count(),
            'total_approved_graduates' => ApprovedGraduate::count(),
            'total_employers' => Employer::count(),
            'pending_requests' => DocumentRequest::whereIn('status', ['SUBMITTED', 'UNDER_REVIEW'])->count(),
            'approved_requests' => DocumentRequest::where('status', 'APPROVED')->count(),
            'ready_requests' => DocumentRequest::where('status', 'READY')->count(),
            'issued_requests' => DocumentRequest::where('status', 'ISSUED')->count(),
            'revoked_documents' => IssuedDocument::where('is_valid', false)->count(),
            'total_jobs' => Job::count(),
            'total_applications' => JobApplication::count(),
            'total_faculties' => DB::table('approved_graduates')
                ->whereNotNull('college')
                ->distinct()
                ->count('college'),
            'total_majors' => DB::table('approved_graduates')
                ->distinct()
                ->count('major'),
            'total_document_requests' => DocumentRequest::count(),
            'total_issued_documents' => IssuedDocument::count(),
            'total_pending_payments' => DocumentRequest::where('payment_status', 'pending_review')->count(),
            'total_active_jobs' => Job::where('status', 'active')->count(),
            'total_events' => Event::count(),
            'pending_signatures' => IssuedDocument::whereNull('all_signed_at')->whereNotNull('document_request_id')->count(),
            'completed_signatures' => IssuedDocument::whereNotNull('all_signed_at')->whereNotNull('document_request_id')->count(),
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

        $lang = app()->getLocale();
        $nameField = $lang === 'en' ? 'name_en' : 'name_ar';

        // Chart 2: Top requested document types
        $topTypes = DB::table('document_requests')
            ->join('document_types', 'document_requests.document_type_id', '=', 'document_types.id')
            ->select("document_types.{$nameField} as label", DB::raw('count(*) as value'))
            ->groupBy('document_types.id', "document_types.{$nameField}")
            ->orderBy('value', 'desc')
            ->limit(5)
            ->get();

        // Chart 3: Requests by major
        $majorStats = DB::table('document_requests')
            ->join('users', 'document_requests.user_id', '=', 'users.id')
            ->join('graduates', 'users.id', '=', 'graduates.user_id')
            ->join('majors', 'graduates.major_id', '=', 'majors.id')
            ->select("majors.{$nameField} as label", DB::raw('count(*) as value'))
            ->groupBy("majors.{$nameField}")
            ->get();

        $statusBreakdown = DB::table('document_requests')
            ->select('status as label', DB::raw('count(*) as value'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                $item->label = __('app.document_status.' . $item->label) ?? $item->label;
                return $item;
            });

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
