@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="admin-users-toolbar" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
    <form method="GET" class="admin-users-filter-form" style="display: flex; gap: 0.75rem; flex: 1; flex-wrap: wrap;">
        <input
            type="text"
            name="search"
            value="{{ $search ?? request('search') }}"
            placeholder="Search name or email"
            style="flex: 1 1 220px; min-width: 180px; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;"
        >

        <select name="role" style="flex: 0 1 180px; min-width: 150px; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            <option value="" {{ ($role ?? request('role', '')) === '' ? 'selected' : '' }}>All Roles</option>
            <option value="admin" {{ ($role ?? request('role')) === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="manager" {{ ($role ?? request('role')) === 'manager' ? 'selected' : '' }}>Manager</option>
            <option value="receptionist" {{ ($role ?? request('role')) === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
        </select>

        <select name="status" style="flex: 0 1 170px; min-width: 140px; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            <option value="" {{ ($status ?? request('status', '')) === '' ? 'selected' : '' }}>All Statuses</option>
            <option value="active" {{ ($status ?? request('status')) === 'active' ? 'selected' : '' }}>Active</option>
            <option value="deactivated" {{ ($status ?? request('status')) === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
        </select>

        <button type="submit" style="padding: 0.75rem 1rem; border: none; border-radius: 8px; background: var(--primary-gold); color: #1f1f1f; font-weight: 700; cursor: pointer;">
            Search
        </button>

        <a href="{{ route('admin.users.index') }}" style="padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid var(--border-gray); color: var(--text-muted); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center;">
            Reset
        </a>
    </form>

    <x-admin.button type="primary" href="{{ route('admin.users.create') }}">
        + Add New User
    </x-admin.button>
</div>

<x-admin.card title="All Users ({{ $users->total() }})">
    <div class="admin-table-wrap" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-gray);">
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Name</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Email</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Role</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Status</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Created</th>
                    <th style="text-align: right; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr style="border-bottom: 1px solid var(--border-gray);">
                    <td style="padding: 1rem 0.75rem;">
                        <div style="font-weight: 600;">{{ $user->name }}</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        {{ $user->email }}
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <x-admin.badge :status="$user->role" />
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        @if($user->deactivated_at)
                            <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background: rgba(220,53,69,0.1); color: var(--danger);">
                                Deactivated
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; background: rgba(40,167,69,0.1); color: var(--success);">
                                Active
                            </span>
                        @endif
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td style="padding: 1rem 0.75rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <x-admin.button type="outline" size="sm" href="{{ route('admin.users.edit', $user) }}">
                                Edit
                            </x-admin.button>
                            @if($user->id !== auth()->id())
                                @if($user->deactivated_at)
                                <form method="POST" action="{{ route('admin.users.activate', $user) }}" style="display: inline;">
                                    @csrf
                                    <x-admin.button type="primary" size="sm">
                                        Activate
                                    </x-admin.button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('Deactivate this user? They will no longer be able to log in.');">
                                    @csrf
                                    @method('DELETE')
                                    <x-admin.button type="danger" size="sm">
                                        Deactivate
                                    </x-admin.button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted);">
                        No users found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div style="margin-top: 1.5rem;">
        {{ $users->links() }}
    </div>
    @endif
</x-admin.card>

@push('styles')
<style>
    @media (max-width: 680px) {
        .admin-users-toolbar {
            justify-content: stretch !important;
            align-items: stretch !important;
        }

        .admin-users-filter-form {
            width: 100%;
        }

        .admin-users-toolbar a {
            width: 100%;
        }

        .admin-users-toolbar a,
        .admin-users-toolbar button,
        .admin-users-filter-form button {
            text-align: center;
        }
    }
</style>
@endpush
@endsection
