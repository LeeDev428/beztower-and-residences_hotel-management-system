<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Acknowledgement</title>
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
        .header img {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
            background: white;
            padding: 10px;
            border-radius: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .info-box {
            background: #f9f9f9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box {
            background: #fff3cd;
            border: 2px solid #ff9800;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .qr-box {
            background: #e8f5e9;
            border: 2px solid #4caf50;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .qr-box img {
            max-width: 250px;
            height: auto;
            margin: 15px auto;
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
        .highlight {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #4caf50;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #4caf50, #81c784);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
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
            color: #4caf50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower and Residences" style="pointer-events: none; user-select: none; -webkit-user-drag: none;">
            <h1>🏨 Booking Acknowledgement</h1>
            <p>Bez Tower and Residences</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $booking->guest->first_name }} {{ $booking->guest->last_name }},</p>
            
            <p>Thank you for choosing Bez Tower and Residences! We have received your booking request and are pleased to confirm the following details:</p>

            <div class="warning-box">
                <h3 style="margin:0 0 10px 0; color:#ff6b00;">⏰ Important: Reservation Expiry</h3>
                <p style="margin:0; font-size:16px;"><strong>You have 8 hours to complete your payment before this reservation expires.</strong></p>
                <p style="margin:10px 0 0 0; font-size:14px;">Expiry Time: <strong>{{ \Carbon\Carbon::parse($booking->created_at)->addHours(8)->format('F d, Y - h:i A') }}</strong></p>
            </div>
            
            <div class="info-box">
                <div class="info-row">
                    <span class="label">Booking Reference:</span>
                    <span class="value"><strong>{{ $booking->booking_reference }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Room:</span>
                    <span class="value">{{ $booking->room->roomType->name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Check-in:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('F d, Y') }} at 2:00 PM</span>
                </div>
                <div class="info-row">
                    <span class="label">Check-out:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }} at 12:00 PM</span>
                </div>
                <div class="info-row">
                    <span class="label">Nights:</span>
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

                <div class="info-row" style="background:#e8f5e9; padding:12px; margin-top:10px; border-radius:5px;">
                    <span class="label" style="font-size:18px; color:#2c2c2c;"><strong>TOTAL AMOUNT:</strong></span>
                    <span class="value" style="font-size:18px; color:#4caf50;"><strong>₱{{ number_format($booking->total_amount, 2) }}</strong></span>
                </div>
            </div>

            <div class="qr-box">
                <h3 style="margin:0 0 15px 0; color:#2c2c2c;">💳 Scan to Pay with GCash</h3>
                <img src="{{ asset('images/gcash/gcash.png') }}" alt="GCash QR Code" style="pointer-events: none; user-select: none; -webkit-user-drag: none;">
                <p style="margin:15px 0 0 0; font-size:14px; color:#666;">Scan this QR code to make your payment via GCash</p>
                <div style="background:#fff3cd; padding:12px; border-radius:5px; margin-top:15px;">
                    <p style="margin:0; font-size:13px;"><strong>📱 After payment, please send your proof of payment to:</strong></p>
                    <p style="margin:5px 0 0 0;">📧 Email: <strong>beztower05@gmail.com</strong></p>
                    <p style="margin:3px 0 0 0;">💬 WhatsApp: <strong>09171221429</strong></p>
                    <p style="margin:10px 0 0 0; font-size:12px; color:#666;"><em>Include your booking reference: {{ $booking->booking_reference }}</em></p>
                </div>
            </div>

            {{-- <div class="highlight">
                <strong>⚠️ Important:</strong> Please proceed with the payment within 48 hours to confirm your reservation.
            </div> --}}

            {{-- <div class="info-box">
                <h3 style="margin-top:0; color:#2c2c2c;">💳 Payment Instructions</h3>
                <p style="margin:10px 0;"><strong>Accepted Payment Methods:</strong></p>
                <ul style="margin:10px 0; padding-left:20px;">
                    <li><strong>Bank Transfer:</strong>
                        <ul style="margin:5px 0;">
                            <li>Bank Name: BDO - Banco de Oro</li>
                            <li>Account Name: Beztower & Residences Inc.</li>
                            <li>Account Number: 1234-5678-9012</li>
                        </ul>
                    </li>
                    <li><strong>GCash:</strong> 0917-123-4567 (Beztower & Residences)</li>
                    <li><strong>PayMaya:</strong> 0917-123-4567 (Beztower & Residences)</li>
                    <li><strong>Over-the-counter:</strong> Available at our front desk</li>
                </ul>

                <div style="background:#e8f5e9; padding:15px; border-radius:5px; margin-top:15px; border:1px solid #4caf50;">
                    <p style="margin:0;"><strong>📧 Where to Send Proof of Payment:</strong></p>
                    <ul style="margin:8px 0; padding-left:20px;">
                        <li><strong>Email:</strong> payments@beztower.com</li>
                        <li><strong>Subject:</strong> Payment Proof - {{ $booking->booking_reference }}</li>
                        <li><strong>Include:</strong> Screenshot/Photo of payment receipt with your booking reference number</li>
                    </ul>
                    <p style="margin:8px 0 0 0; font-size:14px;"><em>📱 You can also send via WhatsApp: +63 917 123 4567</em></p>
                </div>

                <div style="background:#fff3cd; padding:12px; border-radius:5px; margin-top:10px;">
                    <small><strong>Note:</strong> Your booking will be confirmed once we verify your payment. This usually takes 2-4 hours during business hours.</small>
                </div>
            </div> --}}

            @if($booking->special_requests)
            <div class="info-box">
                <div class="info-row">
                    <span class="label">Special Requests:</span>
                    <span class="value">{{ $booking->special_requests }}</span>
                </div>
            </div>
            @endif

            <p>If you have any questions or need to make changes to your booking, please contact us:</p>
            <ul>
                <li><strong>Email:</strong> beztower05@gmail.com</li>
                <li><strong>Phone:</strong> (02) 88075046 or 09171221429</li>
            </ul>

            <p>We look forward to welcoming you!</p>
            
            <p>Best regards,<br>
            <strong>Bez Tower and Residences Team</strong></p>
        </div>
        
        <div class="footer">
            <p><strong>Bez Tower and Residences</strong></p>
            <p>205 F. Blumentritt Street, Brgy. Pedro Cruz<br>San Juan City, Philippines</p>
            <p>Email: <a href="mailto:info@beztower.com">info@beztower.com</a> | Phone: (02) 88075046 or 09171221429</p>
        </div>
    </div>
</body>
</html>
