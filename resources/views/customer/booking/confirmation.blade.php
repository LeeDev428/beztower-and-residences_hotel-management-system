<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Bez Tower & Residences</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f8f8, #ffffff);
            color: #2c2c2c;
            padding: 2rem;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .confirmation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .confirmation-header {
            background: linear-gradient(135deg, #2c2c2c, #1a1a1a);
            color: white;
            padding: 3rem;
            text-align: center;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: #2c2c2c;
        }

        .confirmation-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .gold-text {
            color: #d4af37;
        }

        .booking-ref {
            font-size: 1.2rem;
            margin-top: 1rem;
            padding: 1rem 2rem;
            background: rgba(212, 175, 55, 0.2);
            border-radius: 8px;
            display: inline-block;
        }

        .confirmation-body {
            padding: 3rem;
        }

        .section {
            margin-bottom: 2.5rem;
            padding-bottom: 2rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-size: 1.5rem;
            color: #2c2c2c;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .section-title i {
            color: #d4af37;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .info-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .info-value {
            font-size: 1.1rem;
            color: #2c2c2c;
            font-weight: 600;
        }

        .extras-list {
            list-style: none;
            padding: 0;
        }

        .extra-item {
            padding: 0.8rem;
            background: #f8f8f8;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .extra-name {
            font-weight: 500;
        }

        .extra-price {
            color: #d4af37;
            font-weight: 600;
        }

        .price-summary {
            background: linear-gradient(135deg, #f8f8f8, #fff);
            padding: 1.5rem;
            border-radius: 10px;
            border: 2px solid #d4af37;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .price-row:last-child {
            border-bottom: none;
            font-size: 1.3rem;
            font-weight: 700;
            color: #d4af37;
            padding-top: 1rem;
            margin-top: 0.5rem;
            border-top: 2px solid #d4af37;
        }

        .special-requests {
            background: #fff9e6;
            padding: 1.2rem;
            border-radius: 8px;
            border-left: 4px solid #d4af37;
            font-style: italic;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            flex: 1;
            padding: 1.2rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #2c2c2c;
            border: 2px solid #d4af37;
        }

        .btn-secondary:hover {
            background: #f8f8f8;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .payment-status-card {
            background: linear-gradient(135deg, #fff9e6, #ffffff);
            border: 2px solid #d4af37;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .payment-status-card h3 {
            color: #2c2c2c;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .payment-status-card h3 i {
            color: #d4af37;
        }

        .payment-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .payment-info-item {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .payment-label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .payment-value {
            font-size: 1.1rem;
            color: #2c2c2c;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-verified {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-completed {
            background: #d4edda;
            color: #155724;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .confirmation-header {
                padding: 2rem 1.5rem;
            }

            .confirmation-body {
                padding: 2rem 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }
        }

        @media print {
            body {
                background: white;
            }

            .action-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirmation-card">
            <div class="confirmation-header">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h1>Booking <span class="gold-text">Confirmed!</span></h1>
                <p>Thank you for choosing Bez Tower & Residences</p>
                <div class="booking-ref">
                    <i class="fas fa-barcode"></i> {{ $booking->booking_reference }}
                </div>
            </div>

            <div class="confirmation-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Payment Status Card -->
                @if($booking->payments->count() > 0)
                    @php
                        $latestPayment = $booking->payments->last();
                    @endphp
                    <div class="payment-status-card">
                        <h3><i class="fas fa-credit-card"></i> Payment Status</h3>
                        
                        <div class="payment-info-grid">
                            <div class="payment-info-item">
                                <span class="payment-label">Payment Type</span>
                                <span class="payment-value">{{ ucwords(str_replace('_', ' ', $latestPayment->payment_type)) }}</span>
                            </div>
                            <div class="payment-info-item">
                                <span class="payment-label">Amount Paid</span>
                                <span class="payment-value">₱{{ number_format($latestPayment->amount, 2) }}</span>
                            </div>
                            <div class="payment-info-item">
                                <span class="payment-label">Payment Method</span>
                                <span class="payment-value">{{ strtoupper($latestPayment->payment_method) }}</span>
                            </div>
                            <div class="payment-info-item">
                                <span class="payment-label">Reference Number</span>
                                <span class="payment-value">{{ $latestPayment->payment_reference }}</span>
                            </div>
                            <div class="payment-info-item">
                                <span class="payment-label">Payment Date</span>
                                <span class="payment-value">{{ $latestPayment->payment_date->format('F d, Y g:i A') }}</span>
                            </div>
                            <div class="payment-info-item">
                                <span class="payment-label">Status</span>
                                <span class="payment-value">
                                    <span class="status-badge status-{{ $latestPayment->payment_status }}">
                                        {{ ucfirst($latestPayment->payment_status) }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        @if($latestPayment->payment_status == 'pending')
                            <div class="alert alert-warning" style="margin-top: 1.5rem; margin-bottom: 0;">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Payment Verification Pending</strong>
                                    <p style="margin: 0.5rem 0 0 0;">Your payment proof has been submitted and is being verified by our team. You will receive a confirmation email within 24-48 hours.</p>
                                </div>
                            </div>
                        @elseif($latestPayment->payment_status == 'verified' || $latestPayment->payment_status == 'completed')
                            <div class="alert alert-success" style="margin-top: 1.5rem; margin-bottom: 0;">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Payment Verified!</strong>
                                    <p style="margin: 0.5rem 0 0 0;">Your booking is now confirmed. We are expecting you on {{ $booking->check_in_date->format('F d, Y') }}!</p>
                                </div>
                            </div>
                        @endif

                        @php
                            $remainingAmount = $booking->total_amount - $latestPayment->amount;
                        @endphp

                        @if($remainingAmount > 0 && $latestPayment->payment_type == 'down_payment')
                            <div style="margin-top: 1.5rem; padding: 1.2rem; background: #fff; border-radius: 8px; border: 2px solid #e5e5e5;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <span style="font-size: 0.9rem; color: #666;">Remaining Balance</span>
                                        <p style="font-size: 1.5rem; font-weight: 700; color: #d4af37; margin: 0.3rem 0 0 0;">
                                            ₱{{ number_format($remainingAmount, 2) }}
                                        </p>
                                        <small style="color: #999;">To be paid upon check-in or before</small>
                                    </div>
                                    <i class="fas fa-wallet" style="font-size: 2.5rem; color: #d4af37; opacity: 0.3;"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>Payment Required</strong>
                            <p style="margin: 0.5rem 0 0 0;">Please complete your down payment to confirm this booking. Payment deadline is within 48 hours from booking creation.</p>
                        </div>
                    </div>
                @endif

                <!-- Guest Information -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-user"></i> Guest Information
                    </h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Full Name</span>
                            <span class="info-value">{{ $booking->guest->first_name }} {{ $booking->guest->last_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $booking->guest->email }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Phone</span>
                            <span class="info-value">{{ $booking->guest->phone }}</span>
                        </div>
                        @if($booking->guest->country)
                            <div class="info-item">
                                <span class="info-label">Country</span>
                                <span class="info-value">{{ $booking->guest->country }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-calendar-alt"></i> Booking Details
                    </h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Room Type</span>
                            <span class="info-value">{{ $booking->room->roomType->name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Room Number</span>
                            <span class="info-value">#{{ $booking->room->room_number }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Check-In Date</span>
                            <span class="info-value">{{ $booking->check_in_date->format('F d, Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Check-Out Date</span>
                            <span class="info-value">{{ $booking->check_out_date->format('F d, Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Number of Nights</span>
                            <span class="info-value">{{ $booking->total_nights }} {{ $booking->total_nights == 1 ? 'Night' : 'Nights' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Number of Guests</span>
                            <span class="info-value">{{ $booking->number_of_guests }} {{ $booking->number_of_guests == 1 ? 'Guest' : 'Guests' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Booking Status</span>
                            <span class="info-value">
                                <span class="status-badge status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Additional Services -->
                @if($booking->extras->count() > 0)
                    <div class="section">
                        <h2 class="section-title">
                            <i class="fas fa-plus-circle"></i> Additional Services
                        </h2>
                        <ul class="extras-list">
                            @foreach($booking->extras as $extra)
                                <li class="extra-item">
                                    <span class="extra-name">
                                        <i class="fas fa-check-circle" style="color: #d4af37;"></i>
                                        {{ $extra->name }} (Qty: {{ $extra->pivot->quantity }})
                                    </span>
                                    <span class="extra-price">₱{{ number_format($extra->pivot->quantity * $extra->pivot->price_at_booking, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Special Requests -->
                @if($booking->special_requests)
                    <div class="section">
                        <h2 class="section-title">
                            <i class="fas fa-comment-dots"></i> Special Requests
                        </h2>
                        <div class="special-requests">
                            {{ $booking->special_requests }}
                        </div>
                    </div>
                @endif

                <!-- Price Summary -->
                <div class="section">
                    <h2 class="section-title">
                        <i class="fas fa-receipt"></i> Payment Summary
                    </h2>
                    <div class="price-summary">
                        <div class="price-row">
                            <span>Room Rate (₱{{ number_format($booking->room->roomType->base_price, 2) }} × {{ $booking->total_nights }} {{ $booking->total_nights == 1 ? 'night' : 'nights' }})</span>
                            <span>₱{{ number_format($booking->subtotal, 2) }}</span>
                        </div>
                        @if($booking->extras_total > 0)
                            <div class="price-row">
                                <span>Additional Services</span>
                                <span>₱{{ number_format($booking->extras_total, 2) }}</span>
                            </div>
                        @endif
                        <div class="price-row">
                            <span>Tax (12%)</span>
                            <span>₱{{ number_format($booking->tax_amount, 2) }}</span>
                        </div>
                        <div class="price-row">
                            <span>Total Amount</span>
                            <span>₱{{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                    <a href="{{ route('booking.downloadPDF', $booking->booking_reference) }}" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>

                <!-- Important Information -->
                <div style="margin-top: 2rem; padding: 1.5rem; background: #f8f8f8; border-radius: 8px;">
                    <h3 style="margin-bottom: 1rem; color: #2c2c2c;">
                        <i class="fas fa-info-circle" style="color: #d4af37;"></i> Important Information
                    </h3>
                    <ul style="list-style: none; padding: 0; color: #666; line-height: 1.8;">
                        <li><i class="fas fa-check" style="color: #d4af37; margin-right: 0.5rem;"></i> Check-in time: 2:00 PM</li>
                        <li><i class="fas fa-check" style="color: #d4af37; margin-right: 0.5rem;"></i> Check-out time: 12:00 PM</li>
                        <li><i class="fas fa-check" style="color: #d4af37; margin-right: 0.5rem;"></i> A confirmation email has been sent to {{ $booking->guest->email }}</li>
                        <li><i class="fas fa-check" style="color: #d4af37; margin-right: 0.5rem;"></i> Please present a valid ID upon check-in</li>
                        <li><i class="fas fa-check" style="color: #d4af37; margin-right: 0.5rem;"></i> Free cancellation up to 24 hours before check-in</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
