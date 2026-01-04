<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomType::query();

        // Filter archived/active
        if ($request->filled('archived')) {
            if ($request->archived === 'yes') {
                $query->archived();
            } else {
                $query->active();
            }
        } else {
            $query->active();
        }

        $roomTypes = $query->orderBy('name')->get();
        
        return response()->json([
            'roomTypes' => $roomTypes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'max_guests' => 'required|integer|min:1',
            'bed_type' => 'nullable|string',
            'size_sqm' => 'nullable|numeric|min:0'
        ]);

        // Validate discount is divisible by 5
        if (!empty($validated['discount_percentage'])) {
            $discount = $validated['discount_percentage'];
            if ($discount > 0 && fmod($discount, 5) != 0) {
                return response()->json([
                    'error' => 'Discount percentage must be divisible by 5'
                ], 422);
            }
        }

        $roomType = RoomType::create($validated);

        return response()->json([
            'message' => 'Room type created successfully',
            'roomType' => $roomType
        ]);
    }

    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'max_guests' => 'required|integer|min:1',
            'bed_type' => 'nullable|string',
            'size_sqm' => 'nullable|numeric|min:0'
        ]);

        // Validate discount is divisible by 5
        if (!empty($validated['discount_percentage'])) {
            $discount = $validated['discount_percentage'];
            if ($discount > 0 && fmod($discount, 5) != 0) {
                return response()->json([
                    'error' => 'Discount percentage must be divisible by 5'
                ], 422);
            }
        }

        $roomType->update($validated);

        return response()->json([
            'message' => 'Room type updated successfully',
            'roomType' => $roomType
        ]);
    }

    public function destroy(RoomType $roomType)
    {
        $roomType->archive();

        return response()->json([
            'message' => 'Room type archived successfully'
        ]);
    }

    public function restore(RoomType $roomType)
    {
        $roomType->restore();

        return response()->json([
            'message' => 'Room type restored successfully'
        ]);
    }
}

