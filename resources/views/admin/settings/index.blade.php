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
                <input type="text" name="hotel_name" value="{{ old('hotel_name', $settings['hotel_name'] ?? '') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Contact Email</label>
                <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Contact Phone</label>
                <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Check-in Time</label>
                <input type="time" name="check_in_time" value="{{ old('check_in_time', $settings['check_in_time'] ?? '14:00') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Check-out Time</label>
                <input type="time" name="check_out_time" value="{{ old('check_out_time', $settings['check_out_time'] ?? '12:00') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">VAT Percentage (%)</label>
                <input type="number" name="vat_percentage" value="{{ old('vat_percentage', $settings['vat_percentage'] ?? '12') }}" min="0" max="100" step="0.01" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
            </div>

        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Address</label>
            <textarea name="hotel_address" rows="3" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">{{ old('hotel_address', $settings['hotel_address'] ?? '') }}</textarea>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Terms and Conditions (Customer)</label>
            <textarea name="terms_and_conditions" rows="9" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">{{ old('terms_and_conditions', $settings['terms_and_conditions'] ?? '') }}</textarea>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Booking Policies (Customer)</label>
            <textarea name="booking_policies" rows="9" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">{{ old('booking_policies', $settings['booking_policies'] ?? '') }}</textarea>
        </div>

        <div style="display: flex; gap: 1rem;">
            <x-admin.button type="primary">Save Settings</x-admin.button>
        </div>
    </form>
</x-admin.card>
@endsection
