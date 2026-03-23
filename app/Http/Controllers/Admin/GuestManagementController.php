<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class GuestManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::query();

        $guestTableColumns = [];
        $hasBookingsGuestForeignKey = false;
        $hasGuestCreatedAt = false;
        $hasFirstNameColumn = false;
        $hasLastNameColumn = false;
        $hasNameColumn = false;
        $hasEmailColumn = false;
        $hasPhoneColumn = false;

        try {
            if (Schema::hasTable('guests')) {
                $guestTableColumns = Schema::getColumnListing('guests');
                $hasGuestCreatedAt = in_array('created_at', $guestTableColumns, true);
                $hasFirstNameColumn = in_array('first_name', $guestTableColumns, true);
                $hasLastNameColumn = in_array('last_name', $guestTableColumns, true);
                $hasNameColumn = in_array('name', $guestTableColumns, true);
                $hasEmailColumn = in_array('email', $guestTableColumns, true);
                $hasPhoneColumn = in_array('phone', $guestTableColumns, true);
            }

            $hasBookingsGuestForeignKey = Schema::hasTable('bookings')
                && in_array('guest_id', Schema::getColumnListing('bookings'), true);
        } catch (\Throwable $e) {
            $guestTableColumns = [];
            $hasBookingsGuestForeignKey = false;
            $hasGuestCreatedAt = false;
        }

        if ($hasBookingsGuestForeignKey) {
            $query->withCount('bookings');
        } else {
            $query->select('guests.*')->selectRaw('0 as bookings_count');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search, $hasFirstNameColumn, $hasLastNameColumn, $hasNameColumn, $hasEmailColumn, $hasPhoneColumn) {
                if ($hasFirstNameColumn) {
                    $q->orWhere('first_name', 'LIKE', "%{$search}%");
                }

                if ($hasLastNameColumn) {
                    $q->orWhere('last_name', 'LIKE', "%{$search}%");
                }

                if ($hasNameColumn) {
                    $q->orWhere('name', 'LIKE', "%{$search}%");
                }

                if ($hasEmailColumn) {
                    $q->orWhere('email', 'LIKE', "%{$search}%");
                }

                if ($hasPhoneColumn) {
                    $q->orWhere('phone', 'LIKE', "%{$search}%");
                }
            });
        }

        if ($hasGuestCreatedAt) {
            $query->latest();
        } else {
            $fallbackSortColumn = in_array('id', $guestTableColumns, true)
                ? 'id'
                : (in_array('guest_id', $guestTableColumns, true) ? 'guest_id' : null);

            if ($fallbackSortColumn !== null) {
                $query->orderByDesc($fallbackSortColumn);
            }
        }

        $guests = $query->paginate(20);

        return view('admin.guests.index', compact('guests'));
    }

    public function show($guestIdentifier)
    {
          try {
            $guest = $this->resolveGuestByIdentifier($guestIdentifier);
            $bookings = collect();

            $stats = [
                'total_bookings' => 0,
                'completed_bookings' => 0,
                'total_spent' => 0,
                'upcoming_bookings' => 0,
                'last_booking_date' => null,
                'verified_payments' => 0,
                'pending_payments' => 0,
                'failed_payments' => 0,
            ];

            $relationsToLoad = [];

            $hasBookingsTable = Schema::hasTable('bookings');
            $hasPaymentsTable = Schema::hasTable('payments');
            $hasBookingRoomsTable = Schema::hasTable('booking_rooms');

            if ($hasBookingsTable) {
                $relationsToLoad[] = 'bookings.room';
                $relationsToLoad[] = 'bookings.roomType';

                if ($hasPaymentsTable) {
                    $relationsToLoad[] = 'bookings.payments';
                }

                if ($hasBookingRoomsTable) {
                    $relationsToLoad[] = 'bookings.rooms.roomType';
                }

                if (!empty($relationsToLoad)) {
                    $guest->load($relationsToLoad);
                }

                $bookings = $guest->relationLoaded('bookings')
                    ? ($guest->bookings ?? collect())
                    : collect();

                $bookingQuery = $guest->bookings();
                $stats['total_bookings'] = $bookingQuery->count();

                if (Schema::hasColumn('bookings', 'status')) {
                    $stats['completed_bookings'] = (clone $bookingQuery)->where('status', 'checked_out')->count();
                }

                if (Schema::hasColumn('bookings', 'check_in_date')) {
                    $stats['upcoming_bookings'] = (clone $bookingQuery)->where('check_in_date', '>', now())->count();
                }

                if (Schema::hasColumn('bookings', 'created_at')) {
                    $stats['last_booking_date'] = (clone $bookingQuery)->latest('created_at')->first()?->created_at;
                }

                $allPayments = $bookings->flatMap(function ($booking) {
                    return $booking->payments ?? collect();
                });

                $stats['total_spent'] = $allPayments
                    ->whereIn('payment_status', ['verified', 'completed'])
                    ->sum('amount');
                $stats['verified_payments'] = $allPayments->where('payment_status', 'verified')->count();
                $stats['pending_payments'] = $allPayments->where('payment_status', 'pending')->count();
                $stats['failed_payments'] = $allPayments->where('payment_status', 'failed')->count();
            }

            $guestRouteKey = $guest->id ?? $guest->guest_id ?? $guestIdentifier;

            try {
                // Render immediately so Blade/layout exceptions are caught and can fall back safely.
                $html = view('admin.guests.show', compact('guest', 'stats', 'bookings', 'guestRouteKey'))->render();
                return response($html, 200);
            } catch (\Throwable $renderException) {
                Log::error('Guest profile primary view render failed. Serving fallback view.', [
                    'guest_identifier' => $guestIdentifier,
                    'message' => $renderException->getMessage(),
                ]);

                return response()->view('admin.guests.show-fallback', [
                    'guest' => $guest,
                    'guestIdentifier' => $guestIdentifier,
                ], 200);
            }
        } catch (\Throwable $e) {
            $guestKeyColumn = $this->getGuestPrimaryKeyColumn();
            Log::warning('Guest profile fallback mode enabled due to schema mismatch.', [
                'guest_key' => isset($guest) ? ($guest->{$guestKeyColumn} ?? $guestIdentifier) : $guestIdentifier,
                'message' => $e->getMessage(),
            ]);
            // Keep guest route available even if full profile computation fails.
            $guest = isset($guest) ? $guest : new Guest();
            $guestRouteKey = $guest->id ?? $guest->guest_id ?? $guestIdentifier;

            return response()->view('admin.guests.show-fallback', [
                'guest' => $guest,
                'guestIdentifier' => $guestRouteKey,
            ], 200);
        }
    }

    public function update(Request $request, $guestIdentifier)
    {
        $guest = $this->resolveGuestByIdentifier($guestIdentifier);
        $guestKeyColumn = $this->getGuestPrimaryKeyColumn($guest);
        $guestKeyValue = $guest->{$guestKeyColumn} ?? ($guest->id ?? $guest->guest_id ?? $guestIdentifier);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:guests,email,' . $guestKeyValue . ',' . $guestKeyColumn,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        $guest->update($validated);

        return back()->with('success', 'Guest information updated successfully!');
    }

    private function getGuestPrimaryKeyColumn(?Guest $guest = null): string
    {
        if ($guest instanceof Guest) {
            if (array_key_exists('id', $guest->getAttributes())) {
                return 'id';
            }

            if (array_key_exists('guest_id', $guest->getAttributes())) {
                return 'guest_id';
            }

            return $guest->getKeyName();
        }

        return (new Guest())->getKeyName();
    }

    private function resolveGuestByIdentifier($guestIdentifier): Guest
    {
        $candidateColumns = array_values(array_unique(array_filter([
            (new Guest())->getKeyName(),
            'id',
            'guest_id',
        ])));

        foreach ($candidateColumns as $column) {
            try {
                $guest = Guest::query()->where($column, $guestIdentifier)->first();

                if ($guest instanceof Guest) {
                    return $guest;
                }
            } catch (QueryException $e) {
                // Column likely does not exist in this schema; try next candidate.
                continue;
            }
        }

        // Fallback: load a limited set and match in memory for legacy schemas.
        try {
            $guests = Guest::query()->limit(500)->get();

            foreach ($candidateColumns as $column) {
                $guest = $guests->firstWhere($column, $guestIdentifier);

                if ($guest instanceof Guest) {
                    return $guest;
                }
            }
        } catch (\Throwable $e) {
            // Ignore and throw model not found below.
        }

        throw (new ModelNotFoundException())->setModel(Guest::class, [$guestIdentifier]);
    }
}
