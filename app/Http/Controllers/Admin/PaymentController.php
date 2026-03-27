<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Mail\PaymentApproved;
use App\Mail\PaymentRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.guest', 'booking.room', 'booking.rooms.roomType'])
            ->whereNotNull('proof_of_payment')
            ->whereHas('booking')
            ->whereHas('booking.guest');

        // Filter by payment status
        $status = trim((string) $request->input('status', ''));
        if (in_array($status, ['pending', 'verified', 'failed'], true)) {
            $query->where('payment_status', $status);
        }

        $payments = $query->latest()->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['booking.guest', 'booking.room', 'booking.rooms.roomType']);

        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        $request->validate([
            'payment_notes' => 'nullable|string',
        ]);

        // Load relationships for emails
        $payment->load('booking.guest');

        $payment->update([
            'payment_status' => 'verified',
            'payment_notes' => $request->payment_notes,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        // Send payment-verified acknowledgement only.
        // Booking confirmation email is sent when admin explicitly updates status to Confirmed.
        Log::info('About to send payment emails to: ' . $payment->booking->guest->email);
        try {
            Mail::to($payment->booking->guest->email)->send(new PaymentApproved($payment));
            Log::info('PaymentApproved email sent');
        } catch (\Exception $e) {
            Log::error('Failed to send payment approval email: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment verified successfully!');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // Load relationships for emails and booking status update
        $payment->load('booking.guest', 'booking.room', 'booking.rooms');

        $payment->update([
            'payment_status' => 'failed',
            'payment_notes' => $request->rejection_reason,
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        // Keep booking status in sync with payment rejection.
        if ($payment->booking) {
            $payment->booking->update([
                'status' => 'rejected_payment',
            ]);

            $roomsToRelease = $payment->booking->rooms->isNotEmpty()
                ? $payment->booking->rooms
                : collect([$payment->booking->room])->filter();

            foreach ($roomsToRelease as $room) {
                if ($room->status !== 'available') {
                    $room->update(['status' => 'available']);
                }
            }
        }

        // Send rejection email
        try {
            Mail::to($payment->booking->guest->email)->send(new PaymentRejected($payment, $request->rejection_reason));
        } catch (\Exception $e) {
            Log::error('Failed to send payment rejection email: ' . $e->getMessage());
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment rejected.');
    }
}
