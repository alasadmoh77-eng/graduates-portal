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

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    
    public function login(Request $request) {
        $credentials = $request->validate(['email'=>'required|email', 'password'=>'required']);
        
        if(Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = Auth::user()->role;
            if($role==='admin') return redirect()->route('admin.dashboard');
            if($role==='graduate') return redirect()->route('graduate.dashboard');
            return redirect()->route('employer.dashboard');
        }
        return back()->withErrors(['email'=>__('auth.failed')])->onlyInput('email');
    }

    public function showRegister() { 
        $majors = Major::all();
        return view('auth.register', compact('majors')); 
    }

    public function registerGraduate(RegisterGraduateRequest $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'graduate',
        ]);
        Graduate::create([
            'user_id' => $user->id,
            'university_id' => $request->university_id,
            'phone' => $request->phone,
            'major_id' => $request->major_id,
            'graduation_year' => $request->graduation_year,
        ]);
        Auth::login($user);
        return redirect()->route('graduate.dashboard');
    }

    public function showEmployerRegister() {
        return view('auth.employer-register');
    }

    public function registerEmployer(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'employer',
        ]);
        Employer::create([
            'user_id' => $user->id,
            'company_name' => $request->company_name,
            'company_email' => $request->email,
        ]);
        Auth::login($user);
        return redirect()->route('employer.dashboard');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
