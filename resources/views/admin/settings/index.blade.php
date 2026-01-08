@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<x-admin.card title="Hotel Information">
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Hotel Name</label>
                <input type="text" name="hotel_name" value="Bez Tower & Residences" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Contact Email</label>
                <input type="email" name="contact_email" value="beztower05@gmail.com" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Contact Phone</label>
                <input type="text" name="contact_phone" value="(02) 88075046 or 09171221429" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tax Rate (%)</label>
                <input type="number" name="tax_rate" value="12" step="0.01" min="0" max="100" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Address</label>
            <textarea name="address" rows="3" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">205 F. Blumentritt Street, Brgy. Pedro Cruz, San Juan City, Philippines</textarea>
        </div>

        <div style="display: flex; gap: 1rem;">
            <x-admin.button type="primary">Save Settings</x-admin.button>
        </div>
    </form>
</x-admin.card>

<x-admin.card title="Booking Policies" style="margin-top: 1.5rem;">
    <div style="line-height: 1.8;">
        <h4 style="margin-bottom: 1rem; color: var(--dark-gray);">Current Policies:</h4>
        <ul style="padding-left: 1.5rem;">
            <li>Check-in time: 2:00 PM onwards</li>
            <li>Check-out time: 12:00 PM</li>
            <li>Free cancellation up to 24 hours before check-in</li>
            <li>Valid government-issued ID required upon check-in</li>
            <li>Down payment: 30% of total amount</li>
            <li>Tax: 12% applied to all bookings</li>
        </ul>
    </div>
</x-admin.card>
@endsection
