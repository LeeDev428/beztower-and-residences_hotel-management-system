<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['guest', 'room', 'roomType']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'LIKE', "%{$search}%")
                  ->orWhereHas('guest', function($gq) use ($search) {
                      $gq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('check_in_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('check_in_date', '<=', $request->date_to);
        }

        $bookings = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['guest', 'room', 'roomType']);

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled',
        ]);

        $booking->update(['status' => $validated['status']]);

        // Update room status based on booking status
        if ($validated['status'] === 'checked_in') {
            $booking->room->update(['status' => 'occupied']);
        } elseif ($validated['status'] === 'checked_out') {
            $booking->room->update(['status' => 'maintenance']);
            
            // Create or update housekeeping record
            \App\Models\Housekeeping::updateOrCreate(
                ['room_id' => $booking->room_id],
                ['status' => 'dirty', 'notes' => 'Room needs cleaning after checkout']
            );
        }

        return back()->with('success', 'Booking status updated successfully!');
    }
}
