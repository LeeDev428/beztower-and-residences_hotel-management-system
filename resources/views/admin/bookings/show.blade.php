@extends('layouts.admin')

@section('title', 'Booking Details')
@section('page-title', 'Booking Details')

@section('content')
@php
    $vatPercentage = \App\Models\AppSetting::getVatPercentage();
    $guestIdPhoto = trim((string) (optional($booking->guest)->id_photo ?? ''));
    $guestIdPhotoIsPdf = $guestIdPhoto !== '' && str_ends_with(strtolower($guestIdPhoto), '.pdf');
    $computedGrossTotal = (float) ($grossTotal ?? ($booking->total_amount ?? 0));
    $computedRoomTotal = (float) ($booking->total_amount ?? 0);
    $billingAdjustmentDelta = round($computedGrossTotal - $computedRoomTotal, 2);
@endphp

<div style="margin-bottom: 1.5rem;">
    <x-admin.button type="outline" href="{{ route('admin.bookings.index') }}">← Back to Bookings</x-admin.button>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <div>
        <x-admin.card title="Booking Information">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted);">Reference Number</div>
                    <div style="font-weight: 700;">{{ $booking->booking_reference }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted);">Booking Status</div>
                    <x-admin.badge :status="$booking->status" />
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted);">Check-in</div>
                    <div style="font-weight: 600;">{{ optional($booking->check_in_date)->format('F d, Y') ?? 'N/A' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted);">Check-out</div>
                    <div style="font-weight: 600;">{{ optional($booking->check_out_date)->format('F d, Y') ?? 'N/A' }}</div>
                </div>
            </div>

            <div style="margin-top: 1rem;">
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Assigned Room(s)</div>
                @if($booking->rooms->isNotEmpty())
                    <div style="font-weight: 600;">
                        @foreach($booking->rooms as $reservedRoom)
                            <div>Room {{ $reservedRoom->room_number }} - {{ optional($reservedRoom->roomType)->name ?? 'N/A' }}</div>
                        @endforeach
                    </div>
                @else
                    <div style="font-weight: 600;">No assigned rooms yet</div>
                @endif
            </div>

            @if($booking->special_requests)
                <div style="margin-top: 1rem;">
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Special Requests</div>
                    <div style="padding: 0.75rem; border-radius: 8px; background: var(--light-gray);">{{ $booking->special_requests }}</div>
                </div>
            @endif
        </x-admin.card>

        <x-admin.card title="Guest Information" style="margin-top: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted);">Name</div>
                    <div style="font-weight: 600;">{{ optional($booking->guest)->name ?? 'Guest not available' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted);">Email</div>
                    <div style="font-weight: 600;">{{ optional($booking->guest)->email ?? 'No email' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted);">Phone</div>
                    <div style="font-weight: 600;">{{ optional($booking->guest)->phone ?? 'No phone' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted);">Address</div>
                    <div style="font-weight: 600;">{{ optional($booking->guest)->address ?? 'No address' }}</div>
                </div>
            </div>

            @if($guestIdPhoto !== '')
                <div style="margin-top: 1rem;">
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Government ID</div>
                    @if($guestIdPhotoIsPdf)
                        <a href="{{ asset('storage/' . $guestIdPhoto) }}" target="_blank" rel="noopener" style="color: #0d6efd; font-weight: 600;">View Uploaded ID (PDF)</a>
                    @else
                        <img src="{{ asset('storage/' . $guestIdPhoto) }}" alt="Guest ID" style="max-width: 280px; border-radius: 8px; border: 1px solid var(--border-gray);">
                    @endif
                </div>
            @endif
        </x-admin.card>
    </div>

    <div>
        <x-admin.card title="Payment">
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Room Charges</span>
                    <span style="font-weight: 600;">P{{ number_format($computedRoomTotal, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #666;">
                    <span>VAT ({{ number_format($vatPercentage, 2) }}%) Included</span>
                    <span style="font-weight: 600;">P{{ number_format((float) ($booking->tax_amount ?? 0), 2) }}</span>
                </div>
                @if(abs($billingAdjustmentDelta) > 0.00001)
                    <div style="display: flex; justify-content: space-between;">
                        <span>Billing Adjustment</span>
                        <span style="font-weight: 600; color: {{ $billingAdjustmentDelta < 0 ? '#2e7d32' : '#c62828' }};">{{ $billingAdjustmentDelta < 0 ? '-P' : '+P' }}{{ number_format(abs($billingAdjustmentDelta), 2) }}</span>
                    </div>
                @endif
                <div style="display: flex; justify-content: space-between;">
                    <span>Gross Total</span>
                    <span style="font-weight: 700;">P{{ number_format($computedGrossTotal, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #2e7d32;">
                    <span>Verified Payments</span>
                    <span style="font-weight: 700;">-P{{ number_format((float) ($verifiedPaymentsTotal ?? 0), 2) }}</span>
                </div>
                <div style="border-top: 1px solid var(--border-gray); padding-top: 0.75rem; display: flex; justify-content: space-between;">
                    <span style="font-weight: 700;">Balance Due</span>
                    <span style="font-weight: 700; color: var(--primary-gold);">P{{ number_format((float) ($balanceDue ?? 0), 2) }}</span>
                </div>
            </div>

            @if(($balanceDue ?? 0) > 0)
                <form method="POST" action="{{ route('admin.bookings.settleBalance', $booking) }}" style="margin-top: 1rem;">
                    @csrf
                    <button type="submit" style="width: 100%; padding: 0.7rem; border: none; border-radius: 8px; background: #4caf50; color: white; font-weight: 700; cursor: pointer;">
                        Settle Balance
                    </button>
                </form>
            @endif
        </x-admin.card>

        <x-admin.card title="Actions" style="margin-top: 1.5rem;">
            @php
                $baseRescheduleDate = \Carbon\Carbon::parse($booking->original_check_in_date ?? $booking->check_in_date);
                $rescheduleMinDate = $baseRescheduleDate->copy()->addDay()->toDateString();
                $rescheduleMaxDate = $baseRescheduleDate->copy()->addDays(14)->toDateString();
                $rescheduleNights = max(1, (int) ($booking->total_nights ?? optional($booking->check_in_date)->diffInDays($booking->check_out_date) ?? 1));
                $rescheduleLocked = in_array($booking->status, ['checked_in', 'checked_out'], true);
                $hasVerifiedPayment = $booking->payments->whereIn('payment_status', ['verified', 'completed'])->isNotEmpty();
                $canOpenReschedule = !$rescheduleLocked && $hasVerifiedPayment;
            @endphp

            <div style="margin-bottom: 1rem;">
                @if($billingLocked)
                    <button type="button" disabled style="width: 100%; padding: 0.75rem; border: none; border-radius: 8px; background: #d9d9d9; color: #6b6b6b; font-weight: 600; cursor: not-allowed;">
                        Billing Adjustment Locked
                    </button>
                @else
                    <a href="{{ route('admin.bookings.finalBilling', $booking) }}" style="display: block; text-align: center; text-decoration: none; width: 100%; padding: 0.75rem; border-radius: 8px; background: #4caf50; color: #fff; font-weight: 600;">
                        Billing Adjustment & Charges
                    </a>
                @endif
            </div>

            <div style="margin-bottom: 1rem;">
                @if($canOpenReschedule)
                    <button type="button" onclick="showRescheduleModal()" style="width: 100%; padding: 0.75rem; border: none; border-radius: 8px; background: #ffc107; color: #2c2c2c; font-weight: 600; cursor: pointer;">
                        Change Booking Date
                    </button>
                @else
                    <button type="button" disabled style="width: 100%; padding: 0.75rem; border: none; border-radius: 8px; background: #d9d9d9; color: #6b6b6b; font-weight: 600; cursor: not-allowed;">
                        Change Booking Date
                    </button>
                    @if($rescheduleLocked)
                        <small style="display:block; margin-top:0.5rem; color:#777;">Rescheduling is not allowed after check-in/check-out.</small>
                    @elseif(!$hasVerifiedPayment)
                        <small style="display:block; margin-top:0.5rem; color:#777;">Rescheduling requires verified payment.</small>
                    @endif
                @endif
            </div>

            <form method="POST" action="{{ route('admin.bookings.rescheduleRequest', $booking) }}" style="margin-bottom: 1rem;" onsubmit="return confirm('Are you sure you want to proceed?');">
                @csrf
                <x-admin.button type="outline">Send Reschedule Request Email</x-admin.button>
            </form>

            <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}">
                @csrf
                @method('PUT')
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Update Status</label>
                <select name="status" {{ $statusLocked ? 'disabled' : '' }} style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px; margin-bottom: 1rem;">
                    <option value="{{ $booking->status }}" selected>{{ ucwords(str_replace('_', ' ', $booking->status)) }} (Current)</option>
                    @foreach($allowedStatuses as $nextStatus)
                        <option value="{{ $nextStatus }}">{{ ucwords(str_replace('_', ' ', $nextStatus)) }}</option>
                    @endforeach
                </select>

                <div id="statusCancelReasonWrap" style="display:none; margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Cancellation Reason</label>
                    <textarea name="cancellation_reason" rows="3" maxlength="500" placeholder="Enter reason for cancelling this booking" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;"></textarea>
                </div>

                @if($statusLocked)
                    <button type="button" disabled style="width: 100%; padding: 0.75rem; border: none; border-radius: 8px; background: #d9d9d9; color: #6b6b6b; font-weight: 600; cursor: not-allowed;">
                        Update Status
                    </button>
                @else
                    <x-admin.button type="primary">Update Status</x-admin.button>
                @endif
            </form>
        </x-admin.card>
    </div>
</div>

<div id="rescheduleModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: relative; max-width: 500px; margin: 5% auto; background: white; padding: 2rem; border-radius: 12px;">
        <h3 style="margin-bottom: 1.5rem;">Reschedule Booking</h3>
        <form method="POST" action="{{ route('admin.bookings.reschedule', $booking) }}">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">New Check-in Date</label>
                <input type="date" name="new_check_in_date" required class="form-control" min="{{ $rescheduleMinDate }}" max="{{ $rescheduleMaxDate }}" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">New Check-out Date (Auto Computed)</label>
                <input type="text" id="rescheduleCheckoutPreview" class="form-control" value="Select a new check-in date" readonly style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #555;">
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="hideRescheduleModal()" style="flex: 1; padding: 0.75rem; background: #6c757d; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button type="submit" style="flex: 1; padding: 0.75rem; background: #ffc107; color: #2c2c2c; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Reschedule</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRescheduleModal() {
    document.getElementById('rescheduleModal').style.display = 'block';
}

function hideRescheduleModal() {
    document.getElementById('rescheduleModal').style.display = 'none';
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
        hideRescheduleModal();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const rescheduleCheckInInput = document.querySelector('input[name="new_check_in_date"]');
    const rescheduleCheckoutPreview = document.getElementById('rescheduleCheckoutPreview');
    const rescheduleNights = {{ $rescheduleNights ?? 1 }};
    const statusSelect = document.querySelector('form[action="{{ route('admin.bookings.updateStatus', $booking) }}"] select[name="status"]');
    const statusCancelReasonWrap = document.getElementById('statusCancelReasonWrap');

    function refreshRescheduleCheckoutPreview() {
        if (!rescheduleCheckInInput || !rescheduleCheckoutPreview) {
            return;
        }

        if (!rescheduleCheckInInput.value) {
            rescheduleCheckoutPreview.value = 'Select a new check-in date';
            return;
        }

        const checkInDate = new Date(rescheduleCheckInInput.value + 'T00:00:00');
        if (isNaN(checkInDate.getTime())) {
            rescheduleCheckoutPreview.value = 'Invalid check-in date';
            return;
        }

        checkInDate.setDate(checkInDate.getDate() + rescheduleNights);
        const yyyy = checkInDate.getFullYear();
        const mm = String(checkInDate.getMonth() + 1).padStart(2, '0');
        const dd = String(checkInDate.getDate()).padStart(2, '0');
        rescheduleCheckoutPreview.value = `${yyyy}-${mm}-${dd}`;
    }

    function toggleCancellationReasonField() {
        if (!statusSelect || !statusCancelReasonWrap) {
            return;
        }

        statusCancelReasonWrap.style.display = statusSelect.value === 'cancelled' ? 'block' : 'none';
    }

    if (rescheduleCheckInInput) {
        rescheduleCheckInInput.addEventListener('change', refreshRescheduleCheckoutPreview);
        refreshRescheduleCheckoutPreview();
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', toggleCancellationReasonField);
        toggleCancellationReasonField();
    }
});
</script>
@endsection
