@extends('layouts.admin')

@section('title', 'Walk-In Booking')
@section('page-title', 'Walk-In Booking')

@section('content')
<div style="max-width: 980px; margin: 0 auto;">

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-gold); margin: 0;">New Walk-In Booking</h2>
            <p style="color: var(--text-muted); margin: 0.25rem 0 0;">Create booking(s) for walk-in guest based on selected stay dates</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; background: var(--light-gray); color: var(--text-muted); border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.9rem;">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
    </div>

    <form method="POST" action="{{ route('admin.bookings.walkIn.store') }}" id="walkInForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="payment_type" value="full_payment">

        @if($errors->any())
        <div style="background: #fde8e8; border-left: 4px solid var(--danger); padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1.25rem;">
            @foreach($errors->all() as $error)
            <div style="font-size: 0.85rem; color: #7f1d1d;"><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
            @endforeach
        </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <x-admin.card title="Guest Information">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">First Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name') }}" required style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;" placeholder="Juan">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Last Name <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name') }}" required style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;" placeholder="Dela Cruz">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Phone Number <span style="color: var(--danger);">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required maxlength="11" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;" placeholder="09XXXXXXXXX">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;" placeholder="guest@email.com (optional)">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Address</label>
                    <input type="text" name="address" value="{{ old('address') }}" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;" placeholder="Street, City, Province (optional)">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Valid ID Photo <span style="color: var(--text-muted); font-weight: 400;">(Optional, JPG, PNG, PDF — max 5MB)</span></label>
                    <input type="file" name="id_photo" accept="image/jpeg,image/png,image/jpg,application/pdf" style="width: 100%; padding: 0.55rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; background: white; box-sizing: border-box;">
                </div>
            </x-admin.card>

            <x-admin.card title="Booking Details">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Check-In Date <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="check_in_date" id="checkInDate" value="{{ old('check_in_date', $checkIn) }}" required min="{{ now()->format('Y-m-d') }}" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Check-Out Date <span style="color: var(--danger);">*</span></label>
                        <input type="date" name="check_out_date" id="checkOutDate" value="{{ old('check_out_date', $checkOut) }}" required min="{{ now()->addDay()->format('Y-m-d') }}" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">How Many Rooms <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="number_of_rooms" id="numberOfRooms" value="{{ old('number_of_rooms', 1) }}" min="1" max="5" required style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Number of Guests <span style="color: var(--danger);">*</span></label>
                        <input type="number" name="number_of_guests" value="{{ old('number_of_guests', 1) }}" required min="1" max="10" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;">
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem; color: #444;">Room Selection <span style="color: var(--danger);">*</span></label>
                    <div id="availabilityInfo" style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.6rem;">Checking available rooms...</div>
                    <div id="roomOptions" style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.6rem;"></div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">Special Requests</label>
                    <textarea name="special_requests" rows="2" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem; resize: vertical; box-sizing: border-box;" placeholder="Any special requests or notes...">{{ old('special_requests') }}</textarea>
                </div>

                <div style="background: var(--light-gray); border-radius: 10px; padding: 1rem; font-size: 0.9rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                        <span style="color: var(--text-muted);">Nights</span>
                        <span style="font-weight: 600;" id="nightsDisplay">1</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                        <span style="color: var(--text-muted);">Room Charges</span>
                        <span style="font-weight: 600;" id="roomChargeDisplay">PHP 0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                        <span style="color: var(--text-muted);">Amenities & Services</span>
                        <span style="font-weight: 600;" id="extrasDisplay">PHP 0.00</span>
                    </div>
                    <div style="border-top: 2px solid var(--border-gray); padding-top: 0.6rem; margin-top: 0.4rem; display: flex; justify-content: space-between;">
                        <span style="font-weight: 700;">Total Amount</span>
                        <span style="font-weight: 700; color: var(--primary-gold); font-size: 1.1rem;" id="totalDisplay">PHP 0.00</span>
                    </div>
                </div>
            </x-admin.card>
        </div>

        <div style="margin-top: 1.5rem;">
            <x-admin.card title="Payment">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.75rem; color: #444;">Payment Type</label>
                        <div style="display: flex; align-items: center; gap: 0.6rem; padding: 0.75rem 1rem; border: 2px solid var(--border-gray); border-radius: 8px;">
                            <input type="radio" checked disabled style="accent-color: var(--primary-gold);">
                            <div>
                                <div style="font-weight: 600;">Full Payment Only</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">Walk-in requires complete payment</div>
                            </div>
                        </div>
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

                        <div id="gcashInfo" style="display: none; margin-top: 1rem; background: #e8f4ff; border: 1px solid #bee0ff; border-radius: 8px; padding: 1rem; text-align: center;">
                            <p style="font-weight: 600; margin-bottom: 0.5rem;"><i class="fas fa-qrcode"></i> GCash QR Code</p>
                            <img src="{{ asset('images/gcash/gcash_v2.jpg') }}" alt="GCash QR" style="max-width: 150px; border: 2px solid #0070E0; border-radius: 8px; margin-bottom: 0.5rem;">
                            <p style="font-size: 0.8rem; color: #555;">Number: <strong>+63 912 345 6789</strong></p>
                            <p style="font-size: 0.8rem; color: #555;">Name: <strong>Bez Tower and Residences</strong></p>
                        </div>
                    </div>
                </div>

                @if($extras->count() > 0)
                <div style="margin-top: 1.5rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.95rem; margin-bottom: 0.75rem; color: #444;">Additional Amenities & Services</label>
                    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.7rem;">
                        @foreach($extras as $extra)
                        <div style="padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px; background: #fff;">
                            <label style="display: flex; align-items: flex-start; gap: 0.6rem; cursor: pointer;">
                                <input type="checkbox" class="extra-checkbox" name="extras[]" value="{{ $extra->id }}" data-price="{{ $extra->price }}" onchange="calculateTotal()" style="margin-top: 0.15rem; accent-color: var(--primary-gold);">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600;">{{ $extra->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">PHP {{ number_format($extra->price, 2) }}</div>
                                </div>
                                <input type="number" name="extra_quantities[{{ $extra->id }}]" value="1" min="1" max="20" style="width: 68px; padding: 0.35rem 0.4rem; border: 1px solid var(--border-gray); border-radius: 6px; font-size: 0.8rem;" onchange="calculateTotal()" oninput="calculateTotal()">
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div style="margin-top: 1.5rem; background: linear-gradient(135deg, #2c2c2c, #3a3a3a); color: white; border-radius: 10px; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-size: 0.85rem; color: #aaa;">Amount Due Now</div>
                        <div style="font-size: 1.75rem; font-weight: 700; color: var(--primary-gold);" id="amountDue">PHP 0.00</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.85rem; color: #aaa;">Walk-in status</div>
                        <div style="font-size: 0.95rem; font-weight: 700; color: #4caf50;"><i class="fas fa-walking"></i> Checked In Immediately</div>
                    </div>
                </div>
            </x-admin.card>
        </div>

        <div style="margin-top: 1.5rem; text-align: center;">
            <button type="submit" style="padding: 0.9rem 3rem; background: linear-gradient(135deg, var(--primary-gold), var(--dark-gold)); color: white; border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; cursor: pointer; letter-spacing: 0.5px;">
                <i class="fas fa-check-circle"></i>&nbsp; Confirm Walk-In Booking
            </button>
            <div style="margin-top: 0.75rem; font-size: 0.85rem; color: var(--text-muted);">
                Booking(s) will be created and guest will be marked as <strong>Checked In</strong> immediately.
            </div>
        </div>
    </form>
</div>

@php
    $availableRoomsForJs = $availableRooms->map(function ($room) {
        $roomType = $room->roomType;
        $roomTypeName = $roomType ? $roomType->name : 'Room';
        $basePrice = (float) ($roomType ? $roomType->base_price : 0);

        return [
            'id' => $room->id,
            'room_number' => $room->room_number,
            'room_type' => $roomTypeName,
            'price' => $basePrice,
            'label' => $roomTypeName . ' - Room ' . $room->room_number . ' (PHP ' . number_format($basePrice, 2) . '/night)',
        ];
    })->values();
@endphp

<script>
let availableRooms = @json($availableRoomsForJs);

function formatPeso(value) {
    return 'PHP ' + Number(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function getNights() {
    const ci = document.getElementById('checkInDate').value;
    const co = document.getElementById('checkOutDate').value;
    if (!ci || !co) return 0;
    const diff = (new Date(co) - new Date(ci)) / (1000 * 60 * 60 * 24);
    return Math.max(0, diff);
}

function getSelectedRoomIds() {
    return Array.from(document.querySelectorAll('.walkin-room-checkbox:checked')).map((el) => el.value);
}

function renderRoomSelectors() {
    const container = document.getElementById('roomOptions');
    const availabilityInfo = document.getElementById('availabilityInfo');
    const requestedRooms = Math.max(1, parseInt(document.getElementById('numberOfRooms').value || '1', 10));
    const selectedIds = new Set(getSelectedRoomIds());

    container.innerHTML = '';

    if (!availableRooms.length) {
        availabilityInfo.innerHTML = '<span style="color: var(--danger);"><i class="fas fa-exclamation-triangle"></i> No available rooms for selected dates.</span>';
        calculateTotal();
        return;
    }

    availabilityInfo.innerHTML = 'Available rooms for selected dates: <strong>' + availableRooms.length + '</strong>';

    if (requestedRooms > availableRooms.length) {
        availabilityInfo.innerHTML += ' <span style="color: var(--danger);">(Requested ' + requestedRooms + ', only ' + availableRooms.length + ' available)</span>';
    }

    availableRooms.forEach((room) => {
        const isChecked = selectedIds.has(String(room.id));

        const label = document.createElement('label');
        label.style.display = 'flex';
        label.style.alignItems = 'flex-start';
        label.style.gap = '0.55rem';
        label.style.cursor = 'pointer';
        label.style.border = '1px solid var(--border-gray)';
        label.style.borderRadius = '8px';
        label.style.padding = '0.7rem';
        label.style.background = '#fff';

        const input = document.createElement('input');
        input.type = 'checkbox';
        input.className = 'walkin-room-checkbox';
        input.name = 'room_ids[]';
        input.value = String(room.id);
        input.checked = isChecked;
        input.style.marginTop = '0.12rem';
        input.style.accentColor = 'var(--primary-gold)';

        input.addEventListener('change', () => {
            const currentlySelected = getSelectedRoomIds();
            if (currentlySelected.length > requestedRooms) {
                input.checked = false;
                alert('You can only select up to ' + requestedRooms + ' room(s).');
                return;
            }
            calculateTotal();
        });

        const details = document.createElement('div');
        details.style.flex = '1';

        const title = document.createElement('div');
        title.style.fontWeight = '600';
        title.textContent = room.room_type + ' - Room ' + room.room_number;

        const price = document.createElement('div');
        price.style.fontSize = '0.8rem';
        price.style.color = 'var(--text-muted)';
        price.textContent = formatPeso(room.price) + '/night';

        details.appendChild(title);
        details.appendChild(price);
        label.appendChild(input);
        label.appendChild(details);

        container.appendChild(label);
    });

    calculateTotal();
}

function calculateTotal() {
    const nights = getNights();
    const selectedIds = getSelectedRoomIds();

    let roomCharge = 0;
    selectedIds.forEach((id) => {
        const room = availableRooms.find((r) => String(r.id) === String(id));
        if (room) {
            roomCharge += Number(room.price) * nights;
        }
    });

    let extrasTotal = 0;
    document.querySelectorAll('.extra-checkbox:checked').forEach((checkbox) => {
        const price = Number(checkbox.dataset.price || 0);
        const quantityInput = checkbox.closest('label').querySelector('input[type="number"]');
        const quantity = Math.max(1, Number(quantityInput?.value || 1));
        extrasTotal += price * quantity;
    });

    const grandTotal = roomCharge + extrasTotal;

    document.getElementById('nightsDisplay').textContent = nights;
    document.getElementById('roomChargeDisplay').textContent = formatPeso(roomCharge);
    document.getElementById('extrasDisplay').textContent = formatPeso(extrasTotal);
    document.getElementById('totalDisplay').textContent = formatPeso(grandTotal);
    document.getElementById('amountDue').textContent = formatPeso(grandTotal);
}

function toggleGcash() {
    const isGcash = document.querySelector('input[name="payment_method"]:checked')?.value === 'gcash';
    document.getElementById('gcashInfo').style.display = isGcash ? 'block' : 'none';
}

async function refreshAvailableRooms() {
    const checkIn = document.getElementById('checkInDate').value;
    const checkOut = document.getElementById('checkOutDate').value;

    if (!checkIn || !checkOut || new Date(checkOut) <= new Date(checkIn)) {
        availableRooms = [];
        renderRoomSelectors();
        return;
    }

    const url = `{{ route('admin.bookings.walkIn.availableRooms') }}?check_in_date=${encodeURIComponent(checkIn)}&check_out_date=${encodeURIComponent(checkOut)}`;

    try {
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const data = await response.json();
        availableRooms = Array.isArray(data.rooms) ? data.rooms : [];
    } catch (error) {
        availableRooms = [];
    }

    renderRoomSelectors();
}

document.addEventListener('DOMContentLoaded', function () {
    const checkInEl = document.getElementById('checkInDate');
    const checkOutEl = document.getElementById('checkOutDate');
    const roomsCountEl = document.getElementById('numberOfRooms');
    const formEl = document.getElementById('walkInForm');

    checkInEl.addEventListener('change', function() {
        const ci = new Date(this.value + 'T00:00:00');
        if (checkOutEl.value && new Date(checkOutEl.value + 'T00:00:00') <= ci) {
            const nextDay = new Date(ci);
            nextDay.setDate(nextDay.getDate() + 1);
            const yyyy = nextDay.getFullYear();
            const mm = String(nextDay.getMonth() + 1).padStart(2, '0');
            const dd = String(nextDay.getDate()).padStart(2, '0');
            checkOutEl.value = `${yyyy}-${mm}-${dd}`;
        }
        checkOutEl.min = this.value;
        refreshAvailableRooms();
    });

    checkOutEl.addEventListener('change', refreshAvailableRooms);
    roomsCountEl.addEventListener('input', renderRoomSelectors);

    document.querySelectorAll('input[name^="extra_quantities"], .extra-checkbox').forEach((input) => {
        input.addEventListener('input', calculateTotal);
        input.addEventListener('change', calculateTotal);
    });

    formEl.addEventListener('submit', function (event) {
        const requestedRooms = Math.max(1, parseInt(roomsCountEl.value || '1', 10));
        const selectedRooms = getSelectedRoomIds().length;
        if (selectedRooms !== requestedRooms) {
            event.preventDefault();
            alert('Please select exactly ' + requestedRooms + ' room(s).');
        }
    });

    toggleGcash();
    renderRoomSelectors();
    calculateTotal();
    refreshAvailableRooms();
});
</script>
@endsection
