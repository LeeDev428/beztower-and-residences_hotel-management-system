@foreach($rooms as $room)
    @php
        $requestedRooms = max(1, min(12, (int) request('rooms', 1)));
        $selectedRoomIds = collect(explode(',', (string) request('selected_rooms')))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();
        $isSelected = $selectedRoomIds->contains((int) $room->id);

        $baseParams = [];
        if (request('check_in'))  $baseParams['check_in'] = request('check_in');
        if (request('check_out')) $baseParams['check_out'] = request('check_out');
        if (request('guests'))    $baseParams['guests'] = request('guests');
        $baseParams['rooms'] = $requestedRooms;

        $detailsParams = $baseParams;
        $detailsParams['origin'] = 'learn_more';
        $detailsParams['selection_action'] = 'view';
        $detailsParams['current_selected'] = $isSelected ? '1' : '0';
        $learnMoreSelectedIds = $selectedRoomIds;

        if ($learnMoreSelectedIds->isNotEmpty()) {
            $detailsParams['selected_rooms'] = $learnMoreSelectedIds->implode(',');
        } else {
            unset($detailsParams['selected_rooms']);
        }
        $btnUrl  = route('rooms.show', $room) . ($detailsParams ? '?' . http_build_query($detailsParams) : '');

        $selectParams = $baseParams;
        if ($nextSelectedRoomIds->isNotEmpty()) {
            $selectParams['selected_rooms'] = $nextSelectedRoomIds->implode(',');
        }
        $selectUrl = route('rooms.index') . '?' . http_build_query($selectParams);

        $deselectedRoomIds = $selectedRoomIds->reject(fn ($id) => (int) $id === (int) $room->id)->values();
        $deselectParams = $baseParams;
        if ($deselectedRoomIds->isNotEmpty()) {
            $deselectParams['selected_rooms'] = $deselectedRoomIds->implode(',');
        } else {
            unset($deselectParams['selected_rooms']);
        }
        $deselectUrl = route('rooms.index') . '?' . http_build_query($deselectParams);

    @endphp
    <div class="room-card">
        <div class="room-image">
            @if($room->photos->count() > 0)
                <img src="{{ asset('storage/' . $room->photos->first()->photo_path) }}" alt="{{ $room->roomType->name }}">
            @else
                <img src="https://via.placeholder.com/400x300/d4af37/2c2c2c?text={{ urlencode($room->roomType->name) }}" alt="{{ $room->roomType->name }}">
            @endif
            <div class="room-badge">{{ $room->status }}</div>
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
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap; justify-content:flex-end;">
                    <a href="{{ $btnUrl }}" class="book-btn">Learn More</a>
                </div>
            </div>
        </div>
    </div>
@endforeach
