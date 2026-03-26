<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request to Reschedule Your Booking</title>
</head>
<body style="margin:0; padding:24px; font-family: Arial, sans-serif; line-height:1.6; color:#222; background:#ffffff;">
    <p>Subject: Request to Reschedule Your Booking</p>

    <p>Dear {{ optional($booking->guest)->name ?? 'Guest' }},</p>

    <p>We regret to inform you that we need to move your booking due to unforeseen circumstances.</p>

    <p>As part of our policy, your reservation may be rescheduled within two (2) weeks from your original check-in date.</p>

    <p>Kindly reply with your preferred new date within the allowed period, and we will do our best to accommodate your request based on availability.</p>

    <p>Booking Reference: {{ $booking->booking_reference ?? 'N/A' }}</p>

    <p>Thank you for your understanding, and we look forward to assisting you.</p>

    <p>
        Best regards,<br>
        Bez Tower and Residences
    </p>
</body>
</html>
