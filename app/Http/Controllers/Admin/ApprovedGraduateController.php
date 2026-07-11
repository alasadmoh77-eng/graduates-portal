<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApprovedGraduate;
use App\Imports\ApprovedGraduatesImport;
use App\Exports\ApprovedGraduatesTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Facades\Cache;
use App\Models\Graduate;
use Illuminate\Support\Facades\DB;

class ApprovedGraduateController extends Controller
{
    /**
     * Display a listing of the approved graduates.
     */
    public function index(Request $request)
    {
        $query = ApprovedGraduate::query();

        // Search by name, university ID, or college
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('university_id', 'like', "%{$search}%")
                  ->orWhere('college', 'like', "%{$search}%");
            });
        }

        // Filter by major
        if ($request->filled('major')) {
            $query->where('major', 'like', "%{$request->input('major')}%");
        }

        $graduates = $query->with('graduate.user')->latest()->paginate(15)->withQueryString();
        $totalCount = ApprovedGraduate::count();

        // Get last import date
        $lastImport = Cache::get('approved_graduates_last_import_at');
        if (!$lastImport) {
            $latestRecord = ApprovedGraduate::latest('updated_at')->first();
            $lastImport = $latestRecord ? $latestRecord->updated_at : null;
        }

        return view('admin.graduate-registry.index', compact('graduates', 'totalCount', 'lastImport'));
    }

    /**
     * Handle the Excel import request.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
        ], [
            'excel_file.required' => 'يرجى اختيار ملف أولاً.',
            'excel_file.mimes' => 'يجب أن يكون الملف بصيغة xlsx أو xls فقط.',
            'excel_file.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت.',
        ]);

        try {
            HeadingRowFormatter::default('none');
            Excel::import(new ApprovedGraduatesImport, $request->file('excel_file'));

            // Store last import timestamp in Cache
            Cache::put('approved_graduates_last_import_at', now());

            return redirect()->route('admin.graduate-registry.index')
                ->with('success', __('app.import_success'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Approved Graduates Import Error: ' . $e->getMessage());
            
            return redirect()->route('admin.graduate-registry.index')
                ->with('error', __('app.import_failed') . ' ' . $e->getMessage());
        }
    }

    /**
     * Download the template Excel file.
     */
    public function downloadTemplate()
    {
        return Excel::download(new ApprovedGraduatesTemplateExport, 'approved_graduates_template.xlsx');
    }

    /**
     * Remove the specified approved graduate from storage.
     */
    public function destroy($id)
    {
        try {
            $approvedGraduate = ApprovedGraduate::findOrFail($id);
            $universityId = $approvedGraduate->university_id;

            $graduate = \App\Models\Graduate::where('university_id', $universityId)->with('user')->first();

            if ($graduate && $graduate->user) {
                $graduate->user->update(['is_active' => false]);
            }

            $approvedGraduate->delete();

            return redirect()->route('admin.graduate-registry.index')
                ->with('success', app()->getLocale() == 'ar' ? 'تم حذف الخريج المعتمد بنجاح.' : 'Approved graduate deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.graduate-registry.index')
                ->with('error', app()->getLocale() == 'ar' ? 'حدث خطأ أثناء حذف الخريج.' : 'An error occurred while deleting the graduate.');
        }
    }

    /**
     * Freeze the account of the associated graduate.
     */
    public function freezeAccount($id)
    {
        try {
            $approvedGraduate = ApprovedGraduate::findOrFail($id);
            $universityId = $approvedGraduate->university_id;

            $graduate = Graduate::where('university_id', $universityId)->with('user')->first();

            if (!$graduate || !$graduate->user) {
                return redirect()->route('admin.graduate-registry.index')
                    ->with('error', app()->getLocale() == 'ar' 
                        ? 'لا يوجد حساب مسجل لهذا الخريج حتى يتم تجميده.' 
                        : 'No registered account found for this graduate to freeze.');
            }

            DB::transaction(function () use ($graduate) {
                $graduate->user->update(['is_active' => false]);
            });

            return redirect()->route('admin.graduate-registry.index')
                ->with('success', app()->getLocale() == 'ar' 
                    ? 'تم تجميد حساب الخريج بنجاح دون حذف بياناته.' 
                    : 'Graduate account frozen successfully without deleting data.');
        } catch (\Exception $e) {
            return redirect()->route('admin.graduate-registry.index')
                ->with('error', app()->getLocale() == 'ar' 
                    ? 'حدث خطأ أثناء تجميد الحساب.' 
                    : 'An error occurred while freezing the account.');
        }
    }

    /**
     * Unfreeze the account of the associated graduate.
     */
    public function unfreezeAccount($id)
    {
        try {
            $approvedGraduate = ApprovedGraduate::findOrFail($id);
            $universityId = $approvedGraduate->university_id;

            $graduate = Graduate::where('university_id', $universityId)->with('user')->first();

            if (!$graduate || !$graduate->user) {
                return redirect()->route('admin.graduate-registry.index')
                    ->with('error', app()->getLocale() == 'ar' 
                        ? 'لا يوجد حساب مسجل لهذا الخريج حتى يتم إلغاء تجميده.' 
                        : 'No registered account found for this graduate to unfreeze.');
            }

            DB::transaction(function () use ($graduate) {
                $graduate->user->update(['is_active' => true]);
            });

            return redirect()->route('admin.graduate-registry.index')
                ->with('success', app()->getLocale() == 'ar' 
                    ? 'تم إلغاء تجميد حساب الخريج بنجاح.' 
                    : 'Graduate account unfrozen successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.graduate-registry.index')
                ->with('error', app()->getLocale() == 'ar' 
                    ? 'حدث خطأ أثناء إلغاء تجميد الحساب.' 
                    : 'An error occurred while unfreezing the account.');
        }
    }

    /**
     * Clear the test approved graduates list. Only works in local/testing environments.
     */
    public function clearTestData()
    {
        if (!app()->environment(['local', 'testing'])) {
            abort(403, 'Unauthorized in production.');
        }

        try {
            ApprovedGraduate::query()->delete();

            return redirect()->route('admin.graduate-registry.index')
                ->with('success', app()->getLocale() == 'ar' 
                    ? 'تم تفريغ قائمة الخريجين المعتمدين التجريبية بنجاح.' 
                    : 'Approved graduates test list cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.graduate-registry.index')
                ->with('error', app()->getLocale() == 'ar' 
                    ? 'حدث خطأ أثناء تفريغ القائمة.' 
                    : 'An error occurred while clearing the list.');
        }
    }
}
