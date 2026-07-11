<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Graduate;
use App\Http\Requests\RegisterGraduateRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\Major;
use App\Models\Employer;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewGraduateRegistered;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    
    public function login(Request $request) {
        $credentials = $request->validate(['email'=>'required|email', 'password'=>'required']);
        
        $user = User::where('email', $request->email)->first();
        if ($user && !$user->is_active) {
            return back()->withErrors(['email' => __('auth.inactive')])->onlyInput('email');
        }

        if ($user && $user->role === 'employer') {
            return back()->withErrors(['email' => app()->getLocale() == 'ar' ? 'يرجى استخدام صفحة تسجيل الدخول المخصصة لجهات التوظيف.' : 'Please use the dedicated employer login page.'])->onlyInput('email');
        }
        
        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = Auth::user()->role;
            if(in_array($role, ['admin', 'super_admin', 'academic_admin', 'finance_admin'])) return redirect()->route('admin.dashboard');
            if($role === 'employment_officer') return redirect()->route('employment.dashboard');
            if($role==='graduate') return redirect()->route('graduate.dashboard');
            
            return redirect()->route('employer.dashboard');
        }
        return back()->withErrors(['email'=>__('auth.failed')])->onlyInput('email');
    }

    public function showEmployerLogin() {
        return view('auth.employer-login');
    }

    public function employerLogin(Request $request) {
        $credentials = $request->validate(['email'=>'required|email', 'password'=>'required']);
        
        $user = User::where('email', $request->email)->first();
        if ($user && !$user->is_active) {
            return back()->withErrors(['email' => __('auth.inactive')])->onlyInput('email');
        }
        
        if ($user && $user->role !== 'employer') {
            return back()->withErrors(['email' => app()->getLocale() == 'ar' ? 'صفحة الدخول هذه مخصصة لجهات التوظيف فقط.' : 'This login page is only for employers.'])->onlyInput('email');
        }

        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $employer = Auth::user()->employer;
            if ($employer && !$employer->isApproved()) {
                $status = $employer->status;
                $reason = $employer->rejection_reason;
                
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('employer.pending')
                    ->with('employer_status', $status)
                    ->with('rejection_reason', $reason);
            }
            return redirect()->route('employer.dashboard');
        }
        return back()->withErrors(['email'=>__('auth.failed')])->onlyInput('email');
    }

    public function showRegister() { 
        $majors = Major::all();
        return view('auth.register', compact('majors')); 
    }

    public function registerGraduate(RegisterGraduateRequest $request) {
        $approvedGraduate = \App\Models\ApprovedGraduate::where('university_id', $request->university_id)->firstOrFail();

        $majorName = trim($approvedGraduate->major);
        $major = Major::where('name_ar', $majorName)->orWhere('name_en', $majorName)->first();

        if (!$major) {
            $facultyId = null;
            if (!empty($approvedGraduate->college)) {
                $faculty = \App\Models\Faculty::firstOrCreate([
                    'name_ar' => trim($approvedGraduate->college)
                ], [
                    'name_en' => trim($approvedGraduate->college),
                    'status' => 'active'
                ]);
                $facultyId = $faculty->id;
            }

            $major = Major::create([
                'name_ar' => $majorName,
                'name_en' => $majorName,
                'faculty_id' => $facultyId,
                'degree_name_ar' => null,
                'degree_name_en' => null,
            ]);
        }

        $user = User::create([
            'name' => $approvedGraduate->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'graduate',
        ]);
        Graduate::create([
            'user_id' => $user->id,
            'university_id' => $approvedGraduate->university_id,
            'phone' => $request->phone,
            'major_id' => $major->id,
            'graduation_year' => $approvedGraduate->graduation_year,
        ]);

        try {
            $academicUsers = User::where('role', 'academic_admin')
                ->where(function($query) {
                    $query->whereNull('signer_role')
                          ->orWhere('signer_role', 'مدير إدارة شؤون الخريجين');
                })
                ->where('is_active', true)
                ->get();

            if ($academicUsers->isEmpty()) {
                Log::warning('No active academic_admin or Graduate Affairs Manager users found to notify about new graduate registration.');
            } else {
                $alreadyNotifiedIds = \Illuminate\Support\Facades\DB::table('notifications')
                    ->where('notifiable_type', User::class)
                    ->whereIn('notifiable_id', $academicUsers->pluck('id'))
                    ->where('data->type', 'new_graduate_registered')
                    ->where('data->graduate_id', $user->id)
                    ->whereNull('read_at')
                    ->pluck('notifiable_id')
                    ->toArray();

                $usersToNotify = $academicUsers->reject(function ($u) use ($alreadyNotifiedIds) {
                    return in_array($u->id, $alreadyNotifiedIds);
                });

                if ($usersToNotify->isNotEmpty()) {
                    Notification::send($usersToNotify, new NewGraduateRegistered($user));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify admins about new graduate registration: ' . $e->getMessage());
        }

        Auth::login($user);
        return redirect()->route('graduate.dashboard');
    }

    public function checkGraduate($universityId) {
        $approved = \App\Models\ApprovedGraduate::where('university_id', $universityId)->first();
        if ($approved) {
            $majorName = trim($approved->major);
            $major = Major::with('faculty')->where('name_ar', $majorName)->orWhere('name_en', $majorName)->first();
            $facultyName = $approved->college ?: (($major && $major->faculty) ? $major->faculty->name_ar : null);

            return response()->json([
                'success' => true,
                'graduate' => [
                    'university_id' => $approved->university_id,
                    'name' => $approved->name,
                    'email' => $approved->email,
                    'college' => $facultyName,
                    'major' => $approved->major,
                    'graduation_year' => (string) $approved->graduation_year,
                ]
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'الرقم الجامعي غير موجود في سجل الخريجين المعتمدين.'
        ]);
    }

    public function showEmployerRegister() {
        return view('auth.employer-register');
    }

    public function registerEmployer(Request $request) {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'password'     => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'industry'     => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:30',
            'address'      => 'nullable|string|max:500',
            'website'      => 'nullable|url|max:255',
            'description'  => 'nullable|string|max:2000',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'employer',
        ]);
        Employer::create([
            'user_id'       => $user->id,
            'company_name'  => $request->company_name,
            'company_email' => $request->email,
            'industry'      => $request->industry,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'website'       => $request->website,
            'description'   => $request->description,
            'logo'          => $logoPath,
            'status'        => 'pending', // Must be approved by Employment Officer
        ]);

        // Notify employment officers about new employer registration
        try {
            $officers = User::whereIn('role', ['admin', 'super_admin', 'employment_officer'])
                ->where('is_active', true)
                ->get();
            Notification::send($officers, new \App\Notifications\NewEmployerRegistered($user));
        } catch (\Exception $e) {
            Log::error('Failed to notify about new employer: ' . $e->getMessage());
        }

        // Do NOT log them in — redirect to pending page with success registration flag
        return redirect()->route('employer.pending')
            ->with('employer_status', 'pending')
            ->with('success_registration', true);
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
