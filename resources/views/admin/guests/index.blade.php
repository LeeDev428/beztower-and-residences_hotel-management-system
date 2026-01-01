@extends('layouts.admin')

@section('title', 'Guests')
@section('page-title', 'Guest Management')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; gap: 1rem;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search guests..." style="flex: 1; padding: 0.75rem; border: 1px solid var(--border-gray); border-radius: 8px;">
        <x-admin.button type="primary">Search</x-admin.button>
    </form>
</div>

<x-admin.card title="All Guests ({{ $guests->total() }})">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-gray);">
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Name</th>
                    <th style="text-align: left; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Contact</th>
                    <th style="text-align: center; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Total Bookings</th>
                    <th style="text-align: center; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Last Booking</th>
                    <th style="text-align: right; padding: 0.75rem; font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($guests as $guest)
                <tr style="border-bottom: 1px solid var(--border-gray);">
                    <td style="padding: 1rem 0.75rem;">
                        <div style="font-weight: 600;">{{ $guest->name }}</div>
                    </td>
                    <td style="padding: 1rem 0.75rem;">
                        <div>{{ $guest->email }}</div>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">{{ $guest->phone }}</div>
                    </td>
                    <td style="padding: 1rem 0.75rem; text-align: center; font-weight: 600;">
                        {{ $guest->bookings_count }}
                    </td>
                    <td style="padding: 1rem 0.75rem; text-align: center;">
                        @if($guest->created_at)
                        {{ $guest->created_at->diffForHumans() }}
                        @else
                        N/A
                        @endif
                    </td>
                    <td style="padding: 1rem 0.75rem; text-align: right;">
                        <x-admin.button type="outline" size="sm" href="{{ route('admin.guests.show', $guest) }}">
                            View Profile
                        </x-admin.button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $guests->links() }}
    </div>
</x-admin.card>
@endsection
