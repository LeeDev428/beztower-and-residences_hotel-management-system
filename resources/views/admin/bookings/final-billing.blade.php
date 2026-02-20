@extends('layouts.admin')

@section('title', 'Final Billing - Booking #' . $booking->booking_reference)
@section('page-title', 'Final Billing')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">

    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-gold); margin: 0;">Final Billing</h2>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Booking #{{ $booking->booking_reference }}</div>
        </div>
        <a href="{{ route('admin.bookings.show', $booking) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; background: var(--light-gray); color: var(--text-muted); border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
            <i class="fas fa-arrow-left"></i> Back to Booking
        </a>
    </div>

    @if(session('success'))
    <div style="padding: 0.9rem 1.25rem; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; margin-bottom: 1.5rem; color: #155724; display: flex; align-items: center; gap: 0.6rem;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Booking Details + Final Total -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">

        <!-- Booking Details -->
        <x-admin.card title="Booking Details">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <tr>
                    <td style="padding: 7px 0; color: var(--text-muted); width: 45%;">Reference</td>
                    <td style="padding: 7px 0; font-weight: 700;">{{ $booking->booking_reference }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; color: var(--text-muted);">Guest</td>
                    <td style="padding: 7px 0; font-weight: 700;">{{ $booking->guest->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; color: var(--text-muted);">Room</td>
                    <td style="padding: 7px 0; font-weight: 700;">{{ $booking->room->room_number }} — {{ $booking->roomType->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; color: var(--text-muted);">Check-in</td>
                    <td style="padding: 7px 0; font-weight: 700;">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; color: var(--text-muted);">Check-out</td>
                    <td style="padding: 7px 0; font-weight: 700;">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; color: var(--text-muted);">Nights</td>
                    <td style="padding: 7px 0; font-weight: 700;">{{ $booking->total_nights ?? $booking->number_of_nights }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; color: var(--text-muted);">Guests</td>
                    <td style="padding: 7px 0; font-weight: 700;">{{ $booking->number_of_guests }}</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0 0; color: var(--primary-gold); font-weight: 700; border-top: 1px solid var(--border-gray);">Room Total</td>
                    <td style="padding: 10px 0 0; font-weight: 800; font-size: 1.1rem; color: var(--primary-gold); border-top: 1px solid var(--border-gray);">₱{{ number_format($booking->total_amount, 2) }}</td>
                </tr>
            </table>
        </x-admin.card>

        <!-- Final Total Summary -->
        <x-admin.card title="Final Total">
            <div style="background: linear-gradient(135deg, #2c2c2c, #3a3a3a); border-radius: 10px; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <span style="color: #ccc; font-size: 1rem; font-weight: 600;">Grand Total:</span>
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--primary-gold);" id="grandTotal">₱{{ number_format($booking->final_total ?? $booking->total_amount, 2) }}</span>
            </div>
            <div style="font-size: 0.9rem;">
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--text-muted);">Room Charges</span>
                    <span style="font-weight: 600;">₱{{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--text-muted);">Early Check-in</span>
                    <span style="font-weight: 600;" id="earlyCheckinDisplay">₱{{ number_format($booking->early_checkin_charge ?? 0, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--text-muted);">Late Checkout</span>
                    <span style="font-weight: 600;" id="lateCheckoutDisplay">₱{{ number_format($booking->late_checkout_charge ?? 0, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--success); font-weight: 600;">PWD/Senior Discount</span>
                    <span style="font-weight: 700; color: var(--success);" id="pwdDiscountDisplay">-₱{{ number_format($booking->pwd_senior_discount ?? 0, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0;">
                    <span style="color: var(--text-muted);">Manual Adjustment</span>
                    <span style="font-weight: 600;" id="manualAdjustmentDisplay">₱{{ number_format($booking->manual_adjustment ?? 0, 2) }}</span>
                </div>
            </div>
        </x-admin.card>
    </div>

    <!-- Billing Form -->
    <form action="{{ route('admin.bookings.updateFinalBilling', $booking) }}" method="POST" id="billingForm">
        @csrf

        <!-- Row: Additional Charges + PWD Discount -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">

            <!-- Early Check-in & Late Checkout -->
            <x-admin.card title="Additional Time Charges">

                <!-- Early Check-in -->
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 700; font-size: 0.9rem; margin-bottom: 0.75rem; color: #333;">Early Check-in <span style="font-weight: 400; color: var(--text-muted);">(Max 5 hours)</span></label>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <button type="button" onclick="decrementCounter('earlyCheckin')"
                            style="width: 38px; height: 38px; background: white; border: 2px solid var(--danger); color: var(--danger); border-radius: 8px; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" name="early_checkin_hours" id="earlyCheckinHours"
                            value="{{ $booking->early_checkin_hours ?? 0 }}" min="0" max="5" readonly
                            style="width: 70px; text-align: center; padding: 0.5rem; border: 1px solid var(--border-gray); border-radius: 8px; font-weight: 700; font-size: 1rem;">
                        <button type="button" onclick="incrementCounter('earlyCheckin')"
                            style="width: 38px; height: 38px; background: white; border: 2px solid var(--success); color: var(--success); border-radius: 8px; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-plus"></i>
                        </button>
                        <div style="flex: 1;">
                            <div style="font-size: 1.2rem; font-weight: 800; color: var(--success);" id="earlyCheckinCharge">₱{{ number_format($booking->early_checkin_charge ?? 0, 2) }}</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">Rate: ₱{{ $hourlyRate }}/hour</div>
                        </div>
                    </div>
                    <input type="hidden" name="early_checkin_charge" id="earlyCheckinChargeInput" value="{{ $booking->early_checkin_charge ?? 0 }}">
                </div>

                <!-- Late Checkout -->
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 700; font-size: 0.9rem; margin-bottom: 0.75rem; color: #333;">Late Checkout <span style="font-weight: 400; color: var(--text-muted);">(Max 5 hours)</span></label>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <button type="button" onclick="decrementCounter('lateCheckout')"
                            style="width: 38px; height: 38px; background: white; border: 2px solid var(--danger); color: var(--danger); border-radius: 8px; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" name="late_checkout_hours" id="lateCheckoutHours"
                            value="{{ $booking->late_checkout_hours ?? 0 }}" min="0" max="5" readonly
                            style="width: 70px; text-align: center; padding: 0.5rem; border: 1px solid var(--border-gray); border-radius: 8px; font-weight: 700; font-size: 1rem;">
                        <button type="button" onclick="incrementCounter('lateCheckout')"
                            style="width: 38px; height: 38px; background: white; border: 2px solid var(--success); color: var(--success); border-radius: 8px; font-size: 1.1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-plus"></i>
                        </button>
                        <div style="flex: 1;">
                            <div style="font-size: 1.2rem; font-weight: 800; color: var(--success);" id="lateCheckoutCharge">₱{{ number_format($booking->late_checkout_charge ?? 0, 2) }}</div>
                            <div style="font-size: 0.78rem; color: var(--text-muted);">Rate: ₱{{ $hourlyRate }}/hour</div>
                        </div>
                    </div>
                    <input type="hidden" name="late_checkout_charge" id="lateCheckoutChargeInput" value="{{ $booking->late_checkout_charge ?? 0 }}">
                </div>

                <div style="background: #e0f2fe; border-left: 4px solid var(--info); border-radius: 6px; padding: 0.75rem 1rem; font-size: 0.85rem; color: #1a5276;">
                    <i class="fas fa-info-circle"></i>&nbsp; Maximum 5 hours allowed for each service
                </div>

            </x-admin.card>

            <!-- PWD/Senior Discount -->
            <x-admin.card title="PWD / Senior Citizen Discount">

                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; padding: 0.75rem 1rem; background: var(--light-gray); border-radius: 8px;">
                    <input type="checkbox" id="hasPwdSenior" name="has_pwd_senior" value="1"
                        {{ $booking->has_pwd_senior ? 'checked' : '' }}
                        onchange="togglePwdSenior()"
                        style="width: 18px; height: 18px; accent-color: var(--success); cursor: pointer;">
                    <label for="hasPwdSenior" style="font-weight: 700; cursor: pointer; font-size: 0.95rem; margin: 0;">Apply PWD/Senior Discount (20%)</label>
                </div>

                <div id="pwdSeniorSection" style="display: {{ $booking->has_pwd_senior ? 'block' : 'none' }};">
                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem;">Number of PWD/Senior Citizens</label>
                        <select name="pwd_senior_count" id="pwdSeniorCount" onchange="calculatePwdDiscount()"
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; background: white; outline: none;">
                            <option value="0">Select count</option>
                            @for($i = 1; $i <= $booking->number_of_guests; $i++)
                            <option value="{{ $i }}" {{ ($booking->pwd_senior_count ?? 0) == $i ? 'selected' : '' }}>
                                {{ $i }} {{ $i == 1 ? 'Person' : 'People' }}
                            </option>
                            @endfor
                        </select>
                    </div>
                    <div style="background: #fff9e6; border-left: 4px solid #f0ad4e; border-radius: 6px; padding: 0.75rem 1rem; font-size: 0.82rem; color: #5a4000; margin-bottom: 1rem;">
                        <i class="fas fa-exclamation-triangle"></i>&nbsp;
                        Discount applies to individual's share only (20% of total · guests)
                    </div>
                    <div style="background: var(--light-gray); border-radius: 10px; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; font-size: 0.95rem;">Discount Amount:</span>
                        <span style="font-size: 1.4rem; font-weight: 800; color: var(--success);" id="pwdDiscountAmount">-₱{{ number_format($booking->pwd_senior_discount ?? 0, 2) }}</span>
                    </div>
                    <input type="hidden" name="pwd_senior_discount" id="pwdSeniorDiscountInput" value="{{ $booking->pwd_senior_discount ?? 0 }}">
                </div>

            </x-admin.card>
        </div>

        <!-- Row: Manual Adjustment + Payment Method -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">

            <x-admin.card title="Manual Adjustment">
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem;">Adjustment Amount</label>
                    <div style="display: flex; align-items: center; border: 1px solid var(--border-gray); border-radius: 8px; overflow: hidden;">
                        <span style="padding: 0.65rem 0.85rem; background: var(--light-gray); color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-gray); font-size: 0.9rem;">₱</span>
                        <input type="number" name="manual_adjustment" id="manualAdjustment"
                            value="{{ $booking->manual_adjustment ?? 0 }}" step="0.01"
                            style="flex: 1; border: none; outline: none; padding: 0.65rem 0.85rem; font-size: 0.9rem;"
                            onchange="calculateTotal()">
                    </div>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.4rem;">Positive for additional charges, negative for discounts</p>
                </div>
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem;">Reason for Adjustment</label>
                    <textarea name="adjustment_reason" rows="5"
                        style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; resize: vertical; box-sizing: border-box;"
                        placeholder="Explain the reason for manual adjustment...">{{ $booking->adjustment_reason }}</textarea>
                </div>
            </x-admin.card>

            <x-admin.card title="Payment Method for Additional Charges">
                <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.75rem; color: #444;">Select Payment Method</label>

                <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border: 2px solid var(--border-gray); border-radius: 8px; cursor: pointer; margin-bottom: 0.75rem;">
                    <input type="radio" name="payment_method" id="paymentCash" value="cash" checked onchange="toggleGcashQR()"
                        style="width: 16px; height: 16px; accent-color: var(--primary-gold);">
                    <div style="font-weight: 600;"><i class="fas fa-money-bill-wave" style="color: var(--success);"></i> Cash</div>
                </label>

                <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border: 2px solid var(--border-gray); border-radius: 8px; cursor: pointer; margin-bottom: 0.75rem;">
                    <input type="radio" name="payment_method" id="paymentGcash" value="gcash" onchange="toggleGcashQR()"
                        style="width: 16px; height: 16px; accent-color: var(--primary-gold);">
                    <div style="font-weight: 600;"><i class="fas fa-mobile-alt" style="color: #0070E0;"></i> GCash</div>
                </label>

                <div id="gcashQRSection" style="display: none; text-align: center; padding: 1rem; border: 1px solid #bee0ff; border-radius: 8px; background: #e8f4ff;">
                    <p style="margin-bottom: 0.75rem; font-weight: 700;"><i class="fas fa-qrcode"></i> Scan to Pay via GCash</p>
                    <img src="{{ asset('images/gcash-qr.png') }}" alt="GCash QR Code" style="max-width: 200px; border: 2px solid #007bff; border-radius: 8px; margin-bottom: 0.75rem;">
                    <p style="font-size: 0.85rem; color: #555; margin: 0.2rem 0;"><strong>GCash Number:</strong> 0917-123-4567</p>
                    <p style="font-size: 0.85rem; color: #555; margin: 0.2rem 0;"><strong>Account Name:</strong> Bez Tower and Residences</p>
                </div>
            </x-admin.card>
        </div>

        <!-- Save Button -->
        <div style="text-align: center; padding: 1.5rem; background: white; border: 2px solid var(--primary-gold); border-radius: 12px;">
            <button type="submit"
                style="padding: 1rem 3.5rem; background: linear-gradient(135deg, #4caf50, #3d9140); color: white; border: none; border-radius: 10px; font-size: 1.05rem; font-weight: 700; cursor: pointer;">
                <i class="fas fa-save"></i>&nbsp; Save Final Billing
            </button>
            <div style="margin-top: 0.75rem; font-size: 0.85rem; color: var(--text-muted);">All changes will be saved to the booking record</div>
        </div>

    </form>
</div>

<script>
const hourlyRate = {{ $hourlyRate }};
const roomTotal = {{ $booking->total_amount }};
const numberOfGuests = {{ $booking->number_of_guests }};

function incrementCounter(type) {
    const input = document.getElementById(type + 'Hours');
    const currentValue = parseInt(input.value);
    if (currentValue < 5) {
        input.value = currentValue + 1;
        calculateCharge(type);
    }
}

function decrementCounter(type) {
    const input = document.getElementById(type + 'Hours');
    const currentValue = parseInt(input.value);
    if (currentValue > 0) {
        input.value = currentValue - 1;
        calculateCharge(type);
    }
}

function calculateCharge(type) {
    const hours = parseInt(document.getElementById(type + 'Hours').value);
    const charge = hours * hourlyRate;

    document.getElementById(type + 'Charge').textContent = '₱' + charge.toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById(type + 'ChargeInput').value = charge;

    if (type === 'earlyCheckin') {
        document.getElementById('earlyCheckinDisplay').textContent = '₱' + charge.toLocaleString('en-PH', {minimumFractionDigits: 2});
    } else {
        document.getElementById('lateCheckoutDisplay').textContent = '₱' + charge.toLocaleString('en-PH', {minimumFractionDigits: 2});
    }

    calculateTotal();
}

function togglePwdSenior() {
    const checkbox = document.getElementById('hasPwdSenior');
    const section = document.getElementById('pwdSeniorSection');

    if (checkbox.checked) {
        section.style.display = 'block';
        calculatePwdDiscount();
    } else {
        section.style.display = 'none';
        document.getElementById('pwdSeniorDiscountInput').value = 0;
        document.getElementById('pwdDiscountAmount').textContent = '-₱0.00';
        document.getElementById('pwdDiscountDisplay').textContent = '-₱0.00';
        calculateTotal();
    }
}

function calculatePwdDiscount() {
    const checkbox = document.getElementById('hasPwdSenior');
    if (!checkbox.checked) return;

    const count = parseInt(document.getElementById('pwdSeniorCount').value);
    if (count === 0) {
        document.getElementById('pwdSeniorDiscountInput').value = 0;
        document.getElementById('pwdDiscountAmount').textContent = '-₱0.00';
        document.getElementById('pwdDiscountDisplay').textContent = '-₱0.00';
        calculateTotal();
        return;
    }

    const individualShare = roomTotal / numberOfGuests;
    const discount = (individualShare * 0.20) * count;

    document.getElementById('pwdSeniorDiscountInput').value = discount.toFixed(2);
    document.getElementById('pwdDiscountAmount').textContent = '-₱' + discount.toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('pwdDiscountDisplay').textContent = '-₱' + discount.toLocaleString('en-PH', {minimumFractionDigits: 2});

    calculateTotal();
}

function calculateTotal() {
    const earlyCheckin = parseFloat(document.getElementById('earlyCheckinChargeInput').value) || 0;
    const lateCheckout = parseFloat(document.getElementById('lateCheckoutChargeInput').value) || 0;
    const pwdDiscount  = parseFloat(document.getElementById('pwdSeniorDiscountInput').value) || 0;
    const manualAdjust = parseFloat(document.getElementById('manualAdjustment').value) || 0;

    const grandTotal = roomTotal + earlyCheckin + lateCheckout - pwdDiscount + manualAdjust;

    document.getElementById('grandTotal').textContent = '₱' + grandTotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('manualAdjustmentDisplay').textContent = '₱' + manualAdjust.toLocaleString('en-PH', {minimumFractionDigits: 2});
}

function toggleGcashQR() {
    const gcashRadio = document.getElementById('paymentGcash');
    document.getElementById('gcashQRSection').style.display = gcashRadio.checked ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    calculateTotal();
});
</script>

@endsection
