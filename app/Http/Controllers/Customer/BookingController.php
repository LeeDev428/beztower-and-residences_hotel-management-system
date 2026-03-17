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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function checkout(Request $request, Room $room)
    {
        $room->load(['roomType', 'photos']);

        $preselectedRoomIds = collect($request->input('room_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($preselectedRoomIds->isEmpty() && $request->filled('selected_rooms')) {
            $preselectedRoomIds = collect(explode(',', (string) $request->input('selected_rooms')))
                ->map(fn ($id) => (int) trim($id))
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->values();
        }

        $preselectedRooms = Room::with('roomType')
            ->whereIn('id', $preselectedRoomIds)
            ->whereNull('archived_at')
            ->where('status', 'available')
            ->get();

        $requestedRooms = (int) $request->integer('rooms', $preselectedRooms->count() ?: 1);
        $requestedRooms = max(1, min(12, $requestedRooms));

        $maxGuestCapacity = (int) $preselectedRooms->sum(fn ($selectedRoom) => (int) ($selectedRoom->roomType?->max_guests ?? 0));
        if ($maxGuestCapacity <= 0) {
            $maxGuestCapacity = (int) ($room->roomType?->max_guests ?? 1);
        }

        return view('customer.booking.checkout', compact('room', 'preselectedRooms', 'requestedRooms', 'maxGuestCapacity'));
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'room_ids' => 'nullable|array|min:1|max:12',
            'room_ids.*' => 'distinct|exists:rooms,id',
            'number_of_rooms' => 'required|integer|min:1|max:12',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'country' => 'nullable|string',
            'address' => 'nullable|string',
            'id_photo' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'total_nights' => 'required|integer|min:1',
            'payment_option' => 'required|in:full_payment,down_payment',
            'special_requests' => 'nullable|string',
            'extras' => 'nullable|array',
            'extras.*' => 'exists:extras,id',
            'extra_quantities' => 'nullable|array',
            'extra_quantities.*' => 'integer|min:1|max:50'
        ]);

        $selectedRoomIds = collect($validated['room_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($selectedRoomIds->isEmpty() && !empty($validated['room_id'])) {
            $selectedRoomIds = collect([(int) $validated['room_id']]);
        }

        if ($selectedRoomIds->isEmpty()) {
            return back()->withErrors([
                'room_ids' => 'Please select at least one room.',
            ])->withInput();
        }

        if ($selectedRoomIds->count() !== (int) $validated['number_of_rooms']) {
            return back()->withErrors([
                'room_ids' => 'Unable to assign the exact number of requested rooms. Please review your date range or room count.',
            ])->withInput();
        }

        // Booking limit: max 3 active bookings per email
        $existingGuest = Guest::where('email', $validated['email'])->first();
        if ($existingGuest) {
            $activeCount = Booking::where('guest_id', $existingGuest->id)
                ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->count();
            if ($activeCount >= 3) {
                return back()->withErrors([
                    'error' => 'You already have 3 active bookings under this email address. You cannot make more than 3 bookings at a time. Please contact us if you need further assistance.'
                ])->withInput();
            }
        }

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
                    'id_photo' => $idPhotoPath ?? $existingGuest?->id_photo
                ]
            );

            $selectedRooms = Room::with('roomType')
                ->whereIn('id', $selectedRoomIds)
                ->where('status', 'available')
                ->whereNull('archived_at')
                ->get();

            if ($selectedRooms->count() !== $selectedRoomIds->count()) {
                DB::rollBack();
                return back()->withErrors([
                    'room_ids' => 'One or more selected rooms are unavailable.',
                ])->withInput();
            }

            $hasConflict = Booking::query()
                ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled'])
                ->where('check_in_date', '<', $validated['check_out_date'])
                ->where('check_out_date', '>', $validated['check_in_date'])
                ->where(function ($query) use ($selectedRoomIds) {
                    $query->whereIn('room_id', $selectedRoomIds)
                        ->orWhereHas('rooms', function ($q) use ($selectedRoomIds) {
                            $q->whereIn('rooms.id', $selectedRoomIds);
                        });
                })
                ->exists();

            if ($hasConflict) {
                DB::rollBack();
                return back()->withErrors([
                    'room_ids' => 'One or more selected rooms are already booked for the selected dates.',
                ])->withInput();
            }

            $totalCapacity = (int) $selectedRooms->sum(fn ($room) => (int) ($room->roomType?->max_guests ?? 0));
            if ($validated['number_of_guests'] > $totalCapacity) {
                DB::rollBack();
                return back()->withErrors([
                    'number_of_guests' => 'Selected rooms can only accommodate up to ' . $totalCapacity . ' guests.',
                ])->withInput();
            }

            $nightlyTotal = (float) $selectedRooms->sum(fn ($room) => (float) ($room->roomType?->base_price ?? 0));

            // Calculate costs using base room rate (discounts removed globally)
            $subtotal = $nightlyTotal * $validated['total_nights'];
            $extrasTotal = 0;

            // Get selected extras with quantities
            $selectedExtras = [];
            if (!empty($validated['extras'])) {
                $extras = Extra::whereIn('id', $validated['extras'])->get();
                foreach ($extras as $extra) {
                    $quantity = $validated['extra_quantities'][$extra->id] ?? 1;
                    $lineTotal = $extra->price * $quantity;
                    $extrasTotal += $lineTotal;
                    $selectedExtras[] = [
                        'extra_id' => $extra->id,
                        'quantity' => $quantity,
                        'price_at_booking' => $extra->price
                    ];
                }
            }

            // VAT removed globally
            $taxAmount = 0;
            $totalAmount = $subtotal + $extrasTotal;

            // Generate unique booking reference
            $bookingReference = 'BEZ-' . strtoupper(Str::random(8));

            // Create booking
            $booking = Booking::create([
                'booking_reference' => $bookingReference,
                'guest_id' => $guest->id,
                'room_id' => $selectedRooms->first()->id,
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'number_of_guests' => $validated['number_of_guests'],
                'total_nights' => $validated['total_nights'],
                'subtotal' => $subtotal,
                'extras_total' => $extrasTotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_option' => $validated['payment_option'],
                'status' => 'pending',
                'expires_at' => now()->addHours(8),
                'special_requests' => $validated['special_requests']
            ]);

            foreach ($selectedRooms as $selectedRoom) {
                $booking->rooms()->attach($selectedRoom->id, [
                    'nightly_rate' => (float) ($selectedRoom->roomType?->base_price ?? 0),
                ]);
            }

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

            // Redirect to payment page
            return redirect()->route('booking.payment', ['reference' => $bookingReference])
                ->with('success', 'Booking created! Please complete the down payment to confirm your reservation.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->withErrors([
                'error' => 'An error occurred while processing your booking. Please try again.'
            ])->withInput();
        }
    }

    public function getAvailableRooms(Request $request)
    {
        $validated = $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $rooms = Room::with('roomType')
            ->where('status', 'available')
            ->whereNull('archived_at')
            ->whereDoesntHave('bookings', function ($query) use ($validated) {
                $query->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled'])
                    ->where('check_in_date', '<', $validated['check_out_date'])
                    ->where('check_out_date', '>', $validated['check_in_date']);
            })
            ->whereDoesntHave('reservationBookings', function ($query) use ($validated) {
                $query->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled'])
                    ->where('check_in_date', '<', $validated['check_out_date'])
                    ->where('check_out_date', '>', $validated['check_in_date']);
            })
            ->orderBy('room_type_id')
            ->orderBy('room_number')
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'room_type' => $room->roomType?->name,
                    'capacity' => (int) ($room->roomType?->max_guests ?? 0),
                    'price' => (float) ($room->roomType?->base_price ?? 0),
                ];
            })
            ->values();

        $remainingByType = $rooms
            ->groupBy('room_type')
            ->map(fn ($items, $type) => [
                'room_type' => $type,
                'remaining' => $items->count(),
            ])
            ->values();

        return response()->json([
            'rooms' => $rooms,
            'remaining_by_type' => $remainingByType,
        ]);
    }

    public function payment($reference)
    {
        $booking = Booking::with(['guest', 'room.roomType', 'rooms.roomType', 'extras'])
            ->where('booking_reference', $reference)
            ->firstOrFail();

        // Calculate payment amount based on option
        if ($booking->payment_option === 'full_payment') {
            $paymentAmount = $booking->total_amount;
            $paymentPercentage = 100.00;
        } else {
            $paymentAmount = $booking->total_amount * 0.30;
            $paymentPercentage = 30.00;
        }

        return view('customer.booking.payment', compact('booking', 'paymentAmount', 'paymentPercentage'));
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

            // Calculate payment amount based on option
            $paymentPercentage = $booking->payment_option === 'full_payment' ? 100.00 : 30.00;
            $paymentAmount = $booking->total_amount * ($paymentPercentage / 100);

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_type' => $booking->payment_option,
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'],
                'amount' => $paymentAmount,
                'percentage' => $paymentPercentage,
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
        $booking = Booking::with(['guest', 'room.roomType', 'rooms.roomType', 'extras', 'payments'])
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
        $isBooked = Booking::query()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled'])
            ->where('check_in_date', '<', $validated['check_out_date'])
            ->where('check_out_date', '>', $validated['check_in_date'])
            ->where(function ($query) use ($validated) {
                $query->where('room_id', $validated['room_id'])
                    ->orWhereHas('rooms', function ($q) use ($validated) {
                        $q->where('rooms.id', $validated['room_id']);
                    });
            })
            ->exists();

        return response()->json([
            'available' => !$isBooked,
            'message' => $isBooked ? 'Room is not available for selected dates.' : 'Room is available!'
        ]);
    }

    public function downloadPDF($reference)
    {
        $booking = Booking::with(['guest', 'room.roomType', 'rooms.roomType', 'extras', 'payments'])
            ->where('booking_reference', $reference)
            ->firstOrFail();

        $pdf = Pdf::loadView('customer.booking.pdf', compact('booking'));
        
        return $pdf->download('booking-'.$reference.'.pdf');
    }
}
