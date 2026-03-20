<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\ActivityLog;
use App\Mail\BookingCancelled;
use App\Mail\CheckoutThankYou;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['guest', 'room', 'roomType', 'rooms.roomType', 'payments']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'LIKE', "%{$search}%")
                  ->orWhereHas('guest', function($gq) use ($search) {
                      $gq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('check_in_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('check_in_date', '<=', $request->date_to);
        }

        $bookings = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['guest', 'room.roomType', 'roomType', 'rooms.roomType']);

        $allowedStatuses = $this->getAllowedStatusTransitions($booking->status);
        $statusLocked = in_array($booking->status, ['checked_out', 'cancelled', 'rejected_payment'], true);
        $billingLocked = in_array($booking->status, ['checked_out', 'cancelled', 'rejected_payment'], true);

        $verifiedPaymentsTotal = $booking->payments()
            ->whereIn('payment_status', ['verified', 'completed'])
            ->sum('amount');

        $grossTotal = $booking->final_total ?? $booking->total_amount;
        $balanceDue = max(round($grossTotal - $verifiedPaymentsTotal, 2), 0);

        // Available rooms for the same assigned room type(s) (for assign/transfer)
        $assignedRoomTypeIds = $booking->rooms->isNotEmpty()
            ? $booking->rooms->pluck('room_type_id')->filter()->unique()->values()->all()
            : array_filter([$booking->room?->room_type_id]);
        $assignedRoomIds = $booking->rooms->isNotEmpty()
            ? $booking->rooms->pluck('id')->all()
            : array_filter([$booking->room_id]);
        $availableRooms = Room::with('roomType')
            ->where('status', 'available')
            ->whereNull('archived_at')
            ->when(!empty($assignedRoomTypeIds), fn($q) => $q->whereIn('room_type_id', $assignedRoomTypeIds))
            ->when(!empty($assignedRoomIds), fn($q) => $q->whereNotIn('id', $assignedRoomIds))
            ->orderBy('room_number')
            ->get();

        return view('admin.bookings.show', compact(
            'booking',
            'availableRooms',
            'verifiedPaymentsTotal',
            'balanceDue',
            'grossTotal',
            'allowedStatuses',
            'statusLocked',
            'billingLocked'
        ));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,rescheduled,rejected_payment',
        ]);

        $currentStatus = $booking->status;
        $targetStatus = $validated['status'];

        if (in_array($currentStatus, ['checked_out', 'cancelled', 'rejected_payment'], true)) {
            return back()->with('error', 'Status is locked for this booking and can no longer be updated.');
        }

        if ($currentStatus === $targetStatus) {
            return back()->with('success', 'Booking status remains ' . str_replace('_', ' ', $currentStatus) . '.');
        }

        $allowedTransitions = $this->getAllowedStatusTransitions($currentStatus);
        if (!in_array($targetStatus, $allowedTransitions, true)) {
            return back()->with('error', 'Invalid status transition from ' . str_replace('_', ' ', $currentStatus) . ' to ' . str_replace('_', ' ', $targetStatus) . '.');
        }

        if ($targetStatus === 'checked_in') {
            $hasVerifiedPayment = $booking->payments()->whereIn('payment_status', ['verified', 'completed'])->exists();
            if (!$hasVerifiedPayment) {
                return back()->with('error', 'Check-in is not allowed until payment is verified in the Payment Module.');
            }
        }

        // Load guest relationship for emails
        $booking->load('guest');

        $updateData = ['status' => $targetStatus];
        if ($targetStatus === 'cancelled' && !$booking->cancelled_at) {
            $updateData['cancelled_at'] = now();
            $updateData['cancellation_reason'] = $booking->cancellation_reason ?: 'Cancelled by admin via status update.';
        }
        $booking->update($updateData);

        // Update room status based on booking status
        if ($targetStatus === 'rejected_payment') {
            $roomsToUpdate = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
            foreach ($roomsToUpdate as $room) {
                $room->update(['status' => 'available']);
            }
        } elseif ($targetStatus === 'checked_in') {
            $roomsToUpdate = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
            foreach ($roomsToUpdate as $room) {
                $room->update(['status' => 'occupied']);
            }
        } elseif ($targetStatus === 'cancelled') {
            $roomsToUpdate = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
            foreach ($roomsToUpdate as $room) {
                $room->update(['status' => 'available']);
            }

            try {
                $guestEmail = optional($booking->guest)->email;
                if (!empty($guestEmail)) {
                    Mail::to($guestEmail)->send(new BookingCancelled($booking));
                } else {
                    Log::warning('Skipped cancellation email from status update: guest email missing for booking #' . $booking->booking_reference);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send cancellation email from status update: ' . $e->getMessage());
            }
        } elseif ($targetStatus === 'checked_out') {
            // Rooms become dirty after checkout (need cleaning)
            $roomsToUpdate = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
            foreach ($roomsToUpdate as $room) {
                $room->update(['status' => 'dirty']);
            }

            // Auto-record remaining balance as revenue
            $amountPaid = $booking->payments()
                ->whereIn('payment_status', ['verified', 'completed'])
                ->sum('amount');
            $finalTotal = $booking->final_total ?? $booking->total_amount;
            $remainingBalance = round($finalTotal - $amountPaid, 2);
            if ($remainingBalance > 0) {
                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_type' => 'remaining_payment',
                    'payment_method' => 'cash',
                    'amount' => $remainingBalance,
                    'payment_status' => 'completed',
                    'payment_date' => now(),
                    'payment_notes' => 'Auto-recorded remaining balance on checkout.',
                    'verified_at' => now(),
                    'verified_by' => Auth::id(),
                ]);
            }
            
            // Send thank-you email after checkout
            $guestEmail = optional($booking->guest)->email;
            Log::info('About to send checkout thank-you email to: ' . ($guestEmail ?: '[missing-email]'));
            try {
                if (!empty($guestEmail)) {
                    Mail::to($guestEmail)->send(new CheckoutThankYou($booking));
                    Log::info('Checkout thank-you email sent successfully to: ' . $guestEmail);
                } else {
                    Log::warning('Skipped checkout thank-you email: guest email missing for booking #' . $booking->booking_reference);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send checkout thank-you email: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
            }
        }

        return back()->with('success', 'Booking status updated successfully!');
    }

    private function getAllowedStatusTransitions(string $currentStatus): array
    {
        $map = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['checked_in', 'rescheduled', 'cancelled'],
            'checked_in' => ['checked_out'],
            'rescheduled' => ['checked_in', 'cancelled'],
            'checked_out' => [],
            'cancelled' => [],
            'rejected_payment' => [],
        ];

        return $map[$currentStatus] ?? [];
    }

    /**
     * Assign or transfer a room to a booking.
     * The new room must be 'available'. If the booking already has a room
     * and is not checked_in/checked_out, free the old room back to 'available'.
     */
    public function assignRoom(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'current_room_id' => 'nullable|exists:rooms,id',
        ]);

        $booking->loadMissing(['rooms', 'room']);
        $newRoom = Room::findOrFail($validated['room_id']);

        if ($newRoom->status !== 'available') {
            return back()->with('error', 'Selected room is not available. Only available rooms can be assigned.');
        }

        $statusForNewRoom = $booking->status === 'checked_in' ? 'occupied' : 'available';

        if ($booking->rooms->isNotEmpty()) {
            $currentRoomId = (int) ($validated['current_room_id'] ?? 0);
            $currentRoom = $booking->rooms->firstWhere('id', $currentRoomId);

            if (!$currentRoom) {
                return back()->with('error', 'Please select which assigned room to transfer.');
            }

            if ($booking->rooms->contains('id', $newRoom->id)) {
                return back()->with('error', 'Selected room is already assigned to this booking.');
            }

            if ((int) $newRoom->room_type_id !== (int) $currentRoom->room_type_id) {
                return back()->with('error', 'Room transfer must stay within the same room type as the selected assigned room.');
            }

            $pivotRate = (float) ($currentRoom->pivot->nightly_rate ?? $currentRoom->effective_price ?? optional($currentRoom->roomType)->base_price ?? 0);
            $pivotManualAdjustment = (float) ($currentRoom->pivot->manual_adjustment ?? 0);
            $pivotAdditionalCharge = (float) ($currentRoom->pivot->additional_charge ?? 0);
            $pivotAdditionalReason = $currentRoom->pivot->additional_charge_reason;
            $pivotDiscountAmount = (float) ($currentRoom->pivot->discount_amount ?? 0);
            $pivotDiscountType = $currentRoom->pivot->discount_type;

            $booking->rooms()->detach($currentRoom->id);
            $booking->rooms()->attach($newRoom->id, [
                'nightly_rate' => $pivotRate,
                'manual_adjustment' => $pivotManualAdjustment,
                'additional_charge' => $pivotAdditionalCharge,
                'additional_charge_reason' => $pivotAdditionalReason,
                'discount_amount' => $pivotDiscountAmount,
                'discount_type' => $pivotDiscountType,
            ]);

            if ((int) $booking->room_id === (int) $currentRoom->id) {
                $booking->update(['room_id' => $newRoom->id]);
            }

            $currentRoom->update(['status' => 'available']);
            $newRoom->update(['status' => $statusForNewRoom]);

            ActivityLog::log(
                'room_transfer',
                'Transferred room #' . $currentRoom->room_number . ' to room #' . $newRoom->room_number . ' for booking #' . $booking->booking_reference,
                'App\\Models\\Booking',
                $booking->id,
                [
                    'from_room_number' => $currentRoom->room_number,
                    'to_room_number' => $newRoom->room_number,
                ]
            );

            return back()->with('success', 'Room transfer completed successfully.');
        }

        if ($booking->room && (int) $newRoom->room_type_id !== (int) $booking->room->room_type_id) {
            return back()->with('error', 'Room assignment must stay within the originally booked room type.');
        }

        // Free old room if booking is pending/confirmed (not already checked in)
        if ($booking->room_id && !in_array($booking->status, ['checked_in', 'checked_out'])) {
            $booking->room->update(['status' => 'available']);
        }

        // Assign new room; if already checked in, mark new room occupied
        $newRoom->update(['status' => $statusForNewRoom]);

        $booking->update(['room_id' => $newRoom->id]);

        ActivityLog::log(
            'room_assign',
            'Assigned room #' . $newRoom->room_number . ' to booking #' . $booking->booking_reference,
            'App\Models\Booking',
            $booking->id,
            ['room_number' => $newRoom->room_number]
        );

        return back()->with('success', 'Room #' . $newRoom->room_number . ' successfully assigned to this booking.');
    }

    public function finalBilling(Booking $booking)
    {
        if (in_array($booking->status, ['checked_out', 'cancelled', 'rejected_payment'], true)) {
            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Billing adjustment is locked for this booking status.');
        }

        $booking->load(['guest', 'room.roomType', 'roomType', 'rooms.roomType']);

        $verifiedPaymentsTotal = $booking->payments()
            ->whereIn('payment_status', ['verified', 'completed'])
            ->sum('amount');

        $grossTotal = $booking->final_total ?? $booking->total_amount;
        $balanceDue = max(round($grossTotal - $verifiedPaymentsTotal, 2), 0);

        // Determine hourly rate based on room type
        $primaryRoomTypeName = $booking->rooms->first()?->roomType?->name
            ?? $booking->room?->roomType?->name
            ?? $booking->roomType?->name
            ?? '';
        $hourlyRate = 150; // Default for Standard/Deluxe
        if (stripos($primaryRoomTypeName, 'family') !== false) {
            $hourlyRate = 250;
        }

        return view('admin.bookings.final-billing', compact('booking', 'hourlyRate', 'verifiedPaymentsTotal', 'grossTotal', 'balanceDue'));
    }

    public function updateFinalBilling(Request $request, Booking $booking)
    {
        if (in_array($booking->status, ['checked_out', 'cancelled', 'rejected_payment'], true)) {
            return redirect()->route('admin.bookings.show', $booking)
                ->with('error', 'Billing adjustment is locked for this booking status.');
        }

        $validated = $request->validate([
            'early_checkin_hours' => 'nullable|integer|min:0|max:5',
            'early_checkin_charge' => 'nullable|numeric|min:0',
            'late_checkout_hours' => 'nullable|integer|min:0|max:5',
            'late_checkout_charge' => 'nullable|numeric|min:0',
            'has_pwd_senior' => 'nullable|boolean',
            'pwd_senior_count' => 'nullable|integer|min:0',
            'pwd_senior_discount' => 'nullable|numeric|min:0',
            'manual_adjustment' => 'nullable|numeric',
            'room_additional_charges' => 'nullable|array',
            'room_additional_charges.*' => 'nullable|numeric|min:0',
            'room_additional_reasons' => 'nullable|array',
            'room_additional_reasons.*' => 'nullable|string|max:255',
            'room_discount_amounts' => 'nullable|array',
            'room_discount_amounts.*' => 'nullable|numeric|min:0',
            'room_discount_types' => 'nullable|array',
            'room_discount_types.*' => 'nullable|in:none,pwd,senior,other',
            'room_pwd_senior_counts' => 'nullable|array',
            'room_pwd_senior_counts.*' => 'nullable|integer|min:0',
            'room_manual_adjustments' => 'nullable|array',
            'room_manual_adjustments.*' => 'nullable|numeric',
            'adjustment_reason' => 'nullable|string|max:500',
            'payment_method' => 'nullable|in:cash,gcash',
        ]);

        $booking->loadMissing('rooms');

        $manualAdjustment = (float) ($validated['manual_adjustment'] ?? 0);
        $isMultiRoom = $booking->rooms->isNotEmpty() && $booking->rooms->count() > 1;
        $totalPwdSeniorDiscount = 0.0;
        $hasAnyPwdSeniorDiscount = false;

        if ($isMultiRoom) {
            $manualAdjustment = 0;

            foreach ($booking->rooms as $reservedRoom) {
                $roomId = (int) $reservedRoom->id;
                $perRoomAdditional = (float) ($validated['room_additional_charges'][$roomId] ?? 0);
                $perRoomAdditionalReason = $validated['room_additional_reasons'][$roomId] ?? null;
                $perRoomDiscountType = $validated['room_discount_types'][$roomId] ?? 'none';
                $perRoomDiscount = (float) ($validated['room_discount_amounts'][$roomId] ?? 0);
                $roomPwdSeniorCount = (int) ($validated['room_pwd_senior_counts'][$roomId] ?? 0);

                $roomCapacity = max(1, (int) (optional($reservedRoom->roomType)->max_guests ?? 1));
                $roomNights = (int) ($booking->total_nights ?? $booking->number_of_nights ?? 0);
                $roomNightlyRate = (float) ($reservedRoom->pivot->nightly_rate ?? $reservedRoom->effective_price ?? optional($reservedRoom->roomType)->base_price ?? 0);
                $roomBaseTotal = $roomNightlyRate * max(1, $roomNights);

                $roomPwdSeniorCount = max(0, min($roomPwdSeniorCount, $roomCapacity));

                if (in_array($perRoomDiscountType, ['pwd', 'senior'], true)) {
                    if ($roomPwdSeniorCount <= 0) {
                        $perRoomDiscount = 0;
                    } else {
                        $perPersonShare = $roomBaseTotal / $roomCapacity;
                        $perRoomDiscount = round($perPersonShare * 0.20 * $roomPwdSeniorCount, 2);
                    }
                }

                if ($perRoomDiscountType === 'none') {
                    $perRoomDiscount = 0;
                }

                if (in_array($perRoomDiscountType, ['pwd', 'senior'], true) && $perRoomDiscount > 0) {
                    $hasAnyPwdSeniorDiscount = true;
                    $totalPwdSeniorDiscount += $perRoomDiscount;
                }

                $perRoomNetAdjustment = $perRoomAdditional - $perRoomDiscount;
                $manualAdjustment += $perRoomNetAdjustment;

                $booking->rooms()->updateExistingPivot($reservedRoom->id, [
                    'manual_adjustment' => $perRoomNetAdjustment,
                    'additional_charge' => $perRoomAdditional,
                    'additional_charge_reason' => $perRoomAdditionalReason,
                    'discount_amount' => $perRoomDiscount,
                    'discount_type' => $perRoomDiscountType,
                ]);
            }
        } elseif ($booking->rooms->isNotEmpty() && isset($validated['room_manual_adjustments'])) {
            $manualAdjustment = 0;

            foreach ($booking->rooms as $reservedRoom) {
                $perRoomAdjustment = (float) ($validated['room_manual_adjustments'][$reservedRoom->id] ?? 0);
                $manualAdjustment += $perRoomAdjustment;

                $booking->rooms()->updateExistingPivot($reservedRoom->id, [
                    'manual_adjustment' => $perRoomAdjustment,
                ]);
            }
        }

        $earlyCheckinHours = $validated['early_checkin_hours'] ?? 0;
        $earlyCheckinCharge = $validated['early_checkin_charge'] ?? 0;
        $lateCheckoutHours = $validated['late_checkout_hours'] ?? 0;
        $lateCheckoutCharge = $validated['late_checkout_charge'] ?? 0;
        $hasPwdSenior = $request->has('has_pwd_senior');
        $pwdSeniorCount = $validated['pwd_senior_count'] ?? 0;
        $pwdSeniorDiscount = $validated['pwd_senior_discount'] ?? 0;

        if ($isMultiRoom) {
            $earlyCheckinHours = 0;
            $earlyCheckinCharge = 0;
            $lateCheckoutHours = 0;
            $lateCheckoutCharge = 0;
            $hasPwdSenior = $hasAnyPwdSeniorDiscount;
            $pwdSeniorCount = 0;
            $pwdSeniorDiscount = 0;
        }

        // Update booking with validated data
        $booking->update([
            'early_checkin_hours' => $earlyCheckinHours,
            'early_checkin_charge' => $earlyCheckinCharge,
            'late_checkout_hours' => $lateCheckoutHours,
            'late_checkout_charge' => $lateCheckoutCharge,
            'has_pwd_senior' => $hasPwdSenior,
            'pwd_senior_count' => $pwdSeniorCount,
            'pwd_senior_discount' => $pwdSeniorDiscount,
            'manual_adjustment' => $manualAdjustment,
            'adjustment_reason' => $validated['adjustment_reason'],
        ]);

        // Log the activity
        ActivityLog::log(
            'final_billing_edit',
            'Updated billing adjustment for booking #' . $booking->booking_reference . ' - Final Total: ₱' . number_format($booking->final_total, 2),
            'App\Models\Booking',
            $booking->id,
            [
                'early_checkin' => $validated['early_checkin_hours'] ?? 0,
                'late_checkout' => $validated['late_checkout_hours'] ?? 0,
                'pwd_senior_discount' => $validated['pwd_senior_discount'] ?? 0,
                'manual_adjustment' => $manualAdjustment,
                'final_total' => $booking->final_total,
            ]
        );

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Billing adjustment updated successfully! Total: ₱' . number_format($booking->final_total, 2));
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
            'refund_status' => 'required|in:unpaid,partially_paid,paid',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
            'refund_status' => $validated['refund_status'],
        ]);

        // Free up reserved room(s)
        $booking->loadMissing('rooms', 'room');
        $roomsToUpdate = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
        foreach ($roomsToUpdate as $room) {
            $room->update(['status' => 'available']);
        }

        // Send cancellation email
        $booking->load('guest');
        try {
            $guestEmail = optional($booking->guest)->email;
            if (!empty($guestEmail)) {
                Mail::to($guestEmail)->send(new BookingCancelled($booking));
            } else {
                Log::warning('Skipped cancellation email: guest email missing for booking #' . $booking->booking_reference);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation email: ' . $e->getMessage());
        }

        // Log the activity
        ActivityLog::log(
            'booking_cancel',
            'Cancelled booking #' . $booking->booking_reference . ' - Reason: ' . $validated['cancellation_reason'],
            'App\Models\Booking',
            $booking->id,
            [
                'reason' => $validated['cancellation_reason'],
                'refund_status' => $validated['refund_status'],
            ]
        );

        return back()->with('success', 'Booking cancelled successfully. Cancellation email sent to guest.');
    }

    public function rescheduleBooking(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'new_check_in_date' => 'required|date|after:today',
            'new_check_out_date' => 'required|date|after:new_check_in_date',
        ]);

        // Validate rescheduling eligibility
        if (!$booking->canReschedule()) {
            return back()->with('error', 'This booking cannot be rescheduled. Must be cancelled and within 1 week.');
        }

        if (!$booking->isValidRescheduleDate($validated['new_check_in_date'])) {
            return back()->with('error', 'New check-in date must be within 1 month of the original check-in date.');
        }

        // Store original date if not already stored
        if (!$booking->original_check_in_date) {
            $booking->original_check_in_date = $booking->check_in_date;
        }

        // Update booking dates
        $booking->update([
            'check_in_date' => $validated['new_check_in_date'],
            'check_out_date' => $validated['new_check_out_date'],
            'rescheduled_at' => now(),
            'status' => 'pending', // Reset to pending
        ]);

        // Recalculate nights
        $checkIn = Carbon::parse($validated['new_check_in_date']);
        $checkOut = Carbon::parse($validated['new_check_out_date']);
        $nights = $checkIn->diffInDays($checkOut);
        $booking->update(['number_of_nights' => $nights]);

        // Log the activity
        ActivityLog::log(
            'booking_reschedule',
            'Rescheduled booking #' . $booking->booking_reference . ' to ' . Carbon::parse($validated['new_check_in_date'])->format('M d, Y'),
            'App\Models\Booking',
            $booking->id,
            [
                'original_date' => $booking->original_check_in_date,
                'new_check_in' => $validated['new_check_in_date'],
                'new_check_out' => $validated['new_check_out_date'],
            ]
        );

        return back()->with('success', 'Booking rescheduled successfully!');
    }
}
