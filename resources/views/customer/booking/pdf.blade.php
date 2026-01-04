<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmation - {{ $booking->booking_reference }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #d4af37;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c2c2c;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .booking-ref {
            background: #f4e4c1;
            padding: 10px;
            text-align: center;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c2c2c;
            border-bottom: 2px solid #d4af37;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table td {
            padding: 8px 5px;
            border-bottom: 1px solid #eee;
        }
        table td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .price-table {
            background: #f9f9f9;
            padding: 15px;
        }
        .price-table td {
            padding: 5px 0;
        }
        .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #d4af37;
            padding-top: 10px !important;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">BEZ TOWER & RESIDENCES</div>
        <div class="subtitle">205 F. Blumentritt Street, Brgy. Pedro Cruz, San Juan City, Philippines</div>
        <div class="subtitle">Phone: +1 234 567 8910 | Email: info@beztower.com</div>
    </div>

    <div class="booking-ref">
        BOOKING CONFIRMATION<br>
        Reference: {{ $booking->booking_reference }}
    </div>

    <!-- Guest Information -->
    <div class="section">
        <div class="section-title">Guest Information</div>
        <table>
            <tr>
                <td>Name:</td>
                <td>{{ $booking->guest->first_name }} {{ $booking->guest->last_name }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $booking->guest->email }}</td>
            </tr>
            <tr>
                <td>Phone:</td>
                <td>{{ $booking->guest->phone }}</td>
            </tr>
            @if($booking->guest->country)
            <tr>
                <td>Country:</td>
                <td>{{ $booking->guest->country }}</td>
            </tr>
            @endif
            @if($booking->guest->address)
            <tr>
                <td>Address:</td>
                <td>{{ $booking->guest->address }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Booking Details -->
    <div class="section">
        <div class="section-title">Booking Details</div>
        <table>
            <tr>
                <td>Room Type:</td>
                <td>{{ $booking->room->roomType->name }}</td>
            </tr>
            <tr>
                <td>Room Number:</td>
                <td>{{ $booking->room->room_number }}</td>
            </tr>
            <tr>
                <td>Check-in Date:</td>
                <td>{{ \Carbon\Carbon::parse($booking->check_in_date)->format('F d, Y') }} at 2:00 PM</td>
            </tr>
            <tr>
                <td>Check-out Date:</td>
                <td>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }} at 12:00 PM</td>
            </tr>
            <tr>
                <td>Number of Nights:</td>
                <td>{{ $booking->total_nights }}</td>
            </tr>
            <tr>
                <td>Number of Guests:</td>
                <td>{{ $booking->number_of_guests }}</td>
            </tr>
            <tr>
                <td>Booking Status:</td>
                <td><span class="status-badge status-{{ strtolower($booking->status) }}">{{ strtoupper($booking->status) }}</span></td>
            </tr>
        </table>
    </div>

    <!-- Price Breakdown -->
    <div class="section">
        <div class="section-title">Price Breakdown</div>
        <div class="price-table">
            <table>
                <tr>
                    <td>Room Charges ({{ $booking->total_nights }} night(s) × ₱{{ number_format($booking->room->roomType->base_price, 2) }}):</td>
                    <td style="text-align: right;">₱{{ number_format($booking->subtotal, 2) }}</td>
                </tr>
                
                @if($booking->extras && $booking->extras->count() > 0)
                <tr>
                    <td colspan="2" style="font-weight: bold; padding-top: 10px;">Additional Services:</td>
                </tr>
                @foreach($booking->extras as $extra)
                <tr>
                    <td style="padding-left: 20px;">{{ $extra->name }} ({{ $extra->pivot->quantity }} × ₱{{ number_format($extra->pivot->price_at_booking, 2) }})</td>
                    <td style="text-align: right;">₱{{ number_format($extra->pivot->quantity * $extra->pivot->price_at_booking, 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td>Subtotal Extras:</td>
                    <td style="text-align: right;">₱{{ number_format($booking->extras_total, 2) }}</td>
                </tr>
                @endif

                @if($booking->tax_amount > 0)
                <tr>
                    <td>Tax (12%):</td>
                    <td style="text-align: right;">₱{{ number_format($booking->tax_amount, 2) }}</td>
                </tr>
                @endif

                <tr class="total-row">
                    <td>TOTAL AMOUNT:</td>
                    <td style="text-align: right;">₱{{ number_format($booking->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    @if($booking->special_requests)
    <!-- Special Requests -->
    <div class="section">
        <div class="section-title">Special Requests</div>
        <p>{{ $booking->special_requests }}</p>
    </div>
    @endif

    @if($booking->payments && $booking->payments->count() > 0)
    <!-- Payment Information -->
    <div class="section">
        <div class="section-title">Payment Information</div>
        <table>
            @foreach($booking->payments as $payment)
            <tr>
                <td>Payment Type:</td>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td>
            </tr>
            <tr>
                <td>Payment Method:</td>
                <td>{{ strtoupper($payment->payment_method) }}</td>
            </tr>
            <tr>
                <td>Reference Number:</td>
                <td>{{ $payment->payment_reference }}</td>
            </tr>
            <tr>
                <td>Amount Paid:</td>
                <td>₱{{ number_format($payment->amount, 2) }}</td>
            </tr>
            <tr>
                <td>Payment Status:</td>
                <td><span class="status-badge status-{{ $payment->payment_status }}">{{ strtoupper($payment->payment_status) }}</span></td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <!-- Important Information -->
    <div class="section">
        <div class="section-title">Important Information</div>
        <ul style="margin: 0; padding-left: 20px;">
            <li>Please bring a valid government-issued ID upon check-in</li>
            <li>Check-in time: 2:00 PM onwards</li>
            <li>Check-out time: 12:00 PM</li>
            <li>Free cancellation up to 24 hours before check-in</li>
            <li>For inquiries, contact us at beztower05@gmail.com/(02) 88075046 or 09171221429</li>
        </ul>
    </div>

    <div class="footer">
        <p><strong>Beztower & Residences</strong></p>
        <p>This is a computer-generated document. No signature is required.</p>
        <p>Generated on {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</body>
</html>
