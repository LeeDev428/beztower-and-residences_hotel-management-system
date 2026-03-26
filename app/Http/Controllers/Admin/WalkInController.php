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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WalkInController extends Controller
{
    private const EXCLUDED_WALKIN_EXTRA_NAMES = [
        'Late Check-Out',
        'Early Check-In',
    ];

    public function create()
    {
        $checkIn = Carbon::now()->format('Y-m-d');
        $checkOut = Carbon::now()->addDay()->format('Y-m-d');

        $availableRooms = $this->availableRoomsQuery($checkIn, $checkOut)->get();
        $extras = Extra::where('is_active', true)
            ->whereNotIn('name', self::EXCLUDED_WALKIN_EXTRA_NAMES)
            ->orderBy('name')
            ->get();

        return view('admin.bookings.walk-in', compact('availableRooms', 'extras', 'checkIn', 'checkOut'));
    }

    public function getAvailableRooms(Request $request)
    {
        $validated = $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after_or_equal:check_in_date',
        ]);

        $rooms = $this->availableRoomsQuery($validated['check_in_date'], $validated['check_out_date'])
            ->orderBy('room_type_id')
            ->orderBy('room_number')
            ->get()
            ->filter(function ($room) {
                return $room->roomType !== null;
            })
            ->map(function ($room) {
                $roomTypeName = $room->roomType?->name ?? 'Room';
                $basePrice = (float) ($room->roomType?->base_price ?? 0);
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'room_type' => $roomTypeName,
                    'capacity' => (int) ($room->roomType?->max_guests ?? 0),
                    'price' => (float) ($room->effective_price ?? $basePrice),
                    'label' => $roomTypeName . ' - Room ' . $room->room_number . ' (PHP ' . number_format((float) ($room->effective_price ?? $basePrice), 2) . '/night)',
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
            'check_out_date'  => 'required|date|after_or_equal:check_in_date',
            'number_of_rooms' => 'required|integer|min:1|max:5',
            'room_ids'        => 'required|array|min:1',
            'room_ids.*'      => 'required|distinct|exists:rooms,id',
            'number_of_guests'=> 'nullable|integer|min:1|max:30',
            'adults'          => 'nullable|integer|min:1|max:30',
            'children'        => 'nullable|integer|min:0|max:30',
            'payment_method'  => 'required|in:cash,gcash',
            'payment_reference' => 'nullable|string|max:255|required_if:payment_method,gcash',
            'payment_type'    => 'required|in:full_payment',
            'extras'          => 'nullable|array',
            'extras.*'        => 'exists:extras,id',
            'extra_quantities' => 'nullable|array',
            'special_requests'=> 'nullable|string|max:1000',
        ]);

        $effectiveAdults = $this->resolveEffectiveAdults($validated);

        $checkIn  = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $nights   = max(1, $checkIn->diffInDays($checkOut));

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

        $selectedRooms = Room::with('roomType')->whereIn('id', $selectedRoomIds)->get();
        $totalCapacity = (int) $selectedRooms->sum(fn ($room) => (int) ($room->roomType?->max_guests ?? 0));

        if ($totalCapacity < $effectiveAdults) {
            return back()->withErrors([
                'number_of_guests' => 'Selected room(s) can only accommodate up to ' . $totalCapacity . ' effective adult(s).',
            ])->withInput();
        }

        $selectedExtras = [];
        $extrasTotal = 0;
        if (!empty($validated['extras'])) {
            $extras = Extra::whereIn('id', $validated['extras'])
                ->where('is_active', true)
                ->whereNotIn('name', self::EXCLUDED_WALKIN_EXTRA_NAMES)
                ->get();
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
            $overallTotal = 0;
            $verifiedBy = Auth::id();
            $nightlySubtotal = (float) $selectedRooms->sum(fn ($room) => (float) ($room->effective_price ?? 0));
            $overallSubtotal = $nightlySubtotal * $nights;
            $overallTotal = round($overallSubtotal + $extrasTotal, 2);
            $taxAmount = round($overallSubtotal * (12 / 112), 2);
            $reference = 'WI-' . strtoupper(Str::random(8));

            // Create one reservation (walk-ins are immediately checked-in)
            $booking = Booking::create([
                'booking_reference' => $reference,
                'guest_id' => $guest->id,
                'room_id' => (int) $selectedRoomIds->first(),
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'number_of_guests' => $effectiveAdults,
                'total_nights' => $nights,
                'subtotal' => $overallSubtotal,
                'extras_total' => $extrasTotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $overallTotal,
                'payment_option' => $paymentType,
                'status' => 'checked_in',
                'special_requests' => $validated['special_requests'] ?? null,
            ]);

            /** @var \App\Models\Room $selectedRoom */
            foreach ($selectedRooms as $selectedRoom) {
                $booking->rooms()->attach($selectedRoom->id, [
                    'nightly_rate' => (float) ($selectedRoom->effective_price ?? 0),
                ]);
                $selectedRoom->update(['status' => 'occupied']);
            }

            if (!empty($selectedExtras)) {
                foreach ($selectedExtras as $extra) {
                    $booking->extras()->attach($extra['id'], [
                        'quantity' => $extra['quantity'],
                        'price_at_booking' => $extra['price_at_booking'],
                    ]);
                }
            }

            // Record one payment for the whole reservation
            Payment::create([
                'booking_id' => $booking->id,
                'payment_type' => $paymentType,
                'payment_method' => $validated['payment_method'],
                'payment_reference' => ($validated['payment_reference'] ?? null) ?: $reference,
                'amount' => $overallTotal,
                'percentage' => 100.00,
                'payment_status' => 'completed',
                'payment_date' => now(),
                'payment_notes' => 'Walk-in booking. Recorded by admin.',
                'verified_at' => now(),
                'verified_by' => $verifiedBy,
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create walk-in booking: ' . $e->getMessage()])->withInput();
        }

        $roomSummary = Room::whereIn('id', $selectedRoomIds)->orderBy('room_number')->pluck('room_number')->implode(', ');

        // Log activity
        ActivityLog::log(
            'walk_in_booking',
            'Walk-in booking created for ' . $guest->name . ' — Rooms ' . $roomSummary,
            'App\Models\Booking',
            $booking->id,
            [
                'guest'          => $guest->name,
                'rooms'          => $roomSummary,
                'check_in'       => $validated['check_in_date'],
                'check_out'      => $validated['check_out_date'],
                'bookings_count' => 1,
                'total_amount'   => round($overallTotal, 2),
                'payment_method' => $validated['payment_method'],
            ]
        );

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Walk-in booking created successfully for ' . $guest->name . '. Reservation includes ' . count($selectedRoomIds) . ' room(s).');
    }

    private function availableRoomsQuery(string $checkInDate, string $checkOutDate)
    {
        return Room::with('roomType')
            ->whereIn('status', ['available', 'dirty', 'occupied'])
            ->whereNull('archived_at')
            ->whereHas('roomType')
            ->whereDoesntHave('bookings', function ($query) use ($checkInDate, $checkOutDate) {
                Booking::applyActiveReservationFilter($query);
                Booking::applyDateConflictWindow($query, (string) $checkInDate, (string) $checkOutDate);
            })
            ->whereDoesntHave('reservationBookings', function ($query) use ($checkInDate, $checkOutDate) {
                Booking::applyActiveReservationFilter($query);
                Booking::applyDateConflictWindow($query, (string) $checkInDate, (string) $checkOutDate);
            });
    }

    private function resolveEffectiveAdults(array $validated): int
    {
        $adults = (int) ($validated['adults'] ?? 0);
        $children = (int) ($validated['children'] ?? 0);

        if ($adults <= 0 && $children <= 0) {
            return max(1, (int) ($validated['number_of_guests'] ?? 1));
        }

        // Capacity logic: every 2 children count as 1 adult, remaining 1 child is free.
        return max(1, $adults + intdiv(max(0, $children), 2));
    }
}
