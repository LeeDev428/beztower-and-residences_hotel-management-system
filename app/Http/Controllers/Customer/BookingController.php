<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Extra;
use App\Models\Payment;
use App\Mail\BookingAcknowledgement;
use App\Mail\PaymentConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'country' => 'nullable|string',
            'address' => 'nullable|string',
            'id_photo' => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:5120',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'total_nights' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
            'extras' => 'nullable|array',
            'extras.*' => 'exists:extras,id'
        ]);

        try {
            DB::beginTransaction();

            // Handle ID photo upload
            $idPhotoPath = null;
            if ($request->hasFile('id_photo')) {
                $idPhotoPath = $request->file('id_photo')->store('guests/id_photos', 'public');
            }

            // Create or update guest
            $guest = Guest::updateOrCreate(
                ['email' => $validated['email']],
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'phone' => $validated['phone'],
                    'country' => $validated['country'],
                    'address' => $validated['address'],
                    'id_photo' => $idPhotoPath ?? $guest->id_photo ?? null
                ]
            );

            // Get room details
            $room = Room::with('roomType')->findOrFail($validated['room_id']);

            // Calculate costs
            $subtotal = $room->roomType->base_price * $validated['total_nights'];
            $extrasTotal = 0;

            // Get selected extras if any
            $selectedExtras = [];
            if (!empty($validated['extras'])) {
                $extras = Extra::whereIn('id', $validated['extras'])->get();
                foreach ($extras as $extra) {
                    $extrasTotal += $extra->price;
                    $selectedExtras[] = [
                        'extra_id' => $extra->id,
                        'quantity' => 1,
                        'price_at_booking' => $extra->price
                    ];
                }
            }

            // Calculate tax (12%)
            $taxAmount = ($subtotal + $extrasTotal) * 0.12;
            $totalAmount = $subtotal + $extrasTotal + $taxAmount;

            // Generate unique booking reference
            $bookingReference = 'BEZ-' . strtoupper(Str::random(8));

            // Create booking
            $booking = Booking::create([
                'booking_reference' => $bookingReference,
                'guest_id' => $guest->id,
                'room_id' => $room->id,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'number_of_guests' => $validated['number_of_guests'],
                'total_nights' => $validated['total_nights'],
                'subtotal' => $subtotal,
                'extras_total' => $extrasTotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'special_requests' => $validated['special_requests']
            ]);

            // Attach extras to booking if any
            if (!empty($selectedExtras)) {
                foreach ($selectedExtras as $extra) {
                    DB::table('booking_extras')->insert([
                        'booking_id' => $booking->id,
                        'extra_id' => $extra['extra_id'],
                        'quantity' => $extra['quantity'],
                        'price_at_booking' => $extra['price_at_booking'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            // Send booking acknowledgement email
            try {
                Mail::to($guest->email)->send(new BookingAcknowledgement($booking));
            } catch (\Exception $e) {
                // Log email error but don't fail the booking
                Log::error('Failed to send booking acknowledgement email: ' . $e->getMessage());
            }

            // Redirect to payment page instead of confirmation
            return redirect()->route('booking.payment', ['reference' => $bookingReference])
                ->with('success', 'Booking created! Please complete the down payment to confirm your reservation.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'An error occurred while processing your booking. Please try again.'
            ])->withInput();
        }
    }

    public function payment($reference)
    {
        $booking = Booking::with(['guest', 'room.roomType', 'extras'])
            ->where('booking_reference', $reference)
            ->firstOrFail();

        // Calculate 30% down payment
        $downPaymentAmount = $booking->total_amount * 0.30;

        return view('customer.booking.payment', compact('booking', 'downPaymentAmount'));
    }

    public function processPayment(Request $request, $reference)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:gcash,paymaya,bank_transfer',
            'payment_reference' => 'required|string|max:255',
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        $booking = Booking::where('booking_reference', $reference)->firstOrFail();

        try {
            DB::beginTransaction();

            // Store proof of payment
            $proofPath = $request->file('proof_of_payment')->store('payments/proofs', 'public');

            // Calculate 30% down payment
            $downPaymentAmount = $booking->total_amount * 0.30;

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_type' => 'down_payment',
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'],
                'amount' => $downPaymentAmount,
                'percentage' => 30.00,
                'payment_status' => 'pending', // Will be verified by admin
                'proof_of_payment' => $proofPath,
                'payment_date' => now(),
            ]);

            // Update booking status to pending (waiting for payment verification)
            $booking->update(['status' => 'pending']);

            DB::commit();

            // Send booking acknowledgement email with payment details
            // Mail::to($booking->guest->email)->send(new BookingAcknowledgement($booking, $payment));

            return redirect()->route('booking.confirmation', ['reference' => $reference])
                ->with('success', 'Payment proof submitted successfully! We will verify your payment within 24-48 hours.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'Failed to process payment. Please try again.'
            ])->withInput();
        }
    }

    public function confirmation($reference)
    {
        $booking = Booking::with(['guest', 'room.roomType', 'extras', 'payments'])
            ->where('booking_reference', $reference)
            ->firstOrFail();

        return view('customer.booking.confirmation', compact('booking'));
    }

    public function checkAvailability(Request $request)
    {
        // Implementation for checking room availability
        // This can be used for AJAX requests to check if room is available
        
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date'
        ]);

        // Check if room is already booked for these dates
        $isBooked = Booking::where('room_id', $validated['room_id'])
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($validated) {
                $query->whereBetween('check_in_date', [$validated['check_in_date'], $validated['check_out_date']])
                    ->orWhereBetween('check_out_date', [$validated['check_in_date'], $validated['check_out_date']])
                    ->orWhere(function($q) use ($validated) {
                        $q->where('check_in_date', '<=', $validated['check_in_date'])
                          ->where('check_out_date', '>=', $validated['check_out_date']);
                    });
            })
            ->exists();

        return response()->json([
            'available' => !$isBooked,
            'message' => $isBooked ? 'Room is not available for selected dates.' : 'Room is available!'
        ]);
    }
}
