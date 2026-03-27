<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Reschedule Request Update</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222; background: #f6f6f6; padding: 20px;">
    <div style="max-width: 640px; margin: 0 auto; background: #fff; border-radius: 10px; padding: 24px; border: 1px solid #e5e5e5;">
        <div style="text-align: center; margin-bottom: 14px;">
            <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower and Residences Logo" style="max-width: 130px; height: auto; border-radius: 8px;">
        </div>
        <h2 style="margin-top: 0;">Booking Reschedule Request Update</h2>

        <p>Dear {{ optional($booking->guest)->name ?? 'Guest' }},</p>

        <p>
            @if(!empty($requestedDate))
                Your requested date (<strong>{{ \Carbon\Carbon::parse($requestedDate)->format('F d, Y') }}</strong>) is not available.
            @else
                Your requested date is not available.
            @endif
            Please provide another preferred date within the allowed period.
        </p>

        <p>
            Booking Reference: <strong>{{ $booking->booking_reference }}</strong>
        </p>

        <p style="margin-top: 20px;">
            Best regards,<br>
            <strong>Bez Tower and Residences</strong>
        </p>
    </div>
</body>
</html>
