<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Our Rooms - Beztower & Residences</title>
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192x192.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon-192x192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon-192x192.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            overflow-x: hidden;
            padding-top: 80px;
        }

        .content-section {
            padding: 5rem 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            font-size: 3rem;
            font-weight: 300;
            margin-bottom: 2rem;
            color: #2c2c2c;
            font-family: 'Georgia', serif;
            text-align: center;
        }

        .section-subtitle {
            color: #d4af37;
            font-size: 0.9rem;
            letter-spacing: 3px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .section-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #666;
            text-align: center;
            max-width: 800px;
            margin: 0 auto 3rem;
        }
    </style>
</head>
<body>
    @include('components.navbar')

<section class="content-section">
    <div class="section-subtitle">ACCOMMODATION</div>
    <h2 class="section-title">Our Rooms</h2>
    <p class="section-description">
        Discover our collection of elegantly designed rooms and suites, each offering the perfect blend of comfort and sophistication.
    </p>

    <!-- Filters Section -->
    <div class="filters-container">
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Search rooms by name, type, or description..." />
        </div>

        <div class="filters-grid">
            <!-- Room Type -->
            <div class="filter-group">
                <label><i class="fas fa-bed"></i> Room Type</label>
                <select id="roomType">
                    <option value="">All Types</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sorting -->
            <div class="filter-group">
                <label><i class="fas fa-sort"></i> Sort By</label>
                <select id="sortBy">
                    <option value="">Default</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="name">Name: A-Z</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div class="filter-group">
                <button type="button" id="resetFilters" class="reset-btn">
                    <i class="fas fa-redo"></i> Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Hidden booking context from session-backed flow -->
    <input type="hidden" id="ctxCheckIn"  value="{{ data_get($bookingContext ?? [], 'check_in', '') }}">
    <input type="hidden" id="ctxCheckOut" value="{{ data_get($bookingContext ?? [], 'check_out', '') }}">
    <input type="hidden" id="ctxGuests"   value="{{ data_get($bookingContext ?? [], 'guests', '') }}">
    <input type="hidden" id="ctxAdults"   value="{{ data_get($bookingContext ?? [], 'adults', '') }}">
    <input type="hidden" id="ctxChildren" value="{{ data_get($bookingContext ?? [], 'children', '') }}">
    <input type="hidden" id="ctxRooms"    value="{{ (int) ($requestedRooms ?? data_get($bookingContext ?? [], 'rooms', 1)) }}">
    <input type="hidden" id="ctxSelectedRooms" value="{{ collect($selectedRoomIds ?? data_get($bookingContext ?? [], 'selected_rooms', []))->implode(',') }}">
    <input type="hidden" id="ctxSelectionUpdateUrl" value="{{ route('rooms.selection.update') }}">
    <input type="hidden" id="ctxStartCheckoutUrl" value="{{ route('booking.startCheckout') }}">

    @php
        $requestedRooms = max(1, min(12, (int) ($requestedRooms ?? data_get($bookingContext ?? [], 'rooms', 1))));
        $selectedRoomIds = collect($selectedRoomIds ?? data_get($bookingContext ?? [], 'selected_rooms', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $requestedGuests = (int) ($selectionMeta['requested_guests'] ?? (int) data_get($bookingContext ?? [], 'guests', 0));
        if ($requestedGuests <= 0) {
            $requestedGuests = max((int) data_get($bookingContext ?? [], 'adults', 0) + (int) data_get($bookingContext ?? [], 'children', 0), 0);
        }
        $selectedCapacity = (int) ($selectionMeta['selected_capacity'] ?? 0);
        $remainingGuests = (int) ($selectionMeta['remaining_guests'] ?? max($requestedGuests - $selectedCapacity, 0));
        $remainingRooms = max($requestedRooms - $selectedRoomIds->count(), 0);
        $selectedRoomSummary = collect($selectionMeta['selected_rooms_summary'] ?? []);

        $selectedRoomSummary = $selectedRoomSummary->values();
    @endphp

    @if($requestedRooms > 1)
        <div style="margin-bottom: 1rem; padding: 0.9rem 1rem; background: #fff9e6; border: 1px solid #f1dfab; border-radius: 8px; display:flex; justify-content:space-between; gap:0.8rem; align-items:center; flex-wrap: wrap;">
            <div style="font-size: 0.92rem; color:#5f4b1b; display:flex; flex-direction:column; gap:0.2rem;">
                <div>
                    Room Selection Progress: <strong>{{ $selectedRoomIds->count() }}</strong> of <strong>{{ $requestedRooms }}</strong> selected
                </div>
                @if($requestedGuests > 0)
                    <div>
                        Guests to accommodate: <strong>{{ $requestedGuests }}</strong>
                        | Covered by selected rooms: <strong>{{ $selectedCapacity }}</strong>
                        | Remaining guests: <strong>{{ $remainingGuests }}</strong>
                        | Rooms left to pick: <strong>{{ $remainingRooms }}</strong>
                    </div>
                @endif
                @if($selectedRoomSummary->isNotEmpty())
                    <div style="display:flex; flex-wrap:wrap; gap:0.35rem; margin-top:0.2rem;">
                        @foreach($selectedRoomSummary as $selectedRoom)
                            <span style="display:inline-flex; align-items:center; gap:0.25rem; background:#2c2c2c; color:#fff; border-radius:999px; padding:0.2rem 0.55rem; font-size:0.78rem; font-weight:600;">
                                {{ $selectedRoom['room_type'] ?? 'Room' }}
                                @if(isset($selectedRoom['capacity']))
                                    - {{ (int) $selectedRoom['capacity'] }} pax
                                @endif
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
            <button type="button" id="openSelectedRoomsDrawer" style="text-decoration:none; border:none; background: linear-gradient(135deg, #d4af37, #f4e4c1); color:#2c2c2c; padding:0.55rem 0.9rem; border-radius:6px; font-weight:700; font-size:0.85rem; cursor:pointer;">
                View Selected Rooms ({{ $selectedRoomIds->count() }}/{{ $requestedRooms }})
            </button>
        </div>
    @endif

    <!-- Results Counter -->
    <div class="results-info">
        <span id="resultsCount">Showing {{ $rooms->count() }} of {{ $rooms->total() }} rooms</span>
        <div class="loading-spinner" id="loadingSpinner" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
    </div>

    @if($rooms->count() > 0)
        <div class="rooms-grid" id="roomsGrid">
            @include('customer.rooms.partials.room-cards', [
                'bookingContext' => $bookingContext,
                'requestedRooms' => $requestedRooms,
                'selectedRoomIds' => $selectedRoomIds,
            ])
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper" id="paginationWrapper">
            @include('customer.rooms.partials.pagination')
        </div>
    @else
        <div class="no-rooms" id="noRoomsMessage">
            <i class="fas fa-bed"></i>
            <p>No rooms available for the selected criteria. Please try different dates or filters.</p>
        </div>
    @endif
</section>

<div id="selectedRoomsDrawer" class="selected-rooms-drawer" aria-hidden="true">
    <div class="selected-rooms-panel">
        <div class="selected-rooms-header">
            <h3>Selected Rooms</h3>
            <button type="button" id="closeSelectedRoomsDrawer" class="selected-rooms-close">&times;</button>
        </div>
        <div id="selectedRoomsList" class="selected-rooms-list"></div>
        <div class="selected-rooms-footer">
            <div id="selectedRoomsProgressText" class="selected-rooms-progress"></div>
            <form id="startCheckoutForm" method="POST" action="{{ route('booking.startCheckout') }}">
                @csrf
                <input type="hidden" name="rooms" value="{{ $requestedRooms }}">
                <div id="selectedRoomIdsInputs"></div>
                <button type="submit" id="proceedCheckoutButton" class="proceed-checkout-btn" disabled>Proceed to Billing Details</button>
            </form>
        </div>
    </div>
</div>

<div class="room-preview-modal" id="roomPreviewModal" aria-hidden="true">
    <div class="room-preview-card" role="dialog" aria-modal="true" aria-labelledby="roomPreviewTitle">
        <button type="button" class="room-preview-close" id="roomPreviewClose" aria-label="Close room preview">
            <i class="fas fa-times"></i>
        </button>
        <img src="" alt="Room preview" class="room-preview-image" id="roomPreviewImage">
        <div class="room-preview-thumbnails" id="roomPreviewThumbnails"></div>
        <div class="room-preview-content">
            <h3 class="room-preview-title" id="roomPreviewTitle"></h3>
            <div class="room-preview-meta" id="roomPreviewMeta"></div>
            <p class="room-preview-description" id="roomPreviewDescription"></p>
            <div class="room-preview-inclusions-wrap">
                <div class="room-preview-inclusions-title">Room Inclusions</div>
                <ul class="room-preview-inclusions" id="roomPreviewInclusions"></ul>
            </div>
            <div class="room-preview-footer">
                <div class="room-preview-price" id="roomPreviewPrice"></div>
                <div style="display:flex; gap:0.6rem; flex-wrap:wrap;">
                    <button type="button" class="book-btn" id="roomPreviewBackBtn">Back</button>
                    <button type="button" class="book-btn" id="roomPreviewToggleSelectBtn">Add to Cart</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar Modal -->
<div class="calendar-modal" id="calendarModal">
    <div class="calendar-content">
        <div class="calendar-header">
            <h3><i class="fas fa-calendar-alt"></i> Room Availability Calendar</h3>
            <button class="close-calendar" id="closeCalendar">&times;</button>
        </div>
        <div class="calendar-controls">
            <button class="calendar-nav" id="prevMonth"><i class="fas fa-chevron-left"></i></button>
            <h4 id="calendarMonthYear">Loading...</h4>
            <button class="calendar-nav" id="nextMonth"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="calendar-legend">
            <span class="legend-item"><span class="legend-dot available"></span> Available</span>
            <span class="legend-item"><span class="legend-dot partial"></span> Partially Booked</span>
            <span class="legend-item"><span class="legend-dot full"></span> Fully Booked</span>
        </div>
        <div id="calendarGrid" class="calendar-grid">
            <!-- Calendar will be populated here -->
        </div>
    </div>
</div>

<style>
    /* Filters Section */
    .filters-container {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        margin: 2rem 0;
    }

    .search-bar {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .search-bar i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #d4af37;
        font-size: 1.1rem;
    }

    .search-bar input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #eee;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .search-bar input:focus {
        outline: none;
        border-color: #d4af37;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        position: relative;
        z-index: 1;
    }

    .filter-group label {
        font-weight: 600;
        color: #666;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-group label i {
        color: #d4af37;
    }

    .filter-group select,
    .filter-group input[type="number"] {
        padding: 0.8rem;
        border: 2px solid #eee;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: border-color 0.3s;
        background: white;
        position: relative;
        z-index: 1;
    }

    .filter-group select:focus,
    .filter-group input[type="number"]:focus {
        outline: none;
        border-color: #d4af37;
        z-index: 2;
    }

    .price-inputs {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 0.5rem;
    }

    .price-inputs input {
        flex: none;
        width: 100%;
        min-width: 0;
    }

    .price-inputs span {
        color: #999;
    }

    .amenities-dropdown {
        position: relative;
    }

    .amenities-btn {
        width: 100%;
        padding: 0.8rem;
        border: 2px solid #eee;
        border-radius: 8px;
        background: white;
        text-align: left;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: border-color 0.3s;
    }

    .amenities-btn:hover,
    .amenities-btn.active {
        border-color: #d4af37;
    }

    .amenities-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 2px solid #d4af37;
        border-radius: 8px;
        margin-top: 0.5rem;
        max-height: 250px;
        overflow-y: auto;
        z-index: 100;
        display: none;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .amenities-list.active {
        display: block;
    }

    .amenity-checkbox {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 0.8rem;
        cursor: pointer;
        transition: background 0.2s;
    }

    .amenity-checkbox:hover {
        background: #f9f9f9;
    }

    .amenity-checkbox input {
        cursor: pointer;
    }

    .amenity-checkbox i {
        color: #d4af37;
        width: 20px;
    }

    .reset-btn {
        width: 100%;
        padding: 0.8rem;
        background: #f44336;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: background 0.3s;
        margin-top: 1.5rem;
    }

    .reset-btn:hover {
        background: #d32f2f;
    }

    .results-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 1.5rem 0;
        color: #666;
        font-size: 0.95rem;
    }

    .loading-spinner {
        color: #d4af37;
        font-weight: 600;
    }

    /* Calendar Modal */
    .calendar-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        overflow-y: auto;
        padding: 2rem;
    }

    .calendar-modal.active {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .calendar-content {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        max-width: 800px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #eee;
    }

    .calendar-header h3 {
        color: #2c2c2c;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .calendar-header h3 i {
        color: #d4af37;
    }

    .close-calendar {
        background: none;
        border: none;
        font-size: 2rem;
        color: #999;
        cursor: pointer;
        transition: color 0.3s;
    }

    .close-calendar:hover {
        color: #f44336;
    }

    .calendar-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .calendar-nav {
        background: linear-gradient(135deg, #d4af37, #f4e4c1);
        border: none;
        color: #2c2c2c;
        padding: 0.8rem 1.2rem;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: transform 0.3s;
    }

    .calendar-nav:hover {
        transform: scale(1.05);
    }

    .calendar-legend {
        display: flex;
        gap: 2rem;
        justify-content: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #666;
    }

    .legend-dot {
        width: 15px;
        height: 15px;
        border-radius: 50%;
    }

    .legend-dot.available {
        background: #4caf50;
    }

    .legend-dot.partial {
        background: #ff9800;
    }

    .legend-dot.full {
        background: #f44336;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.5rem;
    }

    .calendar-day-header {
        text-align: center;
        font-weight: 600;
        color: #d4af37;
        padding: 0.5rem;
        font-size: 0.9rem;
    }

    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 2px solid #eee;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
    }

    .calendar-day.empty {
        border: none;
        cursor: default;
    }

    .calendar-day.past {
        background: #f5f5f5;
        color: #ccc;
        cursor: not-allowed;
    }

    .calendar-day.available {
        border-color: #4caf50;
    }

    .calendar-day.available:hover {
        background: #e8f5e9;
        transform: scale(1.05);
    }

    .calendar-day.partial {
        border-color: #ff9800;
    }

    .calendar-day.partial:hover {
        background: #fff3e0;
    }

    .calendar-day.full {
        border-color: #f44336;
        background: #ffebee;
        cursor: not-allowed;
    }

    .calendar-day-number {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c2c2c;
    }

    .calendar-day-info {
        font-size: 0.7rem;
        color: #666;
        margin-top: 0.2rem;
    }

    /* Floating Calendar Button */
    .floating-calendar-btn {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: linear-gradient(135deg, #d4af37, #f4e4c1);
        color: #2c2c2c;
        border: none;
        padding: 1rem 1.5rem;
        border-radius: 50px;
        box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
        cursor: pointer;
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        z-index: 999;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .floating-calendar-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(212, 175, 55, 0.6);
    }
    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2.5rem;
        margin-top: 3rem;
    }
    
    .room-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .room-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }
    
    .room-image {
        position: relative;
        height: 250px;
        overflow: hidden;
    }
    
    .room-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    
    .room-card:hover .room-image img {
        transform: scale(1.1);
    }
    
  
    
    .room-details {
        padding: 1.5rem;
    }
    
    .room-details h3 {
        font-size: 1.5rem;
        color: #2c2c2c;
        margin-bottom: 0.5rem;
        font-family: 'Georgia', serif;
    }
    
    .room-info {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1rem;
        color: #666;
        font-size: 0.9rem;
    }
    
    .room-info i {
        color: #d4af37;
        margin-right: 0.3rem;
    }
    
    .room-description {
        color: #666;
        line-height: 1.6;
        margin-bottom: 1rem;
    }
    
    .room-amenities {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }
    
    .amenity-tag {
        background: #f9f9f9;
        padding: 0.4rem 0.8rem;
        border-radius: 5px;
        font-size: 0.85rem;
        color: #666;
    }
    
    .amenity-tag i {
        color: #d4af37;
        margin-right: 0.3rem;
    }
    
    .room-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
    }
    
    .room-price {
        display: flex;
        flex-direction: column;
    }
    
    .price-label {
        font-size: 0.85rem;
        color: #999;
    }
    
    .price-amount {
        font-size: 1.8rem;
        font-weight: 600;
        color: #d4af37;
    }
    
    .price-period {
        font-size: 0.85rem;
        color: #666;
    }
    
    .discount-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        font-weight: 700;
        font-size: 0.95rem;
        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
        z-index: 2;
    }
    
    .original-price {
        font-size: 1.2rem;
        color: #999;
        text-decoration: line-through;
        margin: 0.25rem 0;
    }
    
    .discounted-price {
        color: #dc3545 !important;
    }
    
    .book-btn {
        background: linear-gradient(135deg, #d4af37, #f4e4c1);
        color: #2c2c2c;
        padding: 0.8rem 1.5rem;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        transition: transform 0.3s;
    }
    
    .book-btn:hover {
        transform: translateY(-2px);
    }
    
    .no-rooms {
        text-align: center;
        padding: 4rem 2rem;
        color: #999;
    }
    
    .no-rooms i {
        font-size: 4rem;
        color: #d4af37;
        margin-bottom: 1rem;
    }
    
    .no-rooms p {
        font-size: 1.1rem;
    }
    
    /* Pagination Styles */
    .pagination-wrapper {
        margin-top: 3rem;
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper nav {
        display: inline-block;
    }

    .pagination-wrapper ul {
        display: flex;
        list-style: none;
        gap: 0.5rem;
        margin: 0;
        padding: 0;
    }

    .pagination-wrapper li {
        display: inline-block;
    }

    .pagination-wrapper a,
    .pagination-wrapper span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 45px;
        height: 45px;
        padding: 0.5rem 1rem;
        background: white;
        border: 2px solid #ddd;
        color: #2c2c2c;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
        cursor: pointer;
    }

    .pagination-wrapper a:hover {
        background: #f9f9f9;
        border-color: #d4af37;
        color: #d4af37;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
    }

    .pagination-wrapper .active span {
        background: linear-gradient(135deg, #d4af37, #f4e4c1);
        border-color: #d4af37;
        color: #2c2c2c;
        cursor: default;
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
    }

    .pagination-wrapper .disabled span {
        background: #f5f5f5;
        border-color: #e0e0e0;
        color: #999;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pagination-wrapper .disabled span:hover {
        transform: none;
        background: #f5f5f5;
        border-color: #e0e0e0;
    }

    .selected-rooms-drawer {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 1200;
        display: none;
        justify-content: flex-end;
    }

    .selected-rooms-drawer.active {
        display: flex;
    }

    .selected-rooms-panel {
        width: 100%;
        max-width: 430px;
        background: #fff;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: -10px 0 35px rgba(0, 0, 0, 0.2);
    }

    .selected-rooms-header {
        padding: 1rem 1.2rem;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .selected-rooms-close {
        border: none;
        background: transparent;
        font-size: 1.6rem;
        line-height: 1;
        cursor: pointer;
        color: #666;
    }

    .selected-rooms-list {
        flex: 1;
        overflow-y: auto;
        padding: 0.85rem 1.2rem;
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
    }

    .selected-room-item {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 0.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
    }

    .selected-room-item button {
        border: none;
        background: #f44336;
        color: #fff;
        border-radius: 6px;
        padding: 0.35rem 0.55rem;
        cursor: pointer;
    }

    .selected-rooms-footer {
        border-top: 1px solid #eee;
        padding: 0.9rem 1.2rem 1rem;
    }

    .selected-rooms-progress {
        font-size: 0.88rem;
        color: #5f4b1b;
        margin-bottom: 0.75rem;
    }

    .proceed-checkout-btn {
        width: 100%;
        border: none;
        border-radius: 8px;
        padding: 0.8rem 0.9rem;
        background: linear-gradient(135deg, #d4af37, #f4e4c1);
        color: #2c2c2c;
        font-weight: 700;
        cursor: pointer;
    }

    .proceed-checkout-btn[disabled] {
        cursor: not-allowed;
        opacity: 0.55;
    }

    .room-preview-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.62);
        z-index: 1250;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .room-preview-modal.active {
        display: flex;
    }

    .room-preview-card {
        width: 100%;
        max-width: 760px;
        background: #fff;
        border-radius: 12px;
        overflow-y: auto;
        max-height: min(92vh, 860px);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    .room-preview-close {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        border: none;
        background: rgba(20, 20, 20, 0.88);
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 1rem;
        cursor: pointer;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .room-preview-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        background: #f4f4f4;
    }

    .room-preview-thumbnails {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.5rem;
        padding: 0.75rem 1rem 0.1rem;
    }

    .room-preview-thumb {
        width: 100%;
        height: 74px;
        border-radius: 8px;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid transparent;
        transition: transform 0.2s, border-color 0.2s;
    }

    .room-preview-thumb.active {
        border-color: #a17d16;
    }

    .room-preview-content {
        padding: 1rem 1.2rem 1.2rem;
    }

    .room-preview-title {
        font-size: 1.35rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
        color: #2c2c2c;
    }

    .room-preview-meta {
        font-size: 0.9rem;
        font-weight: 600;
        color: #a17d16;
        margin-bottom: 0.65rem;
    }

    .room-preview-description {
        font-size: 0.9rem;
        line-height: 1.55;
        color: #555;
        margin-bottom: 0.65rem;
    }

    .room-preview-inclusions-wrap {
        background: #fafafa;
        border: 1px solid #ececec;
        border-radius: 8px;
        padding: 0.7rem;
        margin-bottom: 0.9rem;
    }

    .room-preview-inclusions {
        margin: 0;
        padding-left: 1rem;
        color: #555;
        font-size: 0.86rem;
        line-height: 1.45;
    }

    .room-preview-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .room-preview-price {
        font-size: 1.7rem;
        font-weight: 700;
        color: #d4af37;
    }
    
    @media (max-width: 768px) {
        .content-section {
            padding: 3rem 1.5rem;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .price-inputs {
            grid-template-columns: 1fr auto 1fr;
        }

        .rooms-grid {
            grid-template-columns: 1fr;
        }
        
        .room-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .book-btn {
            width: 100%;
            text-align: center;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .floating-calendar-btn {
            bottom: 1rem;
            right: 1rem;
            padding: 0.8rem 1.2rem;
        }

        .calendar-content {
            padding: 1rem;
        }

        .calendar-grid {
            gap: 0.3rem;
        }

        .room-preview-image {
            height: 230px;
        }

        .room-preview-thumbnails {
            grid-template-columns: repeat(2, 1fr);
        }

        .room-preview-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let filterTimeout = null;
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let calendarData = null;

    // Elements
    const searchInput = document.getElementById('searchInput');
    const minPriceInput = document.getElementById('minPrice');
    const maxPriceInput = document.getElementById('maxPrice');
    const roomTypeSelect = document.getElementById('roomType');
    const guestsSelect = document.getElementById('guests');
    const sortBySelect = document.getElementById('sortBy');
    const resetBtn = document.getElementById('resetFilters');
    const amenitiesBtn = document.getElementById('amenitiesBtn');
    const amenitiesList = document.getElementById('amenitiesList');
    const amenityCheckboxes = document.querySelectorAll('input[name="amenities[]"]');
    const resultsCount = document.getElementById('resultsCount');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const calendarModal = document.getElementById('calendarModal');
    const showCalendarBtn = document.getElementById('showCalendar');
    const closeCalendarBtn = document.getElementById('closeCalendar');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');
    const calendarGrid = document.getElementById('calendarGrid');
    const calendarMonthYear = document.getElementById('calendarMonthYear');
    const requiredRooms = Math.max(1, parseInt((document.getElementById('ctxRooms') || {}).value || '1', 10));
    let selectedRoomIds = (document.getElementById('ctxSelectedRooms') || { value: '' }).value
        .split(',')
        .map((item) => Number(item.trim()))
        .filter((item) => Number.isInteger(item) && item > 0)
        .slice(0, requiredRooms);

    // roomsGrid and paginationWrapper may not exist if no rooms found yet
    let roomsGrid = document.getElementById('roomsGrid');
    let paginationWrapper = document.getElementById('paginationWrapper');

    // Ensure containers exist for AJAX results
    function ensureContainers() {
        if (!roomsGrid) {
            const noRooms = document.getElementById('noRoomsMessage');
            // Create a rooms grid container if missing
            roomsGrid = document.createElement('div');
            roomsGrid.className = 'rooms-grid';
            roomsGrid.id = 'roomsGrid';
            if (noRooms) {
                noRooms.parentNode.insertBefore(roomsGrid, noRooms);
            }
        }
        if (!paginationWrapper) {
            paginationWrapper = document.createElement('div');
            paginationWrapper.className = 'pagination-wrapper';
            paginationWrapper.id = 'paginationWrapper';
            const section = document.querySelector('.content-section');
            if (section) section.appendChild(paginationWrapper);
        }
    }

    // Filter function with debounce
    function applyFilters(page = 1) {
        clearTimeout(filterTimeout);
        filterTimeout = setTimeout(() => {
            ensureContainers();
            loadingSpinner.style.display = 'inline-block';

            const params = new URLSearchParams();
            
            const searchValue = searchInput.value.trim();
            if (searchValue) params.append('search', searchValue);
            
            const minPrice = minPriceInput ? minPriceInput.value : '';
            if (minPrice) params.append('min_price', minPrice);
            
            const maxPrice = maxPriceInput ? maxPriceInput.value : '';
            if (maxPrice) params.append('max_price', maxPrice);
            
            const roomType = roomTypeSelect.value;
            if (roomType) params.append('room_type', roomType);
            
            const guests = guestsSelect ? guestsSelect.value : '';
            if (guests) params.append('guests', guests);
            
            const sortBy = (sortBySelect.value || '').trim();
            if (['price_low', 'price_high', 'name'].includes(sortBy)) {
                params.append('sort', sortBy);
            }
            
            // Pass booking context (check_in/check_out/guests) so Learn More links stay populated
            const ctxRooms = document.getElementById('ctxRooms').value;
            if (ctxRooms) params.append('rooms', ctxRooms);
            
            if (amenityCheckboxes.length > 0) {
                const selectedAmenities = Array.from(amenityCheckboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                selectedAmenities.forEach(amenity => params.append('amenities[]', amenity));
            }
            
            params.append('page', page);

            fetch(`{{ route('rooms.index') }}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                ensureContainers();
                roomsGrid.innerHTML = data.html;
                paginationWrapper.innerHTML = data.pagination;
                const noRooms = document.getElementById('noRoomsMessage');
                if (noRooms) noRooms.style.display = data.total > 0 ? 'none' : 'block';
                roomsGrid.style.display = data.total > 0 ? '' : 'none';
                resultsCount.textContent = `Showing ${data.total > 0 ? '1-' + Math.min(6, data.total) : 0} of ${data.total} rooms`;
                loadingSpinner.style.display = 'none';
                
                // Reattach pagination click handlers
                attachPaginationHandlers();
                bindRoomModalTriggers();
                renderSelectedRoomsDrawer();
            })
            .catch(error => {
                console.error('Error:', error);
                loadingSpinner.style.display = 'none';
            });
        }, 500);
    }

    // Attach pagination handlers
    function attachPaginationHandlers() {
        const paginationLinks = paginationWrapper.querySelectorAll('a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                if (page) {
                    applyFilters(page);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        });
    }

    // Event listeners
    searchInput.addEventListener('input', () => applyFilters());
    if (minPriceInput) minPriceInput.addEventListener('input', () => applyFilters());
    if (maxPriceInput) maxPriceInput.addEventListener('input', () => applyFilters());
    roomTypeSelect.addEventListener('change', () => applyFilters());
    if (guestsSelect) guestsSelect.addEventListener('change', () => applyFilters());
    sortBySelect.addEventListener('change', () => applyFilters());
    
    amenityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            if (typeof updateAmenitiesCount === 'function') updateAmenitiesCount();
            applyFilters();
        });
    });

    // Amenities dropdown toggle
    if (amenitiesBtn) {
        amenitiesBtn.addEventListener('click', () => {
            amenitiesList.classList.toggle('active');
            amenitiesBtn.classList.toggle('active');
        });

        // Close amenities dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!amenitiesBtn.contains(e.target) && amenitiesList && !amenitiesList.contains(e.target)) {
                amenitiesList.classList.remove('active');
                amenitiesBtn.classList.remove('active');
            }
        });
    }

    // Update amenities count
    function updateAmenitiesCount() {
        const count = Array.from(amenityCheckboxes).filter(cb => cb.checked).length;
        const countSpan = document.getElementById('amenitiesCount');
        if (countSpan) countSpan.textContent = count > 0 ? `${count} Selected` : 'Select Amenities';
    }

    // Reset filters
    resetBtn.addEventListener('click', () => {
        searchInput.value = '';
        if (minPriceInput) minPriceInput.value = '';
        if (maxPriceInput) maxPriceInput.value = '';
        roomTypeSelect.value = '';
        if (guestsSelect) guestsSelect.value = '';
        sortBySelect.value = '';
        amenityCheckboxes.forEach(cb => cb.checked = false);
        updateAmenitiesCount();
        applyFilters();
    });

    // Calendar functionality
    if (showCalendarBtn) {
        showCalendarBtn.addEventListener('click', () => {
            calendarModal.classList.add('active');
            loadCalendar(currentMonth, currentYear);
        });
    }

    closeCalendarBtn.addEventListener('click', () => {
        calendarModal.classList.remove('active');
    });

    calendarModal.addEventListener('click', (e) => {
        if (e.target === calendarModal) {
            calendarModal.classList.remove('active');
        }
    });

    prevMonthBtn.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        loadCalendar(currentMonth, currentYear);
    });

    nextMonthBtn.addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        loadCalendar(currentMonth, currentYear);
    });

    function loadCalendar(month, year) {
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];
        
        calendarMonthYear.textContent = `${monthNames[month]} ${year}`;
        calendarGrid.innerHTML = '<div style="text-align:center;padding:2rem;grid-column:1/-1;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

        fetch(`{{ route('calendar.availability') }}?month=${month + 1}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                calendarData = data;
                renderCalendar(month, year, data);
            })
            .catch(error => {
                console.error('Error loading calendar:', error);
                calendarGrid.innerHTML = '<div style="text-align:center;padding:2rem;grid-column:1/-1;color:#f44336;">Error loading calendar</div>';
            });
    }

    function renderCalendar(month, year, data) {
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        let html = '';
        
        // Day headers
        const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        dayHeaders.forEach(day => {
            html += `<div class="calendar-day-header">${day}</div>`;
        });

        // Empty cells before first day
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="calendar-day empty"></div>';
        }

        // Days
        for (let day = 1; day <= daysInMonth; day++) {
            const currentDate = new Date(year, month, day);
            const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            
            const bookedInfo = data.booked_dates.find(d => d.date === dateString);
            const blockedInfo = data.block_dates.find(d => d.date === dateString);
            
            let className = 'calendar-day';
            let statusText = 'Available';
            
            if (currentDate < today) {
                className += ' past';
                statusText = '';
            } else if (blockedInfo) {
                className += ' full';
                statusText = 'Blocked';
            } else if (bookedInfo) {
                const bookedCount = bookedInfo.booked_rooms;
                if (bookedCount >= data.total_rooms) {
                    className += ' full';
                    statusText = 'Full';
                } else {
                    className += ' partial';
                    statusText = `${data.total_rooms - bookedCount} available`;
                }
            } else {
                className += ' available';
                statusText = 'Available';
            }

            html += `
                <div class="${className}" data-date="${dateString}">
                    <div class="calendar-day-number">${day}</div>
                    <div class="calendar-day-info">${statusText}</div>
                </div>
            `;
        }

        calendarGrid.innerHTML = html;
    }

    // Initialize pagination handlers on page load
    attachPaginationHandlers();

    const selectedRoomsDrawer = document.getElementById('selectedRoomsDrawer');
    const selectedRoomsList = document.getElementById('selectedRoomsList');
    const selectedRoomIdsInputs = document.getElementById('selectedRoomIdsInputs');
    const selectedRoomsProgressText = document.getElementById('selectedRoomsProgressText');
    const proceedCheckoutButton = document.getElementById('proceedCheckoutButton');
    const openSelectedRoomsDrawerButton = document.getElementById('openSelectedRoomsDrawer');
    const closeSelectedRoomsDrawerButton = document.getElementById('closeSelectedRoomsDrawer');
    const roomPreviewModal = document.getElementById('roomPreviewModal');
    const roomPreviewClose = document.getElementById('roomPreviewClose');
    const roomPreviewImage = document.getElementById('roomPreviewImage');
    const roomPreviewTitle = document.getElementById('roomPreviewTitle');
    const roomPreviewMeta = document.getElementById('roomPreviewMeta');
    const roomPreviewDescription = document.getElementById('roomPreviewDescription');
    const roomPreviewInclusions = document.getElementById('roomPreviewInclusions');
    const roomPreviewPrice = document.getElementById('roomPreviewPrice');
    const roomPreviewBackBtn = document.getElementById('roomPreviewBackBtn');
    const roomPreviewThumbnails = document.getElementById('roomPreviewThumbnails');
    const roomPreviewToggleSelectBtn = document.getElementById('roomPreviewToggleSelectBtn');
    let roomPreviewImages = [];
    let currentRoomPreviewImageIndex = 0;
    let activeRoomPreviewId = null;
    let activeRoomPreviewName = 'Room';

    function getCsrfToken() {
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        return tokenMeta ? tokenMeta.getAttribute('content') : '';
    }

    function persistSelection() {
        const endpoint = (document.getElementById('ctxSelectionUpdateUrl') || {}).value;
        if (!endpoint) {
            return Promise.resolve();
        }

        return fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            body: JSON.stringify({
                selected_rooms: selectedRoomIds,
                rooms: requiredRooms,
            }),
        }).then((response) => {
            if (!response.ok) {
                throw new Error('Failed to persist room selection.');
            }

            return response.json();
        }).then((payload) => {
            if (Array.isArray(payload.selected_rooms)) {
                selectedRoomIds = payload.selected_rooms
                    .map((value) => Number(value))
                    .filter((value) => Number.isInteger(value) && value > 0)
                    .slice(0, requiredRooms);
            }

            const selectedRoomsInput = document.getElementById('ctxSelectedRooms');
            if (selectedRoomsInput) {
                selectedRoomsInput.value = selectedRoomIds.join(',');
            }
        }).catch((error) => {
            console.error(error);
        });
    }

    function renderSelectedRoomsDrawer() {
        if (!selectedRoomsList || !selectedRoomIdsInputs || !selectedRoomsProgressText || !proceedCheckoutButton) {
            return;
        }

        selectedRoomsList.innerHTML = '';
        selectedRoomIdsInputs.innerHTML = '';

        const cards = Array.from(document.querySelectorAll('.room-card [data-open-room-modal]'));
        const roomLookup = new Map();
        cards.forEach((trigger) => {
            const roomId = Number(trigger.getAttribute('data-room-id'));
            if (!Number.isInteger(roomId) || roomId <= 0) {
                return;
            }

            roomLookup.set(roomId, {
                name: trigger.getAttribute('data-room-name') || `Room ${roomId}`,
                price: trigger.getAttribute('data-room-price') || '0.00',
            });
        });

        if (selectedRoomIds.length === 0) {
            selectedRoomsList.innerHTML = '<p style="color:#666;">No rooms selected yet.</p>';
        }

        selectedRoomIds.forEach((roomId) => {
            const roomData = roomLookup.get(roomId) || { name: `Room ${roomId}`, price: '0.00' };
            const item = document.createElement('div');
            item.className = 'selected-room-item';
            item.innerHTML = `
                <div>
                    <div style="font-weight:700;">${roomData.name}</div>
                    <div style="font-size:0.85rem;color:#666;">₱${roomData.price}/night</div>
                </div>
                <button type="button" data-remove-room-id="${roomId}">Remove</button>
            `;
            selectedRoomsList.appendChild(item);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'room_ids[]';
            input.value = String(roomId);
            selectedRoomIdsInputs.appendChild(input);
        });

        const selectedCount = selectedRoomIds.length;
        selectedRoomsProgressText.textContent = `Selected ${selectedCount} of ${requiredRooms} room(s).`;
        proceedCheckoutButton.disabled = selectedCount < requiredRooms;

        if (openSelectedRoomsDrawerButton) {
            openSelectedRoomsDrawerButton.textContent = `View Selected Rooms (${selectedCount}/${requiredRooms})`;
        }

        selectedRoomsList.querySelectorAll('button[data-remove-room-id]').forEach((button) => {
            button.addEventListener('click', () => {
                const roomId = Number(button.getAttribute('data-remove-room-id'));
                selectedRoomIds = selectedRoomIds.filter((id) => id !== roomId);
                persistSelection().finally(() => {
                    renderSelectedRoomsDrawer();
                    updateModalButtonState();
                    markSelectedCards();
                });
            });
        });
    }

    function markSelectedCards() {
        const cards = Array.from(document.querySelectorAll('.room-card [data-open-room-modal]'));
        cards.forEach((trigger) => {
            const roomId = Number(trigger.getAttribute('data-room-id'));
            const isSelected = selectedRoomIds.includes(roomId);
            trigger.setAttribute('data-is-selected', isSelected ? '1' : '0');

            const selectedBadge = trigger.closest('.room-card').querySelector('[data-selected-badge]');
            if (selectedBadge) {
                selectedBadge.remove();
            }

            if (isSelected) {
                const badge = document.createElement('div');
                badge.setAttribute('data-selected-badge', '1');
                badge.style.cssText = 'position:absolute;top:10px;right:10px;background:#27ae60;color:#fff;padding:0.4rem 0.7rem;border-radius:6px;font-size:0.8rem;font-weight:700;';
                badge.textContent = 'Selected';
                const imageWrap = trigger.closest('.room-card').querySelector('.room-image');
                if (imageWrap) {
                    imageWrap.appendChild(badge);
                }
            }
        });
    }

    function renderRoomPreviewImage(index) {
        if (!roomPreviewImage || roomPreviewImages.length === 0) {
            return;
        }

        currentRoomPreviewImageIndex = Math.max(0, Math.min(index, roomPreviewImages.length - 1));
        roomPreviewImage.setAttribute('src', roomPreviewImages[currentRoomPreviewImageIndex]);

        if (!roomPreviewThumbnails) {
            return;
        }

        Array.from(roomPreviewThumbnails.children).forEach((thumb, thumbIndex) => {
            thumb.classList.toggle('active', thumbIndex === currentRoomPreviewImageIndex);
        });
    }

    function renderRoomPreviewThumbnails() {
        if (!roomPreviewThumbnails) {
            return;
        }

        roomPreviewThumbnails.innerHTML = '';
        roomPreviewImages.forEach((imageUrl, index) => {
            const thumbnail = document.createElement('img');
            thumbnail.className = 'room-preview-thumb' + (index === currentRoomPreviewImageIndex ? ' active' : '');
            thumbnail.setAttribute('src', imageUrl);
            thumbnail.setAttribute('alt', `Room preview ${index + 1}`);
            thumbnail.addEventListener('click', () => renderRoomPreviewImage(index));
            roomPreviewThumbnails.appendChild(thumbnail);
        });
    }

    function updateModalButtonState() {
        if (!roomPreviewToggleSelectBtn || !activeRoomPreviewId) {
            return;
        }

        const isSelected = selectedRoomIds.includes(activeRoomPreviewId);
        roomPreviewToggleSelectBtn.textContent = isSelected ? 'Remove from Cart' : 'Add to Cart';
    }

    function openRoomPreviewModal(trigger) {
        if (!roomPreviewModal || !trigger) {
            return;
        }

        activeRoomPreviewId = Number(trigger.getAttribute('data-room-id'));
        activeRoomPreviewName = trigger.getAttribute('data-room-name') || 'Room';
        const roomPrice = trigger.getAttribute('data-room-price') || '0.00';
        const roomCapacity = trigger.getAttribute('data-room-capacity') || '0';
        const roomImage = trigger.getAttribute('data-room-image') || '';
        const roomImagesRaw = (trigger.getAttribute('data-room-images') || '').trim();
        const roomDescription = trigger.getAttribute('data-room-description') || '';
        const roomInclusions = (trigger.getAttribute('data-room-inclusions') || '')
            .split('|')
            .map((item) => item.trim())
            .filter((item) => item.length > 0);
        roomPreviewImages = roomImagesRaw
            .split('|')
            .map((item) => item.trim())
            .filter((item) => item.length > 0)
            .slice(0, 4);

        if (roomPreviewImages.length === 0 && roomImage) {
            roomPreviewImages = [roomImage];
        }

        if (roomPreviewImages.length === 0) {
            roomPreviewImages = ['https://via.placeholder.com/760x300/d4af37/2c2c2c?text=Room'];
        }

        currentRoomPreviewImageIndex = 0;

        roomPreviewTitle.textContent = activeRoomPreviewName;
        roomPreviewMeta.textContent = `Can accommodate ${roomCapacity} adult${Number(roomCapacity) > 1 ? 's' : ''} + 1 child`;
        roomPreviewDescription.textContent = roomDescription || 'Comfortable and modern accommodation.';
        roomPreviewPrice.textContent = `₱${roomPrice}`;
        renderRoomPreviewThumbnails();
        renderRoomPreviewImage(0);

        roomPreviewInclusions.innerHTML = '';
        roomInclusions.forEach((item) => {
            const li = document.createElement('li');
            li.textContent = item;
            roomPreviewInclusions.appendChild(li);
        });

        updateModalButtonState();
        roomPreviewModal.classList.add('active');
        roomPreviewModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeRoomPreviewModal() {
        if (!roomPreviewModal) {
            return;
        }

        roomPreviewModal.classList.remove('active');
        roomPreviewModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function bindRoomModalTriggers() {
        document.querySelectorAll('[data-open-room-modal]').forEach((trigger) => {
            if (trigger.getAttribute('data-modal-bound') === '1') {
                return;
            }

            trigger.setAttribute('data-modal-bound', '1');
            trigger.addEventListener('click', (event) => {
                event.preventDefault();
                openRoomPreviewModal(trigger);
            });
        });
    }

    if (roomPreviewToggleSelectBtn) {
        roomPreviewToggleSelectBtn.addEventListener('click', () => {
            if (!Number.isInteger(activeRoomPreviewId) || activeRoomPreviewId <= 0) {
                return;
            }

            const isSelected = selectedRoomIds.includes(activeRoomPreviewId);
            if (isSelected) {
                selectedRoomIds = selectedRoomIds.filter((id) => id !== activeRoomPreviewId);
            } else if (selectedRoomIds.length < requiredRooms) {
                selectedRoomIds = [...selectedRoomIds, activeRoomPreviewId];
            } else {
                alert(`You can only select ${requiredRooms} room(s). Remove one first to add another.`);
                return;
            }

            persistSelection().finally(() => {
                updateModalButtonState();
                renderSelectedRoomsDrawer();
                markSelectedCards();
            });
        });
    }

    if (openSelectedRoomsDrawerButton && selectedRoomsDrawer) {
        openSelectedRoomsDrawerButton.addEventListener('click', () => {
            selectedRoomsDrawer.classList.add('active');
            selectedRoomsDrawer.setAttribute('aria-hidden', 'false');
        });
    }

    if (closeSelectedRoomsDrawerButton && selectedRoomsDrawer) {
        closeSelectedRoomsDrawerButton.addEventListener('click', () => {
            selectedRoomsDrawer.classList.remove('active');
            selectedRoomsDrawer.setAttribute('aria-hidden', 'true');
        });
    }

    if (selectedRoomsDrawer) {
        selectedRoomsDrawer.addEventListener('click', (event) => {
            if (event.target === selectedRoomsDrawer) {
                selectedRoomsDrawer.classList.remove('active');
                selectedRoomsDrawer.setAttribute('aria-hidden', 'true');
            }
        });
    }

    if (roomPreviewClose) {
        roomPreviewClose.addEventListener('click', closeRoomPreviewModal);
    }

    if (roomPreviewBackBtn) {
        roomPreviewBackBtn.addEventListener('click', closeRoomPreviewModal);
    }

    if (roomPreviewModal) {
        roomPreviewModal.addEventListener('click', (event) => {
            if (event.target === roomPreviewModal) {
                closeRoomPreviewModal();
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && roomPreviewModal && roomPreviewModal.classList.contains('active')) {
            closeRoomPreviewModal();
        }
    });

    bindRoomModalTriggers();
    renderSelectedRoomsDrawer();
    markSelectedCards();

    // Auto-refresh calendar every 30 seconds if modal is open
    setInterval(() => {
        if (calendarModal.classList.contains('active')) {
            loadCalendar(currentMonth, currentYear);
        }
    }, 30000);
});
</script>
</body>
</html>
