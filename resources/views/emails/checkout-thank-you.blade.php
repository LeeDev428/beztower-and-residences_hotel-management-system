<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Staying with Us</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background:#f4f4f4; color:#2c2c2c;">
    <div style="max-width:600px; margin:20px auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 5px 20px rgba(0,0,0,0.1);">
        <div style="background:linear-gradient(135deg, #4caf50, #81c784); color:#fff; text-align:center; padding:24px;">
            <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower and Residences Logo" style="max-width:130px; height:auto; margin-bottom:10px; border-radius:8px; background:#fff; padding:6px;">
            <h1 style="margin:0; font-size:26px;">Thank You!</h1>
            <p style="margin:8px 0 0;">We appreciate your stay at Beztower & Residences</p>
        </div>

        <div style="padding:24px; line-height:1.6;">
            @php
                $reservedRooms = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
            @endphp
            <p>Dear {{ $booking->guest->name }},</p>

            <p>Thank you for staying with us. Your booking has been successfully checked out.</p>

            <div style="background:#f9f9f9; border-left:4px solid #4caf50; border-radius:6px; padding:14px; margin:18px 0;">
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #eee;">
                    <span style="font-weight:600; color:#666;">Booking Reference:</span>
                    <span>{{ $booking->booking_reference }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #eee;">
                    <span style="font-weight:600; color:#666;">Room(s):</span>
                    <span>
                        @foreach($reservedRooms as $reservedRoom)
                            {{ $reservedRoom->roomType->name }}@if(!$loop->last), @endif
                        @endforeach
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #eee;">
                    <span style="font-weight:600; color:#666;">Check-out Date:</span>
                    <span>{{ \Carbon\Carbon::parse($booking->check_out_date)->format('F d, Y') }}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding:6px 0;">
                    <span style="font-weight:600; color:#666;">Total Amount:</span>
                    <span style="font-weight:700;">PHP {{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>

            @php
                $perRoomAdditionalCharge = 0;
                if ($reservedRooms->isNotEmpty()) {
                    $perRoomAdditionalCharge = (float) $reservedRooms->sum(function ($room) {
                        return (float) ($room->pivot->additional_charge ?? 0);
                    });
                }

                $manualAdjustment = (float) ($booking->manual_adjustment ?? 0);
                $billingAdjustmentTotal = $manualAdjustment;
            @endphp

            @if($booking->extras_total > 0 || abs($billingAdjustmentTotal) > 0.00001 || $perRoomAdditionalCharge > 0)
            <div style="background:#f9f9f9; border-left:4px solid #d4af37; border-radius:6px; padding:14px; margin:18px 0;">
                <div style="font-weight:700; margin-bottom:8px; color:#2c2c2c;">Additional Charges Breakdown</div>

                @if($booking->extras_total > 0)
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #eee;">
                    <span style="font-weight:600; color:#666;">Amenities & Services</span>
                    <span style="font-weight:700;">PHP {{ number_format($booking->extras_total, 2) }}</span>
                </div>
                @endif

                @if(abs($billingAdjustmentTotal) > 0.00001)
                <div style="display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #eee;">
                    <span style="font-weight:600; color:#666;">Billing Adjustment & Charges</span>
                    <span style="font-weight:700; color: {{ $billingAdjustmentTotal < 0 ? '#2e7d32' : '#c62828' }};">
                        {{ $billingAdjustmentTotal < 0 ? '-PHP ' : 'PHP ' }}{{ number_format(abs($billingAdjustmentTotal), 2) }}
                    </span>
                </div>
                @endif

                @if($perRoomAdditionalCharge > 0)
                <div style="display:flex; justify-content:space-between; padding:6px 0;">
                    <span style="font-weight:600; color:#666;">Per-Room Additional Charge</span>
                    <span style="font-weight:700;">PHP {{ number_format($perRoomAdditionalCharge, 2) }}</span>
                </div>
                @endif
            </div>
            @endif

            @php
                $feedbackFormUrl = 'https://docs.google.com/forms/d/e/1FAIpQLSePHkVvYTb0zSOXeD_yz-6hlFbDBn7SrF4pCS71d7ky3Et5tA/viewform';
                $feedbackQrPath = public_path('images/feedback/qrcode.png');
                $feedbackQrUrl = 'https://beztowerresidences.com/images/feedback/qrcode.png';
                $feedbackQrSrc = $feedbackQrUrl;

                if (isset($message) && file_exists($feedbackQrPath)) {
                    $feedbackQrSrc = $message->embed($feedbackQrPath);
                } elseif (file_exists($feedbackQrPath)) {
                    $feedbackQrSrc = $feedbackQrUrl;
                }
            @endphp

            <div style="background:#f7fbff; border:1px solid #d7e9ff; border-radius:8px; padding:16px; margin:18px 0; text-align:center;">
                <div style="font-weight:700; margin-bottom:8px; color:#1d3557;">We Value Your Feedback</div>
                <p style="margin:0 0 10px;">Please scan the QR code or tap the link below to complete our short feedback survey.</p>
                <img src="{{ $feedbackQrSrc }}" alt="Feedback Survey QR" style="max-width:180px; width:100%; height:auto; border:1px solid #d5d5d5; border-radius:8px; background:#fff; padding:6px;">
                <p style="margin:10px 0 0;"><a href="{{ $feedbackFormUrl }}" target="_blank" style="color:#0b63ce; word-break:break-all;">{{ $feedbackFormUrl }}</a></p>
            </div>

            <p>We hope to welcome you again soon. Safe travels.</p>

            <p style="margin-top:20px;">Warm regards,<br><strong>Beztower & Residences Team</strong></p>
        </div>

        <div style="background:#2c2c2c; color:#fff; text-align:center; padding:16px; font-size:13px;">
            <div>205 F. Blumentritt Street, Brgy. Pedro Cruz, San Juan City</div>
            <div style="margin-top:6px;">(02) 88075046 or 09171221429</div>
        </div>
    </div>
</body>
</html>
