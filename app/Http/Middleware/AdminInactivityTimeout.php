<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminInactivityTimeout
{
    private const TIMEOUT_SECONDS = 900;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $role = (string) (Auth::user()->role ?? '');
        if (!in_array($role, ['admin', 'manager', 'receptionist'], true)) {
            return $next($request);
        }

        $lastActivity = (int) $request->session()->get('admin_last_activity_at', 0);
        $now = now()->timestamp;

        if ($lastActivity > 0 && ($now - $lastActivity) > self::TIMEOUT_SECONDS) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('admin.login')
                ->with('warning', 'You were logged out due to inactivity (15 minutes).');
        }

        $request->session()->put('admin_last_activity_at', $now);

        return $next($request);
    }
}
