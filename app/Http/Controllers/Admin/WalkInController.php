<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Extra;
use App\Models\Guest;
use App\Models\Payment;
use App\Models\Room;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WalkInController extends Controller
{
    public function create()
    {
        $checkIn = Carbon::now()->format('Y-m-d');
        $checkOut = Carbon::now()->addDay()->format('Y-m-d');

        $availableRooms = $this->availableRoomsQuery($checkIn, $checkOut)->get();
        $extras = Extra::where('is_active', true)->orderBy('name')->get();

        return view('admin.bookings.walk-in', compact('availableRooms', 'extras', 'checkIn', 'checkOut'));
    }

    public function getAvailableRooms(Request $request)
    {
        $validated = $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        $rooms = $this->availableRoomsQuery($validated['check_in_date'], $validated['check_out_date'])
            ->orderBy('room_type_id')
            ->orderBy('room_number')
            ->get()
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'room_type' => $room->roomType->name,
                    'price' => (float) $room->roomType->base_price,
                    'label' => $room->roomType->name . ' - Room ' . $room->room_number . ' (PHP ' . number_format($room->roomType->base_price, 2) . '/night)',
                ];
            })
            ->values();

        return response()->json(['rooms' => $rooms]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'required|string|max:11',
            'address'         => 'nullable|string|max:500',
            'id_photo'        => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'check_in_date'   => 'required|date',
            'check_out_date'  => 'required|date|after:check_in_date',
            'number_of_rooms' => 'required|integer|min:1|max:5',
            'room_ids'        => 'required|array|min:1',
            'room_ids.*'      => 'required|distinct|exists:rooms,id',
            'number_of_guests'=> 'required|integer|min:1|max:10',
            'payment_method'  => 'required|in:cash,gcash',
            'payment_type'    => 'required|in:full_payment',
            'extras'          => 'nullable|array',
            'extras.*'        => 'exists:extras,id',
            'extra_quantities' => 'nullable|array',
            'special_requests'=> 'nullable|string|max:1000',
        ]);

        $checkIn  = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $nights   = $checkIn->diffInDays($checkOut);

        if ((int) $validated['number_of_rooms'] !== count($validated['room_ids'])) {
            return back()->withErrors(['room_ids' => 'Please select exactly ' . $validated['number_of_rooms'] . ' room(s).'])->withInput();
        }

        $selectedRoomIds = collect($validated['room_ids'])->map(fn($id) => (int) $id)->values();
        $availableRoomIds = $this->availableRoomsQuery($validated['check_in_date'], $validated['check_out_date'])
            ->whereIn('id', $selectedRoomIds)
            ->pluck('id');

        if ($availableRoomIds->count() !== $selectedRoomIds->count()) {
            return back()->withErrors(['room_ids' => 'One or more selected rooms are no longer available for the selected dates.'])->withInput();
        }

        $rooms = Room::with('roomType')->whereIn('id', $selectedRoomIds)->get()->keyBy('id');

        $selectedExtras = [];
        $extrasTotal = 0;
        if (!empty($validated['extras'])) {
            $extras = Extra::whereIn('id', $validated['extras'])->where('is_active', true)->get();
            foreach ($extras as $extra) {
                $quantity = max(1, (int) ($validated['extra_quantities'][$extra->id] ?? 1));
                $lineTotal = (float) $extra->price * $quantity;
                $extrasTotal += $lineTotal;
                $selectedExtras[] = [
                    'id' => $extra->id,
                    'quantity' => $quantity,
                    'price_at_booking' => $extra->price,
                ];
            }
        }

        $paymentType = 'full_payment';

        // Handle ID photo upload
        $idPhotoPath = null;
        if ($request->hasFile('id_photo')) {
            $idPhotoPath = $request->file('id_photo')->store('guests/id_photos', 'public');
        }

        // Always update guest info with what admin entered (match by phone)
        $guestData = [
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'] ?? null,
            'address'    => $validated['address'] ?? null,
        ];
        if ($idPhotoPath) {
            $guestData['id_photo'] = $idPhotoPath;
        }
        $guest = Guest::updateOrCreate(['phone' => $validated['phone']], $guestData);

        DB::beginTransaction();
        try {
            $createdBookings = [];
            $overallTotal = 0;

            foreach ($selectedRoomIds as $roomId) {
                $room = $rooms->get($roomId);
                $roomSubtotal = (float) $room->roomType->base_price * $nights;
                $totalAmount = round($roomSubtotal + $extrasTotal, 2);
                $reference = 'WI-' . strtoupper(Str::random(8));

                // Create booking (walk-ins are immediately checked-in)
                $booking = Booking::create([
                    'booking_reference' => $reference,
                    'guest_id' => $guest->id,
                    'room_id' => $room->id,
                    'check_in_date' => $validated['check_in_date'],
                    'check_out_date' => $validated['check_out_date'],
                    'number_of_guests' => $validated['number_of_guests'],
                    'total_nights' => $nights,
                    'subtotal' => $roomSubtotal,
                    'extras_total' => $extrasTotal,
                    'tax_amount' => 0,
                    'total_amount' => $totalAmount,
                    'payment_option' => $paymentType,
                    'status' => 'checked_in',
                    'special_requests' => $validated['special_requests'] ?? null,
                ]);

                if (!empty($selectedExtras)) {
                    foreach ($selectedExtras as $extra) {
                        $booking->extras()->attach($extra['id'], [
                            'quantity' => $extra['quantity'],
                            'price_at_booking' => $extra['price_at_booking'],
                        ]);
                    }
                }

                // Mark room as occupied
                $room->update(['status' => 'occupied']);

                // Record payment (walk-in is full payment only)
                Payment::create([
                    'booking_id' => $booking->id,
                    'payment_type' => $paymentType,
                    'payment_method' => $validated['payment_method'],
                    'payment_reference' => $reference,
                    'amount' => $totalAmount,
                    'percentage' => 100.00,
                    'payment_status' => 'completed',
                    'payment_date' => now(),
                    'payment_notes' => 'Walk-in booking. Recorded by admin.',
                    'verified_at' => now(),
                    'verified_by' => auth()->id(),
                ]);

                $createdBookings[] = $booking;
                $overallTotal += $totalAmount;
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create walk-in booking: ' . $e->getMessage()])->withInput();
        }

        $firstBooking = $createdBookings[0];
        $roomSummary = $rooms->whereIn('id', $selectedRoomIds)->pluck('room_number')->implode(', ');

        // Log activity
        ActivityLog::log(
            'walk_in_booking',
            'Walk-in booking created for ' . $guest->name . ' — Rooms ' . $roomSummary,
            'App\Models\Booking',
            $firstBooking->id,
            [
                'guest'          => $guest->name,
                'rooms'          => $roomSummary,
                'check_in'       => $validated['check_in_date'],
                'check_out'      => $validated['check_out_date'],
                'bookings_count' => count($createdBookings),
                'total_amount'   => round($overallTotal, 2),
                'payment_method' => $validated['payment_method'],
            ]
        );

        return redirect()->route('admin.bookings.show', $firstBooking)
            ->with('success', 'Walk-in booking created successfully for ' . $guest->name . '. Created ' . count($createdBookings) . ' booking(s).');
    }

    private function availableRoomsQuery(string $checkInDate, string $checkOutDate)
    {
        return Room::with('roomType')
            ->where('status', 'available')
            ->whereNull('archived_at')
            ->whereDoesntHave('bookings', function ($query) use ($checkInDate, $checkOutDate) {
                $query->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled'])
                    ->where(function ($overlap) use ($checkInDate, $checkOutDate) {
                        $overlap->where('check_in_date', '<', $checkOutDate)
                            ->where('check_out_date', '>', $checkInDate);
                    });
            });
    }
}
