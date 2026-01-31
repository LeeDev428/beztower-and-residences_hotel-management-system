<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Cancelled</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #4caf50, #81c784);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
            color: #333;
            line-height: 1.6;
        }
        .content p {
            margin: 15px 0;
        }
        .booking-details {
            background-color: #f9f9f9;
            border-left: 4px solid #4caf50;
            padding: 20px;
            margin: 20px 0;
        }
        .booking-details h3 {
            margin-top: 0;
            color: #4caf50;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #666;
        }
        .value {
            color: #333;
        }
        .contact-info {
            background-color: #e8f5e9;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .contact-info h3 {
            margin-top: 0;
            color: #2e7d32;
        }
        .contact-item {
            margin: 10px 0;
            color: #333;
        }
        .contact-item i {
            color: #4caf50;
            margin-right: 10px;
        }
        .footer {
            background-color: #2c2c2c;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 14px;
        }
        .footer a {
            color: #81c784;
            text-decoration: none;
        }
        .logo-text {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo-text">Bez Tower & Residences</div>
            <h1>Booking Cancellation Notice</h1>
        </div>

        <div class="content">
            <p>Good day, <strong>{{ $booking->guest->name }}</strong>,</p>

            <p>We would like to inform you that your booking has been <strong>successfully canceled</strong>.</p>

            <div class="booking-details">
                <h3>Cancelled Booking Details</h3>
                <div class="detail-row">
                    <span class="label">Booking Reference:</span>
                    <span class="value">{{ $booking->booking_reference }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Room:</span>
                    <span class="value">{{ $booking->room->roomType->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Original Check-in Date:</span>
                    <span class="value">{{ $booking->check_in_date->format('F d, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Original Check-out Date:</span>
                    <span class="value">{{ $booking->check_out_date->format('F d, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Cancelled On:</span>
                    <span class="value">{{ $booking->cancelled_at->format('F d, Y h:i A') }}</span>
                </div>
                @if($booking->cancellation_reason)
                <div class="detail-row">
                    <span class="label">Reason:</span>
                    <span class="value">{{ $booking->cancellation_reason }}</span>
                </div>
                @endif
            </div>

            <p><strong>Important Information:</strong></p>
            <ul>
                <li>Please note that all payments made (down payment or full payment) are <strong>non-refundable</strong>.</li>
                <li>However, you may <strong>reschedule your booking within one week</strong> of this cancellation.</li>
                <li>The new rescheduled date must be <strong>within one month</strong> from your original booking date.</li>
            </ul>

            <p>If you require further clarification or would like to double-check whether your refund has already been processed, or if you wish to reschedule your booking, kindly contact Bez Tower using the details below:</p>

            <div class="contact-info">
                <h3>Contact Information</h3>
                <div class="contact-item">
                    <strong>📧 Email:</strong> beztowerresidences@gmail.com
                </div>
                <div class="contact-item">
                    <strong>📱 Mobile:</strong> 0917 122 1429
                </div>
                <div class="contact-item">
                    <strong>📞 Landline:</strong> (02) 8807 5046
                </div>
                <div class="contact-item">
                    <strong>📍 Address:</strong> 205 F. Blumentritt Street, Brgy. Pedro Cruz, San Juan City, Philippines
                </div>
            </div>

            <p>Thank you for your understanding.</p>

            <p style="margin-top: 30px;">Best regards,<br>
            <strong>Bez Tower Residences Team</strong></p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Bez Tower & Residences. All rights reserved.</p>
            <p>205 F. Blumentritt Street, Brgy. Pedro Cruz, San Juan City, Philippines</p>
        </div>
    </div>
</body>
</html>
