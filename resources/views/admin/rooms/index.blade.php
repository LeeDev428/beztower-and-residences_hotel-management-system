@extends('layouts.admin')

@section('title', 'Rooms Management')
@section('page-title', 'Rooms Management')

@section('content')
<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
    <form method="GET" style="display: flex; gap: 1rem; flex: 1;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search rooms..." style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
        <select name="type" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            <option value="">All Types</option>
            @foreach($roomTypes as $type)
            <option value="{{ $type->id }}" {{ request('type') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
            @endforeach
        </select>
        <select name="status" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            <option value="">All Status</option>
            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
            <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            <option value="blocked" {{ request('status') == 'blocked' ? 'selected' : '' }}>Blocked</option>
        </select>
        <select name="archived" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            <option value="">Active Rooms</option>
            <option value="yes" {{ request('archived') == 'yes' ? 'selected' : '' }}>Archived Rooms</option>
        </select>
        <x-admin.button type="primary">Filter</x-admin.button>
    </form>
</div>

<x-admin.card title="All Rooms ({{ $rooms->total() }})">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-gray);">
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Room #</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Type</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Floor</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Status</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Amenities</th>
                    <th style="text-align: right; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                <tr style="border-bottom: 1px solid var(--border-gray);">
                    <td style="padding: 1rem 0.75rem; font-weight: 600;">{{ $room->room_number }}</td>
                    <td style="padding: 1rem 0.75rem;">{{ $room->roomType->name }}</td>
                    <td style="padding: 1rem 0.75rem;">Floor {{ $room->floor }}</td>
                    <td style="padding: 1rem 0.75rem;">
                        <x-admin.badge :status="$room->status" />
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                            @foreach($room->amenities->take(3) as $amenity)
                            <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: var(--light-gray); border-radius: 4px;">{{ $amenity->name }}</span>
                            @endforeach
                            @if($room->amenities->count() > 3)
                            <span style="font-size: 0.75rem; padding: 0.25rem 0.5rem; background: var(--light-gray); border-radius: 4px;">+{{ $room->amenities->count() - 3 }}</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding: 1rem 0.75rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                            @if(in_array(auth()->user()->role, ['admin', 'manager', 'receptionist']))
                                <x-admin.button type="outline" size="sm" href="{{ route('admin.rooms.edit', $room) }}">Edit</x-admin.button>
                            @endif
                            @if(auth()->user()->role === 'admin')
                                @if($room->isArchived())
                                    <form method="POST" action="{{ route('admin.rooms.restore', $room) }}" onsubmit="return confirm('Are you sure you want to restore this room?');">
                                        @csrf
                                        <x-admin.button type="success" size="sm">Restore</x-admin.button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Are you sure you want to archive this room?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-admin.button type="warning" size="sm">Archive</x-admin.button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $rooms->links() }}
    </div>
</x-admin.card>

<!-- Floating Add Button -->
@if(auth()->user()->role === 'admin')
<a href="{{ route('admin.rooms.create') }}" 
   style="position: fixed; bottom: 2rem; right: 2rem; width: 60px; height: 60px; background: linear-gradient(135deg, var(--primary-gold) 0%, var(--dark-gold) 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; text-decoration: none; box-shadow: 0 4px 20px rgba(201, 169, 97, 0.4); transition: all 0.3s ease; z-index: 999;"
   onmouseover="this.style.transform='scale(1.1) rotate(90deg)'; this.style.boxShadow='0 6px 25px rgba(201, 169, 97, 0.6)';"
   onmouseout="this.style.transform='scale(1) rotate(0deg)'; this.style.boxShadow='0 4px 20px rgba(201, 169, 97, 0.4)';"
   title="Add New Room">
    +
</a>
@endif
@endsection
