@extends('layouts.admin')

@section('title', 'Walk-In Booking')
@section('page-title', 'Walk-In Booking')

@section('content')
<div style="max-width: 900px; margin: 0 auto;">

    <!-- Page Header -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-gold); margin: 0;">New Walk-In Booking</h2>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0;">Create a booking on behalf of a walk-in guest</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; background: var(--light-gray); color: var(--text-muted); border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
    </div>

    <form method="POST" action="{{ route('admin.bookings.walkIn.store') }}" id="walkInForm">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

            <!-- Guest Information -->
            <x-admin.card title="Guest Information">
                @if($errors->any())
                <div style="background: #fde8e8; border-left: 4px solid var(--danger); padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1.25rem;">
                    @foreach($errors->all() as $error)
                    <div style="font-size: 0.85rem; color: #7f1d1d;"><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                    @endforeach
                </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">First Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box;"
                            placeholder="Juan">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Last Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box;"
                            placeholder="Dela Cruz">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Phone Number <span style="color: var(--danger);">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required maxlength="11"
                        style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box;"
                        placeholder="09XXXXXXXXX">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box;"
                        placeholder="guest@email.com (optional)">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box;"
                        placeholder="Street, City, Province (optional)">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Valid ID Type</label>
                    <select name="id_type"
                        style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; background: white; box-sizing: border-box;">
                        <option value="">— Select ID Type —</option>
                        <option value="Driver's License" {{ old('id_type') === "Driver's License" ? 'selected' : '' }}>Driver's License</option>
                        <option value="Passport" {{ old('id_type') === 'Passport' ? 'selected' : '' }}>Passport</option>
                        <option value="SSS ID" {{ old('id_type') === 'SSS ID' ? 'selected' : '' }}>SSS ID</option>
                        <option value="PhilHealth ID" {{ old('id_type') === 'PhilHealth ID' ? 'selected' : '' }}>PhilHealth ID</option>
                        <option value="UMID" {{ old('id_type') === 'UMID' ? 'selected' : '' }}>UMID</option>
                        <option value="Voter's ID" {{ old('id_type') === "Voter's ID" ? 'selected' : '' }}>Voter's ID</option>
                        <option value="National ID" {{ old('id_type') === 'National ID' ? 'selected' : '' }}>National ID</option>
                        <option value="Postal ID" {{ old('id_type') === 'Postal ID' ? 'selected' : '' }}>Postal ID</option>
                        <option value="Senior Citizen ID" {{ old('id_type') === 'Senior Citizen ID' ? 'selected' : '' }}>Senior Citizen ID</option>
                        <option value="PWD ID" {{ old('id_type') === 'PWD ID' ? 'selected' : '' }}>PWD ID</option>
                        <option value="Other" {{ old('id_type') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </x-admin.card>

            <!-- Booking Details -->
            <x-admin.card title="Booking Details">

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Room <span style="color: var(--danger);">*</span></label>
                    <select name="room_id" id="roomSelect" required onchange="updateRoomPrice()"
                        style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; background: white; box-sizing: border-box;">
                        <option value="">— Select Available Room —</option>
                        @foreach($availableRooms as $room)
                        <option value="{{ $room->id }}"
                            data-price="{{ $room->roomType->base_price }}"
                            data-type="{{ $room->roomType->name }}"
                            {{ old('room_id') == $room->id ? 'selected' : '' }}>
                            Room {{ $room->room_number }} — {{ $room->roomType->name }} (₱{{ number_format($room->roomType->base_price, 0) }}/night)
                        </option>
                        @endforeach
                    </select>
                    @if($availableRooms->isEmpty())
                    <div style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--danger);">
                        <i class="fas fa-exclamation-triangle"></i> No available rooms at the moment.
                    </div>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Check-In Date <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="check_in_date" id="checkInDate" value="{{ old('check_in_date', now()->format('Y-m-d')) }}" required
                            min="{{ now()->format('Y-m-d') }}"
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box;"
                            onchange="calculateTotal()">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Check-Out Date <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="check_out_date" id="checkOutDate" value="{{ old('check_out_date', now()->addDay()->format('Y-m-d')) }}" required
                            min="{{ now()->addDay()->format('Y-m-d') }}"
                            style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box;"
                            onchange="calculateTotal()">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Number of Guests <span style="color: var(--danger);">*</span></label>
                    <input type="number" name="number_of_guests" value="{{ old('number_of_guests', 1) }}" required min="1" max="10"
                        style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Special Requests</label>
                    <textarea name="special_requests" rows="2"
                        style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; outline: none; resize: vertical; box-sizing: border-box;"
                        placeholder="Any special requests or notes...">{{ old('special_requests') }}</textarea>
                </div>

                <!-- Nights + Amount Summary -->
                <div style="background: var(--light-gray); border-radius: 10px; padding: 1rem; font-size: 0.9rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                        <span style="color: var(--text-muted);">Nights</span>
                        <span style="font-weight: 600;" id="nightsDisplay">1</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                        <span style="color: var(--text-muted);">Rate / Night</span>
                        <span style="font-weight: 600;" id="rateDisplay">—</span>
                    </div>
                    <div style="border-top: 2px solid var(--border-gray); padding-top: 0.6rem; margin-top: 0.4rem; display: flex; justify-content: space-between;">
                        <span style="font-weight: 700;">Total Amount</span>
                        <span style="font-weight: 700; color: var(--primary-gold); font-size: 1.1rem;" id="totalDisplay">₱0.00</span>
                    </div>
                </div>

            </x-admin.card>
        </div>

        <!-- Payment Method -->
        <div style="margin-top: 1.5rem;">
            <x-admin.card title="Payment">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.75rem; color: #444;">Payment Type</label>
                        <label style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.75rem; cursor: pointer; padding: 0.75rem 1rem; border: 2px solid var(--border-gray); border-radius: 8px; transition: border-color 0.2s;" id="fullPaymentLabel">
                            <input type="radio" name="payment_type" value="full_payment" {{ old('payment_type', 'full_payment') === 'full_payment' ? 'checked' : '' }} onchange="updatePaymentDisplay()" style="accent-color: var(--primary-gold);">
                            <div>
                                <div style="font-weight: 600;">Full Payment</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Pay the complete amount now</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; padding: 0.75rem 1rem; border: 2px solid var(--border-gray); border-radius: 8px; transition: border-color 0.2s;" id="downPaymentLabel">
                            <input type="radio" name="payment_type" value="down_payment" {{ old('payment_type') === 'down_payment' ? 'checked' : '' }} onchange="updatePaymentDisplay()" style="accent-color: var(--primary-gold);">
                            <div>
                                <div style="font-weight: 600;">30% Down Payment</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Pay 30% now, remainder on checkout</div>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.75rem; color: #444;">Payment Method</label>
                        <label style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.75rem; cursor: pointer; padding: 0.75rem 1rem; border: 2px solid var(--border-gray); border-radius: 8px;" id="cashLabel">
                            <input type="radio" name="payment_method" value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'checked' : '' }} onchange="toggleGcash()" style="accent-color: var(--primary-gold);">
                            <div>
                                <div style="font-weight: 600;"><i class="fas fa-money-bill-wave" style="color: var(--success);"></i> Cash</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Physical cash payment</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; padding: 0.75rem 1rem; border: 2px solid var(--border-gray); border-radius: 8px;" id="gcashLabel">
                            <input type="radio" name="payment_method" value="gcash" {{ old('payment_method') === 'gcash' ? 'checked' : '' }} onchange="toggleGcash()" style="accent-color: var(--primary-gold);">
                            <div>
                                <div style="font-weight: 600;"><i class="fas fa-mobile-alt" style="color: #0070E0;"></i> GCash</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">GCash digital payment</div>
                            </div>
                        </label>

                        <!-- GCash QR Section -->
                        <div id="gcashInfo" style="display: none; margin-top: 1rem; background: #e8f4ff; border: 1px solid #bee0ff; border-radius: 8px; padding: 1rem; text-align: center;">
                            <p style="font-weight: 600; margin-bottom: 0.5rem;"><i class="fas fa-qrcode"></i> GCash QR Code</p>
                            {{-- <img src="{{ asset('images/gcash-qr.png') }}" alt="GCash QR" style="max-width: 150px; border: 2px solid #0070E0; border-radius: 8px; margin-bottom: 0.5rem;"> --}}
                            <p style="font-size: 0.8rem; color: #555;">Number: <strong>+63 912 345 6789</strong></p>
                            <p style="font-size: 0.8rem; color: #555;">Name: <strong>Bez Tower and Residences</strong></p>
                        </div>
                    </div>

                </div>

                <!-- Amount Due Summary -->
                <div style="margin-top: 1.5rem; background: linear-gradient(135deg, #2c2c2c, #3a3a3a); color: white; border-radius: 10px; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-size: 0.85rem; color: #aaa;">Amount Due Now</div>
                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--primary-gold);" id="amountDue">₱0.00</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.85rem; color: #aaa;">Walk-in status</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: #4caf50;"><i class="fas fa-walking"></i> Checked In Immediately</div>
                    </div>
                </div>
            </x-admin.card>
        </div>

        <!-- Submit -->
        <div style="margin-top: 1.5rem; text-align: center;">
            <button type="submit" style="padding: 0.9rem 3rem; background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold)); color: white; border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; cursor: pointer; letter-spacing: 0.5px;">
                <i class="fas fa-check-circle"></i>&nbsp; Confirm Walk-In Booking
            </button>
            <div style="margin-top: 0.75rem; font-size: 0.85rem; color: var(--text-muted);">
                Booking will be created and guest marked as <strong>Checked In</strong> immediately.
            </div>
        </div>
    </form>
</div>

<script>
const roomPrices = {};
document.querySelectorAll('#roomSelect option[data-price]').forEach(opt => {
    roomPrices[opt.value] = parseFloat(opt.getAttribute('data-price')) || 0;
});

function getNights() {
    const ci = document.getElementById('checkInDate').value;
    const co = document.getElementById('checkOutDate').value;
    if (!ci || !co) return 0;
    const diff = (new Date(co) - new Date(ci)) / (1000 * 60 * 60 * 24);
    return Math.max(0, diff);
}

function updateRoomPrice() {
    calculateTotal();
}

function calculateTotal() {
    const roomId = document.getElementById('roomSelect').value;
    const pricePerNight = roomPrices[roomId] || 0;
    const nights = getNights();
    const total = pricePerNight * nights;

    document.getElementById('nightsDisplay').textContent = nights;
    document.getElementById('rateDisplay').textContent = pricePerNight > 0 ? '₱' + pricePerNight.toLocaleString('en-PH', {minimumFractionDigits: 2}) : '—';
    document.getElementById('totalDisplay').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2});

    updatePaymentDisplay();
}

function updatePaymentDisplay() {
    const roomId = document.getElementById('roomSelect').value;
    const pricePerNight = roomPrices[roomId] || 0;
    const nights = getNights();
    const total = pricePerNight * nights;

    const isDown = document.querySelector('input[name="payment_type"]:checked')?.value === 'down_payment';
    const due = isDown ? total * 0.30 : total;

    document.getElementById('amountDue').textContent = '₱' + due.toLocaleString('en-PH', {minimumFractionDigits: 2});
}

function toggleGcash() {
    const isGcash = document.querySelector('input[name="payment_method"]:checked')?.value === 'gcash';
    document.getElementById('gcashInfo').style.display = isGcash ? 'block' : 'none';
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function () {
    calculateTotal();
    toggleGcash();

    // Ensure check-out is always after check-in
    document.getElementById('checkInDate').addEventListener('change', function() {
        const ci = new Date(this.value);
        const co = document.getElementById('checkOutDate');
        if (co.value && new Date(co.value) <= ci) {
            const nextDay = new Date(ci);
            nextDay.setDate(nextDay.getDate() + 1);
            co.value = nextDay.toISOString().split('T')[0];
        }
        co.min = this.value;
        calculateTotal();
    });
});
</script>
@endsection
