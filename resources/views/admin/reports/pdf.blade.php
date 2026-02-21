<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Report - {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #2c2c2c; line-height: 1.5; }
        
        .header { background: #2c2c2c; color: white; padding: 24px 30px; margin-bottom: 24px; }
        .header h1 { font-size: 22px; font-weight: 700; letter-spacing: 1px; }
        .header .gold { color: #d4af37; }
        .header .subtitle { font-size: 12px; color: #aaa; margin-top: 4px; }
        .header .period { font-size: 13px; color: #d4af37; margin-top: 8px; font-weight: 600; }
        .header .generated { font-size: 10px; color: #999; margin-top: 4px; }

        .section-title { font-size: 14px; font-weight: 700; color: #2c2c2c; border-bottom: 2px solid #d4af37; padding-bottom: 6px; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px; }

        .stats-grid { display: table; width: 100%; margin-bottom: 24px; border-collapse: collapse; }
        .stat-box { display: table-cell; width: 25%; padding: 14px 10px; text-align: center; border: 1px solid #e0e0e0; }
        .stat-box .value { font-size: 26px; font-weight: 700; color: #d4af37; }
        .stat-box .label { font-size: 10px; color: #666; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }

        .section { margin-bottom: 24px; }
        
        .two-col { width: 100%; display: table; border-collapse: collapse; margin-bottom: 24px; }
        .col-half { display: table-cell; width: 49%; vertical-align: top; padding-right: 12px; }
        .col-half:last-child { padding-right: 0; padding-left: 12px; }

        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        table th { background: #2c2c2c; color: #d4af37; padding: 7px 8px; text-align: left; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; font-size: 9px; }
        table td { padding: 6px 8px; border-bottom: 1px solid #f0f0f0; }
        table tr:nth-child(even) td { background: #fafafa; }
        
        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 9px; font-weight: 600; text-transform: capitalize; }
        .badge-confirmed    { background: #d4edda; color: #155724; }
        .badge-checked_in   { background: #cce5ff; color: #004085; }
        .badge-checked_out  { background: #6c757d; color: white; }
        .badge-pending      { background: #fff3cd; color: #856404; }
        .badge-cancelled    { background: #f8d7da; color: #721c24; }
        .badge-rejected_payment { background: #f8d7da; color: #721c24; }

        .revenue-row { display: table; width: 100%; border-collapse: collapse; }
        .revenue-cell { display: table-cell; padding: 5px 0; border-bottom: 1px solid #f0f0f0; }
        .revenue-cell.label { color: #555; }
        .revenue-cell.amount { text-align: right; font-weight: 600; color: #2c2c2c; }

        .total-row { display: table; width: 100%; border-top: 2px solid #d4af37; margin-top: 6px; padding-top: 8px; }
        .total-cell { display: table-cell; font-weight: 700; font-size: 13px; }
        .total-cell.amount { text-align: right; color: #d4af37; font-size: 15px; }

        .footer { border-top: 1px solid #e0e0e0; padding-top: 10px; margin-top: 20px; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1><span class="gold">Beztower</span> & Residences</h1>
        <div class="subtitle">Hotel Management System â€” Official Report</div>
        <div class="period">{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} &mdash; {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</div>
        <div class="generated">Generated: {{ \Carbon\Carbon::now()->format('F d, Y \a\t h:i A') }}</div>
    </div>

    <!-- Quick Stats -->
    <div class="section">
        <div class="section-title">Quick Overview</div>
        <table class="stats-grid" style="border-collapse: collapse; width: 100%;">
            <tr>
                <td class="stat-box">
                    <div class="value">{{ $totalBookings }}</div>
                    <div class="label">Bookings</div>
                </td>
                <td class="stat-box">
                    <div class="value">{{ $totalGuests }}</div>
                    <div class="label">New Guests</div>
                </td>
                <td class="stat-box">
                    <div class="value">{{ $totalRooms }}</div>
                    <div class="label">Total Rooms</div>
                </td>
                <td class="stat-box">
                    <div class="value" style="font-size: 18px;">₱{{ number_format($totalRevenue, 0) }}</div>
                    <div class="label">Revenue</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Two Column: Status Breakdown + Revenue by Type -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
        <tr>
            <td style="width: 49%; vertical-align: top; padding-right: 10px;">
                <div class="section-title">Bookings by Status</div>
                <table>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th style="text-align: right;">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(['pending','confirmed','checked_in','checked_out','cancelled','rejected_payment','rescheduled'] as $st)
                            @if(isset($bookingsByStatus[$st]))
                            <tr>
                                <td><span class="badge badge-{{ $st }}">{{ str_replace('_', ' ', $st) }}</span></td>
                                <td style="text-align: right; font-weight: 600;">{{ $bookingsByStatus[$st]->count }}</td>
                            </tr>
                            @endif
                        @endforeach
                        @if($bookingsByStatus->isEmpty())
                            <tr><td colspan="2" style="text-align: center; color: #999;">No bookings in this period</td></tr>
                        @endif
                    </tbody>
                </table>
            </td>
            <td style="width: 2%;"></td>
            <td style="width: 49%; vertical-align: top; padding-left: 10px;">
                <div class="section-title">Revenue by Room Type</div>
                <table>
                    <thead>
                        <tr>
                            <th>Room Type</th>
                            <th style="text-align: right;">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenueByType as $rt)
                        <tr>
                            <td>{{ $rt->name }}</td>
                            <td style="text-align: right; font-weight: 600;">₱{{ number_format($rt->revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" style="text-align: center; color: #999;">No revenue data</td></tr>
                        @endforelse
                        @if($revenueByType->isNotEmpty())
                        <tr style="border-top: 2px solid #d4af37;">
                            <td style="font-weight: 700;">Total</td>
                            <td style="text-align: right; font-weight: 700; color: #d4af37;">₱{{ number_format($revenueByType->sum('revenue'), 2) }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <!-- Bookings Table -->
    <div class="section">
        <div class="section-title">Booking Records (Latest 50)</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Reference</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Nights</th>
                    <th style="text-align: right;">Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBookings as $i => $booking)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight: 600; color: #d4af37;">{{ $booking->booking_reference }}</td>
                    <td>{{ $booking->guest->name ?? 'N/A' }}</td>
                    <td>{{ $booking->room->room_number ?? 'N/A' }} â€” {{ $booking->roomType->name ?? '' }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</td>
                    <td style="text-align: center;">{{ $booking->total_nights ?? $booking->number_of_nights ?? '-' }}</td>
                    <td style="text-align: right; font-weight: 600;">₱{{ number_format($booking->total_amount, 2) }}</td>
                    <td><span class="badge badge-{{ $booking->status }}">{{ str_replace('_', ' ', $booking->status) }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; color: #999; padding: 20px;">No bookings found in the selected period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        Beztower &amp; Residences &mdash; Hotel Management System &mdash; Confidential Report &mdash; {{ \Carbon\Carbon::now()->format('Y') }}
    </div>
</body>
</html>
