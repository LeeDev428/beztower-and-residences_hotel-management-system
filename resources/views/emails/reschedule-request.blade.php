<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request to Reschedule Your Booking</title>
</head>
<body style="margin:0; padding:24px; font-family: Arial, sans-serif; line-height:1.6; color:#222; background:#ffffff;">
    <p>Subject: Request to Reschedule Your Booking</p>

    @php
        $reservedRooms = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
        $primaryRoomType = $reservedRooms->first()?->roomType?->name ?? 'N/A';
    @endphp

    <p>Dear {{ optional($booking->guest)->name ?? 'Guest Name' }},</p>

    <p>Greetings from Bez Tower and Residences!</p>

    <p>We regret to inform you that, due to unforeseen circumstances, we need to request a reschedule of your upcoming booking. In line with our policy, your reservation may be moved within two (2) weeks from your original check-in date.</p>

    <p>Here are your booking details for reference:</p>

    <p><strong>Booking Reference:</strong> {{ $booking->booking_reference ?? 'N/A' }}</p>
    <p><strong>Check-in Date:</strong> {{ optional($booking->check_in_date)->format('F d, Y') ?? 'N/A' }} [2:00 PM]</p>
    <p><strong>Check-out Date:</strong> {{ optional($booking->check_out_date)->format('F d, Y') ?? 'N/A' }} [12:00 PM]</p>
    <p><strong>Room Type:</strong> {{ $primaryRoomType }}</p>
    <p><strong>Number of Guests:</strong> {{ $booking->number_of_guests ?? 'N/A' }}</p>

    <p>Kindly reply to this email with your preferred new check-in date within the allowed period. We will do our best to accommodate your request based on room availability.</p>

    <p>If you have any questions or need assistance, please feel free to contact us:</p>
    <p><strong>Phone:</strong> (02) 88075046 or 09171221429</p>
    <p><strong>Email:</strong> beztowerresidences@gmail.com</p>

    <p>Thank you for your understanding, and we look forward to assisting you.</p>

    <p>
        Best regards,<br>
        Bez Tower and Residences
    </p>
</body>
</html>
