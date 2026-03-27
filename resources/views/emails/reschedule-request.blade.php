<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request to Reschedule Your Booking</title>
</head>
<body style="margin:0; padding:24px; font-family: Arial, sans-serif; line-height:1.6; color:#222; background:#ffffff;">
    <div style="text-align:center; margin-bottom: 14px;">
        <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower and Residences Logo" style="max-width: 130px; height: auto; border-radius: 8px;">
    </div>
    @php
        $guestName = trim((string) (optional($booking->guest)->name ?? ''));
        if ($guestName === '') {
            $guestName = trim((string) ((optional($booking->guest)->first_name ?? '') . ' ' . (optional($booking->guest)->last_name ?? '')));
        }
        if ($guestName === '') {
            $guestName = 'Guest Name';
        }

        $checkInTime = \App\Models\AppSetting::getValue('check_in_time', '14:00');
        $checkOutTime = \App\Models\AppSetting::getValue('check_out_time', '12:00');
    @endphp

    <p>Dear {{ $guestName }},</p>

    <p>Greetings from Bez Tower and Residences!</p>

    <p>We would like to inform you that, due to unforeseen circumstances, we kindly request to reschedule your upcoming booking.</p>

    <p><strong>Original Booking Details:</strong></p>
    <p><strong>Booking Reference:</strong> {{ $booking->booking_reference ?? 'N/A' }}</p>
    <p><strong>Check-in Date:</strong> {{ optional($booking->check_in_date)->format('F d, Y') ?? 'N/A' }} {{ \Carbon\Carbon::createFromFormat('H:i', $checkInTime)->format('g:i A') }}</p>
    <p><strong>Check-out Date:</strong> {{ optional($booking->check_out_date)->format('F d, Y') ?? 'N/A' }} {{ \Carbon\Carbon::createFromFormat('H:i', $checkOutTime)->format('g:i A') }}</p>

    <p>We sincerely apologize for any inconvenience this may cause. We kindly ask that the new rescheduled date be within two (2) weeks from your original booking date.</p>

    <p>We would be happy to assist you in selecting a new date that best fits your schedule within this timeframe. Please reply to this email or contact us through the details below so we can arrange your updated booking as soon as possible.</p>

    <p><strong>Phone:</strong> (02) 88075046 or 09171221429</p>
    <p><strong>Email:</strong> beztowerresidences@gmail.com</p>

    <p>Thank you for your understanding, and we look forward to accommodating you soon.</p>

    <p>Best regards,<br>Bez Tower and Residences</p>
</body>
</html>
