<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\ActivityLog;
use App\Mail\BookingCancelled;
use App\Mail\CheckoutThankYou;
use App\Mail\PaymentConfirmation;
use App\Mail\RescheduleConfirmation;
use App\Mail\RescheduleRejection;
use App\Mail\RescheduleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Throwable;

class BookingManagementController extends Controller
{
    public function notificationsSnapshot(Request $request)
    {
        try {
            if (!Schema::hasTable('bookings')) {
                return response()->json([
                    'pending_count' => 0,
                    'latest_booking_id' => null,
                    'latest_booking_reference' => null,
                    'latest_booking_created_at' => null,
                    'latest_guest_name' => null,
                ]);
            }

            $guestColumns = ['id'];
            $hasGuestsTable = Schema::hasTable('guests');

            if ($hasGuestsTable && Schema::hasColumn('guests', 'name')) {
                $guestColumns[] = 'name';
            }
            if ($hasGuestsTable && Schema::hasColumn('guests', 'first_name')) {
                $guestColumns[] = 'first_name';
            }
            if ($hasGuestsTable && Schema::hasColumn('guests', 'last_name')) {
                $guestColumns[] = 'last_name';
            }

            $pendingQuery = Booking::query()
                ->where('status', 'pending')
                ->latest('id')
                ->take(5);

            if ($hasGuestsTable) {
                $pendingQuery->with([
                    'guest' => function ($query) use ($guestColumns) {
                        $query->select(array_values(array_unique($guestColumns)));
                    }
                ]);
            }

            $pendingBookings = $pendingQuery->get();
            $latest = $pendingBookings->first();
            $latestGuestName = null;

            if ($latest && $latest->guest) {
                $latestGuestName = trim((string) ($latest->guest->name ?? (($latest->guest->first_name ?? '') . ' ' . ($latest->guest->last_name ?? ''))));
            }

            return response()->json([
                'pending_count' => $pendingBookings->count(),
                'latest_booking_id' => $latest?->id,
                'latest_booking_reference' => $latest?->booking_reference,
                'latest_booking_created_at' => $latest?->created_at?->toIso8601String(),
                'latest_guest_name' => $latestGuestName,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to build admin booking notifications snapshot', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'pending_count' => 0,
                'latest_booking_id' => null,
                'latest_booking_reference' => null,
                'latest_booking_created_at' => null,
                'latest_guest_name' => null,
            ]);
        }
    }

    public function index(Request $request)
    {
        $query = Booking::with(['guest', 'room', 'roomType', 'rooms.roomType', 'payments']);
        $hasGuestNameColumn = Schema::hasColumn('guests', 'name');
        $hasGuestFirstNameColumn = Schema::hasColumn('guests', 'first_name');
        $hasGuestLastNameColumn = Schema::hasColumn('guests', 'last_name');
        $hasGuestEmailColumn = Schema::hasColumn('guests', 'email');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $hasGuestNameColumn, $hasGuestFirstNameColumn, $hasGuestLastNameColumn, $hasGuestEmailColumn) {
                $q->where('booking_reference', 'LIKE', "%{$search}%")
                  ->orWhereHas('guest', function($gq) use ($search, $hasGuestNameColumn, $hasGuestFirstNameColumn, $hasGuestLastNameColumn, $hasGuestEmailColumn) {
                      $gq->where(function ($guestSearch) use ($search, $hasGuestNameColumn, $hasGuestFirstNameColumn, $hasGuestLastNameColumn, $hasGuestEmailColumn) {
                          if ($hasGuestNameColumn) {
                              $guestSearch->orWhere('name', 'LIKE', "%{$search}%");
                          }

                          if ($hasGuestFirstNameColumn) {
                              $guestSearch->orWhere('first_name', 'LIKE', "%{$search}%");
                          }

                          if ($hasGuestLastNameColumn) {
                              $guestSearch->orWhere('last_name', 'LIKE', "%{$search}%");
                          }

                          if ($hasGuestFirstNameColumn && $hasGuestLastNameColumn) {
                              $guestSearch->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                          }

                          if ($hasGuestEmailColumn) {
                              $guestSearch->orWhere('email', 'LIKE', "%{$search}%");
                          }
                      });
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

        $bookings = $query->latest()->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'checked_out' => Booking::where('status', 'checked_out')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['guest', 'room.roomType', 'roomType', 'rooms.roomType']);

        $allowedStatuses = $this->getAllowedStatusTransitions($booking->status);
        $today = now()->toDateString();
        $checkInDate = optional($booking->check_in_date)->toDateString();

        if ($today !== $checkInDate) {
            $allowedStatuses = array_values(array_filter($allowedStatuses, fn ($status) => $status !== 'checked_in'));
        }

        $statusLocked = in_array($booking->status, ['checked_out', 'cancelled', 'rejected_payment'], true);
        $billingLocked = in_array($booking->status, ['checked_out', 'cancelled', 'rejected_payment'], true);

        $verifiedPaymentsTotal = $booking->payments()
            ->whereIn('payment_status', ['verified', 'completed'])
            ->sum('amount');

        $grossTotal = $booking->final_total ?? $booking->total_amount;
        $balanceDue = max(round($grossTotal - $verifiedPaymentsTotal, 2), 0);

        // Checkout is only allowed when the booking is fully paid.
        if ($balanceDue > 0) {
            $allowedStatuses = array_values(array_filter($allowedStatuses, fn ($status) => $status !== 'checked_out'));
        }

        // Available rooms for the same assigned room type(s) (for assign/transfer)
        $assignedRoomTypeIds = $booking->rooms->isNotEmpty()
            ? $booking->rooms->pluck('room_type_id')->filter()->unique()->values()->all()
            : array_filter([$booking->room?->room_type_id]);
        $assignedRoomIds = $booking->rooms->isNotEmpty()
            ? $booking->rooms->pluck('id')->all()
            : array_filter([$booking->room_id]);
        $availableRooms = Room::with('roomType')
            ->whereIn('status', ['available', 'dirty'])
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
            'cancellation_reason' => 'nullable|string|max:500|required_if:status,cancelled',
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

            $today = now()->toDateString();
            $allowedCheckInDate = optional($booking->check_in_date)->toDateString();
            if ($today !== $allowedCheckInDate) {
                return back()->with('error', 'Check-in is only allowed on the booking check-in date.');
            }
        }

        if ($targetStatus === 'rescheduled') {
            $hasVerifiedPayment = $booking->payments()->whereIn('payment_status', ['verified', 'completed'])->exists();
            if (!$hasVerifiedPayment) {
                return back()->with('error', 'Rescheduling status is not allowed until payment is verified in the Payment Module.');
            }
        }

        if ($targetStatus === 'checked_out') {
            $amountPaid = (float) $booking->payments()
                ->whereIn('payment_status', ['verified', 'completed'])
                ->sum('amount');
            $finalTotal = (float) ($booking->final_total ?? $booking->total_amount ?? 0);
            $remainingBalance = round($finalTotal - $amountPaid, 2);

            if ($remainingBalance > 0.00001) {
                return back()->with('error', 'Checkout is not allowed while there is an outstanding balance. Please click Settle Balance first.');
            }
        }

        // Load guest relationship for emails
        $booking->load('guest');

        $updateData = ['status' => $targetStatus];
        if ($targetStatus === 'cancelled' && !$booking->cancelled_at) {
            $updateData['cancelled_at'] = now();
            $updateData['cancellation_reason'] = $validated['cancellation_reason'] ?? $booking->cancellation_reason ?: 'Cancelled by admin via status update.';
        }
        $booking->update($updateData);

        if ($targetStatus === 'confirmed') {
            $latestPayment = $booking->payments()
                ->latest('payment_date')
                ->latest('created_at')
                ->first();

            $guestEmail = optional($booking->guest)->email;
            if (!empty($guestEmail)) {
                try {
                    Mail::to($guestEmail)->send(new PaymentConfirmation($booking, $latestPayment));
                } catch (\Exception $e) {
                    Log::error('Failed to send booking confirmation email from status update: ' . $e->getMessage());
                }
            } else {
                Log::warning('Skipped confirmation email from status update: guest email missing for booking #' . $booking->booking_reference);
            }
        }

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
            // Release rooms immediately after checkout for same-day turnover.
            $roomsToUpdate = $booking->rooms->isNotEmpty() ? $booking->rooms : collect([$booking->room])->filter();
            foreach ($roomsToUpdate as $room) {
                $room->update(['status' => 'available']);
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

    public function settleBalance(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'nullable|in:cash,gcash,bank_transfer,paymaya',
            'payment_reference' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string|max:500',
        ]);

        $amountPaid = (float) $booking->payments()
            ->whereIn('payment_status', ['verified', 'completed'])
            ->sum('amount');
        $finalTotal = (float) ($booking->final_total ?? $booking->total_amount ?? 0);
        $remainingBalance = round($finalTotal - $amountPaid, 2);

        if ($remainingBalance <= 0) {
            return back()->with('success', 'No outstanding balance to settle.');
        }

        Payment::create([
            'booking_id' => $booking->id,
            'payment_type' => 'remaining_payment',
            'payment_method' => $request->input('payment_method', 'cash'),
            'payment_reference' => $request->input('payment_reference') ?: ('SETTLE-' . strtoupper(substr($booking->booking_reference ?? 'BOOKING', -6)) . '-' . now()->format('YmdHis')),
            'amount' => $remainingBalance,
            'percentage' => 100,
            'payment_status' => 'completed',
            'payment_date' => now(),
            'payment_notes' => $request->input('payment_notes') ?: 'Settled via admin quick-settle action.',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return back()->with('success', 'Balance settled successfully.');
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
            $booking->rooms()->attach($newRoom->id, $this->filterBookingRoomPivotPayload([
                'nightly_rate' => $pivotRate,
                'manual_adjustment' => $pivotManualAdjustment,
                'additional_charge' => $pivotAdditionalCharge,
                'additional_charge_reason' => $pivotAdditionalReason,
                'discount_amount' => $pivotDiscountAmount,
                'discount_type' => $pivotDiscountType,
            ]));

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

        // Always free the old room when transferring, except when the room does not exist.
        if ($booking->room_id && $booking->room && (int) $booking->room_id !== (int) $newRoom->id) {
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
        $existingBillingPaymentReference = null;

        $adjustmentNotes = (string) ($booking->adjustment_reason ?? '');
        if (preg_match('/Billing\s+GCash\s+Reference:\s*(\d{1,13})/i', $adjustmentNotes, $matches)) {
            $existingBillingPaymentReference = $matches[1];
        }

        if (!$existingBillingPaymentReference) {
            $existingBillingPaymentReference = (string) ($booking->payments()
                ->where('payment_method', 'gcash')
                ->whereNotNull('payment_reference')
                ->latest('payment_date')
                ->latest('created_at')
                ->value('payment_reference') ?? '');

            $existingBillingPaymentReference = preg_replace('/\D+/', '', $existingBillingPaymentReference);
            $existingBillingPaymentReference = substr((string) $existingBillingPaymentReference, 0, 13);
        }

        // Determine hourly rate based on room type
        $primaryRoomTypeName = $booking->rooms->first()?->roomType?->name
            ?? $booking->room?->roomType?->name
            ?? $booking->roomType?->name
            ?? '';
        $hourlyRate = 150; // Default for Standard/Deluxe
        if (stripos($primaryRoomTypeName, 'family') !== false) {
            $hourlyRate = 250;
        }

        return view('admin.bookings.final-billing', compact('booking', 'hourlyRate', 'verifiedPaymentsTotal', 'grossTotal', 'balanceDue', 'existingBillingPaymentReference'));
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
            'overall_manual_adjustment' => 'nullable|numeric',
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
            'services_total_adjustment' => 'nullable|numeric|min:0',
            'services_breakdown' => 'nullable|string|max:1000',
            'adjustment_reason' => 'nullable|string|max:500',
            'payment_method' => 'nullable|in:cash,gcash',
            'payment_reference' => 'nullable|required_if:payment_method,gcash|regex:/^\d{1,13}$/',
        ]);

        $booking->loadMissing('rooms');

        $manualAdjustment = (float) ($validated['manual_adjustment'] ?? 0);
        $overallManualAdjustment = (float) ($validated['overall_manual_adjustment'] ?? 0);
        $servicesTotalAdjustment = (float) ($validated['services_total_adjustment'] ?? 0);
        $servicesBreakdown = trim((string) ($validated['services_breakdown'] ?? ''));
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

                $booking->rooms()->updateExistingPivot($reservedRoom->id, $this->filterBookingRoomPivotPayload([
                    'manual_adjustment' => $perRoomNetAdjustment,
                    'additional_charge' => $perRoomAdditional,
                    'additional_charge_reason' => $perRoomAdditionalReason,
                    'discount_amount' => $perRoomDiscount,
                    'discount_type' => $perRoomDiscountType,
                ]));
            }
        } elseif ($booking->rooms->isNotEmpty() && isset($validated['room_manual_adjustments'])) {
            $manualAdjustment = 0;

            foreach ($booking->rooms as $reservedRoom) {
                $perRoomAdjustment = (float) ($validated['room_manual_adjustments'][$reservedRoom->id] ?? 0);
                $manualAdjustment += $perRoomAdjustment;

                $booking->rooms()->updateExistingPivot($reservedRoom->id, $this->filterBookingRoomPivotPayload([
                    'manual_adjustment' => $perRoomAdjustment,
                ]));
            }
        }

        $manualAdjustment += $overallManualAdjustment;
        $manualAdjustment += $servicesTotalAdjustment;

        $earlyCheckinHours = $validated['early_checkin_hours'] ?? 0;
        $earlyCheckinCharge = $validated['early_checkin_charge'] ?? 0;
        $lateCheckoutHours = $validated['late_checkout_hours'] ?? 0;
        $lateCheckoutCharge = $validated['late_checkout_charge'] ?? 0;
        $hasPwdSenior = $request->has('has_pwd_senior');
        $pwdSeniorCount = $validated['pwd_senior_count'] ?? 0;
        $pwdSeniorDiscount = $validated['pwd_senior_discount'] ?? 0;

        if ((int) $lateCheckoutHours > 0) {
            $booking->loadMissing(['rooms', 'room']);

            $bookingRoomIds = $booking->rooms->isNotEmpty()
                ? $booking->rooms->pluck('id')->map(fn ($id) => (int) $id)->all()
                : array_filter([(int) ($booking->room_id ?? 0)]);

            if (!empty($bookingRoomIds)) {
                $hasSameDayArrivalConflict = Booking::query()
                    ->where('bookings.id', '!=', $booking->id)
                    ->whereDate('check_in_date', optional($booking->check_out_date)->toDateString())
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled'])
                    ->where(function ($roomQuery) use ($bookingRoomIds) {
                        $roomQuery->whereIn('room_id', $bookingRoomIds)
                            ->orWhereHas('rooms', function ($roomsRelationQuery) use ($bookingRoomIds) {
                                $roomsRelationQuery->whereIn('rooms.id', $bookingRoomIds);
                            });
                    })
                    ->exists();

                if ($hasSameDayArrivalConflict) {
                    return redirect()->back()->withErrors([
                        'late_checkout_hours' => 'Late checkout cannot be approved because there is already a same-day incoming booking for this room. Please adjust room assignment or booking dates first.',
                    ])->withInput();
                }
            }
        }

        if ($isMultiRoom) {
            $earlyCheckinHours = 0;
            $earlyCheckinCharge = 0;
            $lateCheckoutHours = 0;
            $lateCheckoutCharge = 0;
            $hasPwdSenior = $hasAnyPwdSeniorDiscount;
            $pwdSeniorCount = 0;
            $pwdSeniorDiscount = 0;
        }

        $adjustmentReason = trim((string) ($validated['adjustment_reason'] ?? ''));
        if ($servicesBreakdown !== '') {
            $servicesLine = 'Amenities & Services: ' . $servicesBreakdown;
            if ($adjustmentReason === '') {
                $adjustmentReason = $servicesLine;
            } elseif (stripos($adjustmentReason, $servicesLine) === false) {
                $adjustmentReason .= "\n" . $servicesLine;
            }
        }
        $billingPaymentMethod = $validated['payment_method'] ?? null;
        $billingPaymentReference = trim((string) ($validated['payment_reference'] ?? ''));

        if ($billingPaymentMethod === 'gcash' && $billingPaymentReference !== '') {
            $gcashRefLine = 'Billing GCash Reference: ' . $billingPaymentReference;
            if ($adjustmentReason === '') {
                $adjustmentReason = $gcashRefLine;
            } elseif (stripos($adjustmentReason, $gcashRefLine) === false) {
                $adjustmentReason .= "\n" . $gcashRefLine;
            }
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
            'adjustment_reason' => $adjustmentReason,
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
                'payment_method' => $validated['payment_method'] ?? null,
                'payment_reference' => $validated['payment_reference'] ?? null,
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
            'new_check_in_date' => 'required|date',
        ]);

        $requestedCheckInDate = Carbon::parse($validated['new_check_in_date'])->startOfDay();

        $originalNights = (int) ($booking->total_nights
            ?: Carbon::parse($booking->check_in_date)->diffInDays(Carbon::parse($booking->check_out_date)));
        $originalNights = max(1, $originalNights);
        $requestedCheckOutDate = $requestedCheckInDate->copy()->addDays($originalNights);

        if (in_array($booking->status, ['checked_in', 'checked_out'], true)) {
            return back()->with('error', 'Booking cannot be rescheduled after check-in or check-out.');
        }

        $hasVerifiedPayment = $booking->payments()->whereIn('payment_status', ['verified', 'completed'])->exists();
        if (!$hasVerifiedPayment) {
            return back()->with('error', 'Rescheduling is not allowed until payment is verified in the Payment Module.');
        }

        $baseCheckInDate = Carbon::parse($booking->original_check_in_date ?? $booking->check_in_date)->startOfDay();
        $minAllowedDate = $baseCheckInDate->copy()->addDay();
        $maxAllowedDate = $baseCheckInDate->copy()->addDays(14);

        if ($requestedCheckInDate->lt($minAllowedDate) || $requestedCheckInDate->gt($maxAllowedDate)) {
            $this->sendRescheduleRejectionEmail($booking, $requestedCheckInDate);

            return back()->with('error', 'New check-in date must be within 14 days from the original check-in date.');
        }

        [$isAvailable, $availabilityMessage, $roomAssignments] = $this->validateRescheduleAvailability($booking, $requestedCheckInDate, $requestedCheckOutDate);
        if (!$isAvailable) {
            $this->sendRescheduleRejectionEmail($booking, $requestedCheckInDate);

            return back()->with('error', $availabilityMessage);
        }

        // Store original date if not already stored
        if (!$booking->original_check_in_date) {
            $booking->original_check_in_date = $booking->check_in_date;
            $booking->save();
        }

        DB::transaction(function () use ($booking, $requestedCheckInDate, $requestedCheckOutDate, $roomAssignments, $originalNights) {
            // Update booking dates and keep original night count.
            $booking->update([
                'check_in_date' => $requestedCheckInDate->toDateString(),
                'check_out_date' => $requestedCheckOutDate->toDateString(),
                'rescheduled_at' => now(),
                'status' => 'rescheduled',
                'total_nights' => $originalNights,
            ]);

            if (!empty($roomAssignments)) {
                $booking->loadMissing(['rooms.roomType', 'room.roomType']);

                if ($booking->rooms->isNotEmpty()) {
                    $existingByType = $booking->rooms
                        ->groupBy(fn ($room) => (int) $room->room_type_id)
                        ->map(fn ($rooms) => $rooms->values());

                    $syncPayload = [];
                    foreach ($roomAssignments as $roomTypeId => $assignedRoomIds) {
                        foreach ($assignedRoomIds as $newRoomId) {
                            $oldRoom = optional($existingByType->get((int) $roomTypeId))->shift();
                            $nightlyRate = (float) ($oldRoom?->pivot?->nightly_rate
                                ?? $oldRoom?->effective_price
                                ?? optional($oldRoom?->roomType)->base_price
                                ?? 0);

                            $syncPayload[$newRoomId] = $this->filterBookingRoomPivotPayload([
                                'nightly_rate' => $nightlyRate,
                                'manual_adjustment' => (float) ($oldRoom?->pivot?->manual_adjustment ?? 0),
                                'additional_charge' => (float) ($oldRoom?->pivot?->additional_charge ?? 0),
                                'additional_charge_reason' => $oldRoom?->pivot?->additional_charge_reason ?? null,
                                'discount_amount' => (float) ($oldRoom?->pivot?->discount_amount ?? 0),
                                'discount_type' => $oldRoom?->pivot?->discount_type ?? null,
                            ]);
                        }
                    }

                    $booking->rooms()->sync($syncPayload);
                    if (!empty($syncPayload)) {
                        $booking->update(['room_id' => (int) array_key_first($syncPayload)]);
                    }
                } elseif (!empty($booking->room_id)) {
                    $firstAssignedRoomId = (int) collect($roomAssignments)->flatten()->first();
                    if ($firstAssignedRoomId > 0) {
                        $booking->update(['room_id' => $firstAssignedRoomId]);
                    }
                }
            }
        });

        // Log the activity
        ActivityLog::log(
            'booking_reschedule',
            'Rescheduled booking #' . $booking->booking_reference . ' to ' . $requestedCheckInDate->format('M d, Y'),
            'App\Models\Booking',
            $booking->id,
            [
                'original_date' => $booking->original_check_in_date,
                'new_check_in' => $requestedCheckInDate->toDateString(),
                'new_check_out' => $requestedCheckOutDate->toDateString(),
            ]
        );

        $this->sendRescheduleConfirmationEmail($booking);

        return back()->with('success', 'Booking rescheduled successfully. Confirmation email sent to guest.');
    }

    public function sendRescheduleRequest(Booking $booking)
    {
        if (in_array($booking->status, ['checked_in', 'checked_out'], true)) {
            return back()->with('error', 'Reschedule request cannot be sent for checked-in or checked-out bookings.');
        }

        try {
            $guestEmail = optional($booking->guest)->email;
            if (!empty($guestEmail)) {
                Mail::to($guestEmail)->send(new RescheduleRequest($booking));
            } else {
                return back()->with('error', 'Guest email is missing. Unable to send reschedule request.');
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send admin-initiated reschedule request email: ' . $e->getMessage());
            return back()->with('error', 'Failed to send reschedule request email. Please try again.');
        }

        ActivityLog::log(
            'booking_reschedule_request',
            'Sent admin-initiated reschedule request email for booking #' . $booking->booking_reference,
            'App\\Models\\Booking',
            $booking->id,
            [
                'base_check_in_date' => optional($booking->original_check_in_date ?? $booking->check_in_date)?->toDateString(),
            ]
        );

        return back()->with('success', 'Reschedule request email sent to guest.');
    }

    private function validateRescheduleAvailability(Booking $booking, Carbon $newCheckInDate, Carbon $newCheckOutDate): array
    {
        $booking->loadMissing(['rooms.roomType', 'room.roomType']);

        $requiredRoomTypeCounts = [];

        if ($booking->rooms->isNotEmpty()) {
            foreach ($booking->rooms as $reservedRoom) {
                $typeId = (int) ($reservedRoom->room_type_id ?? 0);
                if ($typeId > 0) {
                    $requiredRoomTypeCounts[$typeId] = ($requiredRoomTypeCounts[$typeId] ?? 0) + 1;
                }
            }
        } elseif ($booking->room) {
            $typeId = (int) ($booking->room->room_type_id ?? 0);
            if ($typeId > 0) {
                $requiredRoomTypeCounts[$typeId] = 1;
            }
        }

        if (empty($requiredRoomTypeCounts)) {
            return [false, 'Unable to validate room availability because booked room type is missing.', []];
        }

        $checkInDate = $newCheckInDate->toDateString();
        $checkOutDate = $newCheckOutDate->toDateString();
        $resolvedAssignments = [];

        foreach ($requiredRoomTypeCounts as $roomTypeId => $requiredCount) {
            $availableRooms = Room::query()
                ->where('room_type_id', $roomTypeId)
                ->whereNull('archived_at')
                ->whereIn('status', ['available', 'dirty', 'occupied'])
                ->whereDoesntHave('blockDates', function ($q) use ($checkInDate, $checkOutDate) {
                    $q->where('start_date', '<', $checkOutDate)
                        ->where('end_date', '>', $checkInDate);
                })
                ->whereDoesntHave('bookings', function ($q) use ($booking, $checkInDate, $checkOutDate) {
                    $q->where('bookings.id', '!=', $booking->id)
                        ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled'])
                        ->where(function ($overlapQuery) use ($checkInDate, $checkOutDate) {
                            $overlapQuery
                                ->where(function ($rangeQuery) use ($checkInDate, $checkOutDate) {
                                    $rangeQuery->where('check_in_date', '<', $checkOutDate)
                                        ->where('check_out_date', '>', $checkInDate);
                                })
                                ->orWhere(function ($lateCheckoutQuery) use ($checkInDate) {
                                    $lateCheckoutQuery->whereDate('check_out_date', $checkInDate)
                                        ->where('late_checkout_hours', '>', 0)
                                        ->whereIn('status', ['confirmed', 'checked_in', 'rescheduled']);
                                });
                        });
                })
                ->whereDoesntHave('reservationBookings', function ($q) use ($booking, $checkInDate, $checkOutDate) {
                    $q->where('bookings.id', '!=', $booking->id)
                        ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled'])
                        ->where(function ($overlapQuery) use ($checkInDate, $checkOutDate) {
                            $overlapQuery
                                ->where(function ($rangeQuery) use ($checkInDate, $checkOutDate) {
                                    $rangeQuery->where('check_in_date', '<', $checkOutDate)
                                        ->where('check_out_date', '>', $checkInDate);
                                })
                                ->orWhere(function ($lateCheckoutQuery) use ($checkInDate) {
                                    $lateCheckoutQuery->whereDate('check_out_date', $checkInDate)
                                        ->where('late_checkout_hours', '>', 0)
                                        ->whereIn('status', ['confirmed', 'checked_in', 'rescheduled']);
                                });
                        });
                })
                ->orderBy('room_number')
                ->get(['id']);

            if ($availableRooms->count() < $requiredCount) {
                return [false, 'Your requested date is not available. Please provide another preferred date within the allowed period.', []];
            }

            $resolvedAssignments[(int) $roomTypeId] = $availableRooms
                ->take($requiredCount)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        return [true, null, $resolvedAssignments];
    }

    private function sendRescheduleConfirmationEmail(Booking $booking): void
    {
        try {
            $guestEmail = optional($booking->guest)->email;
            if (!empty($guestEmail)) {
                Mail::to($guestEmail)->send(new RescheduleConfirmation($booking));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send reschedule confirmation email: ' . $e->getMessage());
        }
    }

    private function sendRescheduleRejectionEmail(Booking $booking, Carbon $requestedCheckInDate): void
    {
        try {
            $guestEmail = optional($booking->guest)->email;
            if (!empty($guestEmail)) {
                Mail::to($guestEmail)->send(new RescheduleRejection($booking, $requestedCheckInDate->toDateString()));
            }
        } catch (\Throwable $e) {
            Log::error('Failed to send reschedule rejection email: ' . $e->getMessage());
        }
    }

    private function filterBookingRoomPivotPayload(array $payload): array
    {
        static $bookingRoomColumns = null;

        if ($bookingRoomColumns === null) {
            try {
                $bookingRoomColumns = Schema::hasTable('booking_rooms')
                    ? Schema::getColumnListing('booking_rooms')
                    : [];
            } catch (\Throwable $e) {
                $bookingRoomColumns = [];
            }
        }

        if (empty($bookingRoomColumns)) {
            return [];
        }

        return array_intersect_key($payload, array_flip($bookingRoomColumns));
    }
}
