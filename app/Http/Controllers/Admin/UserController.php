<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private const STRONG_PASSWORD_REGEX = '/^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/';

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $role = trim((string) $request->input('role', ''));
        $status = trim((string) $request->input('status', ''));

        $allowedRoles = ['admin', 'manager', 'receptionist'];
        $allowedStatuses = ['active', 'deactivated'];

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when(in_array($role, $allowedRoles, true), fn ($query) => $query->where('role', $role))
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status) {
                if ($status === 'active') {
                    $query->whereNull('deactivated_at');
                    return;
                }

                $query->whereNotNull('deactivated_at');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();
        
        $stats = [
            'admin' => User::where('role', 'admin')->count(),
            'manager' => User::where('role', 'manager')->count(),
            'receptionist' => User::where('role', 'receptionist')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats', 'search', 'role', 'status'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:admin,manager,receptionist',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully! Default password is "password".');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,manager,receptionist',
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'regex:' . self::STRONG_PASSWORD_REGEX],
        ], [
            'password.regex' => 'The password is too weak. Use at least 1 capital letter, 1 number, 1 special character, and minimum 8 characters.',
        ]);

        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->withErrors([
                'role' => 'You cannot change your own role from Admin.',
            ])->withInput();
        }

        $originalRole = $user->role;
        $originalStatus = is_null($user->deactivated_at) ? 'active' : 'deactivated';

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        ActivityLog::log(
            'user_update',
            'Updated user account: ' . $user->name,
            User::class,
            $user->id,
            [
                'role_before' => $originalRole,
                'role_after' => $user->role,
                'status_before' => $originalStatus,
                'status_after' => is_null($user->deactivated_at) ? 'active' : 'deactivated',
            ]
        );

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function profileEdit(Request $request)
    {
        $user = $request->user();

        return view('admin.users.profile', compact('user'));
    }

    public function profileUpdate(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'regex:' . self::STRONG_PASSWORD_REGEX],
        ], [
            'password.regex' => 'The password is too weak. Use at least 1 capital letter, 1 number, 1 special character, and minimum 8 characters.',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        ActivityLog::log(
            'profile_update',
            'Updated own profile: ' . $user->name,
            User::class,
            $user->id
        );

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot deactivate your own account!');
        }

        $user->update(['deactivated_at' => now()]);

        ActivityLog::log(
            'user_deactivate',
            'Deactivated user account: ' . $user->name,
            User::class,
            $user->id
        );

        return redirect()->route('admin.users.index')->with('success', 'User account deactivated successfully!');
    }

    public function activate(User $user)
    {
        $user->update(['deactivated_at' => null]);

        ActivityLog::log(
            'user_activate',
            'Activated user account: ' . $user->name,
            User::class,
            $user->id
        );

        return redirect()->route('admin.users.index')->with('success', 'User account activated successfully!');
    }
}
