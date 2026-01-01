@extends('customer.layout')

@section('title', 'Our Rooms - Beztower & Residences')

@section('content')
<section class="content-section">
    <div class="section-subtitle">ACCOMMODATION</div>
    <h2 class="section-title">Our Luxury Rooms</h2>
    <p class="section-description">
        Discover our collection of elegantly designed rooms and suites, each offering the perfect blend of comfort and sophistication.
    </p>

    @if($rooms->count() > 0)
        <div class="rooms-grid">
            @foreach($rooms as $room)
                <div class="room-card">
                    <div class="room-image">
                        @if($room->photos->count() > 0)
                            <img src="{{ asset('storage/' . $room->photos->first()->photo_path) }}" alt="{{ $room->roomType->name }}">
                        @else
                            <img src="https://via.placeholder.com/400x300/d4af37/2c2c2c?text={{ urlencode($room->roomType->name) }}" alt="{{ $room->roomType->name }}">
                        @endif
                        <div class="room-badge">{{ $room->status }}</div>
                    </div>
                    
                    <div class="room-details">
                        <h3>{{ $room->roomType->name }}</h3>
                        <p class="room-number">Room {{ $room->room_number }}</p>
                        
                        <div class="room-info">
                            <span><i class="fas fa-users"></i> Up to {{ $room->roomType->max_guests }} Guests</span>
                            <span><i class="fas fa-bed"></i> {{ $room->roomType->bed_type }}</span>
                        </div>
                        
                        <p class="room-description">{{ Str::limit($room->roomType->description, 100) }}</p>
                        
                        @if($room->amenities->count() > 0)
                            <div class="room-amenities">
                                @foreach($room->amenities->take(4) as $amenity)
                                    <span class="amenity-tag">
                                        <i class="{{ $amenity->icon }}"></i> {{ $amenity->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="room-footer">
                            <div class="room-price">
                                <span class="price-label">From</span>
                                <span class="price-amount">₱{{ number_format($room->roomType->base_price, 2) }}</span>
                                <span class="price-period">/night</span>
                            </div>
                            <a href="{{ route('rooms.show', $room) }}" class="book-btn">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $rooms->links() }}
        </div>
    @else
        <div class="no-rooms">
            <i class="fas fa-bed"></i>
            <p>No rooms available for the selected criteria. Please try different dates or filters.</p>
        </div>
    @endif
</section>

<style>
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
