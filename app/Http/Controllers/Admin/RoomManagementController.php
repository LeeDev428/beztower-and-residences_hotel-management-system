<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\RoomPhoto;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RoomManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with(['roomType', 'amenities', 'photos']);

        // Filter archived/active rooms
        if ($request->filled('archived')) {
            if ($request->archived === 'yes') {
                $query->archived();
            } else {
                $query->active();
            }
        } else {
            // By default, show only active rooms
            $query->active();
        }

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
        $roomTypes = RoomType::active()->get();

        return view('admin.rooms.index', compact('rooms', 'roomTypes'));
    }

    public function create()
    {
        $roomTypes = RoomType::active()->get();
        $amenities = Amenity::active()->get();
        
        return view('admin.rooms.create', compact('roomTypes', 'amenities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|max:3|unique:rooms',
            'room_type_id' => ['required', Rule::exists('room_types', 'id')->whereNull('archived_at')],
            'floor' => 'required|integer|min:1|max:99',
            'status' => 'required|in:available,occupied,dirty,in_progress,maintenance,blocked',
            'discount_percentage' => 'nullable|numeric|min:0|max:100|multiple_of:5',
            'description' => 'nullable|string',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
            'photos.*' => 'nullable|image|max:5120', // 5MB max
        ]);

        $room = Room::create([
            'room_number' => $validated['room_number'],
            'room_type_id' => $validated['room_type_id'],
            'floor' => $validated['floor'],
            'discount_percentage' => (float) ($validated['discount_percentage'] ?? 0),
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
        $roomTypes = RoomType::active()->get();
        $amenities = Amenity::active()->get();
        
        return view('admin.rooms.edit', compact('room', 'roomTypes', 'amenities'));
    }

    public function update(Request $request, Room $room)
    {
        // Manager/Receptionist can only update room status
        if (in_array(Auth::user()?->role, ['manager', 'receptionist'])) {
            $validated = $request->validate(['status' => 'required|in:available,occupied,dirty,in_progress,maintenance']);
            $room->update(['status' => $validated['status']]);
            ActivityLog::log('room_status_update', 'Updated room #' . $room->room_number . ' status to ' . $validated['status'], 'App\Models\Room', $room->id);
            return redirect()->route('admin.rooms.index')->with('success', 'Room status updated successfully!');
        }

        $validated = $request->validate([
            'room_number' => 'required|string|max:3|unique:rooms,room_number,' . $room->id,
            'room_type_id' => [
                'required',
                Rule::exists('room_types', 'id')->whereNull('archived_at'),
            ],
            'floor' => 'required|integer|min:1|max:99',
            'status' => 'required|in:available,occupied,dirty,in_progress,maintenance,blocked',
            'discount_percentage' => 'nullable|numeric|min:0|max:100|multiple_of:5',
            'description' => 'nullable|string',
            'amenities' => 'array',
            'amenities.*' => 'exists:amenities,id',
            'photos.*' => 'nullable|image|max:5120', // 5MB max
        ]);

        $room->update([
            'room_number' => $validated['room_number'],
            'room_type_id' => $validated['room_type_id'],
            'floor' => $validated['floor'],
            'discount_percentage' => (float) ($validated['discount_percentage'] ?? 0),
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
        ]);

        // Sync amenities
        if (isset($validated['amenities'])) {
            $activeAmenityIds = Amenity::active()
                ->whereIn('id', $validated['amenities'])
                ->pluck('id')
                ->all();

            $room->amenities()->sync($activeAmenityIds);
        } else {
            $room->amenities()->detach();
        }

        // Handle new photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('rooms', 'public');
                RoomPhoto::create([
                    'room_id' => $room->id,
                    'photo_path' => $path,
                ]);
            }
        }

        // Log the activity
        ActivityLog::log(
            'room_edit',
            'Updated room #' . $room->room_number . ' (' . $room->roomType->name . ')',
            'App\Models\Room',
            $room->id,
            ['room_number' => $room->room_number, 'status' => $room->status]
        );

        return redirect()->route('admin.rooms.index')->with('success', 'Room updated successfully!');
    }

    public function destroy(Room $room)
    {
        if ($room->status === 'occupied') {
            return redirect()->route('admin.rooms.index')->with('error', 'Occupied rooms cannot be archived. Please check out the booking first.');
        }

        // Archive the room instead of deleting
        $room->archive();

        // Log the activity
        ActivityLog::log(
            'room_archive',
            'Archived room #' . $room->room_number,
            'App\Models\Room',
            $room->id
        );

        return redirect()->route('admin.rooms.index')->with('success', 'Room archived successfully!');
    }

    public function restore(Room $room)
    {
        // Restore archived room
        $room->restore();

        return redirect()->route('admin.rooms.index')->with('success', 'Room restored successfully!');
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

            // Log the activity
            ActivityLog::log(
                'image_update',
                'Added photo to room #' . $room->room_number,
                'App\Models\Room',
                $room->id
            );

            return back()->with('success', 'Photo uploaded successfully!');
        }

        return back()->with('error', 'Failed to upload photo.');
    }

    public function deletePhoto(Room $room, RoomPhoto $photo)
    {
        if ((int) $photo->room_id !== (int) $room->id) {
            return back()->with('error', 'Photo does not belong to this room.');
        }

        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();

        // Log the activity
        ActivityLog::log(
            'image_delete',
            'Deleted photo from room #' . $room->room_number,
            'App\Models\Room',
            $room->id
        );

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
