<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\BlockDate;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['roomType', 'amenities', 'photos'])
            ->where('status', 'available')
            ->whereNull('archived_at')
            ->whereHas('roomType', function ($q) {
                $q->whereNull('archived_at');
            }); // Only show active, available rooms with active room type

        $requestedRooms = max(1, min(12, (int) $request->input('rooms', 1)));
        $requestedGuests = $this->resolveRequestedGuests($request);
        $selectedRoomIds = $this->parseSelectedRoomIds((string) $request->input('selected_rooms', ''));
        $selectedCapacity = 0;
        $selectedRooms = collect();

        if ($selectedRoomIds->isNotEmpty()) {
            $selectedRooms = Room::with('roomType')
                ->whereIn('id', $selectedRoomIds->all())
                ->get();

            $selectedRoomIds = $selectedRooms
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

            $selectedCapacity = (int) $selectedRooms->sum(function (Room $room) {
                return (int) optional($room->roomType)->max_guests;
            });
        }

        $remainingRoomsToSelect = max($requestedRooms - $selectedRoomIds->count(), 0);
        $remainingGuestsToCover = max($requestedGuests - $selectedCapacity, 0);
        $applyCombinationFilter = false;
        
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
            $roomTypeId = (int) $request->room_type;
            $activeRoomTypeExists = RoomType::active()->whereKey($roomTypeId)->exists();

            if ($activeRoomTypeExists) {
                $query->where('room_type_id', $roomTypeId);
            }
        }
        
        // Filter by number of guests.
        // For multi-room requests, use combination logic instead of single-room capacity.
        if ($requestedGuests > 0) {
            if ($requestedRooms > 1) {
                $applyCombinationFilter = true;
            } else {
                $query->whereHas('roomType', function($q) use ($requestedGuests) {
                    $q->where('max_guests', '>=', $requestedGuests);
                });
            }
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
        $amenityFilters = $request->input('amenities', []);
        if (!is_array($amenityFilters)) {
            $amenityFilters = array_filter(array_map('trim', explode(',', (string) $amenityFilters)));
        }
        if (!empty($amenityFilters)) {
            foreach ($amenityFilters as $amenityId) {
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
                Booking::applyActiveReservationFilter($q);
                Booking::applyDateConflictWindow($q, (string) $checkIn, (string) $checkOut);
            });

            $query->whereDoesntHave('reservationBookings', function($q) use ($checkIn, $checkOut) {
                Booking::applyActiveReservationFilter($q);
                Booking::applyDateConflictWindow($q, (string) $checkIn, (string) $checkOut);
            });
            
            // Exclude rooms with block dates
            $query->whereDoesntHave('blockDates', function($q) use ($checkIn, $checkOut) {
                                $q->where('start_date', '<', $checkOut)
                                    ->where('end_date', '>', $checkIn);
            });
        }
        
        // Sorting
        if ($request->filled('sort')) {
            $sort = strtolower(trim((string) $request->sort));

            if (in_array($sort, ['undefined', 'null', 'none'], true)) {
                $sort = '';
            }

            switch ($sort) {
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

        if ($applyCombinationFilter) {
            $targetRooms = $remainingRoomsToSelect > 0 ? $remainingRoomsToSelect : $requestedRooms;
            $targetGuests = $remainingRoomsToSelect > 0 ? $remainingGuestsToCover : $requestedGuests;

            if ($targetRooms === 1) {
                if ($targetGuests > 0) {
                    $query->where(function ($filtered) use ($targetGuests, $selectedRoomIds) {
                        $filtered->whereHas('roomType', function ($q) use ($targetGuests) {
                            $q->where('max_guests', '>=', $targetGuests);
                        });

                        if ($selectedRoomIds->isNotEmpty()) {
                            $filtered->orWhereIn('rooms.id', $selectedRoomIds->all());
                        }
                    });
                }
            } elseif ($targetRooms > 1 && $targetGuests > 0) {
                $roomsForCombination = (clone $query)->with('roomType')->get();
                $combinationMeta = $this->resolveCombinationRoomTypeMeta($roomsForCombination, $targetRooms, $targetGuests);

                if (empty($combinationMeta['typeIds'])) {
                    if ($selectedRoomIds->isNotEmpty()) {
                        $query->whereIn('rooms.id', $selectedRoomIds->all());
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                } else {
                    $query->where(function ($filtered) use ($combinationMeta, $selectedRoomIds) {
                        $filtered->whereIn('room_type_id', $combinationMeta['typeIds']);

                        if ($selectedRoomIds->isNotEmpty()) {
                            $filtered->orWhereIn('rooms.id', $selectedRoomIds->all());
                        }
                    });

                    // Prioritize room types that appear in better-fit combinations when no explicit sort is requested.
                    if (!$request->filled('sort') && !empty($combinationMeta['priorityTypeIds'])) {
                        $caseParts = [];
                        foreach ($combinationMeta['priorityTypeIds'] as $index => $typeId) {
                            $caseParts[] = 'WHEN ' . (int) $typeId . ' THEN ' . $index;
                        }
                        $caseSql = 'CASE rooms.room_type_id ' . implode(' ', $caseParts) . ' ELSE 999 END';
                        $query->orderByRaw($caseSql)->orderBy('room_number');
                    }
                }
            }
        }
        
        $rooms = $query->paginate(6)->withQueryString();
        $roomTypes = RoomType::active()->get();
        $amenities = Amenity::all();
        
        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'html' => view('customer.rooms.partials.room-cards', compact('rooms'))->render(),
                'pagination' => view('customer.rooms.partials.pagination', compact('rooms'))->render(),
                'total' => $rooms->total()
            ]);
        }

        $selectionMeta = [
            'requested_guests' => $requestedGuests,
            'selected_capacity' => $selectedCapacity,
            'remaining_guests' => $remainingGuestsToCover,
            'remaining_rooms' => $remainingRoomsToSelect,
            'selected_count' => $selectedRoomIds->count(),
            'selected_rooms_summary' => $selectedRooms
                ->map(function (Room $room) {
                    return [
                        'id' => (int) $room->id,
                        'room_number' => (string) ($room->room_number ?? ''),
                        'room_type' => (string) (optional($room->roomType)->name ?? 'Room'),
                        'capacity' => (int) (optional($room->roomType)->max_guests ?? 0),
                    ];
                })
                ->values()
                ->all(),
        ];
        
        return view('customer.rooms.index', compact('rooms', 'roomTypes', 'amenities', 'selectionMeta'));
    }

    private function parseSelectedRoomIds(string $rawIds): Collection
    {
        return collect(explode(',', $rawIds))
            ->map(fn ($id) => (int) trim((string) $id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();
    }

    private function resolveRequestedGuests(Request $request): int
    {
        $fallbackGuests = is_numeric($request->input('guests')) ? (int) $request->input('guests') : 0;
        $adults = is_numeric($request->input('adults')) ? (int) $request->input('adults') : 0;
        $children = is_numeric($request->input('children')) ? (int) $request->input('children') : 0;

        if ($adults <= 0 && $children <= 0) {
            return max(0, $fallbackGuests);
        }

        // Capacity logic: every 2 children count as 1 adult, remaining 1 child is free.
        $effectiveAdults = max(0, $adults) + intdiv(max(0, $children), 2);

        return max(1, $effectiveAdults);
    }

    /**
     * Resolve room-type metadata for multi-room booking combinations.
     *
     * Returns room types that can participate in any valid combination where:
     * - exact number of requested rooms is used
     * - combined capacity >= requested guests
     */
    private function resolveCombinationRoomTypeMeta(Collection $rooms, int $requestedRooms, int $requestedGuests): array
    {
        if ($requestedRooms <= 1 || $requestedGuests <= 0 || $rooms->isEmpty()) {
            return [
                'typeIds' => [],
                'priorityTypeIds' => [],
            ];
        }

        $types = $rooms
            ->groupBy('room_type_id')
            ->map(function (Collection $group, $typeId) {
                $capacity = (int) optional($group->first()->roomType)->max_guests;

                return [
                    'type_id' => (int) $typeId,
                    'count' => $group->count(),
                    'capacity' => max(0, $capacity),
                ];
            })
            ->filter(fn ($type) => $type['count'] > 0 && $type['capacity'] > 0)
            ->values()
            ->all();

        if (empty($types)) {
            return [
                'typeIds' => [],
                'priorityTypeIds' => [],
            ];
        }

        $combinations = [];
        $this->buildTypeCombinations($types, 0, $requestedRooms, 0, $requestedGuests, [], $combinations);

        if (empty($combinations)) {
            return [
                'typeIds' => [],
                'priorityTypeIds' => [],
            ];
        }

        $typeIds = [];
        $bestExcessByType = [];

        foreach ($combinations as $combo) {
            $excess = $combo['excess'];
            foreach ($combo['type_counts'] as $typeId => $count) {
                if ($count <= 0) {
                    continue;
                }
                $typeId = (int) $typeId;
                $typeIds[$typeId] = true;

                if (!isset($bestExcessByType[$typeId]) || $excess < $bestExcessByType[$typeId]) {
                    $bestExcessByType[$typeId] = $excess;
                }
            }
        }

        $priorityTypeIds = array_keys($bestExcessByType);
        usort($priorityTypeIds, function ($left, $right) use ($bestExcessByType) {
            return $bestExcessByType[$left] <=> $bestExcessByType[$right];
        });

        return [
            'typeIds' => array_map('intval', array_keys($typeIds)),
            'priorityTypeIds' => array_map('intval', $priorityTypeIds),
        ];
    }

    private function buildTypeCombinations(array $types, int $index, int $remainingRooms, int $currentCapacity, int $requestedGuests, array $currentTypeCounts, array &$combinations): void
    {
        if ($remainingRooms === 0) {
            if ($currentCapacity >= $requestedGuests) {
                $combinations[] = [
                    'type_counts' => $currentTypeCounts,
                    'excess' => $currentCapacity - $requestedGuests,
                ];
            }
            return;
        }

        if ($index >= count($types)) {
            return;
        }

        $type = $types[$index];
        $maxPick = min($remainingRooms, (int) $type['count']);

        for ($pick = 0; $pick <= $maxPick; $pick++) {
            $nextCounts = $currentTypeCounts;
            if ($pick > 0) {
                $nextCounts[(int) $type['type_id']] = $pick;
            }

            $this->buildTypeCombinations(
                $types,
                $index + 1,
                $remainingRooms - $pick,
                $currentCapacity + ($pick * (int) $type['capacity']),
                $requestedGuests,
                $nextCounts,
                $combinations
            );
        }
    }
    
    public function show(Room $room)
    {
        if (!is_null($room->archived_at)) {
            abort(404);
        }

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
                $bookedDates = Booking::where(function($q) use ($startDate, $endDate) {
            $q->where('check_in_date', '<=', $endDate)
              ->where('check_out_date', '>=', $startDate);
        })
        ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'rescheduled'])
        ->with(['room', 'rooms'])
        ->get()
        ->flatMap(function($booking) {
            $dates = [];
            $current = new \DateTime($booking->check_in_date);
            $end = new \DateTime($booking->check_out_date);
            if ((int) ($booking->late_checkout_hours ?? 0) > 0) {
                $end->modify('+1 day');
            }
            $roomIds = $booking->rooms->pluck('id')->all();
            if (empty($roomIds) && $booking->room_id) {
                $roomIds = [$booking->room_id];
            }

            if (empty($roomIds)) {
                return $dates;
            }
            
            while ($current < $end) {
                foreach ($roomIds as $roomId) {
                    $dates[] = [
                        'date' => $current->format('Y-m-d'),
                        'room_id' => $roomId
                    ];
                }
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
        
        $totalRooms = Room::whereIn('status', ['available', 'occupied'])
            ->whereNull('archived_at')
            ->whereHas('roomType', function ($query) {
                $query->whereNull('archived_at');
            })
            ->count();
        
        return response()->json([
            'booked_dates' => $bookedDates,
            'block_dates' => $blockDates,
            'total_rooms' => $totalRooms,
            'month' => $month,
            'year' => $year
        ]);
    }
}

