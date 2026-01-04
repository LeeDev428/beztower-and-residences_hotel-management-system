<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-out Reminder</title>
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
            background: linear-gradient(135deg, #2196f3, #64b5f6);
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
        .reminder-box {
            background: #e3f2fd;
            border: 2px solid #2196f3;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .info-box {
            background: #f9f9f9;
            border-left: 4px solid #2196f3;
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
            <h1>🏨 Check-out Reminder</h1>
            <p>Your stay is coming to an end</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $booking->guest->first_name }} {{ $booking->guest->last_name }},</p>
            
            <p>We hope you've enjoyed your stay at Beztower & Residences! This is a friendly reminder that your check-out is scheduled for today.</p>

            <div class="reminder-box">
                <h2 style="margin:0; color:#2196f3; font-size:24px;">Check-out: Today</h2>
                <p style="margin:10px 0 0 0; font-size:18px;">
                    <strong>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y, l') }}</strong><br>
                    <span style="font-size:16px;">by 12:00 PM</span>
                </p>
            </div>

            <div class="info-box">
                <div class="info-row">
                    <span class="label">Booking Reference:</span>
                    <span class="value"><strong>{{ $booking->booking_reference }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Room:</span>
                    <span class="value">{{ $booking->room->roomType->name }} - Room {{ $booking->room->room_number }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Total Nights:</span>
                    <span class="value">{{ $booking->total_nights }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Guests:</span>
                    <span class="value">{{ $booking->number_of_guests }} Guest(s)</span>
                </div>
            </div>

            <div class="info-box">
                <h3 style="margin-top:0; color:#2c2c2c;">💰 Total Amount Breakdown</h3>
                <div class="info-row">
                    <span class="label">Room Charges ({{ $booking->total_nights }} night(s) × ₱{{ number_format($booking->room->roomType->price_per_night, 2) }}):</span>
                    <span class="value">₱{{ number_format($booking->subtotal, 2) }}</span>
                </div>
                
                @if($booking->extras && $booking->extras->count() > 0)
                    <div class="info-row" style="background:#f0f0f0; margin-top:10px; padding:10px; border-radius:5px;">
                        <span class="label" style="color:#2c2c2c;"><strong>Additional Services/Extras:</strong></span>
                    </div>
                    @foreach($booking->extras as $extra)
                    <div class="info-row" style="padding-left:20px;">
                        <span class="label">{{ $extra->name }} ({{ $extra->pivot->quantity }} × ₱{{ number_format($extra->pivot->price_at_booking, 2) }}):</span>
                        <span class="value">₱{{ number_format($extra->pivot->quantity * $extra->pivot->price_at_booking, 2) }}</span>
                    </div>
                    @endforeach
                    <div class="info-row">
                        <span class="label"><strong>Subtotal Extras:</strong></span>
                        <span class="value"><strong>₱{{ number_format($booking->extras_total, 2) }}</strong></span>
                    </div>
                @endif

                @if($booking->tax_amount > 0)
                <div class="info-row">
                    <span class="label">Tax (12%):</span>
                    <span class="value">₱{{ number_format($booking->tax_amount, 2) }}</span>
                </div>
                @endif

                <div class="info-row" style="background:#e3f2fd; padding:12px; margin-top:10px; border-radius:5px;">
                    <span class="label" style="font-size:18px; color:#2c2c2c;"><strong>TOTAL AMOUNT:</strong></span>
                    <span class="value" style="font-size:18px; color:#2196f3;"><strong>₱{{ number_format($booking->total_amount, 2) }}</strong></span>
                </div>
            </div>

            <div class="info-box">
                <h3 style="margin-top:0; color:#2c2c2c;">📋 Check-out Procedures</h3>
                <ol style="margin:10px 0; padding-left:20px;">
                    <li>Please vacate your room by 12:00 PM</li>
                    <li>Return your room key to the front desk</li>
                    <li>Settle any outstanding charges (if applicable)</li>
                    <li>Request a receipt for your records</li>
                </ol>
            </div>

            <div class="info-box">
                <h3 style="margin-top:0; color:#2c2c2c;">🕐 Late Check-out</h3>
                <p style="margin:10px 0;">If you need to extend your stay or arrange a late check-out, please contact our front desk:</p>
                <ul style="margin:10px 0; padding-left:20px;">
                    <li><strong>Front Desk:</strong> (02) 88075046 or 09171221429</li>
                    <li><strong>Email:</strong> frontdesk@beztower.com</li>
                </ul>
                <small style="background:#fff3cd; padding:8px; display:block; border-radius:4px; margin-top:10px;">
                    ⚠️ Late check-out is subject to availability and may incur additional charges.
                </small>
            </div>

            <div class="info-box">
                <h3 style="margin-top:0; color:#2c2c2c;">🧳 Luggage Storage</h3>
                <p style="margin:10px 0;">Need to store your luggage after check-out? We offer complimentary luggage storage service at the front desk.</p>
            </div>

            <div style="background:#e8f5e9; padding:20px; border-radius:8px; margin:20px 0; text-align:center;">
                <h3 style="margin:0; color:#2c2c2c;">💚 Thank You for Staying With Us!</h3>
                <p style="margin:10px 0 0 0;">We hope you had a wonderful experience. We'd love to welcome you back soon!</p>
            </div>

            <!-- Final Receipt Summary -->
            <div class="info-box">
                <h3 style="margin-top:0; color:#2c2c2c;">📄 Final Receipt</h3>
                <div class="info-row">
                    <span class="label">Room Charges ({{ $booking->total_nights }} night(s)):</span>
                    <span class="value">₱{{ number_format($booking->subtotal, 2) }}</span>
                </div>
                
                @if($booking->extras_total > 0)
                <div class="info-row">
                    <span class="label">Additional Services:</span>
                    <span class="value">₱{{ number_format($booking->extras_total, 2) }}</span>
                </div>
                @endif
                
                <div class="info-row">
                    <span class="label">Tax (12%):</span>
                    <span class="value">₱{{ number_format($booking->tax_amount, 2) }}</span>
                </div>
                
                <div class="info-row" style="background:#e8f5e9; padding:12px; border-radius:5px; margin-top:10px;">
                    <span class="label"><strong>Total Amount Paid:</strong></span>
                    <span class="value"><strong>₱{{ number_format($booking->total_amount, 2) }}</strong></span>
                </div>
            </div>

            @if($booking->payments && $booking->payments->count() > 0)
            <div class="info-box">
                <h3 style="margin-top:0; color:#2c2c2c;">💳 Payment Summary</h3>
                @foreach($booking->payments as $payment)
                <div class="info-row">
                    <span class="label">Payment Method:</span>
                    <span class="value">{{ strtoupper($payment->payment_method) }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Reference Number:</span>
                    <span class="value">{{ $payment->payment_reference }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Amount Paid:</span>
                    <span class="value">₱{{ number_format($payment->amount, 2) }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Book Again Invitation -->
            <div style="background: linear-gradient(135deg, #d4af37, #f4e4c1); padding: 2rem; border-radius: 10px; text-align: center; margin: 2rem 0;">
                <h2 style="margin: 0 0 1rem 0; color: #2c2c2c;">💝 We'd Love to See You Again!</h2>
                <p style="margin: 0 0 1.5rem 0; color: #2c2c2c; font-size: 1.05rem;">
                    Thank you for choosing Beztower & Residences. We hope you had a wonderful stay!
                </p>
                <a href="{{ route('home') }}" style="display: inline-block; background: #2c2c2c; color: #d4af37; padding: 1rem 2.5rem; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 1.1rem; margin: 10px 0;">
                    <i class="fas fa-calendar-alt"></i> Book Your Next Stay
                </a>
                <p style="margin: 1.5rem 0 0 0; color: #2c2c2c; font-size: 0.95rem;">
                    Thank you for choosing us. We look forward to serving you again!
                </p>
            </div>

            <p>If you have any questions or feedback about your stay, please don't hesitate to reach out:</p>
            <ul>
                <li><strong>Email:</strong> feedback@beztower.com</li>
                <li><strong>Phone:</strong> (02) 88075046 or 09171221429</li>
            </ul>

            <p>Safe travels!<br>
            <strong>Beztower & Residences Team</strong></p>
        </div>
        
        <div class="footer">
            <p><strong>Beztower & Residences</strong></p>
            <p>205 F. Blumentritt Street, Brgy. Pedro Cruz<br>San Juan City, Philippines</p>
            <p>Email: <a href="mailto:info@beztower.com">info@beztower.com</a> | Phone: (02) 88075046 or 09171221429</p>
            <p style="margin-top:15px; font-size:12px;">
                ⭐ <a href="#" style="color:#d4af37;">Rate your stay</a> | 
                <a href="#" style="color:#d4af37;">Book again</a>
            </p>
        </div>
    </div>
</body>
</html>
