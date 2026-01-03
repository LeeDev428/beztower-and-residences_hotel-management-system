<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Mail\PaymentApproved;
use App\Mail\PaymentRejected;
use App\Mail\PaymentConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.guest', 'booking.room'])->whereNotNull('proof_of_payment');

        // Filter by payment status
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        } else {
            // Default to pending payments
            $query->where('payment_status', 'pending');
        }

        $payments = $query->latest()->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['booking.guest', 'booking.room']);

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
            'verified_by' => auth()->id(),
        ]);

        // Send confirmation email
        Log::info('About to send payment emails to: ' . $payment->booking->guest->email);
        try {
            Mail::to($payment->booking->guest->email)->send(new PaymentApproved($payment));
            Log::info('PaymentApproved email sent');
            Mail::to($payment->booking->guest->email)->send(new PaymentConfirmation($payment->booking, $payment));
            Log::info('PaymentConfirmation email sent');
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

        // Load relationships for emails
        $payment->load('booking.guest');

        $payment->update([
            'payment_status' => 'failed',
            'payment_notes' => $request->rejection_reason,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        // Send rejection email
        try {
            Mail::to($payment->booking->guest->email)->send(new PaymentRejected($payment, $request->rejection_reason));
        } catch (\Exception $e) {
            Log::error('Failed to send payment rejection email: ' . $e->getMessage());
        }

        return redirect()->route('admin.payments.index')->with('success', 'Payment rejected.');
    }
}
