<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Reschedule Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222; background: #f6f6f6; padding: 20px;">
    <div style="max-width: 640px; margin: 0 auto; background: #fff; border-radius: 10px; padding: 24px; border: 1px solid #e5e5e5;">
        <h2 style="margin-top: 0;">Booking Reschedule Confirmation</h2>

        <p>Dear {{ optional($booking->guest)->name ?? 'Guest' }},</p>

        <p>
            Your requested reschedule to
            <strong>{{ optional($booking->check_in_date)->format('F d, Y') ?? 'N/A' }}</strong>
            has been approved. Your booking has been successfully updated.
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
