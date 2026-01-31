@extends('layouts.admin')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; gap: 1rem;">
        <select name="action" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            <option value="">All Actions</option>
            <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Logins</option>
            <option value="room_edit" {{ request('action') == 'room_edit' ? 'selected' : '' }}>Room Edits</option>
            <option value="room_archive" {{ request('action') == 'room_archive' ? 'selected' : '' }}>Room Archive</option>
            <option value="image_update" {{ request('action') == 'image_update' ? 'selected' : '' }}>Image Updates</option>
            <option value="report_generate" {{ request('action') == 'report_generate' ? 'selected' : '' }}>Reports Generated</option>
        </select>
        <select name="user_id" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            <option value="">All Users</option>
            @foreach(\App\Models\User::all() as $user)
            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->role }})</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
        <x-admin.button type="primary">Filter</x-admin.button>
        <x-admin.button type="outline" href="{{ route('admin.activity-logs.index') }}">Clear</x-admin.button>
    </form>
</div>

<x-admin.card title="Activity History">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-gray);">
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Date/Time</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">User</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Action</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Description</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr style="border-bottom: 1px solid var(--border-gray);">
                    <td style="padding: 1rem 0.75rem;">
                        <div>{{ $log->created_at->format('M d, Y') }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $log->created_at->format('h:i A') }}</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <div style="font-weight: 600;">{{ $log->user->name ?? 'System' }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $log->user->role ?? 'N/A' }}</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <span style="padding: 0.25rem 0.75rem; background: 
                            @if(str_contains($log->action, 'login')) var(--success)
                            @elseif(str_contains($log->action, 'delete') || str_contains($log->action, 'archive')) var(--warning)
                            @else var(--info)
                            @endif
                            ; color: white; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                            {{ ucwords(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </td>
                    <td style="padding: 1rem 0.75rem;">{{ $log->description }}</td>
                    <td style="padding: 1rem 0.75rem; color: var(--text-muted); font-size: 0.875rem;">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        No activity logs found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $logs->links() }}
    </div>
</x-admin.card>
@endsection
