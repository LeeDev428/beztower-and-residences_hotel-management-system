@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
    <x-admin.button type="primary" href="{{ route('admin.users.create') }}">
        + Add New User
    </x-admin.button>
</div>

<x-admin.card title="All Users ({{ $users->total() }})">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-gray);">
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Name</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Email</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Role</th>
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
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td style="padding: 1rem 0.75rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            <x-admin.button type="outline" size="sm" href="{{ route('admin.users.edit', $user) }}">
                                Edit
                            </x-admin.button>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: inline;" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <x-admin.button type="danger" size="sm">
                                    Delete
                                </x-admin.button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 2rem; text-align: center; color: var(--text-muted);">
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
@endsection
