@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
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

    <!-- Bookings Export -->
    <x-admin.card title="Export Bookings">
        <div style="text-align: center; padding: 2rem 0;">
            <svg style="width: 80px; height: 80px; margin: 0 auto 1rem; color: var(--info);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Export Data</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Download booking data as Excel</p>
            <form method="GET" action="{{ route('admin.reports.export', 'bookings') }}">
                <input type="date" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid var(--border-gray); border-radius: 6px;">
                <input type="date" name="end_date" value="{{ now()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid var(--border-gray); border-radius: 6px;">
                <x-admin.button type="info">
                    Export Excel
                </x-admin.button>
            </form>
        </div>
    </x-admin.card>

    <!-- Generate PDF Report -->
    <x-admin.card title="Generate PDF Report">
        <div style="text-align: center; padding: 2rem 0;">
            <svg style="width: 80px; height: 80px; margin: 0 auto 1rem; color: #dc3545;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Generate PDF Report</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Full hotel report with bookings, revenue &amp; stats as PDF</p>
            <form method="GET" action="{{ route('admin.reports.pdf') }}">
                <input type="date" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid var(--border-gray); border-radius: 6px;">
                <input type="date" name="end_date" value="{{ now()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid var(--border-gray); border-radius: 6px;">
                <button type="submit" style="display: inline-block; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #dc3545, #c82333); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.95rem; text-decoration: none; width: 100%;">
                    <i class="fas fa-file-pdf"></i>&nbsp; Download PDF
                </button>
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
