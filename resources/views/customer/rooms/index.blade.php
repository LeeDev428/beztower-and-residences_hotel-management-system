@extends('layouts.customer')

@section('title', 'Our Rooms - Beztower & Residences Hotel')

@section('content')
<!-- Page Header -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-5xl font-bold mb-4">Our Luxury Rooms</h1>
        <p class="text-xl">Find your perfect accommodation</p>
    </div>
</section>

<!-- Filters Section -->
<section class="bg-white shadow-md sticky top-20 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form id="filterForm" method="GET" action="{{ route('rooms.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Check In -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Check In</label>
                <input type="date" name="check_in" value="{{ request('check_in') }}" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" 
                       min="{{ date('Y-m-d') }}">
            </div>
            
            <!-- Check Out -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Check Out</label>
                <input type="date" name="check_out" value="{{ request('check_out') }}" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" 
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
            </div>
            
            <!-- Guests -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Guests</label>
                <select name="guests" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Any</option>
                    <option value="1" {{ request('guests') == '1' ? 'selected' : '' }}>1 Guest</option>
                    <option value="2" {{ request('guests') == '2' ? 'selected' : '' }}>2 Guests</option>
                    <option value="3" {{ request('guests') == '3' ? 'selected' : '' }}>3 Guests</option>
                    <option value="4" {{ request('guests') == '4' ? 'selected' : '' }}>4+ Guests</option>
                </select>
            </div>
            
            <!-- Room Type -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Room Type</label>
                <select name="room_type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ request('room_type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filter Button -->
            <div class="flex items-end">
                <button type="button" onclick="toggleAdvancedFilters()" 
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg">
                    <i class="fas fa-filter mr-2"></i>More Filters
                </button>
            </div>
        </form>
        
        <!-- Advanced Filters (Hidden by default) -->
        <div id="advancedFilters" class="hidden mt-6 p-6 bg-gray-50 rounded-lg">
            <form method="GET" action="{{ route('rooms.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Price Range -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Price Range (per night)</label>
                    <div class="flex items-center space-x-4">
                        <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <span class="text-gray-500">to</span>
                        <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    </div>
                </div>
                
                <!-- Amenities -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Amenities</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($amenities as $amenity)
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" 
                                       {{ in_array($amenity->id, request('amenities', [])) ? 'checked' : '' }}
                                       class="rounded text-blue-600 focus:ring-2 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <!-- Apply Filters -->
                <div class="md:col-span-3 flex justify-end space-x-4">
                    <a href="{{ route('rooms.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
                        Clear All
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Results Section -->
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Results Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Available Rooms</h2>
                <p class="text-gray-600 mt-1">{{ $rooms->total() }} rooms found</p>
            </div>
            
            <div>
                <select name="sort" onchange="this.form.submit()" 
                        class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">Sort By</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                </select>
            </div>
        </div>
        
        <!-- Room Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @forelse($rooms as $room)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-300">
                    <div class="relative h-64">
                        @if($room->photos->count() > 0)
                            <img src="{{ asset('storage/' . $room->photos->first()->photo_path) }}" 
                                 alt="{{ $room->roomType->name }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-bed text-6xl text-gray-400"></i>
                            </div>
                        @endif
                        
                        <div class="absolute top-4 right-4 bg-blue-600 text-white px-4 py-2 rounded-lg font-bold">
                            ₱{{ number_format($room->roomType->base_price, 2) }}/night
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">{{ $room->roomType->name }}</h3>
                                <p class="text-gray-600 text-sm mt-1">Room {{ $room->room_number }} • Floor {{ $room->floor }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-sm text-gray-600">
                                    <i class="fas fa-user mr-1"></i>Up to {{ $room->roomType->max_guests }} guests
                                </span>
                            </div>
                        </div>
                        
                        <p class="text-gray-700 mb-4 line-clamp-2">{{ $room->roomType->description }}</p>
                        
                        <!-- Amenities -->
                        @if($room->amenities->count() > 0)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($room->amenities->take(4) as $amenity)
                                    <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full">
                                        {{ $amenity->name }}
                                    </span>
                                @endforeach
                                @if($room->amenities->count() > 4)
                                    <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full">
                                        +{{ $room->amenities->count() - 4 }} more
                                    </span>
                                @endif
                            </div>
                        @endif
                        
                        <div class="flex space-x-4">
                            <a href="{{ route('rooms.show', $room->id) }}" 
                               class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 px-6 rounded-lg transition">
                                View Details
                            </a>
                            <a href="{{ route('rooms.show', $room->id) }}#book" 
                               class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-center py-20">
                    <i class="fas fa-bed text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-2xl font-semibold text-gray-600 mb-2">No rooms found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your filters or dates</p>
                    <a href="{{ route('rooms.index') }}" 
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg">
                        Clear Filters
                    </a>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination -->
        <div class="mt-12">
            {{ $rooms->links() }}
        </div>
    </div>
</section>

@push('scripts')
<script>
    function toggleAdvancedFilters() {
        const filters = document.getElementById('advancedFilters');
        filters.classList.toggle('hidden');
    }
    
    // Auto-submit on basic filter change
    document.querySelectorAll('#filterForm select, #filterForm input[type="date"]').forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush
@endsection
