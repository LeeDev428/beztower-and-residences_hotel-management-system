<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #2c2c2c, #1a1a1a); color: white; padding: 30px; text-align: center;">
            <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower and Residences" style="max-width: 150px; height: auto; margin-bottom: 15px; border-radius: 10px; pointer-events: none; user-select: none; -webkit-user-drag: none;">
            <h1 style="margin: 0;">Check-in Reminder</h1>
            <p style="margin: 10px 0 0 0;">Your arrival is in 24 hours</p>
        </div>

        <div style="padding: 30px;">
            @php
                $reservedRooms = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
                $configuredCheckInTime = \App\Models\AppSetting::getValue('check_in_time', '14:00');
            @endphp

            <p>Dear {{ $booking->guest->name }},</p>
            <p>This is a friendly reminder that your stay at Bez Tower and Residences starts tomorrow.</p>

            <div style="background: #f9f9f9; border-left: 4px solid #d4af37; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h2 style="margin-top: 0; color: #2c2c2c;">Arrival Details</h2>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #666;">Booking Reference:</td>
                        <td style="padding: 8px 0; font-weight: bold;">{{ $booking->booking_reference }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666;">Check-in Date:</td>
                        <td style="padding: 8px 0; font-weight: bold;">{{ $booking->check_in_date->format('F d, Y') }} {{ \Carbon\Carbon::createFromFormat('H:i', $configuredCheckInTime)->format('g:i A') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666;">Room(s):</td>
                        <td style="padding: 8px 0; font-weight: bold;">
                            @foreach($reservedRooms as $reservedRoom)
                                Room {{ $reservedRoom->room_number }} - {{ $reservedRoom->roomType->name }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #666;">Guests:</td>
                        <td style="padding: 8px 0; font-weight: bold;">{{ $booking->number_of_guests }} Guest(s)</td>
                    </tr>
                </table>
            </div>

            <div style="background: #fff9e6; border-left: 4px solid #d4af37; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #2c2c2c;">Before You Arrive</h3>
                <ul style="margin: 0; padding-left: 20px;">
                    <li>Bring a valid government-issued ID.</li>
                    <li>Keep your booking reference ready at check-in.</li>
                    <li>Contact us in advance for special requests.</li>
                </ul>
            </div>

            <p>If you need assistance, contact us at <strong>beztowerresidences@gmail.com</strong> or <strong>0917 122 1429</strong>.</p>

            <p style="margin-top: 30px;">
                Best regards,<br>
                <strong>Bez Tower and Residences</strong>
            </p>
        </div>

        <div style="text-align: center; padding: 20px; color: #666; font-size: 0.875rem;">
            <p>This is an automated email. Please do not reply to this message.</p>
        </div>
    </div>
</body>
</html>
