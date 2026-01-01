@extends('layouts.admin')

@section('title', 'Booking Details')
@section('page-title', 'Booking Details')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <x-admin.button type="outline" href="{{ route('admin.bookings.index') }}">← Back to Bookings</x-admin.button>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Main Details -->
    <div>
        <x-admin.card title="Booking Information">
            <div style="display: grid; gap: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Reference Number</div>
                        <div style="font-weight: 700; font-size: 1.125rem;">{{ $booking->reference_number }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Booking Status</div>
                        <x-admin.badge :status="$booking->status" />
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Check-in</div>
                        <div style="font-weight: 600;">{{ $booking->check_in_date->format('F d, Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Check-out</div>
                        <div style="font-weight: 600;">{{ $booking->check_out_date->format('F d, Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Room</div>
                        <div style="font-weight: 600;">{{ $booking->room->room_number }} - {{ $booking->roomType->name }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Number of Guests</div>
                        <div style="font-weight: 600;">{{ $booking->number_of_guests }}</div>
                    </div>
                </div>

                <!-- Special Requests -->
                @if($booking->special_requests)
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Special Requests</div>
                    <div style="padding: 1rem; background: var(--light-gray); border-radius: 8px;">
                        {{ $booking->special_requests }}
                    </div>
                </div>
                @endif
            </div>
        </x-admin.card>

        <!-- Guest Information -->
        <x-admin.card title="Guest Information" style="margin-top: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Name</div>
                    <div style="font-weight: 600;">{{ $booking->guest->name }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Email</div>
                    <div style="font-weight: 600;">{{ $booking->guest->email }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Phone</div>
                    <div style="font-weight: 600;">{{ $booking->guest->phone }}</div>
                </div>
                @if($booking->guest->address)
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Address</div>
                    <div style="font-weight: 600;">{{ $booking->guest->address }}</div>
                </div>
                @endif
            </div>
        </x-admin.card>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Payment Summary -->
        <x-admin.card title="Payment">
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Room Rate ({{ $booking->number_of_nights }} nights)</span>
                    <span style="font-weight: 600;">₱{{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div style="border-top: 2px solid var(--border-gray); padding-top: 1rem; display: flex; justify-content: space-between;">
                    <span style="font-weight: 700;">Total</span>
                    <span style="font-weight: 700; color: var(--primary-gold); font-size: 1.25rem;">₱{{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>
        </x-admin.card>

        <!-- Actions -->
        <x-admin.card title="Actions" style="margin-top: 1.5rem;">
            <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" style="margin-bottom: 1rem;">
                @csrf
                @method('PUT')
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Update Status</label>
                <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px; margin-bottom: 1rem;">
                    <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="checked_in" {{ $booking->status === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="checked_out" {{ $booking->status === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                    <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <div style="width: 100%;">
                    <x-admin.button type="primary" style="width: 100%;">Update Status</x-admin.button>
                </div>
            </form>
        </x-admin.card>
    </div>
</div>
@endsection
