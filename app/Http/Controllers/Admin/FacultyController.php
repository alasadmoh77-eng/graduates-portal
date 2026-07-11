<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Graduate;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    /**
     * Display a listing of the faculties.
     */
    public function index()
    {
        $faculties = Faculty::withCount('majors')
            ->latest()
            ->paginate(15);

        return view('admin.faculties.index', compact('faculties'));
    }

    /**
     * Show the form for creating a new faculty.
     */
    public function create()
    {
        return view('admin.faculties.create');
    }

    /**
     * Store a newly created faculty in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255', 'unique:faculties,name_ar'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        Faculty::create($validated);

        return redirect()->route('admin.faculties.index')
            ->with('success', __('app.faculty_created_success'));
    }

    /**
     * Show the form for editing the specified faculty.
     */
    public function edit(Faculty $faculty)
    {
        return view('admin.faculties.edit', compact('faculty'));
    }

    /**
     * Update the specified faculty in storage.
     */
    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255', 'unique:faculties,name_ar,' . $faculty->id],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $faculty->update($validated);

        return redirect()->route('admin.faculties.index')
            ->with('success', __('app.faculty_updated_success'));
    }

    /**
     * Remove the specified faculty from storage.
     */
    public function destroy(Faculty $faculty)
    {
        // 1. Check if faculty has related majors
        if ($faculty->majors()->exists()) {
            return back()->with('error', __('app.faculty_delete_has_majors_error'));
        }

        // 2. Check if graduates exist under this faculty's majors
        // (Wait, since majors exists check is first, this is secondary, but let's check it anyway for extra safety)
        $hasGraduates = Graduate::whereIn('major_id', $faculty->majors()->pluck('id'))->exists();
        if ($hasGraduates) {
            return back()->with('error', __('app.faculty_delete_has_graduates_error'));
        }

        $faculty->delete();

        return redirect()->route('admin.faculties.index')
            ->with('success', __('app.faculty_deleted_success'));
    }

    /**
     * Toggle active/inactive status quickly.
     */
    public function toggleStatus(Faculty $faculty)
    {
        $newStatus = $faculty->status === 'active' ? 'inactive' : 'active';
        
        $faculty->update(['status' => $newStatus]);

        $msg = $newStatus === 'active'
            ? __('app.faculty_activated_success')
            : __('app.faculty_deactivated_success');

        return back()->with('success', $msg);
    }
}
