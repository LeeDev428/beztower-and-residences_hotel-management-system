@extends('layouts.admin')

@section('title', 'Booking Details')
@section('page-title', 'Booking Details')

@section('content')
@php($vatPercentage = \App\Models\AppSetting::getVatPercentage())
<div style="margin-bottom: 1.5rem;">
    <x-admin.button type="outline" href="{{ route('admin.bookings.index') }}">← Back to Bookings</x-admin.button>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Main Details -->
    <div>
        <x-admin.card title="Booking Information">
            <div style="display: grid; gap: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Reference Number</div>
                        <div style="font-weight: 700; font-size: 1.125rem;">{{ $booking->booking_reference }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Booking Status</div>
                        <x-admin.badge :status="$booking->status" />
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Check-in</div>
                        <div style="font-weight: 600;">{{ optional($booking->check_in_date)->format('F d, Y') ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Check-out</div>
                        <div style="font-weight: 600;">{{ optional($booking->check_out_date)->format('F d, Y') ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Room</div>
                        @if($booking->rooms->isNotEmpty())
                            <div style="font-weight: 600;">{{ $booking->rooms->count() }} room(s)</div>
                            <div style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.35rem;">
                                @foreach($booking->rooms as $reservedRoom)
                                    Room {{ $reservedRoom->room_number }} - {{ optional($reservedRoom->roomType)->name ?? 'N/A' }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                        @else
                            <div style="font-weight: 600;">{{ optional($booking->room)->room_number ?? 'Unassigned' }} - {{ optional($booking->roomType)->name ?? 'N/A' }}</div>
                        @endif
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Number of Guests</div>
                        <div style="font-weight: 600;">{{ $booking->number_of_guests }}</div>
                    </div>
                </div>

                <!-- Special Requests -->
                @if($booking->special_requests)
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Special Requests</div>
                    <div style="padding: 1rem; background: var(--light-gray); border-radius: 8px;">
                        {{ $booking->special_requests }}
                    </div>
                </div>
                @endif
            </div>
        </x-admin.card>

        <!-- Room Assignment -->
        @if(!in_array($booking->status, ['checked_out', 'cancelled', 'rejected_payment']))
        <x-admin.card title="Room Assignment / Transfer" style="margin-top: 1.5rem;">
            <div style="margin-bottom: 1rem;">
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Currently Assigned Room(s)</div>
                @if($booking->rooms->isNotEmpty())
                            <div style="font-weight: 600; font-size: 0.9rem; color: #333;">
                        @foreach($booking->rooms as $assignedRoom)
                            <div style="padding: 0.25rem 0;">Room {{ $assignedRoom->room_number }} — {{ optional($assignedRoom->roomType)->name ?? 'N/A' }}</div>
                        @endforeach
                    </div>
                @else
                    <div style="font-weight: 600;">
                        {{ optional($booking->room)->room_number ?? 'Unassigned' }} — {{ optional(optional($booking->room)->roomType)->name ?? 'N/A' }}
                        @if(optional($booking->room)->status)
                            <x-admin.badge :status="optional($booking->room)->status" />
                        @endif
                    </div>
                @endif
            </div>
            @if(isset($availableRooms) && $availableRooms->isNotEmpty())
            <form method="POST" action="{{ route('admin.bookings.assignRoom', $booking) }}">
                @csrf
                <div style="display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap;">
                    @if($booking->rooms->isNotEmpty())
                    <div style="flex: 1; min-width: 220px;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Replace Assigned Room</label>
                        <select id="currentRoomSelect" name="current_room_id" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                            @foreach($booking->rooms as $assignedRoom)
                                <option value="{{ $assignedRoom->id }}" data-room-type-id="{{ $assignedRoom->room_type_id }}">
                                    Room {{ $assignedRoom->room_number }} — {{ optional($assignedRoom->roomType)->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Transfer to Room</label>
                        <select id="transferRoomSelect" name="room_id" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
                            @foreach($availableRooms as $availRoom)
                                <option value="{{ $availRoom->id }}" data-room-type-id="{{ $availRoom->room_type_id }}">
                                    Room {{ $availRoom->room_number }} — {{ $availRoom->roomType->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-admin.button type="primary">Assign / Transfer</x-admin.button>
                    </div>
                </div>
            </form>
            @else
                <p style="color: var(--text-muted); font-size: 0.875rem;">No other available rooms of the same type at the moment.</p>
            @endif
        </x-admin.card>
        @endif

        <!-- Guest Information -->
        <x-admin.card title="Guest Information" style="margin-top: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Name</div>
                    <div style="font-weight: 600;">{{ optional($booking->guest)->name ?? 'Guest not available' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Email</div>
                    <div style="font-weight: 600;">{{ optional($booking->guest)->email ?? 'No email' }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Phone</div>
                    <div style="font-weight: 600;">{{ optional($booking->guest)->phone ?? 'No phone' }}</div>
                </div>
                @if(optional($booking->guest)->address)
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Address</div>
                    <div style="font-weight: 600;">{{ optional($booking->guest)->address }}</div>
                </div>
                @endif
                {{-- @if($booking->guest->id_photo)
                <div style="grid-column: 1 / -1;">
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">ID Photo</div>
                    <div style="display: flex; gap: 1rem; align-items: flex-start;">
                        <div style="position: relative; width: 200px; height: 200px; border: 2px solid #e5e5e5; border-radius: 8px; overflow: hidden; cursor: pointer;" onclick="openIdPhotoModal()">
                            @if(str_ends_with($booking->guest->id_photo, '.pdf'))
                                <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8f8f8;">
                                    <i class="fas fa-file-pdf" style="font-size: 3rem; color: #dc3545; margin-bottom: 0.5rem;"></i>
                                    <span style="font-size: 0.875rem; color: #666;">PDF Document</span>
                                </div>
                            @else
                                <img src="{{ asset('storage/' . $booking->guest->id_photo) }}" alt="ID Photo" style="width: 100%; height: 100%; object-fit: cover;">
                            @endif
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <a href="{{ asset('storage/' . $booking->guest->id_photo) }}" target="_blank" class="btn-primary" style="display: inline-block; padding: 0.5rem 1rem; background: linear-gradient(135deg, #d4af37, #f4e4c1); color: #2c2c2c; text-decoration: none; border-radius: 6px; font-weight: 600; text-align: center;">
                                <i class="fas fa-external-link-alt"></i> View Full Size
                            </a>
                            <a href="{{ asset('storage/' . $booking->guest->id_photo) }}" download class="btn-secondary" style="display: inline-block; padding: 0.5rem 1rem; background: #6c757d; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; text-align: center;">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
                @endif --}}
            </div>
        </x-admin.card>
    
    <!-- ID Photo Modal -->
    <div id="idPhotoModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; padding: 2rem;" onclick="closeIdPhotoModal()">
        <div style="position: relative; max-width: 90%; max-height: 90%; margin: auto; top: 50%; transform: translateY(-50%);">
            <button onclick="closeIdPhotoModal()" style="position: absolute; top: -40px; right: 0; background: white; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 1.5rem; cursor: pointer; color: #2c2c2c;">&times;</button>
            @if(optional($booking->guest)->id_photo && !str_ends_with(optional($booking->guest)->id_photo, '.pdf'))
                <img src="{{ asset('storage/' . optional($booking->guest)->id_photo) }}" alt="ID Photo" style="max-width: 100%; max-height: 80vh; display: block; margin: auto; border-radius: 8px;">
            @endif
        </div>
    </div>
    
    <script>
        function openIdPhotoModal() {
            @if(optional($booking->guest)->id_photo && !str_ends_with(optional($booking->guest)->id_photo, '.pdf'))
                document.getElementById('idPhotoModal').style.display = 'block';
            @endif
        }
        
        function closeIdPhotoModal() {
            document.getElementById('idPhotoModal').style.display = 'none';
        }
        
        // Close modal with Esc key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeIdPhotoModal();
            }
        });
    </script>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Payment Summary -->
        <x-admin.card title="Payment">
            @php
                $baseRoomTotal = (float) ($booking->total_amount ?? 0);
                $grossAmount = (float) ($grossTotal ?? $baseRoomTotal);
                $billingAdjustmentDelta = round($grossAmount - $baseRoomTotal, 2);
            @endphp
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Room Charges ({{ $booking->total_nights ?? 0 }} nights)</span>
                    <span style="font-weight: 600;">₱{{ number_format($baseRoomTotal, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #666;">
                    <span>VAT ({{ number_format($vatPercentage, 2) }}%) Included</span>
                    <span style="font-weight: 600;">₱{{ number_format((float) ($booking->tax_amount ?? 0), 2) }}</span>
                </div>
                @if(abs($billingAdjustmentDelta) > 0.00001)
                <div style="display: flex; justify-content: space-between;">
                    <span>Billing Adjustment</span>
                    <span style="font-weight: 600; color: {{ $billingAdjustmentDelta < 0 ? '#2e7d32' : '#c62828' }};">{{ $billingAdjustmentDelta < 0 ? '-₱' : '+₱' }}{{ number_format(abs($billingAdjustmentDelta), 2) }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between;">
                    <span>Gross Total (After Billing)</span>
                    <span style="font-weight: 700;">₱{{ number_format($grossAmount, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; color: #2e7d32;">
                    <span>Verified Payments</span>
                    <span style="font-weight: 700;">-₱{{ number_format($verifiedPaymentsTotal, 2) }}</span>
                </div>
                <div style="border-top: 2px solid var(--border-gray); padding-top: 1rem; display: flex; justify-content: space-between;">
                    <span style="font-weight: 700;">Balance Due</span>
                    <span style="font-weight: 700; color: var(--primary-gold); font-size: 1.25rem;">₱{{ number_format($balanceDue, 2) }}</span>
                </div>

                @if($balanceDue > 0)
                    <form method="POST" action="{{ route('admin.bookings.settleBalance', $booking) }}" style="margin-top: 0.75rem;">
                        @csrf
                        <button type="submit" style="width: 100%; padding: 0.65rem 0.9rem; border: none; border-radius: 8px; background: linear-gradient(135deg, #4caf50, #45a049); color: white; font-weight: 700; cursor: pointer;">
                            <i class="fas fa-hand-holding-usd"></i> Settle Balance (Auto Zero)
                        </button>
                    </form>
                @endif
            </div>
        </x-admin.card>

        <!-- Actions -->
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

            <!-- Billing Adjustment Button -->
            <div style="margin-bottom: 1rem;">
                @if($billingLocked)
                    <button type="button" disabled style="display: block; width: 100%; padding: 0.75rem; background: #d9d9d9; color: #6b6b6b; border: none; border-radius: 8px; font-weight: 600; text-align: center; cursor: not-allowed;" title="Billing adjustment is locked for this booking status.">
                        <i class="fas fa-calculator"></i> Billing Adjustment & Charges
                    </button>
                @else
                    <a href="{{ route('admin.bookings.finalBilling', $booking) }}" class="btn-success" style="display: block; padding: 0.75rem; background: linear-gradient(135deg, #4caf50, #45a049); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; text-align: center;">
                        <i class="fas fa-calculator"></i> Billing Adjustment & Charges
                    </a>
                @endif
            </div>

            <div style="margin-bottom: 1rem;">
                @if($canOpenReschedule)
                    <button type="button" onclick="showRescheduleModal()" style="display: block; width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #ffc107, #ffca2c); color: #2c2c2c; border: none; border-radius: 8px; font-weight: 600; text-align: center; cursor: pointer;">
                        <i class="fas fa-calendar-alt"></i> Change Booking Date
                    </button>
                @else
                    <button type="button" disabled style="display: block; width: 100%; padding: 0.75rem; background: #d9d9d9; color: #6b6b6b; border: none; border-radius: 8px; font-weight: 600; text-align: center; cursor: not-allowed;">
                        <i class="fas fa-calendar-alt"></i> Change Booking Date
                    </button>
                    @if($rescheduleLocked)
                        <small style="display:block; margin-top:0.5rem; color:#777;">Rescheduling is not allowed after check-in/check-out.</small>
                    @elseif(!$hasVerifiedPayment)
                        <small style="display:block; margin-top:0.5rem; color:#777;">Rescheduling requires verified payment in the Payment Module.</small>
                    @endif
                @endif
            </div>

            <form method="POST" action="{{ route('admin.bookings.rescheduleRequest', $booking) }}" style="margin-bottom: 1rem;" onsubmit="return confirm('Are you sure you want to proceed?');">
                @csrf
                <x-admin.button type="outline">Send Reschedule Request Email</x-admin.button>
            </form>

            <!-- Update Status Form -->
            <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" style="margin-bottom: 1rem;">
                @csrf
                @method('PUT')
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Update Status</label>
                <select name="status" {{ $statusLocked ? 'disabled' : '' }} style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px; margin-bottom: 1rem; {{ $statusLocked ? 'background:#f3f3f3; color:#777;' : '' }}">
                    <option value="{{ $booking->status }}" selected>{{ ucwords(str_replace('_', ' ', $booking->status)) }} (Current)</option>
                    @foreach($allowedStatuses as $nextStatus)
                        <option value="{{ $nextStatus }}">{{ ucwords(str_replace('_', ' ', $nextStatus)) }}</option>
                    @endforeach
                </select>

                <div id="statusCancelReasonWrap" style="display:none; margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Cancellation Reason</label>
                    <textarea name="cancellation_reason" rows="3" maxlength="500" placeholder="Enter reason for cancelling this booking" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;"></textarea>
                </div>

                <div style="width: 100%;">
                    @if($statusLocked)
                        <button type="button" disabled style="width: 100%; padding: 0.75rem; border: none; border-radius: 8px; background: #d9d9d9; color: #6b6b6b; font-weight: 600; cursor: not-allowed;">
                            Update Status
                        </button>
                    @else
                        <x-admin.button type="primary">Update Status</x-admin.button>
                    @endif
                </div>
                @if(!$statusLocked)
                    <small style="display:block; margin-top:0.5rem; color:#777;">Check-in option appears only on {{ optional($booking->check_in_date)->format('M d, Y') ?? 'N/A' }}. Check-out is always available for admin override.</small>
                @endif
                @if($statusLocked)
                    <small style="display:block; margin-top:0.5rem; color:#777;">Status is locked for {{ ucwords(str_replace('_', ' ', $booking->status)) }} bookings.</small>
                @endif
            </form>
        </x-admin.card>
    </div>
</div>

<!-- Reschedule Booking Modal -->
<div id="rescheduleModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: relative; max-width: 500px; margin: 5% auto; background: white; padding: 2rem; border-radius: 12px;">
        <h3 style="margin-bottom: 1.5rem;">Reschedule Booking</h3>
        <form method="POST" action="{{ route('admin.bookings.reschedule', $booking) }}">
            @csrf
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle"></i> Original Check-in: <strong>{{ optional($booking->check_in_date)->format('M d, Y') ?? 'N/A' }}</strong>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">New Check-in Date</label>
                <input type="date" name="new_check_in_date" required class="form-control" min="{{ $rescheduleMinDate }}" max="{{ $rescheduleMaxDate }}" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">New Check-out Date (Auto Computed)</label>
                <input type="text" id="rescheduleCheckoutPreview" class="form-control" value="Select a new check-in date" readonly style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px; background: #f8f9fa; color: #555;">
            </div>
            <div class="alert alert-warning small mb-3">
                <i class="fas fa-exclamation-triangle"></i> Allowed range: {{ \Carbon\Carbon::parse($rescheduleMinDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($rescheduleMaxDate)->format('M d, Y') }} only. Duration is fixed to {{ $rescheduleNights }} night(s), and the system will auto-compute check-out date.
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="hideRescheduleModal()" class="btn-secondary" style="flex: 1; padding: 0.75rem; background: #6c757d; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" class="btn-warning" style="flex: 1; padding: 0.75rem; background: #ffc107; color: #2c2c2c; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Reschedule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showCancelModal() {
    document.getElementById('cancelModal').style.display = 'block';
}

function hideCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
}

function showRescheduleModal() {
    document.getElementById('rescheduleModal').style.display = 'block';
}

function hideRescheduleModal() {
    document.getElementById('rescheduleModal').style.display = 'none';
}

// Close modals with Esc key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        hideCancelModal();
        hideRescheduleModal();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const rescheduleCheckInInput = document.querySelector('input[name="new_check_in_date"]');
    const rescheduleCheckoutPreview = document.getElementById('rescheduleCheckoutPreview');
    const rescheduleNights = {{ $rescheduleNights }};

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

    if (rescheduleCheckInInput) {
        rescheduleCheckInInput.addEventListener('change', refreshRescheduleCheckoutPreview);
        refreshRescheduleCheckoutPreview();
    }

    const currentRoomSelect = document.getElementById('currentRoomSelect');
    const transferRoomSelect = document.getElementById('transferRoomSelect');
    const statusSelect = document.querySelector('form[action="{{ route('admin.bookings.updateStatus', $booking) }}"] select[name="status"]');
    const statusCancelReasonWrap = document.getElementById('statusCancelReasonWrap');

    function toggleCancellationReasonField() {
        if (!statusSelect || !statusCancelReasonWrap) {
            return;
        }

        statusCancelReasonWrap.style.display = statusSelect.value === 'cancelled' ? 'block' : 'none';
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', toggleCancellationReasonField);
        toggleCancellationReasonField();
    }

    if (!currentRoomSelect || !transferRoomSelect) {
        return;
    }

    const transferOptions = Array.from(transferRoomSelect.options).map(option => ({
        value: option.value,
        label: option.textContent,
        roomTypeId: option.dataset.roomTypeId || '',
    }));

    function refreshTransferRooms() {
        const selectedCurrentOption = currentRoomSelect.options[currentRoomSelect.selectedIndex];
        const selectedRoomTypeId = selectedCurrentOption?.dataset.roomTypeId || '';

        transferRoomSelect.innerHTML = '';

        const matchingOptions = transferOptions.filter(option => option.roomTypeId === selectedRoomTypeId);
        matchingOptions.forEach(option => {
            const el = document.createElement('option');
            el.value = option.value;
            el.textContent = option.label;
            el.dataset.roomTypeId = option.roomTypeId;
            transferRoomSelect.appendChild(el);
        });

        if (transferRoomSelect.options.length === 0) {
            const empty = document.createElement('option');
            empty.value = '';
            empty.textContent = 'No available rooms for selected room type';
            transferRoomSelect.appendChild(empty);
            transferRoomSelect.disabled = true;
        } else {
            transferRoomSelect.disabled = false;
        }
    }

    currentRoomSelect.addEventListener('change', refreshTransferRooms);
    refreshTransferRooms();
});
</script>

@endsection

