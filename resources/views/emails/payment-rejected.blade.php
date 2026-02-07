<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
        <div style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 30px; text-align: center;">
            <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower and Residences" style="max-width: 150px; height: auto; margin-bottom: 15px; border-radius: 10px; pointer-events: none; user-select: none; -webkit-user-drag: none;">
            <h1 style="margin: 0;">⚠️ Payment Rejected</h1>
            <p style="margin: 10px 0 0 0;">Bez Tower and Residences</p>
        </div>
        
        <div style="padding: 30px;">
            <p>Dear {{ $payment->booking->guest->name }},</p>
            
            <p>We regret to inform you that your payment could not be verified and has been rejected.</p>
            
            <div style="background: #f9f9f9; border-left: 4px solid #dc3545; padding: 20px; border-radius: 8px; margin: 20px 0;">
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
        
            <div style="background: #f9f9f9; border-left: 4px solid #4caf50; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h2 style="color: #4caf50; margin-top: 0;">Booking Information</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #666;">Booking Reference:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->booking_reference }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;">Room:</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $payment->booking->room->roomType->name }}</td>
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

            <div style="background: #f9f9f9; border-left: 4px solid #4caf50; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h2 style="color: #4caf50; margin-top: 0;">Total Amount Breakdown</h2>
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

                <tr style="background: #fff3cd; border-top: 2px solid #C9A961;">
                    <td style="padding: 12px 8px; font-weight: bold; font-size: 16px; color: #2c2c2c;">TOTAL AMOUNT:</td>
                    <td style="padding: 12px 8px; font-weight: bold; font-size: 16px; text-align: right; color: #dc3545;">₱{{ number_format($payment->booking->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>
            <ol style="line-height: 1.8;">
                <li>Please review the rejection reason above</li>
                <li>Verify your payment details</li>
                <li>Submit a new payment proof if needed</li>
                <li>Contact us if you need assistance</li>
            </ol>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h2 style="color: #C9A961; margin-top: 0;">Next Steps - Payment Instructions</h2>
            <ol style="line-height: 1.8;">
                <li>Please review the rejection reason above</li>
                <li>Verify your payment details</li>
                <li>Submit a new payment using the methods below</li>
                <li>Contact us if you need assistance</li>
            </ol>

            <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin: 15px 0;">
                <h3 style="margin-top: 0; color: #2c2c2c;">💳 Accepted Payment Methods:</h3>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li><strong>Bank Transfer:</strong>
                        <ul style="margin: 5px 0;">
                            <li>Bank Name: BDO - Banco de Oro</li>
                            <li>Account Name: Beztower & Residences Inc.</li>
                            <li>Account Number: 1234-5678-9012</li>
                        </ul>
                    </li>
                    <li><strong>GCash:</strong> 0917-123-4567 (Beztower & Residences)</li>
                    <li><strong>PayMaya:</strong> 0917-123-4567 (Beztower & Residences)</li>
                    <li><strong>Over-the-counter:</strong> Available at our front desk</li>
                </ul>

                <div style="background: #e8f5e9; padding: 15px; border-radius: 5px; margin-top: 15px; border: 1px solid #4caf50;">
                    <p style="margin: 0;"><strong>📧 Where to Send Proof of Payment:</strong></p>
                    <ul style="margin: 8px 0; padding-left: 20px;">
                        <li><strong>Email:</strong> payments@beztower.com</li>
                        <li><strong>Subject:</strong> Payment Proof - {{ $payment->booking->booking_reference }}</li>
                        <li><strong>Include:</strong> Screenshot/Photo of payment receipt with your booking reference number</li>
                    </ul>
                    <p style="margin: 8px 0 0 0; font-size: 14px;"><em>📱 You can also send via WhatsApp: +63 917 123 4567</em></p>
                </div>
            </div>
        </div>
        
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
