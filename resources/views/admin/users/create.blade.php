@extends('layouts.admin')

@section('title', 'Create User')
@section('page-title', 'Create New User')

@section('content')
<div style="max-width: 600px;">
    <x-admin.card title="User Information">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                @error('name')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                @error('email')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <div style="padding: 0.8rem 0.9rem; border-radius: 8px; background: #fff8e1; color: #7a5a00; border: 1px solid #f0d98f; font-size: 0.9rem;">
                    New users are created with default password: <strong>password</strong>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Role</label>
                <select name="role" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="receptionist" {{ old('role') === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                </select>
                @error('role')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem;">
                <x-admin.button type="primary">Create User</x-admin.button>
                <x-admin.button type="secondary" href="{{ route('admin.users.index') }}">Cancel</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection
