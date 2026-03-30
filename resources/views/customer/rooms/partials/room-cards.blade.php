@foreach($rooms as $roomCard)
    @php
        $requestedRooms = max(1, min(12, (int) ($requestedRooms ?? data_get($bookingContext ?? [], 'rooms', 1))));
        $selectedRoomIds = collect($selectedRoomIds ?? data_get($bookingContext ?? [], 'selected_rooms', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $roomIds = collect($roomCard['room_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values();

        $selectedForType = $selectedRoomIds->intersect($roomIds)->count();
        $isSelected = $selectedForType > 0;
        $roomTypeId = (int) ($roomCard['room_type_id'] ?? 0);
        $roomName = (string) ($roomCard['name'] ?? 'Room');
        $roomDescription = (string) ($roomCard['description'] ?? '');
        $roomImage = (string) ($roomCard['primary_image'] ?? 'https://via.placeholder.com/400x300/d4af37/2c2c2c?text=' . urlencode($roomName));
        $roomImages = collect($roomCard['gallery_images'] ?? [])->filter()->values()->all();
        if (empty($roomImages)) {
            $roomImages = [$roomImage];
        }
        $roomImagesText = implode('|', $roomImages);

        $amenities = collect($roomCard['amenities'] ?? []);
        $roomFeatures = collect($roomCard['features'] ?? [])->filter()->values()->all();
        $roomInclusions = $amenities->pluck('name')->filter()->values()->all();
        $roomInclusionsText = !empty($roomFeatures)
            ? implode(' | ', $roomFeatures)
            : (!empty($roomInclusions) ? implode(' | ', $roomInclusions) : 'WiFi | Shower Heater | Smart TV');

        $priceValue = (float) ($roomCard['price'] ?? 0);
        $priceFormatted = number_format($priceValue, 2);
        $basePrice = (float) ($roomCard['base_price'] ?? $priceValue);
        $displayDiscount = (float) ($roomCard['discount_percentage'] ?? 0);
        $availableCount = max(0, (int) ($roomCard['available_count'] ?? $roomIds->count()));
    @endphp

    <div class="room-card">
        <div class="room-image">
            <img src="{{ $roomImage }}" alt="{{ $roomName }}">

            @if($isSelected)
                <div data-selected-badge="1" style="position:absolute;top:10px;right:10px;background:#27ae60;color:#fff;padding:0.4rem 0.7rem;border-radius:6px;font-size:0.8rem;font-weight:700;">Selected {{ $selectedForType }}/{{ $availableCount }}</div>
            @endif
        </div>

        <div class="room-details">
            <h3>{{ $roomName }}</h3>

            <div class="room-info">
                <span><i class="fas fa-users"></i> Up to {{ (int) ($roomCard['max_guests'] ?? 0) }} adults + 1 child</span>
                <span><i class="fas fa-bed"></i> {{ (string) ($roomCard['bed_type'] ?? 'Standard Bed') }}</span>
                <span><i class="fas fa-door-open"></i> {{ $availableCount }} room{{ $availableCount === 1 ? '' : 's' }}</span>
            </div>

            <p class="room-description">{{ Str::limit($roomDescription, 100) }}</p>

            @if($amenities->count() > 0)
                <div class="room-amenities">
                    @foreach($amenities->take(4) as $amenity)
                        <span class="amenity-tag">
                            <i class="{{ $amenity->icon ?? 'fas fa-check' }}"></i> {{ $amenity->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            <div class="room-footer">
                <div class="room-price">
                    <span class="price-label">From</span>
                    <span class="price-amount">₱{{ $priceFormatted }}</span>
                    <span class="price-period">/night</span>
                    @if($displayDiscount > 0)
                        <div style="margin-top: 0.2rem; font-size: 0.8rem; color: #666;">
                            <span style="text-decoration: line-through;">₱{{ number_format($basePrice, 2) }}</span>
                            <span class="discount-badge">{{ number_format($displayDiscount, 0) }}% OFF</span>
                        </div>
                    @endif
                </div>
                <div class="room-card-actions">
                    <div class="room-qty-control" aria-label="Room selection controls">
                        <button type="button" class="room-qty-btn" data-room-remove-type="{{ $roomTypeId }}" {{ $selectedForType > 0 ? '' : 'disabled' }}>-</button>
                        <span class="room-qty-value" data-room-qty-type="{{ $roomTypeId }}">{{ $selectedForType }}</span>
                        <button type="button" class="room-qty-btn" data-room-add-type="{{ $roomTypeId }}" {{ ($selectedForType < $availableCount && $selectedRoomIds->count() < $requestedRooms) ? '' : 'disabled' }}>+</button>
                    </div>
                    <button
                        type="button"
                        class="book-btn"
                        data-open-room-modal
                        data-room-type-id="{{ $roomTypeId }}"
                        data-room-ids="{{ $roomIds->implode(',') }}"
                        data-room-name="{{ e($roomName) }}"
                        data-room-price="{{ $priceFormatted }}"
                        data-room-price-value="{{ $priceValue }}"
                        data-room-capacity="{{ (int) ($roomCard['max_guests'] ?? 0) }}"
                        data-room-image="{{ e($roomImage) }}"
                        data-room-images="{{ e($roomImagesText) }}"
                        data-room-description="{{ e($roomDescription) }}"
                        data-room-inclusions="{{ e($roomInclusionsText) }}"
                        data-room-available-count="{{ $availableCount }}"
                        data-is-selected="{{ $isSelected ? '1' : '0' }}"
                    >
                        Explore Rooms
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach
