<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'تم تعطيل حسابك، يرجى مراجعة إدارة شؤون الخريجين.'])
                ->with('error', 'تم تعطيل حسابك، يرجى مراجعة إدارة شؤون الخريجين.');
        }

        return $next($request);
    }
}
