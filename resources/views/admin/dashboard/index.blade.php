@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div style="display: grid; gap: 1.5rem;">
    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        <x-admin.stat-card 
            title="Total Revenue (Today)"
            :value="'₱' . number_format($revenueToday, 2)"
            color="linear-gradient(135deg, #D4AF37 0%, #B8941F 100%)">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-slot>
            <x-slot name="subtitle">This Month: ₱{{ number_format($revenueThisMonth, 2) }}</x-slot>
        </x-admin.stat-card>

        <x-admin.stat-card 
            title="Arrivals Today"
            :value="$arrivalsToday->count()"
            color="linear-gradient(135deg, #17a2b8 0%, #138496 100%)">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </x-slot>
            <x-slot name="subtitle">Departures: {{ $departuresToday->count() }}</x-slot>
        </x-admin.stat-card>

        <x-admin.stat-card 
            title="Pending Actions"
            :value="$pendingBookings + $pendingPayments"
            color="linear-gradient(135deg, #ffc107 0%, #d39e00 100%)">
            <x-slot name="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 48px; height: 48px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </x-slot>
            <x-slot name="subtitle">Bookings: {{ $pendingBookings }} | Payments: {{ $pendingPayments }}</x-slot>
        </x-admin.stat-card>
    </div>

    <!-- Two Column Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        <!-- Recent Bookings -->
        <x-admin.card title="Recent Bookings">
            <x-slot name="action">
                <x-admin.button type="outline" size="sm" href="{{ route('admin.bookings.index') }}">
                    View All
                </x-admin.button>
            </x-slot>

            @if($recentBookings->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-gray);">
                            <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Guest</th>
                            <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Room</th>
                            <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Check-in</th>
                            <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Status</th>
                            <th style="text-align: right; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                        <tr style="border-bottom: 1px solid var(--border-gray);">
                            <td style="padding: 1rem 0.75rem;">
                                <div style="font-weight: 600;">{{ $booking->guest->name }}</div>
                                <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $booking->guest->email }}</div>
                            </td>
                            <td style="padding: 1rem 0.75rem;">{{ $booking->room->room_number }}</td>
                            <td style="padding: 1rem 0.75rem;">{{ $booking->check_in_date->format('M d, Y') }}</td>
                            <td style="padding: 1rem 0.75rem;">
                                <x-admin.badge :status="$booking->status" />
                            </td>
                            <td style="padding: 1rem 0.75rem; text-align: right; font-weight: 600;">
                                ₱{{ number_format($booking->total_amount, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No recent bookings</p>
            @endif
        </x-admin.card>

        <!-- Quick Stats -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Arrivals Today -->
            <x-admin.card title="Arrivals Today ({{ $arrivalsToday->count() }})">
                @if($arrivalsToday->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($arrivalsToday as $arrival)
                    <div style="padding: 0.75rem; background: var(--light-gray); border-radius: 8px;">
                        <div style="font-weight: 600; margin-bottom: 0.25rem;">{{ $arrival->guest->name }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">Room {{ $arrival->room->room_number }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <p style="text-align: center; color: var(--text-muted); padding: 1rem;">No arrivals today</p>
                @endif
            </x-admin.card>

            <!-- Housekeeping Status -->
            <x-admin.card title="Housekeeping">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--success);"></span>
                            Clean
                        </span>
                        <span style="font-weight: 600;">{{ $cleanRooms }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--danger);"></span>
                            Dirty
                        </span>
                        <span style="font-weight: 600;">{{ $dirtyRooms }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="width: 10px; height: 10px; border-radius: 50%; background: var(--info);"></span>
                            In Progress
                        </span>
                        <span style="font-weight: 600;">{{ $inProgressRooms }}</span>
                    </div>
                </div>
                <div style="margin-top: 1rem;">
                    <x-admin.button type="outline" size="sm" href="{{ route('admin.housekeeping.index') }}" style="width: 100%;">
                        Manage Housekeeping
                    </x-admin.button>
                </div>
            </x-admin.card>
        </div>
    </div>

    <!-- Monthly Revenue Chart -->
    <x-admin.card title="Revenue Overview (Last 12 Months)">
        <canvas id="revenueChart" style="max-height: 300px;"></canvas>
    </x-admin.card>

    <!-- Bookings Chart -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
        <x-admin.card title="Booking Trends (Last 12 Months)">
            <canvas id="bookingsChart" style="max-height: 250px;"></canvas>
        </x-admin.card>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const monthlyData = @json($monthlyRevenue);
    
    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month),
            datasets: [{
                label: 'Revenue (₱)',
                data: monthlyData.map(item => item.revenue),
                borderColor: '#D4AF37',
                backgroundColor: 'rgba(212, 175, 55, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#D4AF37',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Bookings Chart
    const ctxBookings = document.getElementById('bookingsChart').getContext('2d');
    const bookingsData = @json($monthlyBookings);
    
    new Chart(ctxBookings, {
        type: 'bar',
        data: {
            labels: bookingsData.map(item => item.month),
            datasets: [{
                label: 'Bookings',
                data: bookingsData.map(item => item.count),
                backgroundColor: 'rgba(23, 162, 184, 0.8)',
                borderColor: '#17a2b8',
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' bookings';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Occupancy Chart
    const ctxOccupancy = document.getElementById('occupancyChart').getContext('2d');
    const occupancyData = @json($monthlyOccupancy);
    
    new Chart(ctxOccupancy, {
        type: 'line',
        data: {
            labels: occupancyData.map(item => item.month),
            datasets: [{
                label: 'Occupancy Rate (%)',
                data: occupancyData.map(item => item.occupancy),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#28a745',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
