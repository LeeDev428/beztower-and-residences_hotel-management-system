<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    public function index(Request $request)
    {
        $query = Amenity::query();

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

        $amenities = $query->orderBy('name')->get();
        
        return response()->json([
            'amenities' => $amenities
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:amenities,name',
            'icon' => 'nullable|string'
        ]);

        $amenity = Amenity::create($validated);

        return response()->json([
            'message' => 'Amenity created successfully',
            'amenity' => $amenity
        ]);
    }

    public function update(Request $request, Amenity $amenity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:amenities,name,' . $amenity->id,
            'icon' => 'nullable|string'
        ]);

        $amenity->update($validated);

        return response()->json([
            'message' => 'Amenity updated successfully',
            'amenity' => $amenity
        ]);
    }

    public function destroy(Amenity $amenity)
    {
        $amenity->archive();

        return response()->json([
            'message' => 'Amenity archived successfully'
        ]);
    }

    public function restore(Amenity $amenity)
    {
        $amenity->restore();

        return response()->json([
            'message' => 'Amenity restored successfully'
        ]);
    }
}

