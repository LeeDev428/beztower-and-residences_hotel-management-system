@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('content')
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
    <!-- Revenue Report -->
    <x-admin.card title="Revenue Report">
        <div style="text-align: center; padding: 2rem 0;">
            <svg style="width: 80px; height: 80px; margin: 0 auto 1rem; color: var(--primary-gold);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Revenue Report</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Detailed revenue breakdown and analysis</p>
            <x-admin.button type="primary" href="{{ route('admin.reports.revenue') }}">
                Generate Report
            </x-admin.button>
        </div>
    </x-admin.card>

    <!-- Occupancy Report -->
    <x-admin.card title="Occupancy Report">
        <div style="text-align: center; padding: 2rem 0;">
            <svg style="width: 80px; height: 80px; margin: 0 auto 1rem; color: var(--success);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Occupancy Report</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Room occupancy rates and trends</p>
            <x-admin.button type="success" href="{{ route('admin.reports.occupancy') }}">
                Generate Report
            </x-admin.button>
        </div>
    </x-admin.card>

    <!-- Bookings Export -->
    <x-admin.card title="Export Bookings">
        <div style="text-align: center; padding: 2rem 0;">
            <svg style="width: 80px; height: 80px; margin: 0 auto 1rem; color: var(--info);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Export Data</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Download booking data as CSV</p>
            <form method="GET" action="{{ route('admin.reports.export', 'bookings') }}">
                <input type="date" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid var(--border-gray); border-radius: 6px;">
                <input type="date" name="end_date" value="{{ now()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid var(--border-gray); border-radius: 6px;">
                <x-admin.button type="info">
                    Export CSV
                </x-admin.button>
            </form>
        </div>
    </x-admin.card>
</div>

<!-- Quick Stats -->
<div style="margin-top: 2rem;">
    <x-admin.card title="Quick Overview">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; padding: 1rem 0;">
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-gold);">
                    {{ \App\Models\Booking::count() }}
                </div>
                <div style="color: var(--text-muted); margin-top: 0.5rem;">Total Bookings</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--success);">
                    {{ \App\Models\Room::count() }}
                </div>
                <div style="color: var(--text-muted); margin-top: 0.5rem;">Total Rooms</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--info);">
                    {{ \App\Models\Guest::count() }}
                </div>
                <div style="color: var(--text-muted); margin-top: 0.5rem;">Total Guests</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-gold);">
                    ₱{{ number_format(\App\Models\Payment::whereIn('payment_status', ['verified', 'completed'])->sum('amount'), 0) }}
                </div>
                <div style="color: var(--text-muted); margin-top: 0.5rem;">Total Revenue</div>
            </div>
        </div>
    </x-admin.card>
</div>
@endsection
