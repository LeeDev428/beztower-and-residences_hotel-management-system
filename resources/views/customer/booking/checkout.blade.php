<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Information - {{ $room->roomType->name }} - Bez Tower & Residences</title>
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

        .room-viewer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 0.8rem;
        }

        .room-viewer-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: #2c2c2c;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .room-nav-controls {
            display: flex;
            gap: 0.45rem;
        }

        .room-nav-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid #e1e1e1;
            background: #fff;
            color: #2c2c2c;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .room-nav-btn:hover {
            border-color: #d4af37;
            color: #d4af37;
        }

        .room-thumbs {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.45rem;
            margin-top: 0.65rem;
        }

        .room-thumb {
            width: 100%;
            height: 52px;
            object-fit: cover;
            border-radius: 7px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: border-color 0.2s ease;
        }

        .room-thumb.active {
            border-color: #d4af37;
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

        .policies-box {
            margin-top: 1rem;
            padding: 1rem;
            border: 1px solid #e2e2e2;
            border-radius: 10px;
            background: #fafafa;
        }

        .policies-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .policy-chip {
            background: #f0f0f0;
            border-radius: 8px;
            padding: 0.65rem 0.8rem;
        }

        .policy-chip .label {
            display: block;
            font-size: 0.78rem;
            color: #666;
            margin-bottom: 0.2rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .policy-chip .value {
            font-size: 0.95rem;
            font-weight: 700;
            color: #2c2c2c;
        }

        .policy-preview {
            font-size: 0.88rem;
            color: #555;
            line-height: 1.55;
            margin-bottom: 0.75rem;
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

        .legal-consent-box {
            margin-top: 1.25rem;
            padding: 1rem;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            background: #fafafa;
        }

        .legal-consent-box label {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            cursor: pointer;
            color: #2c2c2c;
            font-size: 0.93rem;
            line-height: 1.55;
        }

        .legal-consent-box input[type="checkbox"] {
            margin-top: 0.2rem;
            width: 18px;
            height: 18px;
            accent-color: #d4af37;
            cursor: pointer;
        }

        .legal-link {
            color: #b38c17;
            text-decoration: underline;
            font-weight: 600;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            font: inherit;
        }

        .legal-link:hover {
            color: #8f6d0e;
        }

        .legal-note {
            margin-top: 0.75rem;
            color: #666;
            font-size: 0.88rem;
        }

        .legal-modal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 99999;
        }

        .legal-modal.show {
            display: flex;
        }

        .legal-modal-card {
            width: 100%;
            max-width: 760px;
            max-height: 82vh;
            overflow: hidden;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-direction: column;
        }

        .legal-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #ececec;
        }

        .legal-modal-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2c2c2c;
        }

        .legal-modal-close {
            border: none;
            background: none;
            font-size: 1.5rem;
            line-height: 1;
            cursor: pointer;
            color: #555;
        }

        .legal-modal-content {
            padding: 1rem 1.2rem 1.2rem;
            overflow-y: auto;
            white-space: pre-line;
            color: #2f2f2f;
            line-height: 1.6;
            font-size: 0.92rem;
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
                    <h2><i class="fas fa-file-invoice"></i> Booking Information</h2>
                    <p>Please fill in your details to complete the booking</p>
                </div>

                <div style="background:#fff3cd;border:1px solid #f0cc65;color:#7c5a00;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:0.92rem;">
                    <strong>Important:</strong> Please provide an active Gmail account. We will use this email to send payment verification and booking updates.
                </div>

                @if ($errors->has('error'))
                    <div style="background:#fee2e2;border:1px solid #f87171;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:0.95rem;">
                        <strong>Booking Limit Reached:</strong> {{ $errors->first('error') }}
                    </div>
                @endif

                @if ($errors->has('room_ids'))
                    <div style="background:#fee2e2;border:1px solid #f87171;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:0.95rem;">
                        <strong>Room Availability Conflict:</strong> {{ $errors->first('room_ids') }}
                    </div>
                @endif

                @if ($errors->has('check_in_date') || $errors->has('check_out_date') || $errors->has('number_of_guests') || $errors->has('number_of_rooms'))
                    <div style="background:#fff3cd;border:1px solid #f0cc65;color:#7c5a00;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:0.95rem;">
                        <strong>Please review your booking details:</strong>
                        <ul style="margin:0.5rem 0 0 1rem; padding:0;">
                            @if ($errors->has('check_in_date'))<li>{{ $errors->first('check_in_date') }}</li>@endif
                            @if ($errors->has('check_out_date'))<li>{{ $errors->first('check_out_date') }}</li>@endif
                            @if ($errors->has('number_of_guests'))<li>{{ $errors->first('number_of_guests') }}</li>@endif
                            @if ($errors->has('number_of_rooms'))<li>{{ $errors->first('number_of_rooms') }}</li>@endif
                        </ul>
                    </div>
                @endif

                <div id="availabilityValidationMessage" style="display:none;background:#fee2e2;border:1px solid #f87171;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:0.95rem;"></div>

                <form action="{{ route('booking.create') }}" method="POST" id="bookingForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="submission_key" id="submissionKey" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
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
                    @php
                        $isBookingDetailsLocked = request()->filled('check_in') && request()->filled('check_out') && (request()->filled('guests') || request()->filled('adults') || request()->filled('children'));
                    @endphp
                    <div class="section-title-modal">
                        <i class="fas fa-calendar-alt"></i> Booking Details
                    </div>

                    @if($isBookingDetailsLocked)
                        <div style="margin-bottom: 1rem; padding: 0.8rem 0.9rem; border: 1px solid #f1dfab; border-radius: 8px; background: #fff9e6; color: #5f4b1b; font-size: 0.88rem;">
                            Booking dates and guest count are locked based on your selected room search.
                        </div>
                    @endif
                    
                    <div class="booking-form-grid">
                        <div>
                            <label class="form-label">Check-In Date <span class="required">*</span></label>
                            <input type="date" name="check_in_date" class="form-input" id="checkInDate" 
                                   min="{{ date('Y-m-d') }}" value="{{ request('check_in') }}" required onchange="calculateCheckout()" {{ $isBookingDetailsLocked ? 'readonly' : '' }} style="{{ $isBookingDetailsLocked ? 'background:#f0f0f0;cursor:not-allowed;' : '' }}">
                        </div>

                        <div>
                            <label class="form-label">Check-Out Date <span class="required">*</span></label>
                            <input type="date" name="check_out_date" class="form-input" id="checkOutDate" 
                                   required onchange="calculateCheckout()" {{ $isBookingDetailsLocked ? 'readonly' : '' }} style="{{ $isBookingDetailsLocked ? 'background:#f0f0f0;cursor:not-allowed;' : '' }}">
                        </div>
                        
                        <div>
                            <label class="form-label">Number of Nights (Auto-calculated)</label>
                            <input type="number" name="total_nights" class="form-input" id="totalNights" 
                                   readonly style="background: #f0f0f0; cursor: not-allowed;" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Number of Guests <span class="required">*</span></label>
                            <select name="number_of_guests" class="form-select" id="guestCountSelect" required onchange="showGuestRecommendation()">
                                @php
                                    $maxGuestsOption = max(1, (int) ($maxGuestCapacity ?? ($room->roomType->max_guests ?? 1)));
                                    $adultsCount = (int) request('adults', 0);
                                    $childrenCount = (int) request('children', 0);
                                    $computedGuests = $adultsCount > 0 || $childrenCount > 0
                                        ? ($adultsCount + intdiv(max(0, $childrenCount), 2))
                                        : (int) request('guests', 1);
                                    $requestedGuests = max(1, $computedGuests);
                                    $requestedGuests = max(1, min($requestedGuests, $maxGuestsOption));
                                @endphp
                                @for($i = 1; $i <= $maxGuestsOption; $i++)
                                    <option value="{{ $i }}" {{ $requestedGuests === $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}
                                    </option>
                                @endfor
                            </select>
                            @if($isBookingDetailsLocked)
                                <input type="hidden" name="number_of_guests" id="lockedGuestCountHidden" value="{{ $requestedGuests }}">
                            @endif
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

                        <div>
                            <label class="form-label">How Many Rooms <span class="required">*</span></label>
                            <input type="number" name="number_of_rooms" id="numberOfRooms" class="form-input" min="1" max="12" value="{{ max(1, min(12, (int) ($requestedRooms ?? request('rooms', 1)))) }}" {{ isset($preselectedRooms) && $preselectedRooms->count() > 0 ? 'readonly' : '' }} required onchange="syncAutoSelectedRooms(); updateTotal();" style="{{ isset($preselectedRooms) && $preselectedRooms->count() > 0 ? 'background:#f0f0f0; cursor:not-allowed;' : '' }}">
                        </div>

                        <div class="form-group-full">
                            <label class="form-label">Room Assignment <span class="required">*</span></label>
                            <div id="availabilityByType" style="font-size: 0.9rem; color: #666; margin-bottom: 0.7rem;">Select check-in and check-out dates to load available rooms.</div>
                            @php
                                $lockedRoomMeta = (isset($preselectedRooms) && $preselectedRooms->count() > 0)
                                    ? $preselectedRooms->map(function ($selectedRoom) {
                                        return [
                                            'id' => (int) $selectedRoom->id,
                                            'room_type' => $selectedRoom->roomType->name ?? 'Room',
                                            'price' => (float) ($selectedRoom->effective_price ?? 0),
                                            'capacity' => (int) ($selectedRoom->roomType->max_guests ?? 0),
                                        ];
                                    })->values()
                                    : collect();
                            @endphp
                            <div id="autoAssignedRoomsSummary" style="background:#f8f8f8;border:1px solid #e5e5e5;border-radius:8px;padding:0.9rem;font-size:0.9rem;color:#444;">
                                @if($lockedRoomMeta->isNotEmpty())
                                    {!! $lockedRoomMeta->map(function ($lockedRoom) {
                                        return '<div style="padding:0.2rem 0;">' . e($lockedRoom['room_type']) . ' (₱' . number_format((float) $lockedRoom['price'], 2) . '/night)</div>';
                                    })->implode('') !!}
                                @else
                                    Rooms will be automatically assigned after selecting dates.
                                @endif
                            </div>
                            <div id="autoSelectedRoomInputs">
                                @if($lockedRoomMeta->isNotEmpty())
                                    @foreach($lockedRoomMeta as $lockedRoom)
                                        <input type="hidden" name="room_ids[]" value="{{ $lockedRoom['id'] }}">
                                    @endforeach
                                @endif
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
                                                 class="extra-checkbox" data-price="{{ $extra->price }}" data-name="{{ $extra->name }}"
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
                                <div class="payment-amount" id="downPaymentAmount">₱{{ number_format($room->effective_price * 0.30, 2) }}</div>
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
                                <div class="payment-amount" id="fullPaymentAmount">₱{{ number_format($room->effective_price, 2) }}</div>
                            </div>
                        </label>
                    </div>

                    <div class="section-title-modal" style="margin-top: 1.2rem;">
                        <i class="fas fa-file-contract"></i> Policies
                    </div>

                    <div class="policies-box">
                        <div class="policies-grid">
                            <div class="policy-chip">
                                <span class="label">Check-In</span>
                                <span class="value">2:00 PM</span>
                            </div>
                            <div class="policy-chip">
                                <span class="label">Check-Out</span>
                                <span class="value">12:00 PM</span>
                            </div>
                        </div>
                        <div class="policy-preview">
                            This reservation is non-cancellable and non-refundable but may be rebooked. Rebooking must be requested at least 1 day before arrival and the new date must be within 2 weeks from the original booking date.
                        </div>
                        <button type="button" class="legal-link" data-legal-open="policies">View Full Policy</button>
                    </div>
                    
                    <!-- Special Requests -->
                    <div class="form-group-full" style="margin-top: 1.5rem;">
                        <label class="form-label">Special Requests</label>
                        <textarea name="special_requests" class="form-textarea" 
                                  placeholder="Any special requests or preferences..."></textarea>
                    </div>

                    <div class="legal-consent-box">
                        <label for="termsAccepted">
                            <input type="checkbox" id="termsAccepted" name="terms_accepted" value="1" required>
                            <span>
                                I agree to the
                                <button type="button" class="legal-link" data-legal-open="terms">Terms &amp; Conditions</button>
                                and
                                <button type="button" class="legal-link" data-legal-open="privacy">Privacy Policy</button>.
                            </span>
                        </label>
                        <div class="legal-note">By submitting this booking, you confirm that all provided information is accurate.</div>
                        @error('terms_accepted')
                            <div style="color:#dc3545;font-size:0.85rem;margin-top:0.55rem;">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="submit-booking-btn" id="bookingSubmitBtn">
                        <i class="fas fa-check-circle"></i> Submit Reservation
                    </button>
                </form>
            </div>

            <!-- Right: Price Details -->
            <div class="price-details-sidebar">
                @php
                    $selectedSidebarRooms = isset($preselectedRooms) && $preselectedRooms->count() > 0
                        ? $preselectedRooms->values()
                        : collect([$room]);

                    $sidebarRoomMedia = $selectedSidebarRooms->map(function ($selectedRoom) {
                        $photos = method_exists($selectedRoom, 'photos')
                            ? $selectedRoom->photos->pluck('photo_path')->filter()->values()
                            : collect();

                        $imageUrls = $photos->map(fn ($path) => asset('storage/' . $path))->values()->all();
                        if (empty($imageUrls)) {
                            $imageUrls = [asset('images/default-room.jpg')];
                        }

                        return [
                            'id' => (int) $selectedRoom->id,
                            'name' => $selectedRoom->roomType->name ?? 'Room',
                            'price' => (float) ($selectedRoom->effective_price ?? 0),
                            'images' => $imageUrls,
                        ];
                    })->values();
                @endphp

                <div class="room-summary-card">
                    <div class="room-summary-content" style="padding-bottom: 0.75rem;">
                        <div class="room-viewer-header">
                            <div class="room-viewer-title" id="roomViewerTitle">Selected Room</div>
                            <div class="room-nav-controls" id="roomNavControls" style="display: {{ $sidebarRoomMedia->count() > 1 ? 'flex' : 'none' }};">
                                <button type="button" class="room-nav-btn" id="prevRoomBtn" aria-label="Previous room">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="room-nav-btn" id="nextRoomBtn" aria-label="Next room">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <img src="{{ $sidebarRoomMedia->first()['images'][0] ?? asset('images/default-room.jpg') }}"
                         alt="Selected room image" class="room-summary-image" id="roomViewerMainImage">

                    <div class="room-summary-content" style="padding-top: 0.8rem;">
                        <div class="room-thumbs" id="roomViewerThumbs"></div>
                    </div>

                    <div class="room-summary-content">
                        <div class="room-type-name" id="roomViewerRoomName">{{ $sidebarRoomMedia->first()['name'] ?? ($room->roomType->name ?? 'Room') }}</div>
                        <div class="room-price">
                            ₱<span id="roomViewerRoomPrice" style="font-size:1.1rem;color:#d4af37;font-weight:600;">{{ number_format((float) ($sidebarRoomMedia->first()['price'] ?? $room->effective_price), 2) }}</span>
                            <span>/ night</span>
                        </div>
                    </div>
                </div>

                <div class="price-breakdown-card">
                    <div class="price-breakdown-title">
                        <i class="fas fa-receipt"></i> Price Details
                    </div>

                    <div id="selectedRoomsPriceList" style="margin-bottom: 0.4rem; font-size: 0.9rem; color:#444;"></div>
                    
                    <div class="price-row">
                        <span class="price-label">Room Rate × <span id="nightsCount">1</span> night</span>
                        <span class="price-value" id="subtotalDisplay">₱{{ number_format($room->effective_price, 2) }}</span>
                    </div>

                    <div class="price-row">
                        <span class="price-label">VAT ({{ number_format((float) ($vatPercentage ?? 12), 2) }}%) Included in Room Rate</span>
                        <span class="price-value" id="vatDisplay">₱{{ number_format($room->effective_price * ((float) ($vatInclusiveFraction ?? (12 / 112))), 2) }}</span>
                    </div>

                    <div class="price-row">
                        <span class="price-label">Additional Services</span>
                        <span class="price-value" id="extrasDisplay">₱0.00</span>
                    </div>

                    <div id="extrasBreakdownList" style="font-size: 0.85rem; color: #666; margin-top: 0.2rem; margin-bottom: 0.3rem;"></div>
                    
                    <div class="price-row">
                        <span class="price-label">Total</span>
                        <span class="price-value" id="totalDisplay">₱{{ number_format($room->effective_price, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="legal-modal" id="legalModal" aria-hidden="true">
        <div class="legal-modal-card" role="dialog" aria-modal="true" aria-labelledby="legalModalTitle">
            <div class="legal-modal-header">
                <div class="legal-modal-title" id="legalModalTitle">Terms &amp; Conditions</div>
                <button type="button" class="legal-modal-close" data-legal-close>&times;</button>
            </div>
            <div class="legal-modal-content" id="legalModalContent"></div>
        </div>
    </div>

    <script>
        const legalTermsText = @json($termsAndConditionsText ?? '');
        const vatInclusiveFraction = {{ (float) ($vatInclusiveFraction ?? (12 / 112)) }};

        const legalPrivacyText = `At Bez Tower Residences, your privacy is important to us. When you use our online reservation system, we collect only the information necessary to make your booking smooth and secure.

We ensure that your personal details such as your name, address, contact information, and payment details are kept confidential and used solely to manage your reservation and enhance your experience with us.

We are committed to protecting your personal data in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173) of the Philippines. Your information will not be shared with unauthorized parties and will only be used for purposes directly related to your stay or as required by law.`;

    const billingPoliciesText = `1. Standard Check-in & Check-out Times

Type: Standard Time
- Check-in: 2:00 PM
- Check-out: 12:00 PM (Noon)

2. Late Check-in - Always Allowed
- Guests may check in at any time after 2:00 PM on the check-in date, as long as the booking is paid and confirmed.
- System should not reject valid late arrivals.

3. One-Night Stay Rule
- For one-night bookings, check-out remains 12:00 PM the next day regardless of actual check-in time.

4. Early Check-in - Subject to Room Availability
- Early check-in before 2:00 PM is subject to room availability and admin approval.

5. Room Availability Release Logic
5.1 Default behavior (no approved late check-out)
- Room becomes available at 12:00 PM on the check-out date.

5.2 Immediate release upon checkout
- Once guest checks out (standard or approved extended time), room should be released immediately.

6. Late Check-out - Admin Approved, Subject to Availability
6.1 Eligibility
- Any guest may request late check-out.

6.2 Approval and blocking
- If approved (for example until 3:00 PM), room stays blocked until the approved time.

6.3 Release after late checkout
- After approved late check-out time, room must be released immediately and become bookable again.

7. Developer Summary
- Do not block a room for the whole day by default.
- Release room at 12:00 PM by default, or at approved late check-out time.
- Always allow late check-in for paid/confirmed bookings.
- Flag admin conflicts when approved late check-out overlaps with another booking.`;

        const legalModal = document.getElementById('legalModal');
        const legalModalTitle = document.getElementById('legalModalTitle');
        const legalModalContent = document.getElementById('legalModalContent');

        function openLegalModal(type) {
            if (!legalModal || !legalModalTitle || !legalModalContent) {
                return;
            }

            if (type === 'privacy') {
                legalModalTitle.textContent = 'Privacy Policy';
                legalModalContent.textContent = legalPrivacyText;
            } else if (type === 'policies') {
                legalModalTitle.textContent = 'Hotel Policies';
                legalModalContent.textContent = billingPoliciesText;
            } else {
                legalModalTitle.textContent = 'Terms & Conditions';
                legalModalContent.textContent = legalTermsText;
            }

            legalModal.classList.add('show');
            legalModal.setAttribute('aria-hidden', 'false');
        }

        function closeLegalModal() {
            if (!legalModal) {
                return;
            }

            legalModal.classList.remove('show');
            legalModal.setAttribute('aria-hidden', 'true');
        }

        document.querySelectorAll('[data-legal-open]').forEach((trigger) => {
            trigger.addEventListener('click', () => {
                openLegalModal(trigger.getAttribute('data-legal-open'));
            });
        });

        document.querySelectorAll('[data-legal-close]').forEach((closeTrigger) => {
            closeTrigger.addEventListener('click', closeLegalModal);
        });

        if (legalModal) {
            legalModal.addEventListener('click', (event) => {
                if (event.target === legalModal) {
                    closeLegalModal();
                }
            });
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeLegalModal();
            }
        });

        const selectedSidebarRooms = @json($sidebarRoomMedia->all());
        let currentSidebarRoomIndex = 0;
        let currentSidebarImageIndex = 0;

        function renderRoomViewer() {
            if (!Array.isArray(selectedSidebarRooms) || selectedSidebarRooms.length === 0) {
                return;
            }

            const room = selectedSidebarRooms[currentSidebarRoomIndex] || selectedSidebarRooms[0];
            const images = Array.isArray(room.images) && room.images.length > 0 ? room.images : ['{{ asset('images/default-room.jpg') }}'];

            if (currentSidebarImageIndex >= images.length) {
                currentSidebarImageIndex = 0;
            }

            const mainImage = document.getElementById('roomViewerMainImage');
            const title = document.getElementById('roomViewerTitle');
            const name = document.getElementById('roomViewerRoomName');
            const price = document.getElementById('roomViewerRoomPrice');
            const thumbs = document.getElementById('roomViewerThumbs');

            if (mainImage) {
                mainImage.src = images[currentSidebarImageIndex];
                mainImage.alt = `${room.name} image ${currentSidebarImageIndex + 1}`;
            }

            if (title) {
                title.textContent = selectedSidebarRooms.length > 1
                    ? `Room ${currentSidebarRoomIndex + 1} of ${selectedSidebarRooms.length}`
                    : 'Selected Room';
            }

            if (name) {
                name.textContent = room.name;
            }

            if (price) {
                const roomPrice = Number(room.price || 0);
                price.textContent = roomPrice.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            if (thumbs) {
                thumbs.innerHTML = '';
                images.forEach((imageUrl, index) => {
                    const img = document.createElement('img');
                    img.src = imageUrl;
                    img.alt = `${room.name} thumbnail ${index + 1}`;
                    img.className = 'room-thumb' + (index === currentSidebarImageIndex ? ' active' : '');
                    img.addEventListener('click', () => {
                        currentSidebarImageIndex = index;
                        renderRoomViewer();
                    });
                    thumbs.appendChild(img);
                });
            }
        }

        function moveRoomViewer(direction) {
            if (!Array.isArray(selectedSidebarRooms) || selectedSidebarRooms.length <= 1) {
                return;
            }

            currentSidebarRoomIndex += direction;
            if (currentSidebarRoomIndex < 0) {
                currentSidebarRoomIndex = selectedSidebarRooms.length - 1;
            }
            if (currentSidebarRoomIndex >= selectedSidebarRooms.length) {
                currentSidebarRoomIndex = 0;
            }
            currentSidebarImageIndex = 0;
            renderRoomViewer();
        }

        const prevRoomBtn = document.getElementById('prevRoomBtn');
        const nextRoomBtn = document.getElementById('nextRoomBtn');
        if (prevRoomBtn) {
            prevRoomBtn.addEventListener('click', () => moveRoomViewer(-1));
        }
        if (nextRoomBtn) {
            nextRoomBtn.addEventListener('click', () => moveRoomViewer(1));
        }
        renderRoomViewer();

        const basePrice = {{ (float) $room->effective_price }};
        const preferredRoomId = {{ $room->id }};
        const lockedSelectedRooms = @json($lockedRoomMeta->values()->all());
        const lockedSelectedRoomIds = lockedSelectedRooms.map((roomData) => Number(roomData.id));
        const isRoomCountLocked = {{ (isset($preselectedRooms) && $preselectedRooms->count() > 0) ? 'true' : 'false' }};
        const fallbackRoomCapacity = {{ (int) ($room->roomType->max_guests ?? 1) }};
        let availableRooms = [];
        let recommendedRoomIds = [];

        function syncGuestLimit(selectedRooms) {
            const guestSelect = document.getElementById('guestCountSelect');
            const lockedGuestHidden = document.getElementById('lockedGuestCountHidden');

            if (guestSelect && lockedGuestHidden) {
                guestSelect.value = lockedGuestHidden.value;
                return;
            }

            const currentValue = parseInt(guestSelect.value || '1', 10);

            let maxGuests = 0;
            if (selectedRooms && selectedRooms.length) {
                maxGuests = selectedRooms.reduce((sum, selectedRoom) => sum + Number(selectedRoom.capacity || 0), 0);
            }

            if (maxGuests <= 0) {
                maxGuests = fallbackRoomCapacity;
            }

            guestSelect.innerHTML = '';
            for (let i = 1; i <= maxGuests; i += 1) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `${i} ${i === 1 ? 'Guest' : 'Guests'}`;
                guestSelect.appendChild(option);
            }

            guestSelect.value = String(Math.min(Math.max(currentValue, 1), maxGuests));
        }

        function getSelectedRoomIds() {
            return Array.from(document.querySelectorAll('input[name="room_ids[]"]')).map((el) => Number(el.value));
        }

        function getSelectedRoomNightlyTotal() {
            const selectedIds = new Set(getSelectedRoomIds());
            const roomPriceMap = new Map();

            lockedSelectedRooms.forEach((lockedRoom) => {
                roomPriceMap.set(Number(lockedRoom.id), Number(lockedRoom.price || 0));
            });

            availableRooms.forEach((availableRoom) => {
                roomPriceMap.set(Number(availableRoom.id), Number(availableRoom.price || 0));
            });

            let total = 0;
            selectedIds.forEach((selectedId) => {
                if (roomPriceMap.has(Number(selectedId))) {
                    total += Number(roomPriceMap.get(Number(selectedId)) || 0);
                }
            });
            return total;
        }

        function renderSelectedRoomsPriceList(selectedRoomEntries, nights) {
            const container = document.getElementById('selectedRoomsPriceList');
            if (!container) {
                return;
            }

            if (!selectedRoomEntries || selectedRoomEntries.length === 0) {
                container.innerHTML = '';
                return;
            }

            container.innerHTML = selectedRoomEntries.map((selectedRoom) => {
                const nightly = Number(selectedRoom.price || 0);
                const total = nightly * nights;
                const nightlyText = nightly.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                const totalText = total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                return `<div style="display:flex;justify-content:space-between;gap:0.8rem;padding:0.22rem 0;">
                    <span>${selectedRoom.room_type}</span>
                    <span>₱${nightlyText}/night (₱${totalText})</span>
                </div>`;
            }).join('');
        }

        async function loadAvailableRooms() {
            const checkIn = document.getElementById('checkInDate').value;
            const checkOut = document.getElementById('checkOutDate').value;
            const requestedRooms = Math.max(1, Math.min(12, parseInt(document.getElementById('numberOfRooms').value || '1', 10)));
            const requestedGuests = Math.max(1, parseInt(document.getElementById('guestCountSelect').value || '1', 10));

            if (!checkIn || !checkOut) {
                availableRooms = [];
                syncAutoSelectedRooms();
                updateTotal();
                return;
            }

            try {
                const params = new URLSearchParams({
                    check_in_date: checkIn,
                    check_out_date: checkOut,
                    number_of_rooms: String(requestedRooms),
                    number_of_guests: String(requestedGuests),
                });
                const response = await fetch(`{{ route('booking.availableRooms') }}?${params.toString()}`);
                const data = await response.json();
                availableRooms = data.rooms || [];
                recommendedRoomIds = Array.isArray(data.recommended_room_ids) ? data.recommended_room_ids.map((id) => Number(id)) : [];

                const byType = data.remaining_by_type || [];
                const availabilityByType = document.getElementById('availabilityByType');
                if (byType.length) {
                    availabilityByType.innerHTML = byType.map((item) => `${item.room_type}: <strong>${item.remaining}</strong>`).join(' | ');
                } else {
                    availabilityByType.textContent = 'No rooms available for selected dates.';
                }

                syncAutoSelectedRooms();
                updateTotal();
            } catch (error) {
                availableRooms = [];
                recommendedRoomIds = [];
                document.getElementById('availabilityByType').textContent = 'Unable to load available rooms right now.';
                syncAutoSelectedRooms();
                updateTotal();
            }
        }

        function syncAutoSelectedRooms() {
            const roomCountInput = document.getElementById('numberOfRooms');
            if (isRoomCountLocked && lockedSelectedRoomIds.length > 0) {
                roomCountInput.value = String(lockedSelectedRoomIds.length);
            }

            const requestedRooms = Math.max(1, Math.min(12, parseInt(roomCountInput.value || '1', 10)));
            const summary = document.getElementById('autoAssignedRoomsSummary');
            const hiddenInputs = document.getElementById('autoSelectedRoomInputs');
            hiddenInputs.innerHTML = '';

            if (isRoomCountLocked && lockedSelectedRooms.length > 0) {
                const selectedRooms = lockedSelectedRooms.slice(0, requestedRooms);

                selectedRooms.forEach((roomData) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'room_ids[]';
                    input.value = roomData.id;
                    hiddenInputs.appendChild(input);
                });

                syncGuestLimit(selectedRooms);

                summary.innerHTML = selectedRooms.map((roomData) => {
                    const nightly = Number(roomData.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    return `<div style="padding:0.2rem 0;">${roomData.room_type} (₱${nightly}/night)</div>`;
                }).join('');
                return;
            }

            if (!availableRooms.length) {
                summary.innerHTML = '<span style="color:#b00020;">No available rooms found for the selected date range.</span>';
                syncGuestLimit([]);
                return;
            }

            const prioritized = [...availableRooms].sort((a, b) => {
                if (a.id === preferredRoomId) return -1;
                if (b.id === preferredRoomId) return 1;
                return Number(a.id) - Number(b.id);
            });

            let selectedRooms = [];

            if (isRoomCountLocked && lockedSelectedRoomIds.length > 0) {
                const lockedSet = new Set(lockedSelectedRoomIds);
                selectedRooms = prioritized.filter((room) => lockedSet.has(Number(room.id))).slice(0, requestedRooms);

                if (selectedRooms.length < requestedRooms) {
                    summary.innerHTML = `<span style="color:#b00020;">Some previously selected rooms are no longer available for the selected dates. Please reselect rooms from the rooms page.</span>`;
                    syncGuestLimit(selectedRooms);
                    selectedRooms.forEach((room) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'room_ids[]';
                        input.value = room.id;
                        hiddenInputs.appendChild(input);
                    });
                    return;
                }
            } else {
                if (recommendedRoomIds.length > 0) {
                    const recommendedSet = new Set(recommendedRoomIds);
                    const preferred = prioritized.filter((room) => recommendedSet.has(Number(room.id)));
                    const fallback = prioritized.filter((room) => !recommendedSet.has(Number(room.id)));
                    selectedRooms = [...preferred, ...fallback].slice(0, requestedRooms);
                } else {
                    selectedRooms = prioritized.slice(0, requestedRooms);
                }
            }

            selectedRooms.forEach((room) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'room_ids[]';
                input.value = room.id;
                hiddenInputs.appendChild(input);
            });

            syncGuestLimit(selectedRooms);

            if (selectedRooms.length < requestedRooms) {
                summary.innerHTML = `<span style="color:#b00020;">Only ${selectedRooms.length} room(s) are available for the selected dates, but ${requestedRooms} room(s) were requested.</span>`;
                return;
            }

            summary.innerHTML = selectedRooms.map((room) => {
                const nightly = Number(room.price || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                return `<div style="padding:0.2rem 0;">${room.room_type} (₱${nightly}/night)</div>`;
            }).join('');
        }

        // Calculate number of nights from check-in and check-out dates
        function calculateCheckout() {
            const checkInInput = document.getElementById('checkInDate');
            const checkOutInput = document.getElementById('checkOutDate');
            const checkIn = checkInInput.value;
            const checkOut = checkOutInput.value;

            if (checkIn) {
                const minCheckOutDate = new Date(checkIn + 'T00:00:00');
                minCheckOutDate.setDate(minCheckOutDate.getDate() + 1);
                const minCheckOut = minCheckOutDate.toISOString().split('T')[0];
                checkOutInput.min = minCheckOut;

                if (checkOut && checkOut <= checkIn) {
                    checkOutInput.value = '';
                }
            }

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

            loadAvailableRooms();
        }

        function normalizeBookingDates() {
            const checkInInput = document.getElementById('checkInDate');
            const checkOutInput = document.getElementById('checkOutDate');

            if (!checkInInput || !checkOutInput) {
                return;
            }

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const todayString = today.toISOString().split('T')[0];
            checkInInput.min = todayString;

            if (checkInInput.value && checkInInput.value < todayString) {
                checkInInput.value = todayString;
            }

            if (checkInInput.value) {
                const minCheckOutDate = new Date(checkInInput.value + 'T00:00:00');
                minCheckOutDate.setDate(minCheckOutDate.getDate() + 1);
                const minCheckOut = minCheckOutDate.toISOString().split('T')[0];
                checkOutInput.min = minCheckOut;

                if (checkOutInput.value && checkOutInput.value < minCheckOut) {
                    checkOutInput.value = minCheckOut;
                }
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
            const selectedNightlyTotal = getSelectedRoomNightlyTotal();
            const subtotal = (selectedNightlyTotal > 0 ? selectedNightlyTotal : basePrice) * nights;

            const selectedIds = new Set(getSelectedRoomIds());
            const selectedRoomEntries = [];

            lockedSelectedRooms.forEach((lockedRoom) => {
                if (selectedIds.has(Number(lockedRoom.id))) {
                    selectedRoomEntries.push(lockedRoom);
                }
            });

            if (selectedRoomEntries.length === 0 && availableRooms.length > 0) {
                availableRooms.forEach((availableRoom) => {
                    if (selectedIds.has(Number(availableRoom.id))) {
                        selectedRoomEntries.push(availableRoom);
                    }
                });
            }

            renderSelectedRoomsPriceList(selectedRoomEntries, nights);
            
            // Calculate extras
            let extrasTotal = 0;
            const selectedExtrasLines = [];
            document.querySelectorAll('.extra-checkbox:checked').forEach(checkbox => {
                const extraId = checkbox.value;
                const price = parseFloat(checkbox.dataset.price);
                const extraName = checkbox.dataset.name || 'Extra Service';
                const quantity = parseInt(document.getElementById(`extra_quantity_${extraId}`).value) || 1;
                extrasTotal += price * quantity;
                selectedExtrasLines.push(`${extraName} (${quantity} x ₱${price.toFixed(2)}) : ₱${(price * quantity).toFixed(2)}`);
            });
            
            const total = subtotal + extrasTotal;
            const vatIncluded = subtotal * vatInclusiveFraction;
            
            // Update displays
            document.getElementById('subtotalDisplay').textContent = '₱' + subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            document.getElementById('vatDisplay').textContent = '₱' + vatIncluded.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            document.getElementById('extrasDisplay').textContent = '₱' + extrasTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            document.getElementById('totalDisplay').textContent = '₱' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');

            const extrasBreakdownList = document.getElementById('extrasBreakdownList');
            if (extrasBreakdownList) {
                if (selectedExtrasLines.length === 0) {
                    extrasBreakdownList.innerHTML = '';
                } else {
                    extrasBreakdownList.innerHTML = selectedExtrasLines
                        .map((line) => `<div style="padding-left:0.25rem; margin-bottom:0.15rem;">${line}</div>`)
                        .join('');
                }
            }
            
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
            normalizeBookingDates();
            calculateCheckout();
            showGuestRecommendation();

            const guestSelect = document.getElementById('guestCountSelect');
            const lockedGuestHidden = document.getElementById('lockedGuestCountHidden');
            if (guestSelect && lockedGuestHidden) {
                guestSelect.disabled = true;
                guestSelect.style.background = '#f0f0f0';
                guestSelect.style.cursor = 'not-allowed';
                lockedGuestHidden.value = guestSelect.value;
            }
        })();

        let bookingSubmitInProgress = false;

        function showAvailabilityValidationMessage(message) {
            const validationBox = document.getElementById('availabilityValidationMessage');
            if (!validationBox) {
                return;
            }

            validationBox.style.display = 'block';
            validationBox.innerHTML = message;
        }

        function clearAvailabilityValidationMessage() {
            const validationBox = document.getElementById('availabilityValidationMessage');
            if (!validationBox) {
                return;
            }

            validationBox.style.display = 'none';
            validationBox.textContent = '';
        }

        async function validateRoomAvailabilityBeforeSubmit() {
            const checkIn = document.getElementById('checkInDate').value;
            const checkOut = document.getElementById('checkOutDate').value;
            const requestedRooms = Math.max(1, parseInt(document.getElementById('numberOfRooms').value || '1', 10));
            const requestedGuests = Math.max(1, parseInt(document.getElementById('guestCountSelect').value || '1', 10));
            const selectedRoomIds = getSelectedRoomIds().map((id) => Number(id)).filter((id) => id > 0);

            if (!checkIn || !checkOut) {
                showAvailabilityValidationMessage('Please select a valid check-in and check-out date before submitting.');
                return false;
            }

            if (selectedRoomIds.length !== requestedRooms) {
                showAvailabilityValidationMessage(`Only ${selectedRoomIds.length} room(s) are currently assignable, but ${requestedRooms} room(s) were requested. Please adjust your selection first.`);
                return false;
            }

            try {
                const params = new URLSearchParams({
                    check_in_date: checkIn,
                    check_out_date: checkOut,
                    number_of_rooms: String(requestedRooms),
                    number_of_guests: String(requestedGuests),
                });

                const response = await fetch(`{{ route('booking.availableRooms') }}?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                });

                if (!response.ok) {
                    throw new Error('Failed to validate room availability.');
                }

                const payload = await response.json();
                const latestAvailableIds = new Set((payload.rooms || []).map((roomData) => Number(roomData.id)));
                const noLongerAvailable = selectedRoomIds.filter((roomId) => !latestAvailableIds.has(Number(roomId)));

                if (noLongerAvailable.length > 0) {
                    const summary = document.getElementById('autoAssignedRoomsSummary');
                    if (summary) {
                        summary.innerHTML = '<span style="color:#b00020;">Some selected rooms are no longer available for the selected dates. Please reselect rooms before submitting.</span>';
                    }

                    showAvailabilityValidationMessage('One or more selected rooms are no longer available for the selected dates. Please review the room assignment and try again.');
                    return false;
                }

                clearAvailabilityValidationMessage();
                return true;
            } catch (error) {
                showAvailabilityValidationMessage('We could not confirm the latest room availability right now. Please wait a moment and try again.');
                return false;
            }
        }

        document.getElementById('bookingForm').addEventListener('submit', async function (event) {
            event.preventDefault();

            if (bookingSubmitInProgress) {
                return;
            }

            const requestedRooms = Math.max(1, parseInt(document.getElementById('numberOfRooms').value || '1', 10));
            const selected = getSelectedRoomIds();
            const termsAccepted = document.getElementById('termsAccepted');

            if (!termsAccepted || !termsAccepted.checked) {
                alert('Please agree to the Terms & Conditions and Privacy Policy before submitting your reservation.');
                return;
            }

            if (selected.length !== requestedRooms) {
                alert(`Only ${selected.length} room(s) can be assigned. Please adjust your room count or date range.`);
                return;
            }

            const checkInInput = document.getElementById('checkInDate');
            const checkOutInput = document.getElementById('checkOutDate');
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const checkInDate = checkInInput && checkInInput.value ? new Date(checkInInput.value + 'T00:00:00') : null;
            const checkOutDate = checkOutInput && checkOutInput.value ? new Date(checkOutInput.value + 'T00:00:00') : null;

            if (!checkInDate || checkInDate < today) {
                alert('Check-in date cannot be in the past.');
                return;
            }

            if (!checkOutDate || checkOutDate <= checkInDate) {
                alert('Check-out date must be at least 1 day after check-in.');
                return;
            }

            const guestSelect = document.getElementById('guestCountSelect');
            const lockedGuestHidden = document.getElementById('lockedGuestCountHidden');
            if (guestSelect && lockedGuestHidden) {
                lockedGuestHidden.value = guestSelect.value;
            }

            const submitBtn = document.getElementById('bookingSubmitBtn');
            if (submitBtn && submitBtn.disabled) {
                return;
            }

            const stillAvailable = await validateRoomAvailabilityBeforeSubmit();
            if (!stillAvailable) {
                return;
            }

            bookingSubmitInProgress = true;

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.7';
                submitBtn.style.cursor = 'not-allowed';
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }

            this.submit();
        });
    </script>
</body>
</html>
