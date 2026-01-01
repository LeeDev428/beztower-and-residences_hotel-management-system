<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0;">Payment Rejected</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 8px 8px;">
        <p>Dear {{ $payment->booking->guest->name }},</p>
        
        <p>We regret to inform you that your payment could not be verified and has been rejected.</p>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2 style="color: #dc3545; margin-top: 0;">Payment Details</h2>
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
                    <td style="padding: 8px 0; font-weight: bold; color: #dc3545;">₱{{ number_format($payment->amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Payment Method:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ strtoupper($payment->payment_method) }}</td>
                </tr>
            </table>
        </div>
        
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <h3 style="margin-top: 0; color: #856404;">Reason for Rejection:</h3>
            <p style="margin-bottom: 0;">{{ $reason }}</p>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2 style="color: #C9A961; margin-top: 0;">Next Steps</h2>
            <ol style="line-height: 1.8;">
                <li>Please review the rejection reason above</li>
                <li>Verify your payment details</li>
                <li>Submit a new payment proof if needed</li>
                <li>Contact us if you need assistance</li>
            </ol>
        </div>
        
        <p>If you have any questions or concerns, please don't hesitate to contact us.</p>
        
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
