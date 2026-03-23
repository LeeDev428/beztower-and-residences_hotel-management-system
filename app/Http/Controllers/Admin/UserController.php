<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        
        $stats = [
            'admin' => User::where('role', 'admin')->count(),
            'manager' => User::where('role', 'manager')->count(),
            'receptionist' => User::where('role', 'receptionist')->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
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
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,receptionist',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
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
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
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

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
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
