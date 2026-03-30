<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Guest;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index()
    {
        $quickOverview = $this->buildQuickOverviewStats();
        $roomFrequency = $this->buildRoomBookingFrequency();
        $occupancyTrend = $this->buildRoomOccupancyTrend();

        $mostBookedRooms = $roomFrequency->sortByDesc('booking_count')->take(5)->values();
        $leastBookedRooms = $roomFrequency->sortBy('booking_count')->take(5)->values();

        return view('admin.reports.index', compact(
            'quickOverview',
            'mostBookedRooms',
            'leastBookedRooms',
            'roomFrequency',
            'occupancyTrend'
        ));
    }

    public function generatePdf(Request $request)
    {
        try {
            [$startDate, $endDate] = $this->resolveMonthlyPeriod($request);
            $user = Auth::user();
            $generatedBy = $this->resolveGeneratedByLabel($user?->role, $user?->name);
            $generatedByDisplay = $this->resolveGeneratedByDisplay($user?->name, $user?->role);

            // Stats (active rooms only)
            $filteredBookings = $this->applyActiveRoomFilterToBookingQuery(
                Booking::whereBetween('created_at', [$startDate, $endDate])
            );

            $totalBookings = (clone $filteredBookings)->count();
            $totalGuests = (clone $filteredBookings)->distinct('guest_id')->count('guest_id');
            $totalRooms = Room::query()->whereNull('archived_at')->count();
            $totalRevenue = Payment::query()
                ->whereIn('payment_status', ['verified', 'completed'])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas('booking', function ($bookingQuery) {
                    $this->applyActiveRoomFilterToBookingQuery($bookingQuery);
                })
                ->sum('amount');

            // Bookings breakdown by status
            $bookingsByStatus = collect();
            try {
                $bookingsByStatus = $this->applyActiveRoomFilterToBookingQuery(
                    Booking::whereBetween('created_at', [$startDate, $endDate])
                )
                    ->select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get()
                    ->keyBy('status');
            } catch (\Throwable $queryException) {
                Log::warning('Report PDF: failed to build bookingsByStatus', [
                    'message' => $queryException->getMessage(),
                    'user_id' => Auth::id(),
                ]);
            }

            // Payments in range
            $recentBookings = collect();
            try {
                $recentBookings = $this->applyActiveRoomFilterToBookingQuery(
                    Booking::with(['guest', 'room', 'roomType', 'rooms.roomType'])
                        ->whereBetween('created_at', [$startDate, $endDate])
                )
                    ->latest()
                    ->take(50)
                    ->get();
            } catch (\Throwable $queryException) {
                Log::warning('Report PDF: failed to load recentBookings', [
                    'message' => $queryException->getMessage(),
                    'user_id' => Auth::id(),
                ]);
            }

            $recentBookingRows = $this->buildRecentBookingRows($recentBookings);

            // Revenue by room type
            $revenueByType = collect();
            try {
                $revenueByType = Payment::whereIn('payment_status', ['verified', 'completed'])
                    ->whereBetween('payments.created_at', [$startDate, $endDate])
                    ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
                    ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                    ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                    ->whereNull('rooms.archived_at')
                    ->select('room_types.name', DB::raw('SUM(payments.amount) as revenue'))
                    ->groupBy('room_types.name')
                    ->get();
            } catch (\Throwable $primaryQueryException) {
                try {
                    // Fallback for environments where bookings.room_id is unavailable.
                    $revenueByType = Payment::whereIn('payments.payment_status', ['verified', 'completed'])
                        ->whereBetween('payments.created_at', [$startDate, $endDate])
                        ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
                        ->join('booking_rooms', 'bookings.id', '=', 'booking_rooms.booking_id')
                        ->join('rooms', 'booking_rooms.room_id', '=', 'rooms.id')
                        ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                        ->whereNull('rooms.archived_at')
                        ->select('room_types.name', DB::raw('SUM(payments.amount) as revenue'))
                        ->groupBy('room_types.name')
                        ->get();
                } catch (\Throwable $fallbackQueryException) {
                    Log::warning('Report PDF: failed to build revenueByType', [
                        'primary_message' => $primaryQueryException->getMessage(),
                        'fallback_message' => $fallbackQueryException->getMessage(),
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            // Keep PDF generation working even if activity log write fails.
            try {
                ActivityLog::log(
                    'report_generate',
                    'Generated PDF report for ' . Carbon::parse($startDate)->format('M d, Y') . ' to ' . Carbon::parse($endDate)->format('M d, Y')
                );
            } catch (\Throwable $logException) {
                Log::warning('Unable to write activity log during report PDF generation', [
                    'message' => $logException->getMessage(),
                    'user_id' => Auth::id(),
                ]);
            }

            $pdf = Pdf::loadView('admin.reports.pdf', compact(
                'startDate', 'endDate',
                'totalBookings', 'totalGuests', 'totalRooms', 'totalRevenue',
                'bookingsByStatus', 'revenueByType', 'generatedBy', 'generatedByDisplay', 'recentBookingRows'
            ))->setPaper('a4', 'portrait');

            $filename = 'hotel_report_' . Carbon::parse($startDate)->format('Y_m_d') . '_to_' . Carbon::parse($endDate)->format('Y_m_d') . '.pdf';

            return $pdf->download($filename);
        } catch (\Throwable $e) {
            Log::error('Failed generating report PDF', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'user_id' => Auth::id(),
            ]);

            return back()->with('error', 'Unable to generate PDF report right now. Please try again.');
        }
    }

    public function revenue(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        // Daily revenue from payments
        $dailyRevenue = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['verified', 'completed'])
            ->whereHas('booking', function ($bookingQuery) {
                $this->applyActiveRoomFilterToBookingQuery($bookingQuery);
            })
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by room type
        $revenueByType = Payment::whereBetween('payments.created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['verified', 'completed'])
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->whereNull('rooms.archived_at')
            ->select('room_types.name', DB::raw('SUM(payments.amount) as revenue'))
            ->groupBy('room_types.name')
            ->get();

        // Summary
        $totalRevenue = $dailyRevenue->sum('revenue');
        $totalBookings = $this->applyActiveRoomFilterToBookingQuery(
            Booking::whereBetween('created_at', [$startDate, $endDate])
        )->count();
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

        $totalRooms = Room::query()->whereNull('archived_at')->count();
        
        // Calculate occupancy per day
        $occupancyData = [];
        $currentDate = Carbon::parse($startDate);
        
        while ($currentDate <= Carbon::parse($endDate)) {
            $occupiedRooms = $this->resolveOccupiedRoomsCountForDate($currentDate->copy());
            
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
        [$startDate, $endDate] = $this->resolveMonthlyPeriod($request);

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
        $payments = Payment::with(['booking.guest', 'booking.room', 'booking.rooms'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('payment_status', ['verified', 'completed'])
            ->whereHas('booking', function ($bookingQuery) {
                $this->applyActiveRoomFilterToBookingQuery($bookingQuery);
            })
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
            $roomLabel = $payment->booking->rooms->isNotEmpty()
                ? ('Room ' . optional($payment->booking->rooms->first())->room_number . ($payment->booking->rooms->count() > 1 ? (' +' . ($payment->booking->rooms->count() - 1) . ' more') : ''))
                : ('Room ' . (optional($payment->booking->room)->room_number ?? 'N/A'));

            $sheet->setCellValue('D' . $row, $roomLabel);
            $sheet->setCellValue('E' . $row, ucfirst(str_replace('_', ' ', $payment->payment_type)));
            $sheet->setCellValue('F' . $row, $payment->booking->check_in_date->format('Y-m-d'));
            $sheet->setCellValue('G' . $row, $payment->booking->check_out_date->format('Y-m-d'));
            $sheet->setCellValue('H' . $row, $payment->amount);
            $row++;
        }

        if ($row > 2) {
            $sheet->getStyle('H2:H' . ($row - 1))
                ->getNumberFormat()
                ->setFormatCode('"₱"#,##0.00');
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
        $totalRooms = Room::query()->whereNull('archived_at')->count();
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
            $occupiedRooms = $this->resolveOccupiedRoomsCountForDate($currentDate->copy());
            
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
        $bookings = $this->applyActiveRoomFilterToBookingQuery(
            Booking::with(['guest', 'room', 'roomType', 'rooms.roomType'])
                ->whereBetween('created_at', [$startDate, $endDate])
        )
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
            $roomLabel = $booking->rooms->isNotEmpty()
                ? ('Room ' . optional($booking->rooms->first())->room_number . ($booking->rooms->count() > 1 ? (' +' . ($booking->rooms->count() - 1) . ' more') : ''))
                : ('Room ' . (optional($booking->room)->room_number ?? 'N/A'));

            $sheet->setCellValue('F' . $row, $roomLabel);
            $sheet->setCellValue('G' . $row, $booking->check_in_date->format('Y-m-d'));
            $sheet->setCellValue('H' . $row, $booking->check_out_date->format('Y-m-d'));
            $sheet->setCellValue('I' . $row, $booking->number_of_guests);
            $sheet->setCellValue('J' . $row, $booking->final_total ?? $booking->total_amount);
            $sheet->setCellValue('K' . $row, $booking->status);
            $row++;
        }

        if ($row > 2) {
            $sheet->getStyle('J2:J' . ($row - 1))
                ->getNumberFormat()
                ->setFormatCode('"₱"#,##0.00');
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

    private function resolveMonthlyPeriod(Request $request): array
    {
        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        if (!empty($startDateInput) && !empty($endDateInput)) {
            try {
                $start = Carbon::parse($startDateInput)->startOfDay();
                $end = Carbon::parse($endDateInput)->endOfDay();

                if ($start->gt($end)) {
                    [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
                }

                return [
                    $start->format('Y-m-d H:i:s'),
                    $end->format('Y-m-d H:i:s'),
                ];
            } catch (\Throwable $e) {
                // Fall back to current month when date input is malformed.
            }
        }

        $anchor = Carbon::now()->startOfMonth();

        return [
            $anchor->copy()->startOfMonth()->format('Y-m-d H:i:s'),
            $anchor->copy()->endOfMonth()->format('Y-m-d H:i:s'),
        ];
    }

    private function resolveGeneratedByLabel(?string $role, ?string $name): string
    {
        if (!empty($role)) {
            return Str::title(str_replace('_', ' ', $role));
        }

        if (!empty($name)) {
            return $name;
        }

        return 'System';
    }

    private function resolveGeneratedByDisplay(?string $name, ?string $role): string
    {
        $nameLabel = !empty($name) ? $name : 'System';
        $roleLabel = !empty($role) ? Str::title(str_replace('_', ' ', $role)) : 'System';

        return $nameLabel . ' (' . $roleLabel . ')';
    }

    private function buildRecentBookingRows($recentBookings)
    {
        return collect($recentBookings)->values()->map(function ($booking, $index) {
            $status = (string) ($booking->status ?? 'pending');
            $statusClass = str_replace(' ', '_', strtolower(trim($status)));

            return [
                'index' => $index + 1,
                'reference' => (string) ($booking->booking_reference ?? 'N/A'),
                'guest_name' => (string) (optional($booking->guest)->name ?? 'N/A'),
                'room_label' => $this->resolveRoomLabelForReport($booking),
                'check_in' => $this->safeFormatDate($booking->check_in_date),
                'check_out' => $this->safeFormatDate($booking->check_out_date),
                'nights' => $booking->total_nights ?? $booking->number_of_nights ?? '-',
                'amount' => (float) ($booking->final_total ?? $booking->total_amount ?? 0),
                'status_label' => str_replace('_', ' ', $status),
                'status_class' => $statusClass,
            ];
        });
    }

    private function resolveRoomLabelForReport($booking): string
    {
        try {
            if (method_exists($booking, 'relationLoaded') && $booking->relationLoaded('rooms')) {
                $rooms = $booking->rooms;

                if ($rooms && $rooms->isNotEmpty()) {
                    $firstRoomNumber = optional($rooms->first())->room_number ?? 'N/A';
                    $extraCount = max(0, $rooms->count() - 1);

                    return 'Room ' . $firstRoomNumber . ($extraCount > 0 ? (' +' . $extraCount . ' more') : '');
                }
            }
        } catch (\Throwable $e) {
            // Fall through to single-room fallback below.
        }

        $singleRoomNumber = optional($booking->room)->room_number ?? 'N/A';

        return 'Room ' . $singleRoomNumber;
    }

    private function safeFormatDate($dateValue): string
    {
        try {
            if (empty($dateValue)) {
                return '-';
            }

            return Carbon::parse($dateValue)->format('M d, Y');
        } catch (\Throwable $e) {
            return '-';
        }
    }

    private function applyActiveRoomFilterToBookingQuery($query)
    {
        return $query->where(function ($bookingQuery) {
            $bookingQuery->whereHas('rooms', function ($roomQuery) {
                $roomQuery->whereNull('rooms.archived_at');
            })->orWhereHas('room', function ($roomQuery) {
                $roomQuery->whereNull('rooms.archived_at');
            });
        });
    }

    private function buildQuickOverviewStats(): array
    {
        $activeBookingsQuery = $this->applyActiveRoomFilterToBookingQuery(
            Booking::query()->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled'])
        );

        $activeBookings = (clone $activeBookingsQuery)->count();
        $activeGuests = (clone $activeBookingsQuery)->distinct('guest_id')->count('guest_id');
        $activeRooms = Room::query()->whereNull('archived_at')->count();

        $verifiedPayments = Payment::query()
            ->whereIn('payment_status', ['verified', 'completed'])
            ->whereHas('booking', function ($bookingQuery) {
                $this->applyActiveRoomFilterToBookingQuery($bookingQuery);
            });

        $today = Carbon::today();

        return [
            'active_bookings' => $activeBookings,
            'active_rooms' => $activeRooms,
            'active_guests' => $activeGuests,
            'verified_payments_count' => (clone $verifiedPayments)->count(),
            'total_revenue' => (clone $verifiedPayments)->sum('amount'),
            'occupied_today' => $this->resolveOccupiedRoomsCountForDate($today),
        ];
    }

    private function buildRoomBookingFrequency()
    {
        $bookedStatuses = ['confirmed', 'checked_in', 'checked_out', 'rescheduled'];

        if (Schema::hasTable('booking_rooms')) {
            return DB::table('rooms')
                ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                ->leftJoin('booking_rooms', 'rooms.id', '=', 'booking_rooms.room_id')
                ->leftJoin('bookings', function ($join) use ($bookedStatuses) {
                    $join->on('booking_rooms.booking_id', '=', 'bookings.id')
                        ->whereIn('bookings.status', $bookedStatuses);
                })
                ->whereNull('rooms.archived_at')
                ->select(
                    'rooms.id',
                    'rooms.room_number',
                    'room_types.name as room_type_name',
                    DB::raw('COUNT(DISTINCT bookings.id) as booking_count')
                )
                ->groupBy('rooms.id', 'rooms.room_number', 'room_types.name')
                ->get();
        }

        return DB::table('rooms')
            ->leftJoin('room_types', 'rooms.room_type_id', '=', 'room_types.id')
            ->leftJoin('bookings', function ($join) use ($bookedStatuses) {
                $join->on('rooms.id', '=', 'bookings.room_id')
                    ->whereIn('bookings.status', $bookedStatuses);
            })
            ->whereNull('rooms.archived_at')
            ->select(
                'rooms.id',
                'rooms.room_number',
                'room_types.name as room_type_name',
                DB::raw('COUNT(DISTINCT bookings.id) as booking_count')
            )
            ->groupBy('rooms.id', 'rooms.room_number', 'room_types.name')
            ->get();
    }

    private function buildRoomOccupancyTrend()
    {
        $activeRoomCount = Room::query()->whereNull('archived_at')->count();
        $trendRows = collect();

        for ($offset = 5; $offset >= 0; $offset--) {
            $monthStart = Carbon::now()->subMonths($offset)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $daysInMonth = (int) $monthStart->daysInMonth;
            $availableRoomNights = max($activeRoomCount * $daysInMonth, 0);
            $occupiedRoomNights = $this->calculateOccupiedRoomNights($monthStart, $monthEnd);

            $occupancyRate = $availableRoomNights > 0
                ? round(($occupiedRoomNights / $availableRoomNights) * 100, 2)
                : 0.0;

            $trendRows->push([
                'label' => $monthStart->format('M Y'),
                'occupied_room_nights' => (int) $occupiedRoomNights,
                'available_room_nights' => (int) $availableRoomNights,
                'occupancy_rate' => $occupancyRate,
            ]);
        }

        return $trendRows;
    }

    private function resolveOccupiedRoomsCountForDate(Carbon $date): int
    {
        $dateString = $date->toDateString();
        $activeStatuses = ['confirmed', 'checked_in', 'rescheduled'];

        if (Schema::hasTable('booking_rooms')) {
            return (int) DB::table('booking_rooms')
                ->join('bookings', 'booking_rooms.booking_id', '=', 'bookings.id')
                ->join('rooms', 'booking_rooms.room_id', '=', 'rooms.id')
                ->whereNull('rooms.archived_at')
                ->whereIn('bookings.status', $activeStatuses)
                ->whereDate('bookings.check_in_date', '<=', $dateString)
                ->whereDate('bookings.check_out_date', '>', $dateString)
                ->distinct('booking_rooms.room_id')
                ->count('booking_rooms.room_id');
        }

        return (int) DB::table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->whereNull('rooms.archived_at')
            ->whereIn('bookings.status', $activeStatuses)
            ->whereDate('bookings.check_in_date', '<=', $dateString)
            ->whereDate('bookings.check_out_date', '>', $dateString)
            ->distinct('bookings.room_id')
            ->count('bookings.room_id');
    }

    private function calculateOccupiedRoomNights(Carbon $periodStart, Carbon $periodEnd): int
    {
        $activeStatuses = ['confirmed', 'checked_in', 'checked_out', 'rescheduled'];
        $periodEndExclusive = $periodEnd->copy()->addDay()->startOfDay();
        $totalNights = 0;

        if (Schema::hasTable('booking_rooms')) {
            $bookings = DB::table('booking_rooms')
                ->join('bookings', 'booking_rooms.booking_id', '=', 'bookings.id')
                ->join('rooms', 'booking_rooms.room_id', '=', 'rooms.id')
                ->whereNull('rooms.archived_at')
                ->whereIn('bookings.status', $activeStatuses)
                ->whereDate('bookings.check_in_date', '<=', $periodEnd->toDateString())
                ->whereDate('bookings.check_out_date', '>=', $periodStart->toDateString())
                ->select('bookings.check_in_date', 'bookings.check_out_date')
                ->get();
        } else {
            $bookings = DB::table('bookings')
                ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                ->whereNull('rooms.archived_at')
                ->whereIn('bookings.status', $activeStatuses)
                ->whereDate('bookings.check_in_date', '<=', $periodEnd->toDateString())
                ->whereDate('bookings.check_out_date', '>=', $periodStart->toDateString())
                ->select('bookings.check_in_date', 'bookings.check_out_date')
                ->get();
        }

        foreach ($bookings as $booking) {
            $checkIn = Carbon::parse($booking->check_in_date)->startOfDay();
            $checkOut = Carbon::parse($booking->check_out_date)->startOfDay();

            if ($checkOut->lessThanOrEqualTo($checkIn)) {
                $checkOut = $checkIn->copy()->addDay();
            }

            $effectiveStart = $checkIn->greaterThan($periodStart) ? $checkIn : $periodStart;
            $effectiveEnd = $checkOut->lessThan($periodEndExclusive) ? $checkOut : $periodEndExclusive;
            $nights = $effectiveStart->diffInDays($effectiveEnd, false);

            if ($nights > 0) {
                $totalNights += $nights;
            }
        }

        return (int) $totalNights;
    }
}
