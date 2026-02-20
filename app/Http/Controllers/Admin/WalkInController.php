<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\Room;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WalkInController extends Controller
{
    public function create()
    {
        $availableRooms = Room::with('roomType')
            ->where('status', 'available')
            ->whereNull('archived_at')
            ->get();

        return view('admin.bookings.walk-in', compact('availableRooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'required|string|max:11',
            'address'         => 'nullable|string|max:500',
            'id_type'         => 'nullable|string|max:100',
            'room_id'         => 'required|exists:rooms,id',
            'check_in_date'   => 'required|date',
            'check_out_date'  => 'required|date|after:check_in_date',
            'number_of_guests'=> 'required|integer|min:1|max:10',
            'payment_method'  => 'required|in:cash,gcash',
            'payment_type'    => 'required|in:full_payment,down_payment',
            'special_requests'=> 'nullable|string|max:1000',
        ]);

        $room = Room::with('roomType')->findOrFail($validated['room_id']);

        $checkIn  = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $nights   = $checkIn->diffInDays($checkOut);

        $subtotal    = $room->roomType->base_price * $nights;
        $totalAmount = $subtotal;

        // Create or find guest (match by phone)
        $guest = Guest::firstOrCreate(
            ['phone' => $validated['phone']],
            [
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'] ?? null,
                'address'    => $validated['address'] ?? null,
                'preferences' => $validated['id_type'] ? 'ID Type: ' . $validated['id_type'] : null,
            ]
        );

        // Generate booking reference
        $reference = 'WI-' . strtoupper(Str::random(8));

        // Determine payment amount
        $paymentAmount = $validated['payment_type'] === 'down_payment'
            ? round($totalAmount * 0.30, 2)
            : $totalAmount;

        // Create booking (walk-ins are immediately checked-in)
        $booking = Booking::create([
            'booking_reference' => $reference,
            'guest_id'          => $guest->id,
            'room_id'           => $room->id,
            'check_in_date'     => $validated['check_in_date'],
            'check_out_date'    => $validated['check_out_date'],
            'number_of_guests'  => $validated['number_of_guests'],
            'total_nights'      => $nights,
            'subtotal'          => $subtotal,
            'extras_total'      => 0,
            'tax_amount'        => 0,
            'total_amount'      => $totalAmount,
            'payment_option'    => $validated['payment_type'],
            'status'            => 'checked_in',
            'special_requests'  => $validated['special_requests'] ?? null,
        ]);

        // Mark room as occupied
        $room->update(['status' => 'occupied']);

        // Record payment
        Payment::create([
            'booking_id'      => $booking->id,
            'payment_type'    => $validated['payment_type'],
            'payment_method'  => $validated['payment_method'],
            'amount'          => $paymentAmount,
            'percentage'      => $validated['payment_type'] === 'down_payment' ? 30.00 : 100.00,
            'payment_status'  => 'completed',
            'payment_date'    => now(),
            'payment_notes'   => 'Walk-in booking. Recorded by admin.',
            'verified_at'     => now(),
            'verified_by'     => auth()->id(),
        ]);

        // Log activity
        ActivityLog::log(
            'walk_in_booking',
            'Walk-in booking created: #' . $reference . ' for ' . $guest->name . ' — Room ' . $room->room_number,
            'App\Models\Booking',
            $booking->id,
            [
                'guest'          => $guest->name,
                'room'           => $room->room_number,
                'check_in'       => $validated['check_in_date'],
                'check_out'      => $validated['check_out_date'],
                'total_amount'   => $totalAmount,
                'payment_method' => $validated['payment_method'],
            ]
        );

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Walk-in booking #' . $reference . ' created successfully for ' . $guest->name . '!');
    }
}
