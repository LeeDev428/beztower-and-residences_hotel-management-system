<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Amenity;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['roomType', 'amenities', 'photos'])
            ->where('status', 'available');
        
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
        
        $rooms = $query->paginate(10)->withQueryString();
        $roomTypes = RoomType::all();
        $amenities = Amenity::all();
        
        return view('customer.rooms.index', compact('rooms', 'roomTypes', 'amenities'));
    }
    
    public function show(Room $room)
    {
        $room->load(['roomType', 'amenities', 'photos', 'housekeeping']);
        
        return view('customer.rooms.show', compact('room'));
    }
}

