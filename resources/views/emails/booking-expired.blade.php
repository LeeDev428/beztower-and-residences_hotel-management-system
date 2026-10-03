<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Automatically Declined</title>
</head>
<body style="margin:0; padding:24px; background:#f5f5f5; color:#2c2c2c; font-family:Arial,sans-serif; line-height:1.6;">
    <div style="max-width:620px; margin:0 auto; overflow:hidden; border:1px solid #e3e3e3; border-radius:10px; background:#ffffff;">
        <div style="padding:28px; background:#2c2c2c; color:#ffffff; text-align:center;">
            <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower and Residences" style="max-width:120px; height:auto; margin-bottom:14px; border-radius:8px;">
            <h1 style="margin:0; font-size:23px;">Reservation Automatically Declined</h1>
        </div>

        <div style="padding:30px;">
            <p>Hello {{ $booking->guest?->name ?? 'Guest' }},</p>

            <p>We did not receive your payment proof within the eight-hour payment window, so your reservation was automatically declined and the selected room allocation has been released.</p>

            <div style="margin:22px 0; padding:18px; border-left:4px solid #c0392b; background:#fff7f6;">
                <p style="margin:0 0 8px;"><strong>Booking reference:</strong> {{ $booking->booking_reference }}</p>
                <p style="margin:0 0 8px;"><strong>Check-in:</strong> {{ optional($booking->check_in_date)->format('F d, Y') }}</p>
                <p style="margin:0 0 8px;"><strong>Check-out:</strong> {{ optional($booking->check_out_date)->format('F d, Y') }}</p>
                <p style="margin:0;"><strong>Payment deadline:</strong> {{ optional($booking->expires_at)->format('F d, Y h:i A') }}</p>
            </div>

            <p>No payment was recorded for this reservation. If you still wish to stay with us, please return to the website and create a new reservation, subject to room availability.</p>

            <p>If you submitted payment before the deadline and believe this notice was sent in error, contact us immediately and provide your booking reference and transaction details.</p>

            <p style="margin-bottom:0;">Bez Tower &amp; Residences<br>(02) 88075046 or 09171221429<br>beztowerresidences@gmail.com</p>
        </div>
    </div>
</body>
</html>
