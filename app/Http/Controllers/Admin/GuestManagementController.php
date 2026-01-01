<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use Illuminate\Http\Request;

class GuestManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Guest::withCount('bookings');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $guests = $query->latest()->paginate(20);

        return view('admin.guests.index', compact('guests'));
    }

    public function show(Guest $guest)
    {
        $guest->load(['bookings.room', 'bookings.payments']);
        
        // Calculate total spent from verified payments
        $totalSpent = $guest->bookings->flatMap(function($booking) {
            return $booking->payments;
        })->whereIn('payment_status', ['verified', 'completed'])->sum('amount');
        
        $stats = [
            'total_bookings' => $guest->bookings()->count(),
            'completed_bookings' => $guest->bookings()->where('status', 'checked_out')->count(),
            'total_spent' => $totalSpent,
            'upcoming_bookings' => $guest->bookings()->where('check_in_date', '>', now())->count(),
        ];

        return view('admin.guests.show', compact('guest', 'stats'));
    }

    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:guests,email,' . $guest->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
        ]);

        $guest->update($validated);

        return back()->with('success', 'Guest information updated successfully!');
    }
}
