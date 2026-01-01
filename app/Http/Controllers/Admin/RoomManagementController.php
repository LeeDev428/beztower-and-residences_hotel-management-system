<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\RoomPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['roomType', 'amenities', 'photos']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('room_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('roomType', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('room_type_id', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rooms = $query->paginate(15);
        $roomTypes = RoomType::all();

        return view('admin.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function create()
    {
        $roomTypes = RoomType::all();
        $amenities = Amenity::all();
        
        return view('admin.rooms.create', compact('roomTypes', 'amenities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms',
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance,blocked',
            'description' => 'nullable|string',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
            'photos.*' => 'nullable|image|max:5120', // 5MB max
        ]);

        $room = Room::create([
            'room_number' => $validated['room_number'],
            'room_type_id' => $validated['room_type_id'],
            'floor' => $validated['floor'],
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
        ]);

        // Attach amenities
        if (isset($validated['amenities'])) {
            $room->amenities()->attach($validated['amenities']);
        }

        // Handle photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('rooms', 'public');
                RoomPhoto::create([
                    'room_id' => $room->id,
                    'photo_path' => $path,
                ]);
            }
        }

        return redirect()->route('admin.rooms.index')->with('success', 'Room created successfully!');
    }

    public function edit(Room $room)
    {
        $room->load(['roomType', 'amenities', 'photos']);
        $roomTypes = RoomType::all();
        $amenities = Amenity::all();
        
        return view('admin.rooms.edit', compact('room', 'roomTypes', 'amenities'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance,blocked',
            'description' => 'nullable|string',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        $room->update([
            'room_number' => $validated['room_number'],
            'room_type_id' => $validated['room_type_id'],
            'floor' => $validated['floor'],
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
        ]);

        // Sync amenities
        if (isset($validated['amenities'])) {
            $room->amenities()->sync($validated['amenities']);
        } else {
            $room->amenities()->detach();
        }

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully!');
    }

    public function destroy(Room $room)
    {
        // Delete associated photos
        foreach ($room->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
            $photo->delete();
        }

        $room->delete();

        return redirect()->route('admin.rooms.index')->with('success', 'Room deleted successfully!');
    }

    public function uploadPhoto(Request $request, Room $room)
    {
        $request->validate([
            'photo' => 'required|image|max:5120', // 5MB max
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('rooms', 'public');
            RoomPhoto::create([
                'room_id' => $room->id,
                'photo_path' => $path,
            ]);

            return back()->with('success', 'Photo uploaded successfully!');
        }

        return back()->with('error', 'Failed to upload photo.');
    }

    public function deletePhoto(Room $room, RoomPhoto $photo)
    {
        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        return back()->with('success', 'Photo deleted successfully!');
    }

    public function blockDates(Request $request, Room $room)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:255',
        ]);

        // Update room status to blocked
        $room->update(['status' => 'blocked']);

        // Store block information (you might want to create a RoomBlock model for this)
        // For now, we'll just update the status

        return back()->with('success', 'Room blocked successfully from ' . $validated['start_date'] . ' to ' . $validated['end_date']);
    }
}
