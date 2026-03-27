<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Confirmed</title>
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
            background: linear-gradient(135deg, #2c2c2c, #3a3a3a);
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
            color: #d4af37;
        }
        .content {
            padding: 30px;
        }
        .success-badge {
            background: #4caf50;
            color: white;
            padding: 14px;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            margin: 20px 0;
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
            text-align: right;
            margin-left: 12px;
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
    @php
        $guestName = optional($booking->guest)->name;
        if (empty($guestName)) {
            $guestName = trim((string) ((optional($booking->guest)->first_name ?? '') . ' ' . (optional($booking->guest)->last_name ?? '')));
        }
        if ($guestName === '') {
            $guestName = 'Guest';
        }

        $originalCheckIn = $booking->original_check_in_date ?? $booking->check_in_date;
        $reservedRooms = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
    @endphp

    <div class="container">
        <div class="header" style="background: #2c2c2c; color: #ffffff; text-align: center; padding: 30px;">
            <p style="margin: 0; color: #ffffff; font-size: 18px; font-weight: 600; line-height: 1.4;">Your updated stay details are ready.</p>
        </div>

        <div class="content">
            <p>Dear {{ $guestName }},</p>

            <div class="success-badge">
                Your booking reschedule request has been approved.
            </div>

            <p>Your reservation has been successfully updated. Please review your updated booking details below.</p>

            <div class="info-box">
                <div class="info-row">
                    <span class="label">Booking Reference:</span>
                    <span class="value"><strong>{{ $booking->booking_reference }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Room(s):</span>
                    <span class="value">
                        @foreach($reservedRooms as $reservedRoom)
                            {{ optional($reservedRoom->roomType)->name ?? ('Room ' . ($reservedRoom->room_number ?? 'N/A')) }}@if(!$loop->last), @endif
                        @endforeach
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Original Check-in:</span>
                    <span class="value">{{ $originalCheckIn ? \Carbon\Carbon::parse($originalCheckIn)->format('F d, Y') : 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">New Check-in:</span>
                    <span class="value"><strong>{{ optional($booking->check_in_date)->format('F d, Y') ?? 'N/A' }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="label">Check-out:</span>
                    <span class="value">{{ optional($booking->check_out_date)->format('F d, Y') ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Total Nights:</span>
                    <span class="value">{{ (int) ($booking->total_nights ?? 0) }} night(s)</span>
                </div>
            </div>

            <p>If you have any questions, you can contact us anytime:</p>
            <ul>
                <li><strong>Email:</strong> beztowerresidences@gmail.com</li>
                <li><strong>Phone:</strong> (02) 88075046 or 09171221429</li>
            </ul>

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
