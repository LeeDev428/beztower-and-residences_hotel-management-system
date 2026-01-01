<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #C9A961 0%, #8B7355 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0;">Payment Approved!</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;">
        <p>Dear {{ $payment->booking->guest->name }},</p>
        
        <p>Great news! Your payment has been verified and approved.</p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2 style="color: #C9A961; margin-top: 0;">Payment Details</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #666;">Booking Reference:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->booking_reference }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Payment Reference:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->payment_reference }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Amount:</td>
                    <td style="padding: 8px 0; font-weight: bold; color: #C9A961;">₱{{ number_format($payment->amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Payment Method:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ strtoupper($payment->payment_method) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Verified Date:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->verified_at->format('F d, Y h:i A') }}</td>
                </tr>
            </table>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2 style="color: #C9A961; margin-top: 0;">Booking Information</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #666;">Room:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->room->room_number }} - {{ $payment->booking->roomType->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Check-in:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->check_in_date->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Check-out:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->check_out_date->format('F d, Y') }}</td>
                </tr>
            </table>
        </div>
        
        <p>Your booking is now confirmed. We look forward to welcoming you!</p>
        
        <p style="margin-top: 30px;">
            Best regards,<br>
            <strong>BezTower and Residences</strong>
        </p>
    </div>
    
    <div style="text-align: center; padding: 20px; color: #666; font-size: 0.875rem;">
        <p>This is an automated email. Please do not reply to this message.</p>
    </div>
</body>
</html>
