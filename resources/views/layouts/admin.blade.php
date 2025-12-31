<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - Beztower Hotel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-gray-100">
    @auth
    <!-- Sidebar -->
    <div class="flex h-screen">
        <aside class="w-64 bg-gray-800 text-white">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Beztower Admin</h1>
                <p class="text-sm text-gray-400 mt-1">{{ Auth::user()->role }}</p>
            </div>
            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}" class="block px-6 py-3 hover:bg-gray-700 @if(request()->routeIs('admin.dashboard')) bg-gray-700 @endif">
                    <i class="fas fa-dashboard mr-3"></i>Dashboard
                </a>
                <a href="{{ route('admin.rooms.index') }}" class="block px-6 py-3 hover:bg-gray-700 @if(request()->routeIs('admin.rooms.*')) bg-gray-700 @endif">
                    <i class="fas fa-bed mr-3"></i>Room Management
                </a>
                <a href="{{ route('admin.bookings.index') }}" class="block px-6 py-3 hover:bg-gray-700 @if(request()->routeIs('admin.bookings.*')) bg-gray-700 @endif">
                    <i class="fas fa-calendar-check mr-3"></i>Bookings
                </a>
                <a href="{{ route('admin.guests.index') }}" class="block px-6 py-3 hover:bg-gray-700 @if(request()->routeIs('admin.guests.*')) bg-gray-700 @endif">
                    <i class="fas fa-users mr-3"></i>Guests
                </a>
                <a href="{{ route('admin.housekeeping.index') }}" class="block px-6 py-3 hover:bg-gray-700 @if(request()->routeIs('admin.housekeeping.*')) bg-gray-700 @endif">
                    <i class="fas fa-broom mr-3"></i>Housekeeping
                </a>
                <form action="{{ route('admin.logout') }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="block w-full text-left px-6 py-3 hover:bg-gray-700">
                        <i class="fas fa-sign-out-alt mr-3"></i>Logout
                    </button>
                </form>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 overflow-y-auto">
            <header class="bg-white shadow">
                <div class="px-8 py-4">
                    <h2 class="text-2xl font-semibold text-gray-800">@yield('page-title')</h2>
                </div>
            </header>
            <main class="p-8">
                @yield('content')
            </main>
        </div>
    </div>
    @else
    <main>
        @yield('content')
    </main>
    @endauth

    @stack('scripts')
</body>
</html>
