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
                    <span class="price-amount">₱{{ number_format($room->effective_price, 2) }}</span>
                    <span class="price-period">/night</span>
                    @php
                        $displayDiscount = (float) ($room->discount_percentage ?? 0);
                        if ($displayDiscount <= 0) {
                            $displayDiscount = (float) ($room->roomType->discount_percentage ?? 0);
                        }
                    @endphp
                    @if($displayDiscount > 0)
                        <div style="margin-top: 0.2rem; font-size: 0.8rem; color: #666;">
                            <span style="text-decoration: line-through;">₱{{ number_format((float) $room->roomType->base_price, 2) }}</span>
                            <span class="discount-badge">{{ number_format($displayDiscount, 0) }}% OFF</span>
                        </div>
                    @endif
                </div>
                <a href="{{ route('rooms.show', $room) }}" class="book-btn">Learn More</a>
            </div>
        </div>
    </div>
@endforeach
