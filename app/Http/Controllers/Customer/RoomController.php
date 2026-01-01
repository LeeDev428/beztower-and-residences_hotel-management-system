<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\BlockDate;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['roomType', 'amenities', 'photos'])
            ->where('status', 'available');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('room_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('roomType', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('amenities', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Filter by room type
        if ($request->filled('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }
        
        // Filter by number of guests
        if ($request->filled('guests')) {
            $query->whereHas('roomType', function($q) use ($request) {
                $q->where('max_guests', '>=', $request->guests);
            });
        }
        
        // Filter by price range
        if ($request->filled('min_price')) {
            $query->whereHas('roomType', function($q) use ($request) {
                $q->where('base_price', '>=', $request->min_price);
            });
        }
        
        if ($request->filled('max_price')) {
            $query->whereHas('roomType', function($q) use ($request) {
                $q->where('base_price', '<=', $request->max_price);
            });
        }
        
        // Filter by amenities
        if ($request->filled('amenities')) {
            foreach ($request->amenities as $amenityId) {
                $query->whereHas('amenities', function($q) use ($amenityId) {
                    $q->where('amenities.id', $amenityId);
                });
            }
        }
        
        // Filter by availability dates
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;
            
            // Exclude rooms with conflicting bookings
            $query->whereDoesntHave('bookings', function($q) use ($checkIn, $checkOut) {
                $q->where(function($q) use ($checkIn, $checkOut) {
                    $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                      ->orWhere(function($q) use ($checkIn, $checkOut) {
                          $q->where('check_in_date', '<=', $checkIn)
                            ->where('check_out_date', '>=', $checkOut);
                      });
                })->whereIn('status', ['pending', 'confirmed', 'checked_in']);
            });
            
            // Exclude rooms with block dates
            $query->whereDoesntHave('blockDates', function($q) use ($checkIn, $checkOut) {
                $q->where(function($q) use ($checkIn, $checkOut) {
                    $q->whereBetween('start_date', [$checkIn, $checkOut])
                      ->orWhereBetween('end_date', [$checkIn, $checkOut])
                      ->orWhere(function($q) use ($checkIn, $checkOut) {
                          $q->where('start_date', '<=', $checkIn)
                            ->where('end_date', '>=', $checkOut);
                      });
                });
            });
        }
        
        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                          ->orderBy('room_types.base_price', 'asc')
                          ->select('rooms.*');
                    break;
                case 'price_high':
                    $query->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                          ->orderBy('room_types.base_price', 'desc')
                          ->select('rooms.*');
                    break;
                case 'name':
                    $query->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
                          ->orderBy('room_types.name', 'asc')
                          ->select('rooms.*');
                    break;
            }
        }
        
        $rooms = $query->paginate(6)->withQueryString();
        $roomTypes = RoomType::all();
        $amenities = Amenity::all();
        
        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'html' => view('customer.rooms.partials.room-cards', compact('rooms'))->render(),
                'pagination' => view('customer.rooms.partials.pagination', compact('rooms'))->render(),
                'total' => $rooms->total()
            ]);
        }
        
        return view('customer.rooms.index', compact('rooms', 'roomTypes', 'amenities'));
    }
    
    public function show(Room $room)
    {
        $room->load(['roomType', 'amenities', 'photos', 'housekeeping']);
        
        return view('customer.rooms.show', compact('room'));
    }
    
    /**
     * Get calendar availability for a specific month
     */
    public function getAvailability(Request $request)
    {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));
        
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Get all booked dates in this month
        $bookedDates = \App\Models\Booking::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('check_in_date', [$startDate, $endDate])
              ->orWhereBetween('check_out_date', [$startDate, $endDate])
              ->orWhere(function($q) use ($startDate, $endDate) {
                  $q->where('check_in_date', '<=', $startDate)
                    ->where('check_out_date', '>=', $endDate);
              });
        })
        ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
        ->with('room')
        ->get()
        ->flatMap(function($booking) {
            $dates = [];
            $current = new \DateTime($booking->check_in_date);
            $end = new \DateTime($booking->check_out_date);
            
            while ($current <= $end) {
                $dates[] = [
                    'date' => $current->format('Y-m-d'),
                    'room_id' => $booking->room_id
                ];
                $current->modify('+1 day');
            }
            
            return $dates;
        })
        ->groupBy('date')
        ->map(function($group) {
            return [
                'date' => $group->first()['date'],
                'booked_rooms' => $group->count(),
                'room_ids' => $group->pluck('room_id')->toArray()
            ];
        })
        ->values();
        
        // Get block dates
        $blockDates = BlockDate::where(function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_date', [$startDate, $endDate])
              ->orWhereBetween('end_date', [$startDate, $endDate])
              ->orWhere(function($q) use ($startDate, $endDate) {
                  $q->where('start_date', '<=', $startDate)
                    ->where('end_date', '>=', $endDate);
              });
        })
        ->get()
        ->flatMap(function($block) {
            $dates = [];
            $current = new \DateTime($block->start_date);
            $end = new \DateTime($block->end_date);
            
            while ($current <= $end) {
                $dates[] = [
                    'date' => $current->format('Y-m-d'),
                    'room_id' => $block->room_id
                ];
                $current->modify('+1 day');
            }
            
            return $dates;
        })
        ->groupBy('date')
        ->map(function($group) {
            return [
                'date' => $group->first()['date'],
                'blocked_rooms' => $group->count(),
                'room_ids' => $group->pluck('room_id')->toArray()
            ];
        })
        ->values();
        
        $totalRooms = Room::where('status', 'available')->count();
        
        return response()->json([
            'booked_dates' => $bookedDates,
            'block_dates' => $blockDates,
            'total_rooms' => $totalRooms,
            'month' => $month,
            'year' => $year
        ]);
    }
}

