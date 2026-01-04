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
                    <td style="padding: 8px 0; color: #666;">Booking Reference:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->booking_reference }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Room:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->room->room_number }} - {{ $payment->booking->room->roomType->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Check-in:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->check_in_date->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Check-out:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->check_out_date->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Nights:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->total_nights }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Guests:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->number_of_guests }} Guest(s)</td>
                </tr>
            </table>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2 style="color: #C9A961; margin-top: 0;">Total Amount Breakdown</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #666;">Room Charges ({{ $payment->booking->total_nights }} night(s) × ₱{{ number_format($payment->booking->room->roomType->price_per_night, 2) }}):</td>
                    <td style="padding: 8px 0; text-align: right;">₱{{ number_format($payment->booking->subtotal, 2) }}</td>
                </tr>
                
                @if($payment->booking->extras && $payment->booking->extras->count() > 0)
                    <tr>
                        <td colspan="2" style="padding: 12px 0; font-weight: bold; background: #f9f9f9; border-radius: 4px; padding-left: 8px;">Additional Services/Extras:</td>
                    </tr>
                    @foreach($payment->booking->extras as $extra)
                    <tr>
                        <td style="padding: 5px 0 5px 20px; color: #666;">{{ $extra->name }} ({{ $extra->pivot->quantity }} × ₱{{ number_format($extra->pivot->price_at_booking, 2) }}):</td>
                        <td style="padding: 5px 0; text-align: right;">₱{{ number_format($extra->pivot->quantity * $extra->pivot->price_at_booking, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Subtotal Extras:</td>
                        <td style="padding: 8px 0; text-align: right; font-weight: bold;">₱{{ number_format($payment->booking->extras_total, 2) }}</td>
                    </tr>
                @endif

                @if($payment->booking->tax_amount > 0)
                <tr>
                    <td style="padding: 8px 0; color: #666;">Tax (12%):</td>
                    <td style="padding: 8px 0; text-align: right;">₱{{ number_format($payment->booking->tax_amount, 2) }}</td>
                </tr>
                @endif

                <tr style="background: #f0f7f0; border-top: 2px solid #C9A961;">
                    <td style="padding: 12px 8px; font-weight: bold; font-size: 16px; color: #2c2c2c;">TOTAL AMOUNT:</td>
                    <td style="padding: 12px 8px; font-weight: bold; font-size: 16px; text-align: right; color: #C9A961;">₱{{ number_format($payment->booking->total_amount, 2) }}</td>
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
