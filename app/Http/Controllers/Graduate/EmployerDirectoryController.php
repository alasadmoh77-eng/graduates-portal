<?php

namespace App\Http\Controllers\Graduate;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\Request;

class EmployerDirectoryController extends Controller
{
    /**
     * Display a listing of approved employers.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Employer::approved();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Count only active jobs
        $employers = $query->withCount(['jobs' => function($q) {
            $q->active();
        }])->orderBy('company_name', 'asc')->paginate(10);

        return view('graduate.employers.index', compact('employers', 'search'));
    }

    /**
     * Display the details of a specific approved employer.
     */
    public function show($id)
    {
        $employer = Employer::approved()->where('user_id', $id)->firstOrFail();

        // Fetch only active jobs
        $jobs = $employer->jobs()->active()->latest()->get();

        return view('graduate.employers.show', compact('employer', 'jobs'));
    }
}
