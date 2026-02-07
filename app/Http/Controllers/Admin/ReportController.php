<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function revenue(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        // Daily revenue from payments
        $dailyRevenue = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['verified', 'completed'])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by room type
        $revenueByType = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['verified', 'completed'])
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->select('room_types.name', DB::raw('SUM(payments.amount) as revenue'))
            ->groupBy('room_types.name')
            ->get();

        // Summary
        $totalRevenue = $dailyRevenue->sum('revenue');
        $totalBookings = Booking::whereBetween('created_at', [$startDate, $endDate])->count();
        $avgRevenuePerBooking = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;

        return view('admin.reports.revenue', compact(
            'dailyRevenue',
            'revenueByType',
            'totalRevenue',
            'totalBookings',
            'avgRevenuePerBooking',
            'startDate',
            'endDate'
        ));
    }

    public function occupancy(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $totalRooms = Room::count();
        
        // Calculate occupancy per day
        $occupancyData = [];
        $currentDate = Carbon::parse($startDate);
        
        while ($currentDate <= Carbon::parse($endDate)) {
            $occupiedRooms = Booking::where('check_in_date', '<=', $currentDate)
                ->where('check_out_date', '>', $currentDate)
                ->where('status', 'confirmed')
                ->count();
            
            $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;
            
            $occupancyData[] = [
                'date' => $currentDate->format('Y-m-d'),
                'occupied' => $occupiedRooms,
                'available' => $totalRooms - $occupiedRooms,
                'rate' => $occupancyRate,
            ];
            
            $currentDate->addDay();
        }

        // Average occupancy rate
        $avgOccupancyRate = collect($occupancyData)->avg('rate');

        // Log report generation
        ActivityLog::log(
            'report_generate',
            'Generated occupancy report for ' . Carbon::parse($startDate)->format('M d, Y') . ' to ' . Carbon::parse($endDate)->format('M d, Y')
        );

        return view('admin.reports.occupancy', compact(
            'occupancyData',
            'totalRooms',
            'avgOccupancyRate',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request, $type)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        if ($type === 'revenue') {
            return $this->exportRevenue($startDate, $endDate);
        } elseif ($type === 'occupancy') {
            return $this->exportOccupancy($startDate, $endDate);
        } elseif ($type === 'bookings') {
            return $this->exportBookings($startDate, $endDate);
        }

        return back()->with('error', 'Invalid report type');
    }

    private function exportRevenue($startDate, $endDate)
    {
        $payments = Payment::with(['booking.guest', 'booking.room'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['verified', 'completed'])
            ->get();

        $filename = 'revenue_report_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Reference', 'Guest', 'Room', 'Payment Type', 'Check-in', 'Check-out', 'Amount']);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->created_at->format('Y-m-d'),
                    $payment->booking->booking_reference,
                    $payment->booking->guest->name,
                    $payment->booking->room->room_number,
                    ucfirst(str_replace('_', ' ', $payment->payment_type)),
                    $payment->booking->check_in_date->format('Y-m-d'),
                    $payment->booking->check_out_date->format('Y-m-d'),
                    $payment->amount,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportOccupancy($startDate, $endDate)
    {
        $totalRooms = Room::count();
        $filename = 'occupancy_report_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($startDate, $endDate, $totalRooms) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Total Rooms', 'Occupied', 'Available', 'Occupancy Rate (%)']);

            $currentDate = Carbon::parse($startDate);
            while ($currentDate <= Carbon::parse($endDate)) {
                $occupiedRooms = Booking::where('check_in_date', '<=', $currentDate)
                    ->where('check_out_date', '>', $currentDate)
                    ->where('status', 'confirmed')
                    ->count();
                
                $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;

                fputcsv($file, [
                    $currentDate->format('Y-m-d'),
                    $totalRooms,
                    $occupiedRooms,
                    $totalRooms - $occupiedRooms,
                    $occupancyRate,
                ]);

                $currentDate->addDay();
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportBookings($startDate, $endDate)
    {
        $bookings = Booking::with(['guest', 'room', 'roomType'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $filename = 'bookings_report_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Reference', 'Guest', 'Email', 'Phone', 'Room', 'Check-in', 'Check-out', 'Guests', 'Amount', 'Status']);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->created_at->format('Y-m-d H:i'),
                    $booking->booking_reference,
                    $booking->guest->name,
                    $booking->guest->email,
                    $booking->guest->phone,
                    $booking->room->room_number,
                    $booking->check_in_date->format('Y-m-d'),
                    $booking->check_out_date->format('Y-m-d'),
                    $booking->number_of_guests,
                    $booking->total_amount,
                    $booking->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
