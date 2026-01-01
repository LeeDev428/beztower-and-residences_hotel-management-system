<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to access the admin panel.');
        }

        if (!in_array(auth()->user()->role, ['admin', 'manager', 'receptionist'])) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
