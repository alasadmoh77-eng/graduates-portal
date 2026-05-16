<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Major;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Document Requests Report
     */
    public function requests(Request $request)
    {
        $query = DocumentRequest::with(['user.graduate.major', 'documentType'])->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type_id')) $query->where('document_type_id', $request->type_id);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $requests = $query->paginate(20)->withQueryString();
        $types = DocumentType::all();

        return view('admin.reports.requests', compact('requests', 'types'));
    }

    /**
     * Graduates Report
     */
    public function graduates(Request $request)
    {
        $query = User::where('role', 'graduate')->with('graduate.major');

        if ($request->filled('major_id')) {
            $query->whereHas('graduate', fn($q) => $q->where('major_id', $request->major_id));
        }
        if ($request->filled('year')) {
            $query->whereHas('graduate', fn($q) => $q->where('graduation_year', $request->year));
        }
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $graduates = $query->paginate(20)->withQueryString();
        $majors = Major::all();

        return view('admin.reports.graduates', compact('graduates', 'majors'));
    }

    /**
     * Export Requests CSV
     */
    public function exportRequests(Request $request)
    {
        $query = DocumentRequest::with(['user.graduate.major', 'documentType']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('type_id')) $query->where('document_type_id', $request->type_id);

        $filename = "requests_report_" . now()->format('Y-m-d') . ".csv";
        
        return response()->streamDownload(function() use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel Arabic support
            
            fputcsv($handle, ['كود التتبع', 'الخريج', 'الرقم الجامعي', 'نوع المستند', 'الحالة', 'التاريخ']);
            
            $query->chunk(100, function($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->tracking_code,
                        $row->user->name,
                        $row->user->graduate->university_id,
                        $row->documentType->name_ar,
                        $row->status,
                        $row->created_at->format('Y-m-d')
                    ]);
                }
            });
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export Graduates CSV
     */
    public function exportGraduates(Request $request)
    {
        $query = User::where('role', 'graduate')->with('graduate.major');

        $filename = "graduates_list_" . now()->format('Y-m-d') . ".csv";

        return response()->streamDownload(function() use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($handle, ['الاسم', 'البريد الإلكتروني', 'الرقم الجامعي', 'التخصص', 'سنة التخرج']);
            
            $query->chunk(100, function($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->name,
                        $row->email,
                        $row->graduate->university_id ?? 'N/A',
                        $row->graduate->major->name_ar ?? 'N/A',
                        $row->graduate->graduation_year ?? 'N/A'
                    ]);
                }
            });
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
