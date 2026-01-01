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
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
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
        .info-box {
            background: #f9f9f9;
            border-left: 4px solid #d4af37;
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
        .highlight {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #d4af37;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
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
            color: #d4af37;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏨 Booking Acknowledgement</h1>
            <p>Beztower & Residences</p>
        </div>
        
        <div class="content">
            <p>Dear {{ $booking->guest->first_name }} {{ $booking->guest->last_name }},</p>
            
            <p>Thank you for choosing Beztower & Residences! We have received your booking request and are pleased to confirm the following details:</p>
            
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
                    <span class="value">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('F d, Y') }} at 2:00 PM</span>
                </div>
                <div class="info-row">
                    <span class="label">Check-out:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }} at 12:00 PM</span>
                </div>
                <div class="info-row">
                    <span class="label">Nights:</span>
                    <span class="value">{{ $booking->number_of_nights }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Guests:</span>
                    <span class="value">{{ $booking->number_of_adults }} Adult(s), {{ $booking->number_of_children }} Child(ren)</span>
                </div>
                <div class="info-row">
                    <span class="label">Total Amount:</span>
                    <span class="value"><strong>₱{{ number_format($booking->total_amount, 2) }}</strong></span>
                </div>
            </div>

            <div class="highlight">
                <strong>⚠️ Important:</strong> Please proceed with the payment within 48 hours to confirm your reservation. You will receive payment instructions in a separate email.
            </div>

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
                <li><strong>Email:</strong> reservations@beztower.com</li>
                <li><strong>Phone:</strong> +1 234 567 8910</li>
            </ul>

            <p>We look forward to welcoming you!</p>
            
            <p>Best regards,<br>
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
