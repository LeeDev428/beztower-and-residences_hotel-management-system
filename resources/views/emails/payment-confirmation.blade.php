<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; color: #222; margin: 0; padding: 0; }
        .container { max-width: 640px; margin: 20px auto; background: #fff; border-radius: 10px; overflow: hidden; border: 1px solid #e5e5e5; }
        .header { background: #2c2c2c; color: #fff; text-align: center; padding: 24px; }
        .header img { max-width: 130px; background: #fff; border-radius: 8px; padding: 8px; margin-bottom: 12px; }
        .content { padding: 24px; line-height: 1.65; }
        .details { background: #fafafa; border-left: 4px solid #d4af37; padding: 14px; margin: 14px 0; }
        .footer { background: #2c2c2c; color: #fff; text-align: center; padding: 16px; font-size: 13px; }
    </style>
</head>
<body>
    @php
        $reservedRooms = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
        $roomTypeLabel = $reservedRooms->pluck('roomType.name')->filter()->implode(', ');
        if ($roomTypeLabel === '') {
            $roomTypeLabel = optional(optional($booking->room)->roomType)->name ?? 'N/A';
        }
        $guestName = trim((string) ((optional($booking->guest)->first_name ?? '') . ' ' . (optional($booking->guest)->last_name ?? '')));
        if ($guestName === '') {
            $guestName = optional($booking->guest)->name ?? 'Guest';
        }
    @endphp

    <div class="container">
        <div class="header">
            <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower and Residences Logo">
            <h2 style="margin: 0; font-weight: 700;">Booking Confirmation</h2>
        </div>

        <div class="content">
            <p>Dear {{ $guestName }},</p>
            <p>Thank you for choosing Bez Tower and Residences!</p>
            <p>Your booking has been successfully confirmed, and the room is currently reserved for you for the next 8 hours. Please ensure that you send your proof of payment within this time frame to secure your reservation.</p>
            <p>If we do not receive your proof of payment within 8 hours, the reservation will be cancelled. However, if you have already submitted your proof of payment, kindly disregard this message.</p>

            <p>Here are your booking details:</p>
            <div class="details">
                <div>Booking Reference: {{ $booking->booking_reference }}</div>
                <div>Check-in Date: {{ \Carbon\Carbon::parse($booking->check_in_date)->format('F d, Y') }} 2:00 PM</div>
                <div>Check-out Date: {{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }} 12:00 PM</div>
                <div>Room Type: {{ $roomTypeLabel }}</div>
                <div>Number of Guests: {{ (int) ($booking->number_of_guests ?? 0) }}</div>
            </div>

            <p>If you have any questions or need assistance, please feel free to contact us:</p>
            <div>Phone: (02) 88075046 or 09171221429</div>
            <div>Email: beztowerresidences@gmail.com</div>

            <p>We look forward to welcoming you!</p>
            <p>Best regards,<br>Bez Tower and Residences.</p>
        </div>

        <div class="footer">
            Bez Tower and Residences
        </div>
    </div>
</body>
</html>
