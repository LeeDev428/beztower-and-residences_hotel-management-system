<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $room->roomType->name }} - Bez Tower & Residences</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            color: #2c2c2c;
            padding-top: 80px;
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .checkout-layout {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 2rem;
            align-items: start;
        }

        .checkout-form-section {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .section-header {
            margin-bottom: 2rem;
        }

        .section-header h2 {
            font-size: 1.8rem;
            color: #2c2c2c;
            margin-bottom: 0.5rem;
        }

        .section-header p {
            color: #666;
            font-size: 0.95rem;
        }

        .section-title-modal {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c2c2c;
            margin: 2rem 0 1.5rem 0;
            padding-bottom: 0.8rem;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-title-modal i {
            color: #d4af37;
        }

        .booking-form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .form-group-full {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2c2c2c;
            font-size: 0.95rem;
        }

        .required {
            color: #dc3545;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s;
            font-family: inherit;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #d4af37;
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Price Details Sidebar */
        .price-details-sidebar {
            position: sticky;
            top: 100px;
        }

        .room-summary-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }

        .room-summary-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .room-summary-content {
            padding: 1.5rem;
        }

        .room-type-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c2c2c;
            margin-bottom: 0.5rem;
        }

        .room-price {
            font-size: 1.1rem;
            color: #d4af37;
            font-weight: 600;
        }

        .room-price span {
            font-size: 0.85rem;
            color: #666;
            font-weight: normal;
        }

        .price-breakdown-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .price-breakdown-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #2c2c2c;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .price-breakdown-title i {
            color: #d4af37;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .price-row:last-child {
            border-bottom: none;
            padding-top: 1.5rem;
            margin-top: 1rem;
            border-top: 2px solid #d4af37;
        }

        .price-label {
            color: #666;
            font-size: 0.95rem;
        }

        .price-value {
            font-weight: 600;
            color: #2c2c2c;
        }

        .price-row:last-child .price-label {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c2c2c;
        }

        .price-row:last-child .price-value {
            font-size: 1.5rem;
            color: #d4af37;
        }

        /* Extras */
        .extras-grid {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }

        .extra-item {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 1rem;
            align-items: center;
            padding: 1rem;
            background: #f8f8f8;
            border-radius: 8px;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .extra-item:has(input:checked) {
            background: #fff9e6;
            border-color: #d4af37;
        }

        .extra-info {
            display: flex;
            flex-direction: column;
        }

        .extra-name {
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 0.2rem;
        }

        .extra-desc {
            font-size: 0.85rem;
            color: #666;
        }

        .extra-price {
            font-weight: 700;
            color: #d4af37;
            font-size: 1.1rem;
        }

        .extra-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #d4af37;
        }

        .qty-controls {
            display: none;
            align-items: center;
            gap: 0.5rem;
        }

        .qty-controls.active {
            display: flex;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border: 2px solid #d4af37;
            background: white;
            color: #d4af37;
            border-radius: 50%;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .qty-btn:hover {
            background: #d4af37;
            color: white;
        }

        .qty-input {
            width: 50px;
            text-align: center;
            border: 2px solid #e5e5e5;
            border-radius: 6px;
            padding: 0.3rem;
            font-weight: 600;
        }

        /* Payment Options */
        .payment-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 1rem;
        }

        .payment-option-card {
            cursor: pointer;
            border: 2px solid #e5e5e5;
            border-radius: 12px;
            padding: 1.5rem;
            background: #f8f8f8;
            transition: all 0.3s;
            position: relative;
        }

        .payment-option-card:hover {
            background: #fff;
            border-color: #d4af37;
        }

        .payment-option-card input[type="radio"] {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #d4af37;
        }

        .payment-option-card:has(input[type="radio"]:checked) {
            border-color: #d4af37;
            background: linear-gradient(135deg, #fff9e6, #fff);
        }

        .payment-card-header {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 0.8rem;
            font-size: 1rem;
            font-weight: 600;
            color: #2c2c2c;
        }

        .payment-card-header i {
            color: #d4af37;
        }

        .payment-card-desc {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.5;
        }

        .payment-amount {
            font-size: 1.3rem;
            font-weight: 700;
            color: #d4af37;
            text-align: right;
            margin-top: 1rem;
        }

        /* Submit Button */
        .submit-booking-btn {
            width: 100%;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            padding: 1.3rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .submit-booking-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }

        .submit-booking-btn i {
            margin-right: 0.5rem;
        }

        /* Guest Recommendation */
        .guest-recommendation {
            margin-top: 0.8rem;
            padding: 1rem;
            background: #fff3cd;
            border-left: 4px solid #d4af37;
            border-radius: 5px;
            display: none;
        }

        .guest-recommendation.show {
            display: block;
        }

        .guest-recommendation i {
            color: #d4af37;
        }

        .recommendation-text {
            margin: 0.3rem 0 0 0;
            color: #856404;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .checkout-layout {
                grid-template-columns: 1fr;
            }

            .price-details-sidebar {
                position: static;
                order: -1;
            }

            .booking-form-grid {
                grid-template-columns: 1fr;
            }

            .payment-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    @include('components.navbar')

    <div class="container">
        <x-back-button url="{{ route('rooms.show', $room->id) }}" text="Back to Room Details" />

        <div class="checkout-layout">
            <!-- Left: Checkout Form -->
            <div class="checkout-form-section">
                <div class="section-header">
                    <h2><i class="fas fa-file-invoice"></i> Checkout</h2>
                    <p>Please fill in your details to complete the booking</p>
                </div>

                @if ($errors->has('error'))
                    <div style="background:#fee2e2;border:1px solid #f87171;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:0.95rem;">
                        <strong>Booking Limit Reached:</strong> {{ $errors->first('error') }}
                    </div>
                @endif

                <form action="{{ route('booking.create') }}" method="POST" id="bookingForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    
                    <!-- Guest Information -->
                    <div class="section-title-modal">
                        <i class="fas fa-user"></i> Guest Information
                    </div>
                    
                    <div class="booking-form-grid">
                        <div>
                            <label class="form-label">First Name <span class="required">*</span></label>
                            <input type="text" name="first_name" class="form-input" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Last Name <span class="required">*</span></label>
                            <input type="text" name="last_name" class="form-input" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" name="email" class="form-input" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Phone <span class="required">*</span></label>
                            <input type="tel" name="phone" class="form-input" required maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" placeholder="09XXXXXXXXX">
                        </div>
                        
                        <div>
                            <label class="form-label">Country</label>
                            <select name="country" class="form-select">
                                <option value="">Select Country</option>
                                <option value="Philippines" selected>Philippines</option>
                                <option value="United States">United States</option>
                                <option value="Canada">Canada</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Australia">Australia</option>
                                <option value="Japan">Japan</option>
                                <option value="South Korea">South Korea</option>
                                <option value="China">China</option>
                                <option value="Singapore">Singapore</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Thailand">Thailand</option>
                                <option value="Indonesia">Indonesia</option>
                                <option value="Vietnam">Vietnam</option>
                                <option value="India">India</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="form-label">Upload ID Photo <span class="required">*</span></label>
                            <input type="file" name="id_photo" id="idPhotoInput" class="form-input" 
                                   accept="image/jpeg,image/png,image/jpg,application/pdf" required>
                            <small style="color: #666; font-size: 0.85rem; margin-top: 0.3rem; display: block;">
                                Accepted: JPG, PNG, PDF (Max: 5MB)
                            </small>
                        </div>
                        
                        <div class="form-group-full">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-input" placeholder="Street Address, City, State/Province">
                        </div>
                    </div>
                    
                    <!-- Booking Details -->
                    <div class="section-title-modal">
                        <i class="fas fa-calendar-alt"></i> Booking Details
                    </div>
                    
                    <div class="booking-form-grid">
                        <div>
                            <label class="form-label">Check-In Date <span class="required">*</span></label>
                            <input type="date" name="check_in_date" class="form-input" id="checkInDate" 
                                   min="{{ date('Y-m-d') }}" value="{{ request('check_in') }}" required onchange="calculateCheckout()">
                        </div>

                        <div>
                            <label class="form-label">Check-Out Date <span class="required">*</span></label>
                            <input type="date" name="check_out_date" class="form-input" id="checkOutDate" 
                                   required onchange="calculateCheckout()">
                        </div>
                        
                        <div>
                            <label class="form-label">Number of Nights (Auto-calculated)</label>
                            <input type="number" name="total_nights" class="form-input" id="totalNights" 
                                   readonly style="background: #f0f0f0; cursor: not-allowed;" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Number of Guests <span class="required">*</span></label>
                            <select name="number_of_guests" class="form-select" id="guestCountSelect" required onchange="showGuestRecommendation()">
                                @for($i = 1; $i <= $room->roomType->max_guests; $i++)
                                    <option value="{{ $i }}" {{ request('guests') == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                    </option>
                                @endfor
                            </select>
                            <div id="guestRecommendation" class="guest-recommendation">
                                <div style="display: flex; align-items: start; gap: 0.8rem;">
                                    <i class="fas fa-info-circle" style="font-size: 1.2rem; margin-top: 0.2rem;"></i>
                                    <div>
                                        <strong>Recommendation:</strong>
                                        <p id="recommendationText" class="recommendation-text"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Services -->
                    <div class="section-title-modal">
                        <i class="fas fa-plus-circle"></i> Additional Services (Optional)
                    </div>
                    
                    @php
                        $extras = \App\Models\Extra::where('is_active', true)->get();
                    @endphp
                    
                    @if($extras->count() > 0)
                        <div class="extras-grid">
                            @foreach($extras as $extra)
                                <div class="extra-item">
                                    <div class="extra-info">
                                        <div class="extra-name">{{ $extra->name }}</div>
                                        <div class="extra-desc">{{ $extra->description }}</div>
                                    </div>
                                    <div class="extra-price">₱{{ number_format((float)$extra->price, 2) }}</div>
                                    <div style="display: flex; align-items: center; gap: 0.8rem;">
                                        <input type="checkbox" name="extras[]" value="{{ $extra->id }}" 
                                               class="extra-checkbox" data-price="{{ $extra->price }}" 
                                               id="extra_checkbox_{{ $extra->id }}"
                                               onchange="toggleExtraQuantity({{ $extra->id }}); updateTotal()">
                                        <div id="extra_quantity_controls_{{ $extra->id }}" class="qty-controls">
                                            <button type="button" class="qty-btn" onclick="decreaseExtraQuantity({{ $extra->id }})">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" name="extra_quantities[{{ $extra->id }}]" 
                                                   id="extra_quantity_{{ $extra->id }}" 
                                                   value="1" min="1" max="50"
                                                   class="qty-input" 
                                                   onchange="updateTotal()"
                                                   readonly>
                                            <button type="button" class="qty-btn" onclick="increaseExtraQuantity({{ $extra->id }})">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="color: #666; font-style: italic;">No additional services available.</p>
                    @endif
                    
                    <!-- Payment Option -->
                    <div class="section-title-modal">
                        <i class="fas fa-credit-card"></i> Payment Option
                    </div>
                    
                    <div class="payment-options">
                        <label class="payment-option-card">
                            <input type="radio" name="payment_option" value="down_payment" checked onchange="updatePaymentSummary()">
                            <div class="payment-card-content">
                                <div class="payment-card-header">
                                    <i class="fas fa-hand-holding-usd"></i>
                                    <span>Down Payment (30%)</span>
                                </div>
                                <p class="payment-card-desc">Pay 30% now, settle remaining 70% upon check-in.</p>
                                <div class="payment-amount" id="downPaymentAmount">₱{{ number_format($room->roomType->base_price * 0.30, 2) }}</div>
                            </div>
                        </label>
                        
                        <label class="payment-option-card">
                            <input type="radio" name="payment_option" value="full_payment" onchange="updatePaymentSummary()">
                            <div class="payment-card-content">
                                <div class="payment-card-header">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span>Full Payment (100%)</span>
                                </div>
                                <p class="payment-card-desc">Pay the full amount now for hassle-free check-in.</p>
                                <div class="payment-amount" id="fullPaymentAmount">₱{{ number_format($room->roomType->base_price, 2) }}</div>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Special Requests -->
                    <div class="form-group-full" style="margin-top: 1.5rem;">
                        <label class="form-label">Special Requests</label>
                        <textarea name="special_requests" class="form-textarea" 
                                  placeholder="Any special requests or preferences..."></textarea>
                    </div>
                    
                    <button type="submit" class="submit-booking-btn">
                        <i class="fas fa-check-circle"></i> Submit Reservation
                    </button>
                </form>
            </div>

            <!-- Right: Price Details -->
            <div class="price-details-sidebar">
                <div class="room-summary-card">
                    @if($room->photos->count() > 0)
                        <img src="{{ asset('storage/' . $room->photos->first()->photo_path) }}" 
                             alt="{{ $room->roomType->name }}" class="room-summary-image">
                    @else
                        <img src="{{ asset('images/default-room.jpg') }}" 
                             alt="{{ $room->roomType->name }}" class="room-summary-image">
                    @endif
                    <div class="room-summary-content">
                        <div class="room-type-name">{{ $room->roomType->name }}</div>
                        <div class="room-price">
                            ₱{{ number_format($room->roomType->base_price, 2) }}
                            <span>/ night</span>
                        </div>
                    </div>
                </div>

                <div class="price-breakdown-card">
                    <div class="price-breakdown-title">
                        <i class="fas fa-receipt"></i> Price Details
                    </div>
                    
                    <div class="price-row">
                        <span class="price-label">Room Rate × <span id="nightsCount">1</span> night</span>
                        <span class="price-value" id="subtotalDisplay">₱{{ number_format($room->roomType->base_price, 2) }}</span>
                    </div>
                    
                    <div class="price-row">
                        <span class="price-label">Additional Services</span>
                        <span class="price-value" id="extrasDisplay">₱0.00</span>
                    </div>
                    
                    <div class="price-row">
                        <span class="price-label">Tax (12%)</span>
                        <span class="price-value" id="taxDisplay">₱{{ number_format($room->roomType->base_price * 0.12, 2) }}</span>
                    </div>
                    
                    <div class="price-row">
                        <span class="price-label">Total</span>
                        <span class="price-value" id="totalDisplay">₱{{ number_format($room->roomType->base_price, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const basePrice = {{ $room->roomType->base_price }};
        const taxRate = 0.12;

        // Calculate number of nights from check-in and check-out dates
        function calculateCheckout() {
            const checkIn = document.getElementById('checkInDate').value;
            const checkOut = document.getElementById('checkOutDate').value;

            if (checkIn && checkOut) {
                const d1 = new Date(checkIn + 'T00:00:00');
                const d2 = new Date(checkOut + 'T00:00:00');
                const nights = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
                document.getElementById('totalNights').value = nights > 0 ? nights : '';
                if (document.getElementById('nightsCount')) {
                    document.getElementById('nightsCount').textContent = nights > 0 ? nights : 0;
                }
                if (nights > 0) updateTotal();
            } else {
                document.getElementById('totalNights').value = '';
            }
        }

        // Toggle extra quantity controls
        function toggleExtraQuantity(extraId) {
            const checkbox = document.getElementById(`extra_checkbox_${extraId}`);
            const controls = document.getElementById(`extra_quantity_controls_${extraId}`);
            
            if (checkbox.checked) {
                controls.classList.add('active');
            } else {
                controls.classList.remove('active');
            }
        }

        // Increase/Decrease extra quantity
        function increaseExtraQuantity(extraId) {
            const input = document.getElementById(`extra_quantity_${extraId}`);
            if (parseInt(input.value) < parseInt(input.max)) {
                input.value = parseInt(input.value) + 1;
                updateTotal();
            }
        }

        function decreaseExtraQuantity(extraId) {
            const input = document.getElementById(`extra_quantity_${extraId}`);
            if (parseInt(input.value) > parseInt(input.min)) {
                input.value = parseInt(input.value) - 1;
                updateTotal();
            }
        }

        // Update total calculation
        function updateTotal() {
            const nights = parseInt(document.getElementById('totalNights').value) || 1;
            const subtotal = basePrice * nights;
            
            // Calculate extras
            let extrasTotal = 0;
            document.querySelectorAll('.extra-checkbox:checked').forEach(checkbox => {
                const extraId = checkbox.value;
                const price = parseFloat(checkbox.dataset.price);
                const quantity = parseInt(document.getElementById(`extra_quantity_${extraId}`).value) || 1;
                extrasTotal += price * quantity;
            });
            
            // Calculate 12% tax on subtotal + extras
            const taxAmount = (subtotal + extrasTotal) * taxRate;
            const total = subtotal + extrasTotal + taxAmount; // Tax added on top
            
            // Update displays
            document.getElementById('subtotalDisplay').textContent = '₱' + subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            document.getElementById('extrasDisplay').textContent = '₱' + extrasTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            document.getElementById('taxDisplay').textContent = '₱' + taxAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            document.getElementById('totalDisplay').textContent = '₱' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            
            updatePaymentSummary();
        }

        // Update payment amounts
        function updatePaymentSummary() {
            const totalText = document.getElementById('totalDisplay').textContent;
            const total = parseFloat(totalText.replace(/[₱,]/g, ''));
            
            const downPayment = total * 0.30;
            const fullPayment = total;
            
            document.getElementById('downPaymentAmount').textContent = '₱' + downPayment.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            document.getElementById('fullPaymentAmount').textContent = '₱' + fullPayment.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        // Guest recommendation
        function showGuestRecommendation() {
            const guestCount = parseInt(document.getElementById('guestCountSelect').value);
            const roomMaxGuests = {{ $room->roomType->max_guests }};
            const roomTypeName = "{{ $room->roomType->name }}";
            const recommendationBox = document.getElementById('guestRecommendation');
            const recommendationText = document.getElementById('recommendationText');
            
            let message = '';
            let showBox = false;
            
            if (roomTypeName.toLowerCase().includes('deluxe')) {
                if (guestCount === 4 && roomMaxGuests >= 4) {
                    message = 'For 4 guests, we <strong>strongly recommend</strong> adding an extra bed (₱800/stay). Select "Extra Bedding" below for optimal comfort.';
                    showBox = true;
                }
            }
            
            if (showBox && message) {
                recommendationText.innerHTML = message;
                recommendationBox.classList.add('show');
            } else {
                recommendationBox.classList.remove('show');
            }
        }

        // Set minimum check-out date
        document.getElementById('checkInDate').addEventListener('change', function() {
            calculateCheckout();
        });

        // Initialize on page load - auto-fill if URL has check_in/check_out params
        (function init() {
            @if(request('check_in') && request('check_out'))
                document.getElementById('checkInDate').value = '{{ request('check_in') }}';
                document.getElementById('checkOutDate').value = '{{ request('check_out') }}';
            @endif
            calculateCheckout();
            showGuestRecommendation();
        })();
    </script>
</body>
</html>
