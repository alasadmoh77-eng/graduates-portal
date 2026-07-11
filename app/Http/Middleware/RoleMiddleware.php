<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized access.');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors(['email' => __('auth.inactive')]);
        }

        $userRole = $user->role;
        if ($role === 'admin') {
            if (in_array($userRole, ['admin', 'super_admin', 'academic_admin', 'finance_admin', 'employment_officer'])) {
                return $next($request);
            }
        } elseif ($userRole === $role) {
            if ($role === 'employer') {
                $employer = $user->employer;
                if (!$employer || !$employer->isApproved()) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('employer.pending')
                        ->with('employer_status', $employer ? $employer->status : 'pending');
                }
            }
            return $next($request);
        }

        abort(403, 'Unauthorized access.');
    }
}
