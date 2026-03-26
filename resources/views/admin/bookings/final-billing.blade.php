@extends('layouts.admin')

@section('title', 'Billing Adjustment & Charges - Booking #' . $booking->booking_reference)
@section('page-title', 'Billing Adjustment & Charges')

@section('content')
<div style="max-width: 1100px; margin: 0 auto;">

    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-gold); margin: 0;">Billing Adjustment & Charges</h2>
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
    @php
        $reservedRooms = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
        $vatPercentage = \App\Models\AppSetting::getVatPercentage();
        $isMultiRoomBilling = $reservedRooms->count() > 1;
        $roomManualAdjustments = [];
        $roomAdditionalCharges = [];
        $roomAdditionalReasons = [];
        $roomDiscountAmounts = [];
        $roomDiscountTypes = [];
        $roomPwdSeniorCounts = [];
        $roomBaseTotals = [];
        $pivotManualAdjustmentTotal = 0.0;

        foreach ($reservedRooms as $idx => $reservedRoom) {
            $pivotAdjustment = (float) ($reservedRoom->pivot->manual_adjustment ?? 0);
            $roomManualAdjustments[$reservedRoom->id] = $pivotAdjustment;
            $pivotManualAdjustmentTotal += $pivotAdjustment;

            $roomAdditionalCharges[$reservedRoom->id] = (float) ($reservedRoom->pivot->additional_charge ?? 0);
            $roomAdditionalReasons[$reservedRoom->id] = $reservedRoom->pivot->additional_charge_reason ?? '';
            $roomDiscountAmounts[$reservedRoom->id] = (float) ($reservedRoom->pivot->discount_amount ?? 0);
            $roomDiscountTypes[$reservedRoom->id] = $reservedRoom->pivot->discount_type ?? 'none';

            $nightlyRate = (float) ($reservedRoom->pivot->nightly_rate ?? $reservedRoom->effective_price ?? optional($reservedRoom->roomType)->base_price ?? 0);
            $roomBaseTotals[$reservedRoom->id] = $nightlyRate * (int) ($booking->total_nights ?? $booking->number_of_nights ?? 0);

            $roomCapacity = max(1, (int) (optional($reservedRoom->roomType)->max_guests ?? 1));
            $roomPerPersonShare = $roomBaseTotals[$reservedRoom->id] / $roomCapacity;
            $existingDiscount = $roomDiscountAmounts[$reservedRoom->id];
            $derivedCount = ($roomPerPersonShare > 0 && in_array($roomDiscountTypes[$reservedRoom->id], ['pwd', 'senior'], true))
                ? (int) round($existingDiscount / ($roomPerPersonShare * 0.20))
                : 0;
            $roomPwdSeniorCounts[$reservedRoom->id] = max(0, min($derivedCount, $roomCapacity));
        }

        if ($reservedRooms->count() > 1 && abs($pivotManualAdjustmentTotal) < 0.00001 && abs((float) ($booking->manual_adjustment ?? 0)) > 0.00001) {
            $firstRoom = $reservedRooms->first();
            if ($firstRoom) {
                $roomManualAdjustments[$firstRoom->id] = (float) $booking->manual_adjustment;
            }
        }

        if ($isMultiRoomBilling) {
            $initialManualAdjustment = 0;
            foreach ($reservedRooms as $reservedRoom) {
                $roomId = $reservedRoom->id;
                $initialManualAdjustment += (float) ($roomAdditionalCharges[$roomId] ?? 0) - (float) ($roomDiscountAmounts[$roomId] ?? 0);
            }
            $initialOverallManualAdjustment = round((float) ($booking->manual_adjustment ?? 0) - $initialManualAdjustment, 2);
        } else {
            $initialManualAdjustment = (float) ($booking->manual_adjustment ?? 0);
            $initialOverallManualAdjustment = 0;
        }

        $billingAmenityCatalog = [
            ['key' => 'extra_bed', 'name' => 'Extra Bed', 'price' => 800],
            ['key' => 'extra_bedding', 'name' => 'Extra Bedding', 'price' => 300],
            ['key' => 'extra_towels', 'name' => 'Extra Towels', 'price' => 200],
            ['key' => 'soap_set', 'name' => 'Soap, Shampoo, Conditioner', 'price' => 50],
        ];

        $initialPerRoomAdditionalTotal = array_sum($roomAdditionalCharges);
        $initialPerRoomDiscountTotal = array_sum($roomDiscountAmounts);
    @endphp
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
                    <td style="padding: 7px 0; font-weight: 700;">{{ $booking->guest->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 7px 0; color: var(--text-muted);">Rooms</td>
                    <td style="padding: 7px 0; font-weight: 700;">{{ $reservedRooms->count() }} room(s)</td>
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

            <div style="margin-top: 1rem; border-top: 1px dashed var(--border-gray); padding-top: 0.8rem;">
                <div style="font-weight: 700; margin-bottom: 0.5rem; font-size: 0.88rem; color: #333;">Per-Room Charges</div>
                @foreach($reservedRooms as $reservedRoom)
                    @php
                        $nightlyRate = (float) ($reservedRoom->pivot->nightly_rate ?? $reservedRoom->effective_price ?? optional($reservedRoom->roomType)->base_price ?? 0);
                        $roomTotal = $nightlyRate * (int) ($booking->total_nights ?? $booking->number_of_nights ?? 0);
                    @endphp
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:6px 0;border-bottom:1px solid #f1f1f1;font-size:0.86rem;">
                        <span style="color:var(--text-muted);">Room {{ $reservedRoom->room_number }} - {{ optional($reservedRoom->roomType)->name ?? 'N/A' }}<br><small>₱{{ number_format($nightlyRate, 2) }} x {{ $booking->total_nights ?? $booking->number_of_nights }} night(s)</small></span>
                        <span style="font-weight:700;">₱{{ number_format($roomTotal, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </x-admin.card>

        <!-- Final Total Summary -->
        <x-admin.card title="Final Total">
            <div style="background: linear-gradient(135deg, #2c2c2c, #3a3a3a); border-radius: 10px; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <span style="color: #ccc; font-size: 1rem; font-weight: 600;">Balance Due:</span>
                <span style="font-size: 1.75rem; font-weight: 800; color: var(--primary-gold);" id="grandTotal">₱{{ number_format($balanceDue, 2) }}</span>
            </div>
            <div style="font-size: 0.9rem;">
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--text-muted);">Room Charges</span>
                    <span style="font-weight: 600;">₱{{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--text-muted);">VAT ({{ number_format($vatPercentage, 2) }}%) Included</span>
                    <span style="font-weight: 600;">₱{{ number_format((float) ($booking->tax_amount ?? 0), 2) }}</span>
                </div>
                @if($isMultiRoomBilling)
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--text-muted);">Per-Room Additional Charges</span>
                    <span style="font-weight: 600;" id="perRoomAdditionalDisplay">₱{{ number_format($initialPerRoomAdditionalTotal, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--success); font-weight: 600;">Per-Room Discounts</span>
                    <span style="font-weight: 700; color: var(--success);" id="perRoomDiscountDisplay">-₱{{ number_format($initialPerRoomDiscountTotal, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--text-muted);">Overall Total Adjustment</span>
                    <span style="font-weight: 600;" id="overallManualAdjustmentDisplay">₱{{ number_format($initialOverallManualAdjustment, 2) }}</span>
                </div>
                @endif
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
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--success); font-weight: 600;">Verified Payments</span>
                    <span style="font-weight: 700; color: var(--success);" id="verifiedPaymentsDisplay">-₱{{ number_format($verifiedPaymentsTotal, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px solid var(--border-gray);">
                    <span style="color: var(--text-muted);">Amenities & Services</span>
                    <span style="font-weight: 600;" id="servicesAdjustmentDisplay">₱0.00</span>
                </div>
                @if(!$isMultiRoomBilling)
                <div style="display: flex; justify-content: space-between; padding: 5px 0;">
                    <span style="color: var(--text-muted);">Manual Adjustment</span>
                    <span style="font-weight: 600;" id="manualAdjustmentDisplay">₱{{ number_format($initialManualAdjustment, 2) }}</span>
                </div>
                @endif
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
                            style="width: 38px; height: 38px; background: white; border: 2px solid var(--danger); color: var(--danger); border-radius: 8px; font-size: 1.4rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; line-height: 1;">
                            &minus;
                        </button>
                        <input type="number" name="early_checkin_hours" id="earlyCheckinHours"
                            value="{{ $booking->early_checkin_hours ?? 0 }}" min="0" max="5" readonly
                            style="width: 70px; text-align: center; padding: 0.5rem; border: 1px solid var(--border-gray); border-radius: 8px; font-weight: 700; font-size: 1rem;">
                        <button type="button" onclick="incrementCounter('earlyCheckin')"
                            style="width: 38px; height: 38px; background: white; border: 2px solid var(--success); color: var(--success); border-radius: 8px; font-size: 1.4rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; line-height: 1;">
                            &plus;
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
                            style="width: 38px; height: 38px; background: white; border: 2px solid var(--danger); color: var(--danger); border-radius: 8px; font-size: 1.4rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; line-height: 1;">
                            &minus;
                        </button>
                        <input type="number" name="late_checkout_hours" id="lateCheckoutHours"
                            value="{{ $booking->late_checkout_hours ?? 0 }}" min="0" max="5" readonly
                            style="width: 70px; text-align: center; padding: 0.5rem; border: 1px solid var(--border-gray); border-radius: 8px; font-weight: 700; font-size: 1rem;">
                        <button type="button" onclick="incrementCounter('lateCheckout')"
                            style="width: 38px; height: 38px; background: white; border: 2px solid var(--success); color: var(--success); border-radius: 8px; font-size: 1.4rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; line-height: 1;">
                            &plus;
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
                @if($reservedRooms->count() > 1)
                    <div style="background: #e8f5e9; border-left: 4px solid var(--success); border-radius: 6px; padding: 0.85rem 1rem; font-size: 0.85rem; color: #1b5e20;">
                        <i class="fas fa-check-circle"></i>&nbsp;
                        For multi-room bookings, PWD/Senior discount is applied per room under <strong>Manual Adjustment → Per-Room Billing</strong>.
                    </div>
                    <input type="hidden" name="pwd_senior_discount" id="pwdSeniorDiscountInput" value="0">
                @else
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
                @endif

            </x-admin.card>
        </div>

        <!-- Row: Manual Adjustment + Payment Method -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">

            <x-admin.card title="Manual Adjustment">
                <div style="margin-bottom: 1rem; border: 1px solid var(--border-gray); border-radius: 8px; padding: 0.9rem; background: #fbfbfb;">
                    <div style="font-weight: 700; font-size: 0.9rem; color: #333; margin-bottom: 0.7rem;">Additional Amenities & Services</div>
                    <div style="display: grid; grid-template-columns: repeat(2, minmax(180px, 1fr)); gap: 0.65rem;">
                        @foreach($billingAmenityCatalog as $billingAmenity)
                            <div style="border: 1px solid #e5e5e5; border-radius: 8px; padding: 0.55rem 0.7rem; background: white;">
                                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.55rem; margin-bottom: 0.35rem;">
                                    <label style="display: flex; align-items: center; gap: 0.45rem; margin: 0; cursor: pointer; font-size: 0.88rem; font-weight: 600; color: #2c2c2c;">
                                        <input
                                            type="checkbox"
                                            class="service-item-checkbox"
                                            data-service-key="{{ $billingAmenity['key'] }}"
                                            data-service-name="{{ $billingAmenity['name'] }}"
                                            data-service-price="{{ $billingAmenity['price'] }}"
                                            onchange="updateServicesAdjustment()"
                                            style="width: 14px; height: 14px; accent-color: var(--primary-gold);"
                                        >
                                        <span>{{ $billingAmenity['name'] }}</span>
                                    </label>
                                    <input
                                        type="number"
                                        class="service-item-qty"
                                        data-service-key="{{ $billingAmenity['key'] }}"
                                        min="1"
                                        max="50"
                                        value="1"
                                        disabled
                                        oninput="updateServicesAdjustment()"
                                        style="width: 54px; padding: 0.25rem 0.35rem; border: 1px solid var(--border-gray); border-radius: 6px; font-size: 0.82rem;"
                                    >
                                </div>
                                <div style="font-size: 0.8rem; color: #777;">PHP {{ number_format((float) $billingAmenity['price'], 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div style="margin-top: 0.7rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #ddd; padding-top: 0.6rem; font-size: 0.86rem;">
                        <span style="font-weight: 600; color: #555;">Services Total</span>
                        <span style="font-weight: 700; color: #2c2c2c;" id="servicesTotalPreview">₱0.00</span>
                    </div>
                    <input type="hidden" name="services_total_adjustment" id="servicesTotalAdjustmentInput" value="0">
                    <input type="hidden" name="services_breakdown" id="servicesBreakdownInput" value="">
                </div>

                <div style="margin-bottom: 1rem; padding: 0.8rem 0.9rem; border: 1px solid var(--border-gray); border-radius: 8px; background: #fafafa;">
                    <div style="font-weight: 700; font-size: 0.86rem; color: #333; margin-bottom: 0.45rem;">Additional Amenities & Services (Reference)</div>
                    <ul style="margin: 0; padding-left: 1rem; color: #555; font-size: 0.82rem; line-height: 1.5;">
                        <li>Extra Bed - ₱800 per night</li>
                        <li>Extra Bedding - ₱300 (includes 2 pillows, 1 bedding, 1 cover)</li>
                        <li>Soap, Shampoo, Conditioner - ₱50 per set</li>
                        <li>Towel - ₱200</li>
                        <li>Motorcycle Parking - ₱100 per night</li>
                        <li>Car Parking - ₱200 per night</li>
                    </ul>
                </div>

                @if($reservedRooms->count() > 1)
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.75rem;">Per-Room Billing</label>
                        <div style="display: flex; flex-direction: column; gap: 0.7rem;">
                            @foreach($reservedRooms as $reservedRoom)
                                @php
                                    $roomId = $reservedRoom->id;
                                    $roomBaseTotal = (float) ($roomBaseTotals[$roomId] ?? 0);
                                    $roomAdditional = (float) ($roomAdditionalCharges[$roomId] ?? 0);
                                    $roomDiscount = (float) ($roomDiscountAmounts[$roomId] ?? 0);
                                    $roomDisplayTotal = $roomBaseTotal + $roomAdditional - $roomDiscount;
                                @endphp
                                <div data-room-row="1" data-room-id="{{ $roomId }}" data-room-base-total="{{ $roomBaseTotal }}" data-room-capacity="{{ max(1, (int) (optional($reservedRoom->roomType)->max_guests ?? 1)) }}" style="border: 1px solid var(--border-gray); border-radius: 8px; padding: 0.65rem 0.75rem;">
                                    <div style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 0.45rem;">
                                        Room {{ $reservedRoom->room_number }} - {{ optional($reservedRoom->roomType)->name ?? 'N/A' }}
                                    </div>
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.6rem;margin-bottom:0.5rem;">
                                        <div>
                                            <label style="display:block;font-size:0.78rem;color:var(--text-muted);margin-bottom:0.3rem;">Additional Charge</label>
                                            <div style="display: flex; align-items: center; border: 1px solid var(--border-gray); border-radius: 8px; overflow: hidden; background: white;">
                                                <span style="padding: 0.55rem 0.75rem; background: var(--light-gray); color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-gray); font-size: 0.85rem;">₱</span>
                                                <input
                                                    type="number"
                                                    name="room_additional_charges[{{ $roomId }}]"
                                                    class="room-additional-charge"
                                                    value="{{ $roomAdditional }}"
                                                    step="0.01"
                                                    min="0"
                                                    style="flex: 1; border: none; outline: none; padding: 0.55rem 0.75rem; font-size: 0.88rem;"
                                                    oninput="calculateTotal()"
                                                >
                                            </div>
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:0.78rem;color:var(--text-muted);margin-bottom:0.3rem;">Discount Amount</label>
                                            <div style="display: flex; align-items: center; border: 1px solid var(--border-gray); border-radius: 8px; overflow: hidden; background: white;">
                                                <span style="padding: 0.55rem 0.75rem; background: var(--light-gray); color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-gray); font-size: 0.85rem;">₱</span>
                                                <input
                                                    type="number"
                                                    name="room_discount_amounts[{{ $roomId }}]"
                                                    class="room-discount-amount"
                                                    value="{{ $roomDiscount }}"
                                                    step="0.01"
                                                    min="0"
                                                    style="flex: 1; border: none; outline: none; padding: 0.55rem 0.75rem; font-size: 0.88rem;"
                                                    oninput="calculateTotal()"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.6rem;">
                                        <div>
                                            <label style="display:block;font-size:0.78rem;color:var(--text-muted);margin-bottom:0.3rem;">Additional Charge Notes</label>
                                            <input
                                                type="text"
                                                name="room_additional_reasons[{{ $roomId }}]"
                                                value="{{ $roomAdditionalReasons[$roomId] ?? '' }}"
                                                maxlength="255"
                                                placeholder="e.g. Extra bedding"
                                                style="width: 100%; border: 1px solid var(--border-gray); border-radius: 8px; outline: none; padding: 0.55rem 0.65rem; font-size: 0.83rem; box-sizing:border-box;"
                                            >
                                        </div>
                                        <div>
                                            <label style="display:block;font-size:0.78rem;color:var(--text-muted);margin-bottom:0.3rem;">Discount Type</label>
                                            <select
                                                name="room_discount_types[{{ $roomId }}]"
                                                class="room-discount-type"
                                                onchange="calculateTotal()"
                                                style="width: 100%; border: 1px solid var(--border-gray); border-radius: 8px; outline: none; padding: 0.55rem 0.65rem; font-size: 0.83rem; box-sizing:border-box;"
                                            >
                                                <option value="none" {{ ($roomDiscountTypes[$roomId] ?? 'none') === 'none' ? 'selected' : '' }}>No Discount</option>
                                                <option value="pwd" {{ ($roomDiscountTypes[$roomId] ?? '') === 'pwd' ? 'selected' : '' }}>PWD</option>
                                                <option value="senior" {{ ($roomDiscountTypes[$roomId] ?? '') === 'senior' ? 'selected' : '' }}>Senior</option>
                                                <option value="other" {{ ($roomDiscountTypes[$roomId] ?? '') === 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="room-pwd-count-section" style="margin-top:0.55rem; display: none;">
                                        <label style="display:block;font-size:0.78rem;color:var(--text-muted);margin-bottom:0.3rem;">PWD/Senior Guest Count</label>
                                        <select
                                            name="room_pwd_senior_counts[{{ $roomId }}]"
                                            class="room-pwd-senior-count"
                                            onchange="calculateTotal()"
                                            style="width: 100%; border: 1px solid var(--border-gray); border-radius: 8px; outline: none; padding: 0.55rem 0.65rem; font-size: 0.83rem; box-sizing:border-box;"
                                        >
                                            @for($count = 0; $count <= max(1, (int) (optional($reservedRoom->roomType)->max_guests ?? 1)); $count++)
                                                <option value="{{ $count }}" {{ ($roomPwdSeniorCounts[$roomId] ?? 0) === $count ? 'selected' : '' }}>
                                                    {{ $count === 0 ? 'Select count' : ($count . ' ' . ($count === 1 ? 'Person' : 'People')) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div style="margin-top:0.5rem;display:flex;justify-content:space-between;align-items:center;background:#f8f8f8;border-radius:6px;padding:0.4rem 0.55rem;font-size:0.82rem;">
                                        <span style="color:var(--text-muted);">Room Total</span>
                                        <span style="font-weight:700;color:#2c2c2c;" id="roomNetTotalDisplay_{{ $roomId }}">₱{{ number_format($roomDisplayTotal, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <input type="hidden" name="manual_adjustment" id="manualAdjustment" value="{{ $initialManualAdjustment }}">
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.55rem;">Each room has independent additional charges and discounts. Grand total is automatically combined at the bottom.</p>
                    </div>
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem;">Overall Total Adjustment (All Rooms)</label>
                        <div style="display: flex; align-items: center; border: 1px solid var(--border-gray); border-radius: 8px; overflow: hidden;">
                            <span style="padding: 0.65rem 0.85rem; background: var(--light-gray); color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-gray); font-size: 0.9rem;">₱</span>
                            <input type="number" name="overall_manual_adjustment" id="overallManualAdjustment"
                                value="{{ $initialOverallManualAdjustment }}" step="0.01"
                                style="flex: 1; border: none; outline: none; padding: 0.65rem 0.85rem; font-size: 0.9rem;"
                                onchange="calculateTotal()">
                        </div>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.4rem;">Use this for whole-booking adjustments (e.g., manual down payment recorded outside system).</p>
                    </div>
                @else
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem;">Adjustment Amount</label>
                        <div style="display: flex; align-items: center; border: 1px solid var(--border-gray); border-radius: 8px; overflow: hidden;">
                            <span style="padding: 0.65rem 0.85rem; background: var(--light-gray); color: var(--text-muted); font-weight: 600; border-right: 1px solid var(--border-gray); font-size: 0.9rem;">₱</span>
                            <input type="number" name="manual_adjustment" id="manualAdjustment"
                                value="{{ $initialManualAdjustment }}" step="0.01"
                                style="flex: 1; border: none; outline: none; padding: 0.65rem 0.85rem; font-size: 0.9rem;"
                                onchange="calculateTotal()">
                        </div>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.4rem;">Positive for additional charges, negative for discounts</p>
                    </div>
                    <input type="hidden" name="overall_manual_adjustment" id="overallManualAdjustment" value="0">
                @endif
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
                    <img src="{{ asset('images/gcash/gcash_v2.jpg') }}" alt="GCash QR Code" style="max-width: 200px; border: 2px solid #007bff; border-radius: 8px; margin-bottom: 0.75rem;">
                    <p style="font-size: 0.85rem; color: #555; margin: 0.2rem 0;"><strong>GCash Number:</strong> 09778325550</p>
                    <p style="font-size: 0.85rem; color: #555; margin: 0.2rem 0;"><strong>Account Name:</strong> MICHAEL ANG</p>
                </div>

                <div id="gcashReferenceSection" style="display: none; margin-top: 0.8rem;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; color: #444;">GCash Reference Number</label>
                    <input type="text" name="payment_reference" id="billingPaymentReference" value="{{ old('payment_reference') }}" placeholder="Enter customer GCash reference" style="width: 100%; padding: 0.65rem 0.85rem; border: 1px solid var(--border-gray); border-radius: 8px; font-size: 0.9rem;">
                    <small style="display:block; margin-top:0.35rem; color:#777; font-size:0.78rem;">Saved to booking adjustment notes for record keeping.</small>
                </div>
            </x-admin.card>
        </div>

        <!-- Save Button -->
        <div style="text-align: center; padding: 1.5rem; background: white; border: 2px solid var(--primary-gold); border-radius: 12px;">
            <button type="submit"
                style="padding: 1rem 3.5rem; background: linear-gradient(135deg, #4caf50, #3d9140); color: white; border: none; border-radius: 10px; font-size: 1.05rem; font-weight: 700; cursor: pointer;">
                <i class="fas fa-save"></i>&nbsp; Save Billing Adjustment & Charges
            </button>
            <div style="margin-top: 0.75rem; font-size: 0.85rem; color: var(--text-muted);">All changes will be saved to the booking record</div>
        </div>

    </form>
</div>

<script>
const hourlyRate = {{ $hourlyRate }};
const roomTotal = {{ $booking->total_amount }};
const numberOfGuests = {{ $booking->number_of_guests }};
const verifiedPaymentsTotal = {{ $verifiedPaymentsTotal }};

function calculatePerRoomNetAdjustmentTotal() {
    const roomRows = document.querySelectorAll('[data-room-row="1"]');

    if (roomRows.length === 0) {
        return null;
    }

    let additionalTotal = 0;
    let discountTotal = 0;
    let netTotal = 0;

    roomRows.forEach(row => {
        const roomId = row.dataset.roomId;
        const roomBase = parseFloat(row.dataset.roomBaseTotal) || 0;
        const additionalInput = row.querySelector('.room-additional-charge');
        const discountInput = row.querySelector('.room-discount-amount');
        const discountTypeInput = row.querySelector('.room-discount-type');

        const additional = parseFloat(additionalInput?.value) || 0;
        const discountType = discountTypeInput?.value || 'none';
        const syncedDiscount = syncPerRoomDiscountInput(row, discountType);
        const discount = syncedDiscount !== null ? syncedDiscount : (parseFloat(discountInput?.value) || 0);

        const roomTotalValue = roomBase + additional - discount;
        const roomDisplay = document.getElementById('roomNetTotalDisplay_' + roomId);
        if (roomDisplay) {
            roomDisplay.textContent = '₱' + roomTotalValue.toLocaleString('en-PH', {minimumFractionDigits: 2});
        }

        additionalTotal += additional;
        discountTotal += discount;
        netTotal += (additional - discount);
    });

    const additionalDisplay = document.getElementById('perRoomAdditionalDisplay');
    if (additionalDisplay) {
        additionalDisplay.textContent = '₱' + additionalTotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
    }

    const discountDisplay = document.getElementById('perRoomDiscountDisplay');
    if (discountDisplay) {
        discountDisplay.textContent = '-₱' + discountTotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
    }

    const aggregateInput = document.getElementById('manualAdjustment');
    if (aggregateInput) {
        aggregateInput.value = netTotal.toFixed(2);
    }

    return netTotal;
}

function getManualAdjustmentTotal() {
    const perRoomNet = calculatePerRoomNetAdjustmentTotal();
    if (perRoomNet !== null) {
        return perRoomNet;
    }

    const roomAdjustmentInputs = document.querySelectorAll('.room-manual-adjustment');

    if (roomAdjustmentInputs.length > 0) {
        let sum = 0;
        roomAdjustmentInputs.forEach(input => {
            sum += parseFloat(input.value) || 0;
        });

        const aggregateInput = document.getElementById('manualAdjustment');
        if (aggregateInput) {
            aggregateInput.value = sum.toFixed(2);
        }

        return sum;
    }

    return parseFloat(document.getElementById('manualAdjustment').value) || 0;
}

function updateServicesAdjustment() {
    const serviceCheckboxes = document.querySelectorAll('.service-item-checkbox');
    const selectedBreakdown = [];
    let servicesTotal = 0;

    serviceCheckboxes.forEach((checkbox) => {
        const serviceKey = checkbox.dataset.serviceKey;
        const qtyInput = document.querySelector('.service-item-qty[data-service-key="' + serviceKey + '"]');
        const servicePrice = parseFloat(checkbox.dataset.servicePrice || '0') || 0;
        const serviceName = checkbox.dataset.serviceName || 'Service';

        if (!qtyInput) {
            return;
        }

        qtyInput.disabled = !checkbox.checked;
        if (!checkbox.checked) {
            qtyInput.value = '1';
            return;
        }

        const qty = Math.max(1, Math.min(50, parseInt(qtyInput.value || '1', 10) || 1));
        qtyInput.value = String(qty);
        const lineTotal = servicePrice * qty;
        servicesTotal += lineTotal;
        selectedBreakdown.push(serviceName + ' x' + qty + ' (₱' + lineTotal.toLocaleString('en-PH', {minimumFractionDigits: 2}) + ')');
    });

    const servicesTotalPreview = document.getElementById('servicesTotalPreview');
    if (servicesTotalPreview) {
        servicesTotalPreview.textContent = '₱' + servicesTotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
    }

    const servicesAdjustmentDisplay = document.getElementById('servicesAdjustmentDisplay');
    if (servicesAdjustmentDisplay) {
        servicesAdjustmentDisplay.textContent = '₱' + servicesTotal.toLocaleString('en-PH', {minimumFractionDigits: 2});
    }

    const servicesTotalAdjustmentInput = document.getElementById('servicesTotalAdjustmentInput');
    if (servicesTotalAdjustmentInput) {
        servicesTotalAdjustmentInput.value = servicesTotal.toFixed(2);
    }

    const servicesBreakdownInput = document.getElementById('servicesBreakdownInput');
    if (servicesBreakdownInput) {
        servicesBreakdownInput.value = selectedBreakdown.join(', ');
    }

    calculateTotal();
}

function syncPerRoomDiscountInput(row, discountType = null) {
    const roomBase = parseFloat(row.dataset.roomBaseTotal) || 0;
    const roomCapacity = Math.max(parseInt(row.dataset.roomCapacity || '1', 10), 1);
    const discountInput = row.querySelector('.room-discount-amount');
    const pwdCountSection = row.querySelector('.room-pwd-count-section');
    const pwdCountSelect = row.querySelector('.room-pwd-senior-count');
    const resolvedType = discountType || row.querySelector('.room-discount-type')?.value || 'none';

    if (!discountInput) {
        return null;
    }

    if (resolvedType === 'pwd' || resolvedType === 'senior') {
        if (pwdCountSection) {
            pwdCountSection.style.display = 'block';
        }

        const selectedCountRaw = pwdCountSelect ? parseInt(pwdCountSelect.value || '0', 10) : 0;
        const selectedCount = Math.max(0, Math.min(isNaN(selectedCountRaw) ? 0 : selectedCountRaw, roomCapacity));
        const perPersonShare = roomBase / roomCapacity;
        const autoDiscount = selectedCount > 0 ? (perPersonShare * 0.20 * selectedCount) : 0;

        if (pwdCountSelect && String(selectedCount) !== pwdCountSelect.value) {
            pwdCountSelect.value = String(selectedCount);
        }

        discountInput.value = autoDiscount.toFixed(2);
        discountInput.readOnly = true;
        discountInput.style.background = '#f3f4f6';
        discountInput.style.cursor = 'not-allowed';
        return autoDiscount;
    }

    if (pwdCountSection) {
        pwdCountSection.style.display = 'none';
    }
    if (pwdCountSelect) {
        pwdCountSelect.value = '0';
    }

    if (resolvedType === 'none') {
        discountInput.value = '0';
        discountInput.readOnly = true;
        discountInput.style.background = '#f3f4f6';
        discountInput.style.cursor = 'not-allowed';
        return 0;
    }

    discountInput.readOnly = false;
    discountInput.style.background = 'white';
    discountInput.style.cursor = 'text';
    return parseFloat(discountInput.value) || 0;
}

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
    if (document.getElementById('hasPwdSenior') && document.getElementById('hasPwdSenior').checked) {
        calculatePwdDiscount();
    }
}

function togglePwdSenior() {
    const checkbox = document.getElementById('hasPwdSenior');
    const section = document.getElementById('pwdSeniorSection');

    if (!checkbox || !section) {
        return;
    }

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
    const pwdDiscountInput = document.getElementById('pwdSeniorDiscountInput');
    const pwdDiscountAmount = document.getElementById('pwdDiscountAmount');
    const pwdDiscountDisplay = document.getElementById('pwdDiscountDisplay');
    const pwdSeniorCount = document.getElementById('pwdSeniorCount');

    if (!checkbox || !pwdDiscountInput || !pwdDiscountAmount || !pwdDiscountDisplay || !pwdSeniorCount) {
        calculateTotal();
        return;
    }
    if (!checkbox.checked) return;

    const count = parseInt(pwdSeniorCount.value);
    if (count === 0) {
        pwdDiscountInput.value = 0;
        pwdDiscountAmount.textContent = '-₱0.00';
        pwdDiscountDisplay.textContent = '-₱0.00';
        calculateTotal();
        return;
    }

    const earlyCheckinAmt = parseFloat(document.getElementById('earlyCheckinChargeInput').value) || 0;
    const lateCheckoutAmt = parseFloat(document.getElementById('lateCheckoutChargeInput').value) || 0;
    const individualShare = (roomTotal + earlyCheckinAmt + lateCheckoutAmt) / numberOfGuests;
    const discount = (individualShare * 0.20) * count;

    pwdDiscountInput.value = discount.toFixed(2);
    pwdDiscountAmount.textContent = '-₱' + discount.toLocaleString('en-PH', {minimumFractionDigits: 2});
    pwdDiscountDisplay.textContent = '-₱' + discount.toLocaleString('en-PH', {minimumFractionDigits: 2});

    calculateTotal();
}

function calculateTotal() {
    const hasPerRoomBilling = document.querySelectorAll('[data-room-row="1"]').length > 0;
    const perRoomNetAdjustment = hasPerRoomBilling ? (calculatePerRoomNetAdjustmentTotal() || 0) : null;
    const earlyCheckin = parseFloat(document.getElementById('earlyCheckinChargeInput').value) || 0;
    const lateCheckout = parseFloat(document.getElementById('lateCheckoutChargeInput').value) || 0;
    const pwdDiscountInput = document.getElementById('pwdSeniorDiscountInput');
    const pwdDiscount  = pwdDiscountInput ? (parseFloat(pwdDiscountInput.value) || 0) : 0;
    const overallManualAdjust = parseFloat(document.getElementById('overallManualAdjustment')?.value || '0') || 0;
    const servicesAdjustment = parseFloat(document.getElementById('servicesTotalAdjustmentInput')?.value || '0') || 0;
    const manualAdjust = hasPerRoomBilling
        ? (perRoomNetAdjustment + overallManualAdjust + servicesAdjustment)
        : (getManualAdjustmentTotal() + servicesAdjustment);

    const grossTotal = roomTotal + earlyCheckin + lateCheckout - pwdDiscount + manualAdjust;
    const balanceDue = Math.max(grossTotal - verifiedPaymentsTotal, 0);

    document.getElementById('grandTotal').textContent = '₱' + balanceDue.toLocaleString('en-PH', {minimumFractionDigits: 2});
    const manualAdjustmentDisplay = document.getElementById('manualAdjustmentDisplay');
    if (manualAdjustmentDisplay) {
        manualAdjustmentDisplay.textContent = '₱' + manualAdjust.toLocaleString('en-PH', {minimumFractionDigits: 2});
    }

    const overallManualAdjustmentDisplay = document.getElementById('overallManualAdjustmentDisplay');
    if (overallManualAdjustmentDisplay) {
        overallManualAdjustmentDisplay.textContent = '₱' + overallManualAdjust.toLocaleString('en-PH', {minimumFractionDigits: 2});
    }
}

function toggleGcashQR() {
    const gcashRadio = document.getElementById('paymentGcash');
    const showGcash = gcashRadio.checked;
    document.getElementById('gcashQRSection').style.display = showGcash ? 'block' : 'none';
    document.getElementById('gcashReferenceSection').style.display = showGcash ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.room-additional-charge, .room-discount-amount, .room-discount-type').forEach(input => {
        input.addEventListener('change', calculateTotal);
        input.addEventListener('input', calculateTotal);
    });

    document.querySelectorAll('[data-room-row="1"]').forEach(row => {
        const typeInput = row.querySelector('.room-discount-type');
        syncPerRoomDiscountInput(row, typeInput ? typeInput.value : 'none');
    });

    document.querySelectorAll('.room-manual-adjustment').forEach(input => {
        input.addEventListener('change', calculateTotal);
    });

    document.querySelectorAll('.service-item-checkbox, .service-item-qty').forEach(input => {
        input.addEventListener('change', updateServicesAdjustment);
        input.addEventListener('input', updateServicesAdjustment);
    });

    updateServicesAdjustment();
    calculateTotal();
});
</script>

@endsection
