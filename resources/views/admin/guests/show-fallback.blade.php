<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            color: #222;
        }
        .container {
            max-width: 900px;
            margin: 2rem auto;
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: 10px;
            padding: 1.25rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .btn {
            display: inline-block;
            text-decoration: none;
            border: 1px solid #d0d0d0;
            border-radius: 8px;
            padding: 0.45rem 0.8rem;
            color: #333;
            background: #fafafa;
        }
        .muted {
            color: #666;
            font-size: 0.95rem;
        }
        .row {
            margin: 0.45rem 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">Guest Profile</h2>
            <a class="btn" href="{{ route('admin.guests.index') }}">Back to Guests</a>
        </div>

        <p class="muted">Fallback view loaded to avoid a server error while full profile components are being stabilized.</p>

        <div class="row"><strong>Guest Identifier:</strong> {{ $guestIdentifier ?? 'N/A' }}</div>
        <div class="row"><strong>Name:</strong> {{ $guest->name ?? trim(($guest->first_name ?? '') . ' ' . ($guest->last_name ?? '')) ?: 'N/A' }}</div>
        <div class="row"><strong>Email:</strong> {{ $guest->email ?? 'N/A' }}</div>
        <div class="row"><strong>Phone:</strong> {{ $guest->phone ?? 'N/A' }}</div>

        @php
            $routeKey = $guest->id ?? $guest->guest_id ?? $guestIdentifier ?? null;
        @endphp

        @if($routeKey)
            <div style="margin-top:1rem;">
                <a class="btn" href="{{ route('admin.guests.show', $routeKey) }}">Retry Full Profile</a>
            </div>
        @endif
    </div>
</body>
</html>
