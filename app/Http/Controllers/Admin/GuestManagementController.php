<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

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
        $guest->load(['bookings.room', 'bookings.roomType', 'bookings.rooms.roomType', 'bookings.payments']);
        
        // Calculate total spent from verified payments
        $totalSpent = $guest->bookings->flatMap(function($booking) {
            return $booking->payments;
        })->whereIn('payment_status', ['verified', 'completed'])->sum('amount');
        
        // Payment statistics
        $allPayments = $guest->bookings->flatMap(function($booking) {
            return $booking->payments;
        });
        
        $stats = [
            'total_bookings' => $guest->bookings()->count(),
            'completed_bookings' => $guest->bookings()->where('status', 'checked_out')->count(),
            'total_spent' => $totalSpent,
            'upcoming_bookings' => $guest->bookings()->where('check_in_date', '>', now())->count(),
            'last_booking_date' => $guest->bookings()->latest('created_at')->first()?->created_at,
            'verified_payments' => $allPayments->where('payment_status', 'verified')->count(),
            'pending_payments' => $allPayments->where('payment_status', 'pending')->count(),
            'failed_payments' => $allPayments->where('payment_status', 'failed')->count(),
        ];

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
