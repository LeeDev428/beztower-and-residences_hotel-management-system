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
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'Date');
        $sheet->setCellValue('B1', 'Reference');
        $sheet->setCellValue('C1', 'Guest');
        $sheet->setCellValue('D1', 'Room');
        $sheet->setCellValue('E1', 'Payment Type');
        $sheet->setCellValue('F1', 'Check-in');
        $sheet->setCellValue('G1', 'Check-out');
        $sheet->setCellValue('H1', 'Amount');
        
        // Style headers
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD4AF37');
        
        // Add data
        $row = 2;
        foreach ($payments as $payment) {
            $sheet->setCellValue('A' . $row, $payment->created_at->format('Y-m-d'));
            $sheet->setCellValue('B' . $row, $payment->booking->booking_reference);
            $sheet->setCellValue('C' . $row, $payment->booking->guest->name);
            $sheet->setCellValue('D' . $row, $payment->booking->room->room_number);
            $sheet->setCellValue('E' . $row, ucfirst(str_replace('_', ' ', $payment->payment_type)));
            $sheet->setCellValue('F' . $row, $payment->booking->check_in_date->format('Y-m-d'));
            $sheet->setCellValue('G' . $row, $payment->booking->check_out_date->format('Y-m-d'));
            $sheet->setCellValue('H' . $row, $payment->amount);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function exportOccupancy($startDate, $endDate)
    {
        $totalRooms = Room::count();
        $filename = 'occupancy_report_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'Date');
        $sheet->setCellValue('B1', 'Total Rooms');
        $sheet->setCellValue('C1', 'Occupied');
        $sheet->setCellValue('D1', 'Available');
        $sheet->setCellValue('E1', 'Occupancy Rate (%)');
        
        // Style headers
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD4AF37');
        
        // Add data
        $currentDate = Carbon::parse($startDate);
        $row = 2;
        
        while ($currentDate <= Carbon::parse($endDate)) {
            $occupiedRooms = Booking::where('check_in_date', '<=', $currentDate)
                ->where('check_out_date', '>', $currentDate)
                ->where('status', 'confirmed')
                ->count();
            
            $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 2) : 0;

            $sheet->setCellValue('A' . $row, $currentDate->format('Y-m-d'));
            $sheet->setCellValue('B' . $row, $totalRooms);
            $sheet->setCellValue('C' . $row, $occupiedRooms);
            $sheet->setCellValue('D' . $row, $totalRooms - $occupiedRooms);
            $sheet->setCellValue('E' . $row, $occupancyRate);

            $currentDate->addDay();
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function exportBookings($startDate, $endDate)
    {
        $bookings = Booking::with(['guest', 'room', 'roomType'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $filename = 'bookings_report_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $sheet->setCellValue('A1', 'Date');
        $sheet->setCellValue('B1', 'Reference');
        $sheet->setCellValue('C1', 'Guest');
        $sheet->setCellValue('D1', 'Email');
        $sheet->setCellValue('E1', 'Phone');
        $sheet->setCellValue('F1', 'Room');
        $sheet->setCellValue('G1', 'Check-in');
        $sheet->setCellValue('H1', 'Check-out');
        $sheet->setCellValue('I1', 'Guests');
        $sheet->setCellValue('J1', 'Amount');
        $sheet->setCellValue('K1', 'Status');
        
        // Style headers
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD4AF37');
        
        // Add data
        $row = 2;
        foreach ($bookings as $booking) {
            $sheet->setCellValue('A' . $row, $booking->created_at->format('Y-m-d H:i'));
            $sheet->setCellValue('B' . $row, $booking->booking_reference);
            $sheet->setCellValue('C' . $row, $booking->guest->name);
            $sheet->setCellValue('D' . $row, $booking->guest->email);
            $sheet->setCellValue('E' . $row, $booking->guest->phone);
            $sheet->setCellValue('F' . $row, $booking->room->room_number);
            $sheet->setCellValue('G' . $row, $booking->check_in_date->format('Y-m-d'));
            $sheet->setCellValue('H' . $row, $booking->check_out_date->format('Y-m-d'));
            $sheet->setCellValue('I' . $row, $booking->number_of_guests);
            $sheet->setCellValue('J' . $row, $booking->total_amount);
            $sheet->setCellValue('K' . $row, $booking->status);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create writer and output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
