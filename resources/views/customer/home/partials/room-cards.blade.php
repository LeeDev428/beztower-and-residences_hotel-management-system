@foreach($rooms as $room)
    @php
        $roomName = (string) ($room->roomType->name ?? 'Room');
        $roomDescription = (string) ($room->roomType->description ?? '');
        $roomImage = $room->photos->count() > 0
            ? asset('storage/' . $room->photos->first()->photo_path)
            : 'https://via.placeholder.com/400x300/d4af37/2c2c2c?text=' . urlencode($roomName);
        $roomImages = $room->photos
            ->pluck('photo_path')
            ->filter()
            ->take(4)
            ->map(fn ($photoPath) => asset('storage/' . $photoPath))
            ->values()
            ->all();
        if (empty($roomImages)) {
            $roomImages = [$roomImage];
        }
        $roomImagesText = implode('|', $roomImages);
        $roomInclusions = $room->amenities->pluck('name')->filter()->values()->all();
        $roomTypeFeatures = collect(preg_split('/\r\n|\r|\n/', (string) ($room->roomType->features_text ?? '')))
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->values()
            ->all();
        $roomInclusionsText = !empty($roomInclusions)
            ? implode(' | ', (!empty($roomTypeFeatures) ? $roomTypeFeatures : $roomInclusions))
            : 'WiFi | Shower Heater | Smart TV';
    @endphp
    <div class="room-card">
        <div class="room-image">
            @if($room->photos->count() > 0)
                <img src="{{ asset('storage/' . $room->photos->first()->photo_path) }}" alt="{{ $room->roomType->name }}">
            @else
                <img src="https://via.placeholder.com/400x300/d4af37/2c2c2c?text={{ urlencode($room->roomType->name) }}" alt="{{ $room->roomType->name }}">
            @endif
        </div>
        
        <div class="room-details">
            <h3>{{ $room->roomType->name }}</h3>
            
            <div class="room-info">
                <span><i class="fas fa-users"></i> Can accommodate {{ (int) ($room->roomType->max_guests ?? 1) }} adult{{ ((int) ($room->roomType->max_guests ?? 1)) > 1 ? 's' : '' }} + 1 child</span>
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
                </div>
                <button
                    type="button"
                    class="book-btn"
                    data-open-room-modal
                    data-room-name="{{ e($roomName) }}"
                    data-room-price="{{ number_format((float) $room->effective_price, 2) }}"
                    data-room-capacity="{{ (int) ($room->roomType->max_guests ?? 0) }}"
                    data-room-image="{{ e($roomImage) }}"
                    data-room-images="{{ e($roomImagesText) }}"
                    data-room-description="{{ e($roomDescription) }}"
                    data-room-inclusions="{{ e($roomInclusionsText) }}"
                    data-room-url="{{ route('rooms.index', ['room_type' => $room->room_type_id]) }}"
                >
                    View Details
                </button>
            </div>
        </div>
    </div>
@endforeach
