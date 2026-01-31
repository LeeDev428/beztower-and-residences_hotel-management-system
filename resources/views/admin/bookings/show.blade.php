@extends('layouts.admin')

@section('title', 'Booking Details')
@section('page-title', 'Booking Details')

@section('content')
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
                        <div style="font-weight: 600;">{{ $booking->check_in_date->format('F d, Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Check-out</div>
                        <div style="font-weight: 600;">{{ $booking->check_out_date->format('F d, Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Room</div>
                        <div style="font-weight: 600;">{{ $booking->room->room_number }} - {{ $booking->roomType->name }}</div>
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

        <!-- Guest Information -->
        <x-admin.card title="Guest Information" style="margin-top: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Name</div>
                    <div style="font-weight: 600;">{{ $booking->guest->name }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Email</div>
                    <div style="font-weight: 600;">{{ $booking->guest->email }}</div>
                </div>
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Phone</div>
                    <div style="font-weight: 600;">{{ $booking->guest->phone }}</div>
                </div>
                @if($booking->guest->address)
                <div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Address</div>
                    <div style="font-weight: 600;">{{ $booking->guest->address }}</div>
                </div>
                @endif
                @if($booking->guest->id_photo)
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
                @endif
            </div>
        </x-admin.card>
    
    <!-- ID Photo Modal -->
    <div id="idPhotoModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; padding: 2rem;" onclick="closeIdPhotoModal()">
        <div style="position: relative; max-width: 90%; max-height: 90%; margin: auto; top: 50%; transform: translateY(-50%);">
            <button onclick="closeIdPhotoModal()" style="position: absolute; top: -40px; right: 0; background: white; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 1.5rem; cursor: pointer; color: #2c2c2c;">&times;</button>
            @if($booking->guest->id_photo && !str_ends_with($booking->guest->id_photo, '.pdf'))
                <img src="{{ asset('storage/' . $booking->guest->id_photo) }}" alt="ID Photo" style="max-width: 100%; max-height: 80vh; display: block; margin: auto; border-radius: 8px;">
            @endif
        </div>
    </div>
    
    <script>
        function openIdPhotoModal() {
            @if($booking->guest->id_photo && !str_ends_with($booking->guest->id_photo, '.pdf'))
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
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span>Room Rate ({{ $booking->number_of_nights }} nights)</span>
                    <span style="font-weight: 600;">₱{{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div style="border-top: 2px solid var(--border-gray); padding-top: 1rem; display: flex; justify-content: space-between;">
                    <span style="font-weight: 700;">Total</span>
                    <span style="font-weight: 700; color: var(--primary-gold); font-size: 1.25rem;">₱{{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>
        </x-admin.card>

        <!-- Actions -->
        <x-admin.card title="Actions" style="margin-top: 1.5rem;">
            <!-- Final Billing Button -->
            <div style="margin-bottom: 1rem;">
                <a href="{{ route('admin.bookings.finalBilling', $booking) }}" class="btn-success" style="display: block; padding: 0.75rem; background: linear-gradient(135deg, #4caf50, #45a049); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; text-align: center;">
                    <i class="fas fa-calculator"></i> Final Billing & Charges
                </a>
            </div>

            <!-- Update Status Form -->
            <form method="POST" action="{{ route('admin.bookings.updateStatus', $booking) }}" style="margin-bottom: 1rem;">
                @csrf
                @method('PUT')
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Update Status</label>
                <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px; margin-bottom: 1rem;">
                    <option value="pending" {{ $booking->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="checked_in" {{ $booking->status === 'checked_in' ? 'selected' : '' }}>Checked In</option>
                    <option value="checked_out" {{ $booking->status === 'checked_out' ? 'selected' : '' }}>Checked Out</option>
                    <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <div style="width: 100%;">
                    <x-admin.button type="primary">Update Status</x-admin.button>
                </div>
            </form>

            <!-- Cancel Booking Button -->
            @if($booking->status !== 'cancelled' && $booking->status !== 'checked_out')
            <div style="margin-bottom: 1rem;">
                <button type="button" class="btn-danger" onclick="showCancelModal()" style="display: block; width: 100%; padding: 0.75rem; background: #dc3545; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-times-circle"></i> Cancel Booking
                </button>
            </div>
            @endif

            <!-- Reschedule Button -->
            @if($booking->status === 'cancelled' && $booking->canReschedule())
            <div style="margin-bottom: 1rem;">
                <button type="button" class="btn-warning" onclick="showRescheduleModal()" style="display: block; width: 100%; padding: 0.75rem; background: #ffc107; color: #2c2c2c; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    <i class="fas fa-calendar-alt"></i> Reschedule Booking
                </button>
            </div>
            @endif
        </x-admin.card>
    </div>
</div>

<!-- Cancel Booking Modal -->
<div id="cancelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: relative; max-width: 500px; margin: 5% auto; background: white; padding: 2rem; border-radius: 12px;">
        <h3 style="margin-bottom: 1.5rem;">Cancel Booking</h3>
        <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Cancellation Reason</label>
                <textarea name="cancellation_reason" required class="form-control" rows="4" placeholder="Enter reason for cancellation..." style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;"></textarea>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Refund Status</label>
                <select name="refund_status" required class="form-select" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
                    <option value="unpaid">Unpaid (No refund needed)</option>
                    <option value="partially_paid">Partially Paid</option>
                    <option value="paid">Paid (Full refund)</option>
                </select>
            </div>
            <div style="display: flex; gap: 1rem;">
                <button type="button" onclick="hideCancelModal()" class="btn-secondary" style="flex: 1; padding: 0.75rem; background: #6c757d; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" class="btn-danger" style="flex: 1; padding: 0.75rem; background: #dc3545; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Confirm Cancellation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reschedule Booking Modal -->
<div id="rescheduleModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div style="position: relative; max-width: 500px; margin: 5% auto; background: white; padding: 2rem; border-radius: 12px;">
        <h3 style="margin-bottom: 1.5rem;">Reschedule Booking</h3>
        <form method="POST" action="{{ route('admin.bookings.reschedule', $booking) }}">
            @csrf
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle"></i> Original Check-in: <strong>{{ $booking->check_in_date->format('M d, Y') }}</strong>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">New Check-in Date</label>
                <input type="date" name="new_check_in_date" required class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">New Check-out Date</label>
                <input type="date" name="new_check_out_date" required class="form-control" min="{{ date('Y-m-d', strtotime('+2 days')) }}" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;">
            </div>
            <div class="alert alert-warning small mb-3">
                <i class="fas fa-exclamation-triangle"></i> New check-in date must be within 1 month of original date
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
</script>

@endsection

