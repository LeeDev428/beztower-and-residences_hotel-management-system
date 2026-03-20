<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
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
            $query->orderByDesc('id');
        }

        $guests = $query->paginate(20);

        return view('admin.guests.index', compact('guests'));
    }

    public function show(Guest $guest)
    {
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

        try {
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

                $allPayments = $guest->bookings->flatMap(function ($booking) {
                    return $booking->payments ?? collect();
                });

                $stats['total_spent'] = $allPayments
                    ->whereIn('payment_status', ['verified', 'completed'])
                    ->sum('amount');
                $stats['verified_payments'] = $allPayments->where('payment_status', 'verified')->count();
                $stats['pending_payments'] = $allPayments->where('payment_status', 'pending')->count();
                $stats['failed_payments'] = $allPayments->where('payment_status', 'failed')->count();
            }
        } catch (\Throwable $e) {
            Log::warning('Guest profile fallback mode enabled due to schema mismatch.', [
                'guest_id' => $guest->id,
                'message' => $e->getMessage(),
            ]);
            // Keep default safe stats and continue rendering profile page.
        }

        return view('admin.guests.show', compact('guest', 'stats'));
    }

    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:guests,email,' . $guest->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        $guest->update($validated);

        return back()->with('success', 'Guest information updated successfully!');
    }
}
