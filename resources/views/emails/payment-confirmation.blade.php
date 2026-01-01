<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmed</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #4caf50, #81c784);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .success-badge {
            background: #4caf50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            margin: 20px 0;
        }
        .info-box {
            background: #f9f9f9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #2c2c2c;
        }
        .payment-box {
            background: #e8f5e9;
            border: 2px solid #4caf50;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            background: #2c2c2c;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .footer a {
            color: #d4af37;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Payment Confirmed</h1>
            <p>Your reservation is now confirmed!</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $booking->guest->first_name }} {{ $booking->guest->last_name }},</p>
            
            <div class="success-badge">
                <strong>🎉 Your payment has been verified and confirmed!</strong>
            </div>

            <p>We are delighted to confirm your reservation at Beztower & Residences. Your room is now secured for your stay.</p>
            
            <div class="info-box">
                <div class="info-row">
                    <span class="label">Booking Reference:</span>
                    <span class="value"><strong>{{ $booking->reference_number }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Room:</span>
                    <span class="value">{{ $booking->room->roomType->name }} - Room {{ $booking->room->room_number }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Check-in:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('F d, Y, l') }} at 2:00 PM</span>
                </div>
                <div class="info-row">
                    <span class="label">Check-out:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y, l') }} at 12:00 PM</span>
                </div>
                <div class="info-row">
                    <span class="label">Nights:</span>
                    <span class="value">{{ $booking->number_of_nights }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Guests:</span>
                    <span class="value">{{ $booking->number_of_adults }} Adult(s), {{ $booking->number_of_children }} Child(ren)</span>
                </div>
            </div>

            <div class="payment-box">
                <h3 style="margin-top:0; color:#2c2c2c;">💳 Payment Details</h3>
                <div class="info-row">
                    <span class="label">Payment Type:</span>
                    <span class="value">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Amount Paid:</span>
                    <span class="value"><strong>₱{{ number_format($payment->amount, 2) }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Payment Method:</span>
                    <span class="value">{{ ucfirst($payment->payment_method) }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Reference Number:</span>
                    <span class="value">{{ $payment->reference_number }}</span>
                </div>
                @if($payment->payment_type === 'down_payment')
                <div class="info-row">
                    <span class="label">Remaining Balance:</span>
                    <span class="value"><strong>₱{{ number_format($booking->total_amount - $payment->amount, 2) }}</strong></span>
                </div>
                <div style="margin-top:15px; padding:10px; background:#fff3cd; border-radius:5px;">
                    <small>📌 The remaining balance can be paid upon check-in or through our online portal.</small>
                </div>
                @endif
            </div>

            <div class="info-box">
                <h3 style="margin-top:0; color:#2c2c2c;">📋 What to Bring</h3>
                <ul style="margin:10px 0; padding-left:20px;">
                    <li>Valid government-issued ID</li>
                    <li>Booking reference number: <strong>{{ $booking->reference_number }}</strong></li>
                    <li>Payment receipt (this email)</li>
                </ul>
            </div>

            <div class="info-box">
                <h3 style="margin-top:0; color:#2c2c2c;">🕒 Check-in/Check-out Times</h3>
                <ul style="margin:10px 0; padding-left:20px;">
                    <li><strong>Check-in:</strong> 2:00 PM onwards</li>
                    <li><strong>Check-out:</strong> 12:00 PM</li>
                    <li><strong>Early check-in/Late check-out:</strong> Subject to availability</li>
                </ul>
            </div>

            <p>Should you have any questions or require assistance, please don't hesitate to contact us:</p>
            <ul>
                <li><strong>Email:</strong> reservations@beztower.com</li>
                <li><strong>Phone:</strong> +1 234 567 8910</li>
            </ul>

            <p>We look forward to providing you with an exceptional stay!</p>
            
            <p>Warm regards,<br>
            <strong>Beztower & Residences Team</strong></p>
        </div>
        
        <div class="footer">
            <p><strong>Beztower & Residences</strong></p>
            <p>205 F. Blumentritt Street, Brgy. Pedro Cruz<br>San Juan City, Philippines</p>
            <p>Email: <a href="mailto:info@beztower.com">info@beztower.com</a> | Phone: +1 234 567 8910</p>
        </div>
    </div>
</body>
</html>
