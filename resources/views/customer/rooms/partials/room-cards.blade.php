@foreach($rooms as $room)
    @php
        $requestedRooms = max(1, min(12, (int) ($requestedRooms ?? data_get($bookingContext ?? [], 'rooms', 1))));
        $selectedRoomIds = collect($selectedRoomIds ?? data_get($bookingContext ?? [], 'selected_rooms', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();
        $isSelected = $selectedRoomIds->contains((int) $room->id);
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
       
            @if($isSelected)
                <div data-selected-badge="1" style="position:absolute;top:10px;right:10px;background:#27ae60;color:#fff;padding:0.4rem 0.7rem;border-radius:6px;font-size:0.8rem;font-weight:700;">Selected</div>
            @endif
        </div>

        <div class="room-details">
            <h3>{{ $room->roomType->name }}</h3>

            <div class="room-info">
                <span><i class="fas fa-users"></i> Up to {{ $room->roomType->max_guests }} adults + 1 child</span>
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
                <div class="room-card-actions">
                    <div class="room-qty-control" aria-label="Room selection controls">
                        <button type="button" class="room-qty-btn" data-room-remove="{{ (int) $room->id }}" {{ $isSelected ? '' : 'disabled' }}>-</button>
                        <span class="room-qty-value" data-room-qty="{{ (int) $room->id }}">{{ $isSelected ? 1 : 0 }}</span>
                        <button type="button" class="room-qty-btn" data-room-add="{{ (int) $room->id }}" {{ (!$isSelected && $selectedRoomIds->count() < $requestedRooms) ? '' : 'disabled' }}>+</button>
                    </div>
                    <button
                        type="button"
                        class="book-btn"
                        data-open-room-modal
                        data-room-id="{{ (int) $room->id }}"
                        data-room-name="{{ e($roomName) }}"
                        data-room-price="{{ number_format((float) $room->effective_price, 2) }}"
                        data-room-price-value="{{ (float) $room->effective_price }}"
                        data-room-capacity="{{ (int) ($room->roomType->max_guests ?? 0) }}"
                        data-room-image="{{ e($roomImage) }}"
                        data-room-images="{{ e($roomImagesText) }}"
                        data-room-description="{{ e($roomDescription) }}"
                        data-room-inclusions="{{ e($roomInclusionsText) }}"
                        data-is-selected="{{ $isSelected ? '1' : '0' }}"
                    >
                        Explore Rooms
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach
