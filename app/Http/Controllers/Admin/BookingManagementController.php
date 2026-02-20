<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\ActivityLog;
use App\Mail\CheckoutReminder;
use App\Mail\BookingCancelled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['guest', 'room', 'roomType']);

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
        $booking->load(['guest', 'room', 'roomType']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,rescheduled,rejected_payment',
        ]);

        // Load guest relationship for emails
        $booking->load('guest');

        $booking->update(['status' => $validated['status']]);

        // Update room status based on booking status
        if ($validated['status'] === 'rejected_payment') {
            // Free up room when payment is rejected
            $booking->room->update(['status' => 'available']);
        } elseif ($validated['status'] === 'checked_in') {
            $booking->room->update(['status' => 'occupied']);
        } elseif ($validated['status'] === 'checked_out') {
            $booking->room->update(['status' => 'maintenance']);

            // Auto-record remaining balance as revenue
            $amountPaid = $booking->payments()
                ->whereIn('payment_status', ['verified', 'completed'])
                ->sum('amount');
            $finalTotal = $booking->final_total ?? $booking->total_amount;
            $remainingBalance = round($finalTotal - $amountPaid, 2);
            if ($remainingBalance > 0) {
                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_type' => 'remaining_balance',
                    'payment_method' => 'cash',
                    'amount' => $remainingBalance,
                    'payment_status' => 'completed',
                    'payment_date' => now(),
                    'payment_notes' => 'Auto-recorded remaining balance on checkout.',
                    'verified_at' => now(),
                    'verified_by' => auth()->id(),
                ]);
            }
            
            // Create or update housekeeping record
            \App\Models\Housekeeping::updateOrCreate(
                ['room_id' => $booking->room_id],
                ['status' => 'dirty', 'notes' => 'Room needs cleaning after checkout']
            );
            
            // Send checkout confirmation email
            Log::info('About to send checkout email to: ' . $booking->guest->email);
            try {
                Mail::to($booking->guest->email)->send(new CheckoutReminder($booking));
                Log::info('Checkout email sent successfully to: ' . $booking->guest->email);
            } catch (\Exception $e) {
                Log::error('Failed to send checkout email: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
            }
        }

        return back()->with('success', 'Booking status updated successfully!');
    }

    public function finalBilling(Booking $booking)
    {
        $booking->load(['guest', 'room', 'roomType']);

        // Determine hourly rate based on room type
        $hourlyRate = 150; // Default for Standard/Deluxe
        if (stripos($booking->roomType->name, 'family') !== false) {
            $hourlyRate = 250;
        }

        return view('admin.bookings.final-billing', compact('booking', 'hourlyRate'));
    }

    public function updateFinalBilling(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'early_checkin_hours' => 'nullable|integer|min:0|max:5',
            'early_checkin_charge' => 'nullable|numeric|min:0',
            'late_checkout_hours' => 'nullable|integer|min:0|max:5',
            'late_checkout_charge' => 'nullable|numeric|min:0',
            'has_pwd_senior' => 'nullable|boolean',
            'pwd_senior_count' => 'nullable|integer|min:0',
            'pwd_senior_discount' => 'nullable|numeric|min:0',
            'manual_adjustment' => 'nullable|numeric',
            'adjustment_reason' => 'nullable|string|max:500',
            'payment_method' => 'nullable|in:cash,gcash',
        ]);

        // Update booking with validated data
        $booking->update([
            'early_checkin_hours' => $validated['early_checkin_hours'] ?? 0,
            'early_checkin_charge' => $validated['early_checkin_charge'] ?? 0,
            'late_checkout_hours' => $validated['late_checkout_hours'] ?? 0,
            'late_checkout_charge' => $validated['late_checkout_charge'] ?? 0,
            'has_pwd_senior' => $request->has('has_pwd_senior'),
            'pwd_senior_count' => $validated['pwd_senior_count'] ?? 0,
            'pwd_senior_discount' => $validated['pwd_senior_discount'] ?? 0,
            'manual_adjustment' => $validated['manual_adjustment'] ?? 0,
            'adjustment_reason' => $validated['adjustment_reason'],
        ]);

        // Log the activity
        ActivityLog::log(
            'final_billing_edit',
            'Updated final billing for booking #' . $booking->booking_reference . ' - Final Total: ₱' . number_format($booking->final_total, 2),
            'App\Models\Booking',
            $booking->id,
            [
                'early_checkin' => $validated['early_checkin_hours'] ?? 0,
                'late_checkout' => $validated['late_checkout_hours'] ?? 0,
                'pwd_senior_discount' => $validated['pwd_senior_discount'] ?? 0,
                'manual_adjustment' => $validated['manual_adjustment'] ?? 0,
                'final_total' => $booking->final_total,
            ]
        );

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Final billing updated successfully! Total: ₱' . number_format($booking->final_total, 2));
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

        // Free up the room
        $booking->room->update(['status' => 'available']);

        // Send cancellation email
        $booking->load('guest');
        try {
            Mail::to($booking->guest->email)->send(new BookingCancelled($booking));
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
