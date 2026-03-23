<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = AppSetting::getMany([
            'hotel_name',
            'contact_email',
            'contact_phone',
            'hotel_address',
            'check_in_time',
            'check_out_time',
            'terms_and_conditions',
            'booking_policies',
        ]);

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'hotel_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:50',
            'hotel_address' => 'required|string',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'terms_and_conditions' => 'required|string',
            'booking_policies' => 'required|string',
        ]);

        foreach ($validated as $key => $value) {
            AppSetting::setValue($key, is_string($value) ? trim($value) : (string) $value);
        }

        return back()->with('success', 'Settings updated successfully!');
    }
}
