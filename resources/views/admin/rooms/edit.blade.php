@extends('layouts.admin')

@section('title', 'Edit Room')
@section('page-title', 'Edit Room #' . $room->room_number)

@section('content')
<div style="margin-bottom: 1.5rem;">
    <x-admin.button type="outline" href="{{ route('admin.rooms.index') }}">← Back to Rooms</x-admin.button>
</div>

<x-admin.card title="Room Information">
    <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Room Number *</label>
                <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                @error('room_number')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Room Type *</label>
                <div style="display: flex; gap: 0.5rem; align-items: flex-start;">
                    <select name="room_type_id" required style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                        <option value="">Select Type</option>
                        @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ old('room_type_id', $room->room_type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }} - ₱{{ number_format($type->base_price, 2) }}
                            @if($type->discount_percentage > 0)
                                ({{ $type->discount_percentage }}% OFF)
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <button type="button" onclick="openRoomTypesModal()" class="btn-view-details">View Details</button>
                </div>
                @error('room_type_id')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Floor *</label>
                <input type="number" name="floor" value="{{ old('floor', $room->floor) }}" required min="1" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                @error('floor')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Status *</label>
                <select name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                    <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
                @error('status')
                <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Amenities</label>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; padding: 1rem; background: var(--light-gray); border-radius: 8px;">
                @foreach($amenities as $amenity)
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" 
                        {{ in_array($amenity->id, old('amenities', $room->amenities->pluck('id')->toArray())) ? 'checked' : '' }} 
                        style="cursor: pointer;">
                    <span>{{ $amenity->name }}</span>
                </label>
                @endforeach
            </div>
            @error('amenities')
            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Description</label>
            <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">{{ old('description', $room->description) }}</textarea>
            @error('description')
            <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; gap: 1rem;">
            <x-admin.button type="primary">Update Room</x-admin.button>
            <x-admin.button type="outline" href="{{ route('admin.rooms.index') }}">Cancel</x-admin.button>
        </div>
    </form>
</x-admin.card>
@endsection
