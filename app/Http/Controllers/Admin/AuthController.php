<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const ADMIN_ROLES = ['admin', 'manager', 'receptionist'];

    public function showLogin()
    {
        return view('admin.login');
    }

    public function showForgotPassword()
    {
        return view('admin.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $eligibleUser = User::where('email', $request->input('email'))
            ->whereIn('role', self::ADMIN_ROLES)
            ->whereNull('deactivated_at')
            ->first();

        if (!$eligibleUser) {
            return back()->with('status', 'If your account exists, a password reset link has been sent to your email.');
        }

        $status = Password::broker('users')->sendResetLink([
            'email' => $eligibleUser->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => __($status),
        ]);
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('admin.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $eligibleUser = User::where('email', $request->input('email'))
            ->whereIn('role', self::ADMIN_ROLES)
            ->whereNull('deactivated_at')
            ->first();

        if (!$eligibleUser) {
            return back()->withErrors([
                'email' => 'This account is not eligible for admin password reset.',
            ])->withInput($request->only('email'));
        }

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            ActivityLog::log('password_reset', 'Password reset via admin forgot password flow', 'App\\Models\\User', $eligibleUser->id);

            return redirect()->route('admin.login')->with('status', __($status));
        }

        return back()->withErrors([
            'email' => __($status),
        ])->withInput($request->only('email'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = Str::lower((string) $request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            
            // Log the login activity
            ActivityLog::log(
                'login',
                Auth::user()->name . ' logged in to admin panel',
                'App\Models\User',
                Auth::id()
            );
            
            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($throttleKey, 900);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('admin.login');
    }
}

