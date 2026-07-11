<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized access.');
        }

        $user = Auth::user();
        $role = $user->role;

        // super_admin and normal admin (backward compatibility) have access to everything
        if (in_array($role, ['admin', 'super_admin'])) {
            return $next($request);
        }

        if ($permission === 'academic' && $role === 'academic_admin') {
            return $next($request);
        }

        if ($permission === 'finance' && $role === 'finance_admin') {
            return $next($request);
        }

        if ($permission === 'employment' && $role === 'employment_officer') {
            return $next($request);
        }

        // 'super' permission: only admin / super_admin pass (already handled above, this is a fallback deny)
        abort(403, 'Unauthorized access.');
    }
}
