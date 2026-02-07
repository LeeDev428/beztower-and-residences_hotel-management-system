@extends('layouts.admin')

@section('title', 'Final Billing - Booking #' . $booking->booking_reference)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Final Billing</h1>
                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Booking
                </a>
            </div>
        </div>
    </div>

    <!-- Booking Details Card -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Booking Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th width="40%" class="text-muted">Reference</th>
                            <td class="fw-bold">{{ $booking->booking_reference }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Guest</th>
                            <td class="fw-bold">{{ $booking->guest->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Room</th>
                            <td class="fw-bold">{{ $booking->room->room_number }} - {{ $booking->roomType->name }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Check-in</th>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Check-out</th>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Nights</th>
                            <td class="fw-bold">{{ $booking->number_of_nights }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted">Guests</th>
                            <td class="fw-bold">{{ $booking->number_of_guests }}</td>
                        </tr>
                        <tr>
                            <th class="text-primary pt-3">Room Total</th>
                            <td class="text-primary fw-bold pt-3 fs-5">₱{{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-success h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Final Total</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mb-3">
                        <h4 class="mb-0">Grand Total:</h4>
                        <h3 class="mb-0 text-success fw-bold" id="grandTotal">₱{{ number_format($booking->final_total ?? $booking->total_amount, 2) }}</h3>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between text-muted mb-2">
                            <span>Room Charges</span>
                            <span class="fw-semibold">₱{{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mb-2">
                            <span>Early Check-in</span>
                            <span class="fw-semibold" id="earlyCheckinDisplay">₱{{ number_format($booking->early_checkin_charge ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mb-2">
                            <span>Late Checkout</span>
                            <span class="fw-semibold" id="lateCheckoutDisplay">₱{{ number_format($booking->late_checkout_charge ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-success mb-2">
                            <span class="fw-semibold">PWD/Senior Discount</span>
                            <span class="fw-bold" id="pwdDiscountDisplay">-₱{{ number_format($booking->pwd_senior_discount ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-muted mb-2">
                            <span>Manual Adjustment</span>
                            <span class="fw-semibold" id="manualAdjustmentDisplay">₱{{ number_format($booking->manual_adjustment ?? 0, 2) }}</span>
                        </div>
                        <hr class="my-3">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Form -->
    <form action="{{ route('admin.bookings.updateFinalBilling', $booking) }}" method="POST" id="billingForm">
        @csrf

        <div class="row mb-4">
            <!-- Early Check-in & Late Checkout -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Additional Time Charges</h5>
                    </div>
                    <div class="card-body">
                        <!-- Early Check-in -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Early Check-in (Max 5 hours)</label>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-danger" onclick="decrementCounter('earlyCheckin')">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="early_checkin_hours" id="earlyCheckinHours" 
                                    class="form-control text-center fw-bold" value="{{ $booking->early_checkin_hours ?? 0 }}" 
                                    min="0" max="5" readonly style="width: 80px;">
                                <button type="button" class="btn btn-outline-success" onclick="incrementCounter('earlyCheckin')">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <div class="ms-3 flex-grow-1">
                                    <div class="fw-bold text-success fs-5" id="earlyCheckinCharge">₱{{ number_format(($booking->early_checkin_charge ?? 0), 2) }}</div>
                                    <small class="text-muted">Rate: ₱{{ $hourlyRate }}/hour</small>
                                </div>
                            </div>
                            <input type="hidden" name="early_checkin_charge" id="earlyCheckinChargeInput" value="{{ $booking->early_checkin_charge ?? 0 }}">
                        </div>

                        <!-- Late Checkout -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Late Checkout (Max 5 hours)</label>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-danger" onclick="decrementCounter('lateCheckout')">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="late_checkout_hours" id="lateCheckoutHours" 
                                    class="form-control text-center fw-bold" value="{{ $booking->late_checkout_hours ?? 0 }}" 
                                    min="0" max="5" readonly style="width: 80px;">
                                <button type="button" class="btn btn-outline-success" onclick="incrementCounter('lateCheckout')">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <div class="ms-3 flex-grow-1">
                                    <div class="fw-bold text-success fs-5" id="lateCheckoutCharge">₱{{ number_format(($booking->late_checkout_charge ?? 0), 2) }}</div>
                                    <small class="text-muted">Rate: ₱{{ $hourlyRate }}/hour</small>
                                </div>
                            </div>
                            <input type="hidden" name="late_checkout_charge" id="lateCheckoutChargeInput" value="{{ $booking->late_checkout_charge ?? 0 }}">
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Maximum 5 hours allowed for each service</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PWD/Senior Discount -->
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-percentage me-2"></i>PWD / Senior Citizen Discount</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="hasPwdSenior" name="has_pwd_senior" 
                                    value="1" {{ $booking->has_pwd_senior ? 'checked' : '' }} onchange="togglePwdSenior()">
                                <label class="form-check-label fw-bold" for="hasPwdSenior">
                                    Apply PWD/Senior Discount (20%)
                                </label>
                            </div>
                        </div>

                        <div id="pwdSeniorSection" style="display: {{ $booking->has_pwd_senior ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Number of PWD/Senior Citizens</label>
                                <select class="form-select" name="pwd_senior_count" id="pwdSeniorCount" onchange="calculatePwdDiscount()">
                                    <option value="0">Select count</option>
                                    @for($i = 1; $i <= $booking->number_of_guests; $i++)
                                        <option value="{{ $i }}" {{ ($booking->pwd_senior_count ?? 0) == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ $i == 1 ? 'Person' : 'People' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <small>Discount applies to individual's share only (20% of total ÷ guests)</small>
                            </div>

                            <div class="p-3 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Discount Amount:</span>
                                <h4 class="text-success mb-0" id="pwdDiscountAmount">-₱{{ number_format($booking->pwd_senior_discount ?? 0, 2) }}</h4>
                            </div>
                            <input type="hidden" name="pwd_senior_discount" id="pwdSeniorDiscountInput" value="{{ $booking->pwd_senior_discount ?? 0 }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manual Adjustment -->
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Manual Adjustment</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Adjustment Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" name="manual_adjustment" id="manualAdjustment" 
                                    value="{{ $booking->manual_adjustment ?? 0 }}" step="0.01" onchange="calculateTotal()">
                            </div>
                            <small class="text-muted">Positive for additional charges, negative for discounts</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reason for Adjustment</label>
                            <textarea class="form-control" name="adjustment_reason" rows="3" 
                                placeholder="Explain the reason for manual adjustment...">{{ $booking->adjustment_reason }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Payment Method for Additional Charges</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Payment Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paymentCash" 
                                    value="cash" checked onchange="toggleGcashQR()">
                                <label class="form-check-label" for="paymentCash">
                                    <i class="fas fa-money-bill-wave me-2"></i>Cash
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paymentGcash" 
                                    value="gcash" onchange="toggleGcashQR()">
                                <label class="form-check-label" for="paymentGcash">
                                    <i class="fas fa-mobile-alt me-2"></i>GCash
                                </label>
                            </div>
                        </div>

                        <!-- GCash QR Code -->
                        <div id="gcashQRSection" style="display: none;">
                            <div class="text-center p-3 border rounded bg-light">
                                <p class="mb-2"><strong>Scan to Pay via GCash</strong></p>
                                <img src="{{ asset('images/gcash-qr.png') }}" alt="GCash QR Code" class="img-fluid" style="max-width: 200px;">
                                <p class="small text-muted mt-2 mb-0">
                                    GCash Number: 0917-123-4567<br>
                                    Account Name: Bez Tower and Residences
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-save me-2"></i>Save Final Billing
                        </button>
                    </div>
                </div>
            </div>
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
    
    // Calculate individual share and apply 20% discount
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
    const pwdDiscount = parseFloat(document.getElementById('pwdSeniorDiscountInput').value) || 0;
    const manualAdjustment = parseFloat(document.getElementById('manualAdjustment').value) || 0;
    
    const grandTotal = roomTotal + earlyCheckin + lateCheckout - pwdDiscount + manualAdjustment;
    
    document.getElementById('grandTotal').textContent = '₱' + grandTotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
    document.getElementById('manualAdjustmentDisplay').textContent = '₱' + manualAdjustment.toLocaleString('en-PH', {minimumFractionDigits: 2});
}

function toggleGcashQR() {
    const gcashRadio = document.getElementById('paymentGcash');
    const gcashSection = document.getElementById('gcashQRSection');
    
    gcashSection.style.display = gcashRadio.checked ? 'block' : 'none';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
</script>

@endsection
