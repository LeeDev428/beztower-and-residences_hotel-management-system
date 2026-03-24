<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extra;
use Illuminate\Http\Request;

class ExtraController extends Controller
{
    public function index(Request $request)
    {
        $query = Extra::query()->withCount('bookings');

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            }

            if ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $extras = $query->orderBy('name')->paginate(15)->withQueryString();
        $editExtra = null;

        if ($request->filled('edit')) {
            $editExtra = Extra::find($request->integer('edit'));
        }

        return view('admin.extras.index', compact('extras', 'editExtra'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:extras,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        Extra::create($validated);

        return redirect()
            ->route('admin.extras.index')
            ->with('success', 'Extra created successfully.');
    }

    public function update(Request $request, Extra $extra)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:extras,name,' . $extra->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $extra->update($validated);

        return redirect()
            ->route('admin.extras.index')
            ->with('success', 'Extra updated successfully.');
    }

    public function destroy(Extra $extra)
    {
        if ($extra->bookings()->exists()) {
            return redirect()
                ->route('admin.extras.index')
                ->with('error', 'This extra is already used in bookings and cannot be deleted. You can set it as inactive instead.');
        }

        $extra->delete();

        return redirect()
            ->route('admin.extras.index')
            ->with('success', 'Extra deleted successfully.');
    }
}
