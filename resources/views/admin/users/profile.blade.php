@extends('layouts.admin')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div style="max-width: 680px;">
    <x-admin.card title="Profile Information">
        <form method="POST" action="{{ route('admin.profile.update') }}">
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
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Role</label>
                <input type="text" value="{{ ucfirst($user->role) }}" readonly style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px; background: #f3f3f3; color: #666;">
                <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.35rem;">Role cannot be edited from profile settings.</div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">New Password (optional)</label>
                <input type="password" name="password" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                <div style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.35rem;">Use at least 1 capital letter, 1 number, 1 special character, and minimum 8 characters.</div>
                @error('password')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Confirm New Password</label>
                <input type="password" name="password_confirmation" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <x-admin.button type="primary">Update Profile</x-admin.button>
                <x-admin.button type="secondary" href="{{ route('admin.dashboard') }}">Back</x-admin.button>
            </div>
        </form>
    </x-admin.card>
</div>
@endsection
