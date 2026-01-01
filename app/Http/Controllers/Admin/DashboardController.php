<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Housekeeping;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        // Arrivals Today - bookings where check_in_date is today and status is confirmed
        $arrivalsToday = Booking::whereDate('check_in_date', $today)
            ->where('status', 'confirmed')
            ->with(['guest', 'room'])
            ->get();

        // Departures Today - bookings where check_out_date is today and status is confirmed
        $departuresToday = Booking::whereDate('check_out_date', $today)
            ->where('status', 'confirmed')
            ->with(['guest', 'room'])
            ->get();

        // Current Occupants - bookings where today is between check_in_date and check_out_date
        $currentOccupants = Booking::where('check_in_date', '<=', $today)
            ->where('check_out_date', '>', $today)
            ->where('status', 'confirmed')
            ->with(['guest', 'room'])
            ->get();

        // Revenue Statistics - using Payment model
        $revenueToday = Payment::whereDate('created_at', $today)
            ->whereIn('payment_status', ['verified', 'completed'])
            ->sum('amount');

        $revenueThisMonth = Payment::whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->whereIn('payment_status', ['verified', 'completed'])
            ->sum('amount');

        $revenueThisYear = Payment::whereYear('created_at', $today->year)
            ->whereIn('payment_status', ['verified', 'completed'])
            ->sum('amount');

        // Room Statistics
        $totalRooms = Room::count();
        $occupiedRooms = $currentOccupants->count();
        $availableRooms = $totalRooms - $occupiedRooms;
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Booking Statistics
        $pendingBookings = Booking::where('status', 'pending')->count();
        $pendingPayments = Payment::where('payment_status', 'pending')->count();
        $confirmedToday = Booking::whereDate('created_at', $today)->where('status', 'confirmed')->count();

        // Housekeeping Statistics
        $dirtyRooms = Housekeeping::where('status', 'dirty')->count();
        $cleanRooms = Housekeeping::where('status', 'clean')->count();
        $inProgressRooms = Housekeeping::where('status', 'in_progress')->count();

        // Recent Bookings
        $recentBookings = Booking::with(['guest', 'room'])
            ->latest()
            ->take(5)
            ->get();

        // Monthly Revenue Chart Data (Last 12 months)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenue = Payment::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->whereIn('payment_status', ['verified', 'completed'])
                ->sum('amount');
            
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
            ];
        }

        return view('admin.dashboard.index', compact(
            'arrivalsToday',
            'departuresToday',
            'currentOccupants',
            'revenueToday',
            'revenueThisMonth',
            'revenueThisYear',
            'totalRooms',
            'occupiedRooms',
            'availableRooms',
            'occupancyRate',
            'pendingBookings',
            'pendingPayments',
            'confirmedToday',
            'dirtyRooms',
            'cleanRooms',
            'inProgressRooms',
            'recentBookings',
            'monthlyRevenue'
        ));
    }
}
