@extends('layouts.admin')

@section('title', 'Guest Profile')
@section('page-title', 'Guest Profile')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <x-admin.button type="outline" href="{{ route('admin.guests.index') }}">← Back to Guests</x-admin.button>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Guest Info -->
    <div>
        <x-admin.card title="Guest Information">
            <form method="POST" action="{{ route('admin.guests.update', $guest) }}">
                @csrf
                @method('PUT')
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">First Name</label>
                        <input type="text" name="first_name" value="{{ $guest->first_name }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;" required>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Last Name</label>
                        <input type="text" name="last_name" value="{{ $guest->last_name }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;" required>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email</label>
                        <input type="email" name="email" value="{{ $guest->email }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;" required>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Phone</label>
                        <input type="text" name="phone" value="{{ $guest->phone }}" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;" required>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Address</label>
                        <textarea name="address" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px; min-height: 100px;">{{ $guest->address }}</textarea>
                    </div>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
                    <x-admin.button type="primary">Update Information</x-admin.button>
                    @endif
                </div>
            </form>
        </x-admin.card>

        <!-- Stats -->
        <x-admin.card title="Statistics" style="margin-top: 1.5rem;">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Total Bookings</span>
                    <span style="font-weight: 700;">{{ $stats['total_bookings'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Completed</span>
                    <span style="font-weight: 700;">{{ $stats['completed_bookings'] }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span>Upcoming</span>
                    <span style="font-weight: 700;">{{ $stats['upcoming_bookings'] }}</span>
                </div>
                <div style="border-top: 2px solid var(--border-gray); padding-top: 1rem; display: flex; justify-content: space-between;">
                    <span style="font-weight: 700;">Total Spent</span>
                    <span style="font-weight: 700; color: var(--primary-gold);">₱{{ number_format($stats['total_spent'], 2) }}</span>
                </div>
            </div>
        </x-admin.card>
    </div>

    <!-- Booking History -->
    <x-admin.card title="Booking History">
        @if($guest->bookings->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($guest->bookings as $booking)
            <div style="padding: 1.5rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <div style="font-weight: 700; font-size: 1.125rem;">{{ $booking->reference_number }}</div>
                        <div style="color: var(--text-muted); font-size: 0.875rem;">{{ $booking->created_at->format('F d, Y') }}</div>
                    </div>
                    <x-admin.badge :status="$booking->status" />
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Room</div>
                        <div style="font-weight: 600;">{{ $booking->room->room_number }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Check-in</div>
                        <div style="font-weight: 600;">{{ $booking->check_in_date->format('M d, Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Amount</div>
                        <div style="font-weight: 700; color: var(--primary-gold);">₱{{ number_format($booking->total_amount, 2) }}</div>
                    </div>
                </div>

                <div style="margin-top: 1rem;">
                    <x-admin.button type="outline" size="sm" href="{{ route('admin.bookings.show', $booking) }}">
                        View Details
                    </x-admin.button>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p style="text-align: center; padding: 3rem; color: var(--text-muted);">No booking history</p>
        @endif
    </x-admin.card>
</div>
@endsection
