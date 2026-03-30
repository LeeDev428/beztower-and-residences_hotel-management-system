@extends('layouts.admin')

@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="admin-reports-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
    <!-- Revenue Report -->
    {{-- <x-admin.card title="Revenue Report">
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
    </x-admin.card> --}}

    <!-- Bookings Export -->
    <x-admin.card title="Export Bookings">
        <div style="text-align: center; padding: 2rem 0;">
            <svg style="width: 80px; height: 80px; margin: 0 auto 1rem; color: var(--info);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Export Data</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Download booking data as Excel for a selected calendar range</p>
            <form method="GET" action="{{ route('admin.reports.export', 'bookings') }}">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; margin-bottom: 0.75rem;">
                    <input type="date" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-gray); border-radius: 6px;">
                    <input type="date" name="end_date" value="{{ now()->endOfMonth()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-gray); border-radius: 6px;">
                </div>
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
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Hotel report with bookings, revenue and stats as PDF for a selected calendar range</p>
            <form method="GET" action="{{ route('admin.reports.pdf') }}">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; margin-bottom: 0.75rem;">
                    <input type="date" name="start_date" value="{{ now()->startOfMonth()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-gray); border-radius: 6px;">
                    <input type="date" name="end_date" value="{{ now()->endOfMonth()->format('Y-m-d') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-gray); border-radius: 6px;">
                </div>
                <button type="submit" style="display: inline-block; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #dc3545, #c82333); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.95rem; text-decoration: none; width: 100%;">
                    <i class="fas fa-file-pdf"></i>&nbsp; Download PDF
                </button>
            </form>
        </div>
    </x-admin.card>


</div>

<!-- Management Analytics -->
<div style="margin-top: 2rem; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.5rem;">
    <x-admin.card title="Most Frequently Booked Rooms">
        <div class="admin-table-wrap" style="overflow-x: auto;">
            <table style="width: 100%; min-width: 420px; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-gray);">
                        <th style="text-align: left; padding: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">Room</th>
                        <th style="text-align: right; padding: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mostBookedRooms as $room)
                        <tr style="border-bottom: 1px solid var(--border-gray);">
                            <td style="padding: 0.6rem;">Room {{ $room->room_number }} <span style="color: var(--text-muted);">({{ $room->room_type_name ?? 'N/A' }})</span></td>
                            <td style="padding: 0.6rem; text-align: right; font-weight: 700; color: var(--primary-gold);">{{ (int) $room->booking_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="padding: 1rem; text-align: center; color: var(--text-muted);">No booking data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>

    <x-admin.card title="Least Booked Rooms">
        <div class="admin-table-wrap" style="overflow-x: auto;">
            <table style="width: 100%; min-width: 420px; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-gray);">
                        <th style="text-align: left; padding: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">Room</th>
                        <th style="text-align: right; padding: 0.6rem; color: var(--text-muted); font-size: 0.82rem;">Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leastBookedRooms as $room)
                        <tr style="border-bottom: 1px solid var(--border-gray);">
                            <td style="padding: 0.6rem;">Room {{ $room->room_number }} <span style="color: var(--text-muted);">({{ $room->room_type_name ?? 'N/A' }})</span></td>
                            <td style="padding: 0.6rem; text-align: right; font-weight: 700; color: #8d8d8d;">{{ (int) $room->booking_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="padding: 1rem; text-align: center; color: var(--text-muted);">No booking data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>
</div>

<div style="margin-top: 2rem;">
    <x-admin.card title="Room Occupancy Trends (Last 6 Months)">
        <div style="display: grid; gap: 0.8rem;">
            @forelse($occupancyTrend as $trend)
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.86rem; margin-bottom: 0.25rem;">
                        <span style="font-weight: 600;">{{ $trend['label'] }}</span>
                        <span style="color: var(--text-muted);">{{ number_format((float) $trend['occupancy_rate'], 2) }}%</span>
                    </div>
                    <div style="width: 100%; height: 12px; border-radius: 999px; background: #ededed; overflow: hidden;">
                        <div style="height: 100%; width: {{ min(100, max(0, (float) $trend['occupancy_rate'])) }}%; background: linear-gradient(135deg, #d4af37, #b8941f);"></div>
                    </div>
                </div>
            @empty
                <p style="color: var(--text-muted);">No occupancy trend data available.</p>
            @endforelse
        </div>
    </x-admin.card>
</div>

<div style="margin-top: 2rem;">
    <x-admin.card title="Booking Frequency Per Room">
        <div class="admin-table-wrap" style="overflow-x: auto;">
            <table style="width: 100%; min-width: 520px; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-gray);">
                        <th style="text-align: left; padding: 0.65rem; color: var(--text-muted); font-size: 0.82rem;">Room</th>
                        <th style="text-align: left; padding: 0.65rem; color: var(--text-muted); font-size: 0.82rem;">Type</th>
                        <th style="text-align: right; padding: 0.65rem; color: var(--text-muted); font-size: 0.82rem;">Bookings</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roomFrequency->sortByDesc('booking_count') as $room)
                        <tr style="border-bottom: 1px solid var(--border-gray);">
                            <td style="padding: 0.65rem; font-weight: 600;">Room {{ $room->room_number }}</td>
                            <td style="padding: 0.65rem; color: var(--text-muted);">{{ $room->room_type_name ?? 'N/A' }}</td>
                            <td style="padding: 0.65rem; text-align: right; font-weight: 700;">{{ (int) $room->booking_count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="padding: 1rem; text-align: center; color: var(--text-muted);">No booking frequency data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>
</div>

<!-- Quick Stats -->
<div style="margin-top: 2rem;">
    <x-admin.card title="Quick Overview">
        <div class="admin-reports-quick-stats" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.2rem; padding: 1rem 0;">
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-gold);">
                    {{ $quickOverview['active_bookings'] ?? 0 }}
                </div>
                <div style="color: var(--text-muted); margin-top: 0.5rem;">Active Bookings</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--success);">
                    {{ $quickOverview['active_rooms'] ?? 0 }}
                </div>
                <div style="color: var(--text-muted); margin-top: 0.5rem;">Active Rooms</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--info);">
                    {{ $quickOverview['active_guests'] ?? 0 }}
                </div>
                <div style="color: var(--text-muted); margin-top: 0.5rem;">Active Guests</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.25rem; font-weight: 700; color: var(--primary-gold);">
                    ₱{{ number_format((float) ($quickOverview['total_revenue'] ?? 0), 0) }}
                </div>
                <div style="color: var(--text-muted); margin-top: 0.5rem;">Revenue (Active Rooms)</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--warning);">
                    {{ $quickOverview['verified_payments_count'] ?? 0 }}
                </div>
                <div style="color: var(--text-muted); margin-top: 0.5rem;">Verified Payments</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: #1976d2;">
                    {{ $quickOverview['occupied_today'] ?? 0 }}
                </div>
                <div style="color: var(--text-muted); margin-top: 0.5rem;">Occupied Today</div>
            </div>
        </div>
    </x-admin.card>
</div>

@push('styles')
<style>
    @media (max-width: 1024px) {
        .admin-reports-grid {
            grid-template-columns: 1fr !important;
        }

        .admin-reports-grid + div[style*="grid-template-columns: repeat(2"] {
            grid-template-columns: 1fr !important;
        }

        .admin-reports-quick-stats {
            grid-template-columns: 1fr 1fr !important;
            gap: 1rem !important;
        }
    }

    @media (max-width: 600px) {
        .admin-reports-quick-stats {
            grid-template-columns: 1fr !important;
            gap: 0.8rem !important;
        }
    }
</style>
@endpush
@endsection
