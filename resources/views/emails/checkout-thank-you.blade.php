<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Staying with Us</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background:#f4f4f4; color:#2c2c2c;">
    <div style="max-width:600px; margin:20px auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 5px 20px rgba(0,0,0,0.1);">
        <div style="background:linear-gradient(135deg, #4caf50, #81c784); color:#fff; text-align:center; padding:24px;">
            <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower and Residences" style="max-width:140px; background:#fff; padding:8px; border-radius:8px; margin-bottom:10px;">
            <h1 style="margin:0; font-size:26px;">Thank You!</h1>
            <p style="margin:8px 0 0;">We appreciate your stay at Beztower & Residences</p>
        </div>

        <div style="padding:24px; line-height:1.6;">
            <p>Dear {{ $booking->guest->name }},</p>

            <p>Thank you for staying with us. Your booking has been successfully checked out.</p>

            <div style="background:#f9f9f9; border-left:4px solid #4caf50; border-radius:6px; padding:14px; margin:18px 0;">
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #eee;">
                    <span style="font-weight:600; color:#666;">Booking Reference:</span>
                    <span>{{ $booking->booking_reference }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #eee;">
                    <span style="font-weight:600; color:#666;">Room:</span>
                    <span>{{ $booking->room->roomType->name }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #eee;">
                    <span style="font-weight:600; color:#666;">Check-out Date:</span>
                    <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0;">
                    <span style="font-weight:600; color:#666;">Total Amount:</span>
                    <span style="font-weight:700;">PHP {{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>

            <p>We hope to welcome you again soon. Safe travels.</p>

            <p style="margin-top:20px;">Warm regards,<br><strong>Beztower & Residences Team</strong></p>
        </div>

        <div style="background:#2c2c2c; color:#fff; text-align:center; padding:16px; font-size:13px;">
            <div>205 F. Blumentritt Street, Brgy. Pedro Cruz, San Juan City</div>
            <div style="margin-top:6px;">(02) 88075046 or 09171221429</div>
        </div>
    </div>
</body>
</html>
