@extends('layouts.admin')

@section('title', 'Housekeeping')
@section('page-title', 'Housekeeping Management')

@section('content')
<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
    <div style="background: linear-gradient(135deg, var(--success) 0%, #20873a 100%); color: white; padding: 1.5rem; border-radius: 12px;">
        <div style="font-size: 2rem; font-weight: 700;">{{ $stats['clean'] }}</div>
        <div style="opacity: 0.9; margin-top: 0.5rem;">Clean Rooms</div>
    </div>
    <div style="background: linear-gradient(135deg, var(--danger) 0%, #c82333 100%); color: white; padding: 1.5rem; border-radius: 12px;">
        <div style="font-size: 2rem; font-weight: 700;">{{ $stats['dirty'] }}</div>
        <div style="opacity: 0.9; margin-top: 0.5rem;">Dirty Rooms</div>
    </div>
    <div style="background: linear-gradient(135deg, var(--info) 0%, #138496 100%); color: white; padding: 1.5rem; border-radius: 12px;">
        <div style="font-size: 2rem; font-weight: 700;">{{ $stats['in_progress'] }}</div>
        <div style="opacity: 0.9; margin-top: 0.5rem;">In Progress</div>
    </div>
</div>

<!-- Filters -->
<div style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; gap: 1rem;">
        <select name="status" style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="clean" {{ request('status') == 'clean' ? 'selected' : '' }}>Clean</option>
            <option value="dirty" {{ request('status') == 'dirty' ? 'selected' : '' }}>Dirty</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
        </select>
        <select name="assigned_to" style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;" onchange="this.form.submit()">
            <option value="">All Staff</option>
            @foreach($staff as $member)
            <option value="{{ $member->id }}" {{ request('assigned_to') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
            @endforeach
        </select>
    </form>
</div>

<x-admin.card title="Rooms ({{ $housekeeping->total() }})">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-gray);">
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Room</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Type</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Status</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Assigned To</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Last Cleaned</th>
                    <th style="text-align: right; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($housekeeping as $item)
                <tr style="border-bottom: 1px solid var(--border-gray);">
                    <td style="padding: 1rem 0.75rem; font-weight: 600;">{{ $item->room->room_number }}</td>
                    <td style="padding: 1rem 0.75rem;">{{ $item->room->roomType->name }}</td>
                    <td style="padding: 1rem 0.75rem;">
                        <x-admin.badge :status="$item->status" />
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        @if($item->assigned_to)
                        {{ $item->assignedUser->name }}
                        @else
                        <span style="color: var(--text-muted);">Unassigned</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        @if($item->last_cleaned_at)
                        {{ $item->last_cleaned_at->diffForHumans() }}
                        @else
                        <span style="color: var(--text-muted);">Never</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 0.75rem; text-align: right;">
                        <button onclick="openStatusModal({{ $item->id }}, '{{ $item->status }}', '{{ $item->notes }}')" style="padding: 0.5rem 1rem; background: var(--primary-gold); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            Update
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $housekeeping->links() }}
    </div>
</x-admin.card>

<!-- Status Update Modal -->
<div id="statusModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 2rem; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; font-weight: 600;">Update Housekeeping Status</h3>
        <form id="statusForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Status</label>
                <select name="status" id="statusSelect" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                    <option value="dirty">Dirty</option>
                    <option value="in_progress">In Progress</option>
                    <option value="clean">Clean</option>
                </select>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Notes</label>
                <textarea name="notes" id="notesTextarea" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px; min-height: 100px;"></textarea>
            </div>
            <div style="display: flex; gap: 1rem;">
                <x-admin.button type="primary" style="flex: 1;">Update Status</x-admin.button>
                <button type="button" onclick="closeStatusModal()" style="flex: 1; padding: 0.75rem; background: var(--text-muted); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openStatusModal(id, status, notes) {
    document.getElementById('statusForm').action = '/admin/housekeeping/' + id;
    document.getElementById('statusSelect').value = status;
    document.getElementById('notesTextarea').value = notes || '';
    document.getElementById('statusModal').style.display = 'flex';
}

function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}
</script>
@endpush
@endsection
