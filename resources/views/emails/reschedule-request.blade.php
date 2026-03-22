<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request to Reschedule Your Booking</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #222; background: #f6f6f6; padding: 20px;">
    <div style="max-width: 640px; margin: 0 auto; background: #fff; border-radius: 10px; padding: 24px; border: 1px solid #e5e5e5;">
        <h2 style="margin-top: 0;">Request to Reschedule Your Booking</h2>

        <p>Dear {{ optional($booking->guest)->name ?? 'Guest' }},</p>

        <p>
            We regret to inform you that we need to move your booking due to unforeseen circumstances.
            In line with our policy, your reservation may be rescheduled within two (2) weeks from your original check-in date.
        </p>

        <p>
            Kindly reply with your preferred new date within the allowed period, and we will do our best to accommodate your request based on availability.
        </p>

        <p>Thank you for your understanding, and we look forward to assisting you.</p>

        <p style="margin-top: 20px;">
            Best regards,<br>
            <strong>Bez Tower and Residences</strong>
        </p>
    </div>
</body>
</html>
