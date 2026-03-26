<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed</title>
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
            <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower and Residences" style="pointer-events: none; user-select: none; -webkit-user-drag: none;">
            <h1>✅ Booking Confirmed</h1>
            <p>Your booking is now confirmed.</p>
        </div>
        
        <div class="content">
            @php
                $reservedRooms = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
            @endphp
            <p>Dear {{ $booking->guest->first_name }} {{ $booking->guest->last_name }},</p>
            
            <div class="success-badge">
                <strong>Your booking has been successfully confirmed.</strong>
            </div>

            <p>Thank you for choosing Bez Tower and Residences!</p>
            <p>Your booking has been successfully confirmed, and the room is currently reserved for you for the next 8 hours. Please ensure that you send your proof of payment within this time frame to secure your reservation.</p>
            <p>If we do not receive your proof of payment within 8 hours, the reservation will be cancelled. However, if you have already submitted your proof of payment, kindly disregard this message.</p>

            <p><strong>Here are your booking details:</strong></p>
            
            <div class="info-box">
                <div class="info-row">
                    <span class="label">Booking Reference:</span>
                    <span class="value"><strong>{{ $booking->booking_reference }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Room(s):</span>
                    <span class="value">
                        @foreach($reservedRooms as $reservedRoom)
                            {{ $reservedRoom->roomType->name }}@if(!$loop->last), @endif
                        @endforeach
                    </span>
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
                    <span class="label">Guests:</span>
                    <span class="value">{{ $booking->number_of_guests }}</span>
                </div>
            </div>

            <p>If you have any questions or need assistance, please feel free to contact us:</p>
            <ul>
                <li><strong>Email:</strong> beztowerresidences@gmail.com</li>
                <li><strong>Phone:</strong> (02) 88075046 or 09171221429</li>
            </ul>

            <p>We look forward to welcoming you!</p>
            
            <p>Best regards,<br>
            <strong>Bez Tower and Residences</strong></p>
        </div>
        
        <div class="footer">
            <p><strong>Bez Tower and Residences</strong></p>
            <p>205 F. Blumentritt Street, Brgy. Pedro Cruz<br>San Juan City, Philippines</p>
            <p>Email: <a href="mailto:beztowerresidences@gmail.com">beztowerresidences@gmail.com</a> | Phone: (02) 88075046 or 09171221429</p>
        </div>
    </div>
</body>
</html>
