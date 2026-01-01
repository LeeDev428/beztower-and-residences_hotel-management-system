<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Housekeeping;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class HousekeepingController extends Controller
{
    public function index(Request $request)
    {
        $query = Housekeeping::with(['room', 'room.roomType']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $housekeeping = $query->paginate(20);
        
        // Get users who can be assigned (receptionist role or housekeeping staff)
        $staff = User::whereIn('role', ['receptionist', 'admin', 'manager'])->get();

        // Statistics
        $stats = [
            'clean' => Housekeeping::where('status', 'clean')->count(),
            'dirty' => Housekeeping::where('status', 'dirty')->count(),
            'in_progress' => Housekeeping::where('status', 'in_progress')->count(),
        ];

        return view('admin.housekeeping.index', compact('housekeeping', 'staff', 'stats'));
    }

    public function update(Request $request, Housekeeping $housekeeping)
    {
        $validated = $request->validate([
            'status' => 'required|in:clean,dirty,in_progress',
            'notes' => 'nullable|string',
        ]);

        $data = [
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $housekeeping->notes,
        ];

        if ($validated['status'] === 'clean') {
            $data['last_cleaned_at'] = now();
        }

        $housekeeping->update($data);

        // Update room status
        if ($validated['status'] === 'clean') {
            $housekeeping->room->update(['status' => 'available']);
        }

        return back()->with('success', 'Housekeeping status updated successfully!');
    }

    public function assign(Request $request)
    {
        $validated = $request->validate([
            'housekeeping_id' => 'required|exists:housekeeping,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        $housekeeping = Housekeeping::findOrFail($validated['housekeeping_id']);
        $housekeeping->update([
            'assigned_to' => $validated['assigned_to'],
            'status' => 'in_progress',
        ]);

        return back()->with('success', 'Task assigned successfully!');
    }
}
