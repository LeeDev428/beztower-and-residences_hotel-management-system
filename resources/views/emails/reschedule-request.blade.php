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

    <p>We are pleased to inform you that your requested reschedule has been successfully approved. Your booking has now been updated with the new schedule.</p>

    <p>Here are your updated booking details:</p>

    <p><strong>Booking Reference:</strong> {{ $booking->booking_reference ?? 'N/A' }}</p>
    <p><strong>Check-in Date:</strong> {{ optional($booking->check_in_date)->format('F d, Y') ?? 'N/A' }} [2:00 PM]</p>
    <p><strong>Check-out Date:</strong> {{ optional($booking->check_out_date)->format('F d, Y') ?? 'N/A' }} [12:00 PM]</p>
    {{-- <p><strong>Room Type:</strong> {{ $primaryRoomType }}</p>
    <p><strong>Number of Guests:</strong> {{ $booking->number_of_guests ?? 'N/A' }}</p> --}}

    {{-- <p>Kindly reply to this email with your preferred new check-in date within the allowed period. We will do our best to accommodate your request based on room availability.</p> --}}

    <p>If you have any questions or need further assistance, please feel free to contact us:</p>
    <p><strong>Phone:</strong> (02) 88075046 or 09171221429</p>
    <p><strong>Email:</strong> beztowerresidences@gmail.com</p>

    <p>We look forward to welcoming you!</p>

    <p>
        Best regards,<br>
        Bez Tower and Residences
    </p>
</body>
</html>
