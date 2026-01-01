@extends('customer.layout')

@section('title', 'Our Rooms - Beztower & Residences')

@section('content')
<section class="content-section">
    <div class="section-subtitle">ACCOMMODATION</div>
    <h2 class="section-title">Our Luxury Rooms</h2>
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
            <!-- Price Range -->
            <div class="filter-group">
                <label><i class="fas fa-dollar-sign"></i> Price Range</label>
                <div class="price-inputs">
                    <input type="number" id="minPrice" placeholder="Min" min="0" step="100">
                    <span>-</span>
                    <input type="number" id="maxPrice" placeholder="Max" min="0" step="100">
                </div>
            </div>

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

            <!-- Guests -->
            <div class="filter-group">
                <label><i class="fas fa-users"></i> Guests</label>
                <select id="guests">
                    <option value="">Any</option>
                    <option value="1">1 Guest</option>
                    <option value="2">2 Guests</option>
                    <option value="3">3 Guests</option>
                    <option value="4">4+ Guests</option>
                </select>
            </div>

            <!-- Amenities -->
            <div class="filter-group">
                <label><i class="fas fa-star"></i> Amenities</label>
                <div class="amenities-dropdown">
                    <button type="button" class="amenities-btn" id="amenitiesBtn">
                        <span id="amenitiesCount">Select Amenities</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="amenities-list" id="amenitiesList">
                        @foreach($amenities as $amenity)
                            <label class="amenity-checkbox">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}">
                                <i class="{{ $amenity->icon }}"></i>
                                <span>{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
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

    <!-- Results Counter -->
    <div class="results-info">
        <span id="resultsCount">Showing {{ $rooms->count() }} of {{ $rooms->total() }} rooms</span>
        <div class="loading-spinner" id="loadingSpinner" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
    </div>

    @if($rooms->count() > 0)
        <div class="rooms-grid" id="roomsGrid">
            @include('customer.rooms.partials.room-cards')
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

<!-- Floating Calendar Button -->
<button class="floating-calendar-btn" id="showCalendar">
    <i class="fas fa-calendar-check"></i>
    <span>Check Availability</span>
</button>

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
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
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
    }

    .filter-group select:focus,
    .filter-group input[type="number"]:focus {
        outline: none;
        border-color: #d4af37;
    }

    .price-inputs {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .price-inputs input {
        flex: 1;
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
    
    .room-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: linear-gradient(135deg, #d4af37, #f4e4c1);
        color: #2c2c2c;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: capitalize;
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
    
    .room-number {
        color: #d4af37;
        font-size: 0.9rem;
        margin-bottom: 1rem;
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
    
    .pagination-wrapper {
        margin-top: 3rem;
        display: flex;
        justify-content: center;
    }
    
    @media (max-width: 768px) {
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
    }
</style>
@endsection
