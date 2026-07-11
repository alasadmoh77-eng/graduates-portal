<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AcademicRecordExcelImportService;
use Illuminate\Http\Request;

class AcademicRecordImportController extends Controller
{
    protected $importService;

    public function __construct(AcademicRecordExcelImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Show the upload form.
     */
    public function showImportForm()
    {
        return view('admin.academic-record-import.index');
    }

    /**
     * Handle the Excel import request.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
            'update_student_profile' => 'nullable|boolean',
        ]);

        $updateProfile = (bool) $request->input('update_student_profile', false);

        try {
            $result = $this->importService->import(
                $request->file('excel_file'),
                $updateProfile
            );

            return view('admin.academic-record-import.index', compact('result'));
        } catch (\Exception $e) {
            $errorMsg = "فشل الاستيراد: " . $e->getMessage();
            return view('admin.academic-record-import.index')
                ->with('error', $errorMsg);
        }
    }

    /**
     * Download CSV template.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="academic_records_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Microsoft Excel Arabic compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header Row
            fputcsv($file, [
                'university_id',
                'student_name',
                'college',
                'department',
                'degree',
                'admission_year',
                'graduation_year',
                'level',
                'academic_year',
                'semester',
                'subject_name',
                'credit_hours',
                'score',
                'grade'
            ]);
            
            // Sample Row 1
            fputcsv($file, [
                '2023001',
                'أحمد محمد علي',
                'كلية الحاسبات وتكنولوجيا المعلومات',
                'علوم حاسوب',
                'بكالوريوس',
                '2019',
                '2023',
                'الأول',
                '2019/2020',
                'الفصل الأول',
                'برمجة حاسوب 1',
                '3',
                '95',
                'ممتاز'
            ]);

            // Sample Row 2
            fputcsv($file, [
                '2023001',
                'أحمد محمد علي',
                'كلية الحاسبات وتكنولوجيا المعلومات',
                'علوم حاسوب',
                'بكالوريوس',
                '2019',
                '2023',
                'الأول',
                '2019/2020',
                'الفصل الثاني',
                'تراكيب محددة',
                '3',
                '88',
                'جيد جداً'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
