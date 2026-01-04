@extends('layouts.admin')

@section('title', 'Bookings')
@section('page-title', 'Booking Management')

@section('content')
<!-- Stats -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
    <div style="background: linear-gradient(135deg, var(--warning) 0%, #d39e00 100%); color: white; padding: 1.25rem; border-radius: 12px;">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['pending'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Pending</div>
    </div>
    <div style="background: linear-gradient(135deg, var(--success) 0%, #20873a 100%); color: white; padding: 1.25rem; border-radius: 12px;">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['confirmed'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Confirmed</div>
    </div>
    <div style="background: linear-gradient(135deg, var(--info) 0%, #138496 100%); color: white; padding: 1.25rem; border-radius: 12px;">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['checked_in'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Checked In</div>
    </div>
    <div style="background: linear-gradient(135deg, var(--primary-gold) 0%, var(--dark-gold) 100%); color: white; padding: 1.25rem; border-radius: 12px;">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $bookings->total() }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Total Bookings</div>
    </div>
</div>

<!-- Filters -->
<div style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
        <select name="status" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Checked In</option>
            <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Checked Out</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
        <x-admin.button type="primary">Filter</x-admin.button>
    </form>
</div>

<x-admin.card title="All Bookings ({{ $bookings->total() }})">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-gray);">
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Reference</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Guest</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Room</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Dates</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Payment Type</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Status</th>
                    <th style="text-align: right; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Amount</th>
                    <th style="text-align: right; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr style="border-bottom: 1px solid var(--border-gray);">
                    <td style="padding: 1rem 0.75rem;">
                        <div style="font-weight: 600;">{{ $booking->booking_reference }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $booking->created_at->format('M d, Y') }}</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <div style="font-weight: 600;">{{ $booking->guest->name }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $booking->guest->email }}</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <div style="font-weight: 600;">{{ $booking->room->room_number }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $booking->roomType->name }}</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <div>{{ $booking->check_in_date->format('M d') }} - {{ $booking->check_out_date->format('M d, Y') }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $booking->total_nights }} nights</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        @if($booking->payments->where('payment_status', 'verified')->first())
                            @php
                                $payment = $booking->payments->where('payment_status', 'verified')->first();
                            @endphp
                            @if($payment->payment_type === 'down_payment')
                            <span style="padding: 0.375rem 0.75rem; background: linear-gradient(135deg, #3498db, #2980b9); color: white; border-radius: 6px; font-size: 0.875rem; font-weight: 600; display: inline-block;">Downpayment</span>
                            @else
                            <span style="padding: 0.375rem 0.75rem; background: linear-gradient(135deg, #27ae60, #229954); color: white; border-radius: 6px; font-size: 0.875rem; font-weight: 600; display: inline-block;">Full Payment</span>
                            @endif
                        @else
                            <span style="font-size: 0.875rem; color: var(--text-muted);">—</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <x-admin.badge :status="$booking->status" />
                    </td>
                    <td style="padding: 1rem 0.75rem; text-align: right; font-weight: 700; color: var(--primary-gold);">
                        ₱{{ number_format($booking->total_amount, 2) }}
                    </td>
                    <td style="padding: 1rem 0.75rem; text-align: right;">
                        <x-admin.button type="outline" size="sm" href="{{ route('admin.bookings.show', $booking) }}">
                            View
                        </x-admin.button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $bookings->links() }}
    </div>
</x-admin.card>
@endsection
