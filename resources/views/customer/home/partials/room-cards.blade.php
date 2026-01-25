@foreach($rooms as $room)
    <div class="room-card">
        <div class="room-image">
            @if($room->photos->count() > 0)
                <img src="{{ asset('storage/' . $room->photos->first()->photo_path) }}" alt="{{ $room->roomType->name }}">
            @else
                <img src="https://via.placeholder.com/400x300/d4af37/2c2c2c?text={{ urlencode($room->roomType->name) }}" alt="{{ $room->roomType->name }}">
            @endif
            <div class="room-badge">{{ $room->status }}</div>
            @if($room->discount_percentage > 0)
                <div class="discount-badge">{{ $room->discount_percentage }}% OFF</div>
            @endif
        </div>
        
        <div class="room-details">
            <h3>{{ $room->roomType->name }}</h3>
            
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
                    @if($room->discount_percentage > 0)
                        <span class="price-label">From</span>
                        <span class="original-price">₱{{ number_format($room->roomType->base_price, 2) }}</span>
                        <span class="price-amount discounted-price">₱{{ number_format($room->roomType->base_price * (1 - $room->discount_percentage / 100), 2) }}</span>
                        <span class="price-period">/night</span>
                    @else
                        <span class="price-label">From</span>
                        <span class="price-amount">₱{{ number_format($room->roomType->base_price, 2) }}</span>
                        <span class="price-period">/night</span>
                    @endif
                </div>
                <a href="{{ route('rooms.show', $room) }}" class="book-btn">View Details</a>
            </div>
        </div>
    </div>
@endforeach
