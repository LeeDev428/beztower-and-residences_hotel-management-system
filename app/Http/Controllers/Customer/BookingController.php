<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function checkout(Request $request, Room $room)
    {
        $room->load(['roomType', 'photos']);

        $roomIdsInput = $request->input('room_ids', []);
        $preselectedRoomIds = collect();

        if (is_array($roomIdsInput)) {
            $preselectedRoomIds = collect($roomIdsInput);
        } elseif (is_string($roomIdsInput) && trim($roomIdsInput) !== '') {
            $preselectedRoomIds = collect(explode(',', $roomIdsInput));
        }

        // Compatibility: some query strings may arrive as room_ids[0]=... keys.
        foreach ($request->query() as $key => $value) {
            if (preg_match('/^room_ids\[\d+\]$/', (string) $key)) {
                $preselectedRoomIds->push($value);
            }
        }

        if ($request->filled('selected_rooms')) {
            $selectedRoomsFromQuery = collect(explode(',', (string) $request->input('selected_rooms')));
            $preselectedRoomIds = $preselectedRoomIds->merge($selectedRoomsFromQuery);
        }

        $preselectedRoomIds = $preselectedRoomIds
            ->map(fn ($id) => (int) trim((string) $id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($preselectedRoomIds->isEmpty() && $request->filled('room_id')) {
            $preselectedRoomIds = collect([(int) $request->input('room_id')])
                ->filter(fn ($id) => $id > 0)
                ->values();
        }

        if ($preselectedRoomIds->isEmpty()) {
            $preselectedRoomIds = collect([(int) $room->id]);
        }

        $preselectedRooms = Room::with(['roomType', 'photos'])
            ->whereIn('id', $preselectedRoomIds)
            ->whereNull('archived_at')
            ->whereHas('roomType', function ($roomTypeQuery) {
                $roomTypeQuery->whereNull('archived_at');
            })
            ->get();

        $preselectedRooms = $preselectedRooms
            ->sortBy(fn (Room $selectedRoom) => $preselectedRoomIds->search((int) $selectedRoom->id))
            ->values();

        $resolvedPreselectedIds = $preselectedRooms
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $requestedRooms = (int) $request->integer('rooms', $preselectedRooms->count() ?: 1);
        $requestedRooms = max(1, min(12, $requestedRooms));

        // For multi-room bookings, force explicit room-by-room selection first.
        // This avoids auto-populating unselected rooms on checkout.
        if ($requestedRooms > 1 && $preselectedRoomIds->count() < $requestedRooms) {
            $selectedForFlow = $preselectedRoomIds;

            $remainingRooms = max($requestedRooms - $selectedForFlow->count(), 0);

            return redirect()->route('rooms.index', [
                'check_in' => $request->input('check_in'),
                'check_out' => $request->input('check_out'),
                'guests' => $request->input('guests'),
                'adults' => $request->input('adults'),
                'children' => $request->input('children'),
                'rooms' => $requestedRooms,
                'selected_rooms' => $selectedForFlow->implode(','),
            ])->with('warning', 'Please select ' . $remainingRooms . ' more room(s) before checkout.');
        }

        $maxGuestCapacity = (int) $preselectedRooms->sum(fn ($selectedRoom) => (int) ($selectedRoom->roomType?->max_guests ?? 0));
        if ($maxGuestCapacity <= 0) {
            $maxGuestCapacity = (int) ($room->roomType?->max_guests ?? 1);
        }

        $policySettings = AppSetting::getMany([
            'terms_and_conditions',
            'booking_policies',
        ]);

        $termsAndConditionsText = (string) ($policySettings['terms_and_conditions'] ?? '');
        $bookingPoliciesText = (string) ($policySettings['booking_policies'] ?? '');

        return view('customer.booking.checkout', compact(
            'room',
            'preselectedRooms',
            'requestedRooms',
            'maxGuestCapacity',
            'termsAndConditionsText',
            'bookingPoliciesText'
        ));
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
            'terms_accepted' => 'accepted',
            'extras' => 'nullable|array',
            'extras.*' => 'exists:extras,id',
            'extra_quantities' => 'nullable|array',
            'extra_quantities.*' => 'integer|min:1|max:50',
            'submission_key' => 'nullable|string|max:120',
        ]);

        $submissionKey = trim((string) ($validated['submission_key'] ?? ''));
        $dedupeRoomIds = array_map('intval', $validated['room_ids'] ?? []);
        if (empty($dedupeRoomIds) && !empty($validated['room_id'])) {
            $dedupeRoomIds = [(int) $validated['room_id']];
        }
        $dedupePayload = [
            'session' => $request->session()->getId(),
            'email' => strtolower((string) ($validated['email'] ?? '')),
            'check_in' => (string) ($validated['check_in_date'] ?? ''),
            'check_out' => (string) ($validated['check_out_date'] ?? ''),
            'rooms' => $dedupeRoomIds,
            'guests' => (int) ($validated['number_of_guests'] ?? 0),
            'submission_key' => $submissionKey,
        ];
        sort($dedupePayload['rooms']);
        $dedupeKey = 'booking:submit:' . hash('sha256', json_encode($dedupePayload));

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

        if (!Cache::add($dedupeKey, true, now()->addMinutes(2))) {
            return back()->withErrors([
                'error' => 'Your reservation is already being processed. Please wait a moment and avoid submitting multiple times.',
            ])->withInput();
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

            $duplicateBooking = $this->findRecentDuplicateBooking(
                (int) $guest->id,
                $selectedRoomIds,
                (string) $validated['check_in_date'],
                (string) $validated['check_out_date']
            );

            if ($duplicateBooking) {
                DB::rollBack();

                return redirect()->route('booking.payment', ['reference' => $duplicateBooking->booking_reference])
                    ->with('warning', 'Reservation already submitted. Redirected to your existing booking payment page.');
            }

            $selectedRooms = Room::with('roomType')
                ->whereIn('id', $selectedRoomIds)
                ->lockForUpdate()
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
                ->tap(fn ($query) => Booking::applyActiveReservationFilter($query))
                ->tap(fn ($query) => Booking::applyDateConflictWindow($query, (string) $validated['check_in_date'], (string) $validated['check_out_date']))
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

            $nightlyTotal = (float) $selectedRooms->sum(fn ($room) => (float) ($room->effective_price ?? 0));

            // Room price is VAT-inclusive; compute VAT portion for reporting.
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

            $taxAmount = round($subtotal * (12 / 112), 2);
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
                    'nightly_rate' => (float) ($selectedRoom->effective_price ?? 0),
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
            Cache::forget($dedupeKey);
            
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
            'number_of_rooms' => 'nullable|integer|min:1|max:12',
            'number_of_guests' => 'nullable|integer|min:1|max:50',
        ]);

        $requestedRooms = max(1, min(12, (int) ($validated['number_of_rooms'] ?? 1)));
        $requestedGuests = max(1, (int) ($validated['number_of_guests'] ?? 1));

        $availableRooms = Room::with('roomType')
            ->where('status', 'available')
            ->whereNull('archived_at')
            ->whereHas('roomType', function ($roomTypeQuery) {
                $roomTypeQuery->whereNull('archived_at');
            })
            ->whereDoesntHave('bookings', function ($query) use ($validated) {
                Booking::applyActiveReservationFilter($query);
                Booking::applyDateConflictWindow($query, (string) $validated['check_in_date'], (string) $validated['check_out_date']);
            })
            ->whereDoesntHave('reservationBookings', function ($query) use ($validated) {
                Booking::applyActiveReservationFilter($query);
                Booking::applyDateConflictWindow($query, (string) $validated['check_in_date'], (string) $validated['check_out_date']);
            })
            ->orderBy('room_type_id')
            ->orderBy('room_number')
            ->get();

        $recommendedRoomIds = [];
        if ($requestedRooms > 1) {
            $combinationMeta = $this->resolveAvailabilityCombinationMeta($availableRooms, $requestedRooms, $requestedGuests);
            $eligibleTypeIds = $combinationMeta['eligible_type_ids'] ?? [];
            $recommendedRoomIds = $combinationMeta['recommended_room_ids'] ?? [];

            if (empty($eligibleTypeIds)) {
                $availableRooms = collect();
            } else {
                $availableRooms = $availableRooms
                    ->filter(fn ($room) => in_array((int) $room->room_type_id, $eligibleTypeIds, true))
                    ->values();
            }
        }

        $rooms = $availableRooms
            ->map(function ($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'room_type_id' => (int) $room->room_type_id,
                    'room_type' => $room->roomType?->name,
                    'capacity' => (int) ($room->roomType?->max_guests ?? 0),
                    'price' => (float) ($room->effective_price ?? 0),
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
            'recommended_room_ids' => array_values(array_map('intval', $recommendedRoomIds)),
        ]);
    }

    private function resolveAvailabilityCombinationMeta(Collection $availableRooms, int $requestedRooms, int $requestedGuests): array
    {
        if ($requestedRooms <= 1 || $availableRooms->isEmpty()) {
            return [
                'eligible_type_ids' => [],
                'recommended_room_ids' => [],
            ];
        }

        $typeBuckets = $availableRooms
            ->groupBy('room_type_id')
            ->map(function (Collection $group, $typeId) {
                $sortedRooms = $group->sortBy('room_number')->values();
                $capacity = (int) optional($sortedRooms->first()->roomType)->max_guests;

                return [
                    'type_id' => (int) $typeId,
                    'capacity' => max(0, $capacity),
                    'count' => $sortedRooms->count(),
                    'rooms' => $sortedRooms,
                ];
            })
            ->filter(fn ($bucket) => $bucket['count'] > 0 && $bucket['capacity'] > 0)
            ->values()
            ->all();

        if (empty($typeBuckets)) {
            return [
                'eligible_type_ids' => [],
                'recommended_room_ids' => [],
            ];
        }

        $combinations = [];
        $this->buildAvailabilityTypeCombinations(
            $typeBuckets,
            0,
            $requestedRooms,
            0,
            $requestedGuests,
            [],
            $combinations
        );

        if (empty($combinations)) {
            return [
                'eligible_type_ids' => [],
                'recommended_room_ids' => [],
            ];
        }

        $eligibleTypeIds = [];
        foreach ($combinations as $combination) {
            foreach (($combination['type_counts'] ?? []) as $typeId => $count) {
                if ($count > 0) {
                    $eligibleTypeIds[(int) $typeId] = true;
                }
            }
        }

        usort($combinations, function ($left, $right) {
            $leftExcess = (int) ($left['excess'] ?? PHP_INT_MAX);
            $rightExcess = (int) ($right['excess'] ?? PHP_INT_MAX);
            if ($leftExcess !== $rightExcess) {
                return $leftExcess <=> $rightExcess;
            }

            return (int) ($left['total_capacity'] ?? PHP_INT_MAX) <=> (int) ($right['total_capacity'] ?? PHP_INT_MAX);
        });

        $bestTypeCounts = $combinations[0]['type_counts'] ?? [];
        $recommendedRoomIds = [];

        foreach ($typeBuckets as $bucket) {
            $typeId = (int) $bucket['type_id'];
            $pick = (int) ($bestTypeCounts[$typeId] ?? 0);
            if ($pick <= 0) {
                continue;
            }

            $pickedRoomIds = collect($bucket['rooms'])
                ->take($pick)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $recommendedRoomIds = array_merge($recommendedRoomIds, $pickedRoomIds);
        }

        return [
            'eligible_type_ids' => array_values(array_map('intval', array_keys($eligibleTypeIds))),
            'recommended_room_ids' => array_slice(array_values(array_unique($recommendedRoomIds)), 0, $requestedRooms),
        ];
    }

    private function buildAvailabilityTypeCombinations(
        array $types,
        int $index,
        int $remainingRooms,
        int $currentCapacity,
        int $requestedGuests,
        array $currentTypeCounts,
        array &$combinations
    ): void {
        if ($remainingRooms === 0) {
            if ($currentCapacity >= $requestedGuests) {
                $combinations[] = [
                    'type_counts' => $currentTypeCounts,
                    'total_capacity' => $currentCapacity,
                    'excess' => $currentCapacity - $requestedGuests,
                ];
            }
            return;
        }

        if ($index >= count($types)) {
            return;
        }

        $type = $types[$index];
        $maxPick = min($remainingRooms, (int) ($type['count'] ?? 0));

        for ($pick = 0; $pick <= $maxPick; $pick++) {
            $nextCounts = $currentTypeCounts;
            if ($pick > 0) {
                $nextCounts[(int) $type['type_id']] = $pick;
            }

            $this->buildAvailabilityTypeCombinations(
                $types,
                $index + 1,
                $remainingRooms - $pick,
                $currentCapacity + ($pick * (int) ($type['capacity'] ?? 0)),
                $requestedGuests,
                $nextCounts,
                $combinations
            );
        }
    }

    public function payment($reference)
    {
        $booking = Booking::with(['guest', 'room.roomType', 'rooms.roomType', 'extras'])
            ->where('booking_reference', $reference)
            ->firstOrFail();

        $existingSubmittedPayment = $booking->payments()
            ->whereIn('payment_status', ['pending', 'verified', 'completed'])
            ->latest('id')
            ->first();

        // Calculate payment amount based on option
        if ($booking->payment_option === 'full_payment') {
            $paymentAmount = $booking->total_amount;
            $paymentPercentage = 100.00;
        } else {
            $paymentAmount = $booking->total_amount * 0.30;
            $paymentPercentage = 30.00;
        }

        return view('customer.booking.payment', compact('booking', 'paymentAmount', 'paymentPercentage', 'existingSubmittedPayment'));
    }

    public function processPayment(Request $request, $reference)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:gcash,paymaya,bank_transfer',
            'payment_reference' => 'required|string|max:255',
            'proof_of_payment' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
        ]);

        $booking = Booking::where('booking_reference', $reference)->firstOrFail();

        $existingSubmittedPayment = $booking->payments()
            ->whereIn('payment_status', ['pending', 'verified', 'completed'])
            ->latest('id')
            ->first();

        if ($existingSubmittedPayment) {
            return redirect()->route('booking.payment', ['reference' => $reference])
                ->withErrors([
                    'error' => 'Payment proof was already submitted for this booking. If you already submitted, please wait for verification.',
                ]);
        }

        try {
            DB::beginTransaction();

            // Store proof of payment
            $proofPath = $request->file('proof_of_payment')->store('payments/proofs', 'public');

            // Calculate payment amount based on option
            $paymentPercentage = $booking->payment_option === 'full_payment' ? 100.00 : 30.00;
            $paymentAmount = $booking->total_amount * ($paymentPercentage / 100);

            // Create payment record
            Payment::create([
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
            ->tap(fn ($query) => Booking::applyActiveReservationFilter($query))
            ->tap(fn ($query) => Booking::applyDateConflictWindow($query, (string) $validated['check_in_date'], (string) $validated['check_out_date']))
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

    private function findRecentDuplicateBooking(int $guestId, Collection $selectedRoomIds, string $checkInDate, string $checkOutDate): ?Booking
    {
        $normalizedSelectedRoomIds = $selectedRoomIds
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (empty($normalizedSelectedRoomIds)) {
            return null;
        }

        $recentBookings = Booking::with('rooms:id')
            ->where('guest_id', $guestId)
            ->whereDate('check_in_date', $checkInDate)
            ->whereDate('check_out_date', $checkOutDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('created_at', '>=', now()->subMinutes(10))
            ->latest('id')
            ->get();

        foreach ($recentBookings as $recentBooking) {
            $existingRoomIds = $recentBooking->rooms->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->sort()
                ->values()
                ->all();

            if (empty($existingRoomIds) && !empty($recentBooking->room_id)) {
                $existingRoomIds = [(int) $recentBooking->room_id];
            }

            if ($existingRoomIds === $normalizedSelectedRoomIds) {
                return $recentBooking;
            }
        }

        return null;
    }
}
