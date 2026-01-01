@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div style="max-width: 600px;">
    <x-admin.card title="User Information">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                @error('name')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                @error('email')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Password (leave blank to keep current)</label>
                <input type="password" name="password" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                @error('password')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Confirm Password</label>
                <input type="password" name="password_confirmation" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Role</label>
                <select name="role" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="receptionist" {{ old('role', $user->role) === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                </select>
                @error('role')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem;">
                <x-admin.button type="primary">Update User</x-admin.button>
                <x-admin.button type="secondary" href="{{ route('admin.users.index') }}">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection
