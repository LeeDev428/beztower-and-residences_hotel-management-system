<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // In a real application, you would fetch settings from a database
        $settings = [
            'hotel_name' => 'Beztower & Residences',
            'hotel_email' => 'info@beztower.com',
            'hotel_phone' => '+63 123 456 7890',
            'hotel_address' => 'Manila, Philippines',
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
            'currency' => 'PHP',
            'tax_rate' => 12,
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'hotel_name' => 'required|string|max:255',
            'hotel_email' => 'required|email',
            'hotel_phone' => 'required|string|max:20',
            'hotel_address' => 'required|string',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'currency' => 'required|string|max:3',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        // In a real application, you would save these to a database
        // For now, we'll just return success

        return back()->with('success', 'Settings updated successfully!');
    }
}
