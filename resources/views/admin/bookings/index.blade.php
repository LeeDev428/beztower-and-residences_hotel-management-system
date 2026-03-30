@extends('layouts.admin')

@section('title', 'Bookings')
@section('page-title', 'Booking Management')

@section('content')
@php
    $activeStatus = (string) request('status', '');
    $filterBase = request()->except(['status', 'page']);
@endphp
<!-- Stats -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
    <a href="{{ route('admin.bookings.index', array_merge($filterBase, ['status' => 'pending'])) }}" style="background: linear-gradient(135deg, var(--warning) 0%, #d39e00 100%); color: white; padding: 1.25rem; border-radius: 12px; text-decoration: none; border: {{ $activeStatus === 'pending' ? '2px solid #2c2c2c' : '2px solid transparent' }};">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['pending'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Pending</div>
    </a>
    <a href="{{ route('admin.bookings.index', array_merge($filterBase, ['status' => 'confirmed'])) }}" style="background: linear-gradient(135deg, var(--success) 0%, #20873a 100%); color: white; padding: 1.25rem; border-radius: 12px; text-decoration: none; border: {{ $activeStatus === 'confirmed' ? '2px solid #2c2c2c' : '2px solid transparent' }};">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['confirmed'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Confirmed</div>
    </a>
    <a href="{{ route('admin.bookings.index', array_merge($filterBase, ['status' => 'checked_in'])) }}" style="background: linear-gradient(135deg, var(--info) 0%, #138496 100%); color: white; padding: 1.25rem; border-radius: 12px; text-decoration: none; border: {{ $activeStatus === 'checked_in' ? '2px solid #2c2c2c' : '2px solid transparent' }};">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['checked_in'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Checked In</div>
    </a>
    <a href="{{ route('admin.bookings.index', array_merge($filterBase, ['status' => 'checked_out'])) }}" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; padding: 1.25rem; border-radius: 12px; text-decoration: none; border: {{ $activeStatus === 'checked_out' ? '2px solid #2c2c2c' : '2px solid transparent' }};">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['checked_out'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Checked Out</div>
    </a>
    <a href="{{ route('admin.bookings.index', array_merge($filterBase, ['status' => 'cancelled'])) }}" style="background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%); color: white; padding: 1.25rem; border-radius: 12px; text-decoration: none; border: {{ $activeStatus === 'cancelled' ? '2px solid #2c2c2c' : '2px solid transparent' }};">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $stats['cancelled'] }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">Cancelled</div>
    </a>
    <a href="{{ route('admin.bookings.index', $filterBase) }}" style="background: linear-gradient(135deg, var(--primary-gold) 0%, var(--dark-gold) 100%); color: white; padding: 1.25rem; border-radius: 12px; text-decoration: none; border: {{ $activeStatus === '' ? '2px solid #2c2c2c' : '2px solid transparent' }};">
        <div style="font-size: 1.75rem; font-weight: 700;">{{ $bookings->total() }}</div>
        <div style="opacity: 0.9; font-size: 0.875rem;">All Bookings</div>
    </a>
</div>

<!-- Filters -->
<div style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
        <input type="date" name="date_from" value="{{ request('date_from') }}" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
        <input type="date" name="date_to" value="{{ request('date_to') }}" style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
        @if($activeStatus !== '')
            <input type="hidden" name="status" value="{{ $activeStatus }}">
        @endif
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
                        <div style="font-size: 0.75rem; color: var(--text-muted);">{{ optional($booking->created_at)->format('M d, Y') ?? 'N/A' }}</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <div style="font-weight: 600;">{{ optional($booking->guest)->name ?? 'Guest not available' }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">{{ optional($booking->guest)->email ?? 'No email' }}</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        @if($booking->rooms->isNotEmpty())
                            <div style="font-weight: 600;">{{ $booking->rooms->count() }} room(s)</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">
                                Room {{ optional($booking->rooms->first())->room_number ?? 'Unassigned' }}
                                @if($booking->rooms->count() > 1)
                                    +{{ $booking->rooms->count() - 1 }} more
                                @endif
                            </div>
                        @else
                            <div style="font-weight: 600;">{{ optional($booking->room)->room_number ?? 'Unassigned' }}</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted);">{{ optional($booking->roomType)->name ?? 'Room type not available' }}</div>
                        @endif
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <div>{{ optional($booking->check_in_date)->format('M d') ?? 'N/A' }} - {{ optional($booking->check_out_date)->format('M d, Y') ?? 'N/A' }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $booking->total_nights ?? 0 }} nights</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        @if($booking->payments->whereIn('payment_status', ['verified', 'completed'])->first())
                            @php
                                $payment = $booking->payments->whereIn('payment_status', ['verified', 'completed'])->first();
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
                        ₱{{ number_format($booking->final_total ?? $booking->total_amount, 2) }}
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
        {{ $bookings->appends(request()->query())->links() }}
    </div>
</x-admin.card>
@endsection
