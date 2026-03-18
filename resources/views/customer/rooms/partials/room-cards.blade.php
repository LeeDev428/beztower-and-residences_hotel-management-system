@foreach($rooms as $room)
    @php
        $requestedRooms = max(1, min(12, (int) request('rooms', 1)));
        $selectedRoomIds = collect(explode(',', (string) request('selected_rooms')))
            ->map(fn ($id) => (int) trim($id))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();
        $isSelected = $selectedRoomIds->contains((int) $room->id);
        $canSelectMore = $selectedRoomIds->count() < $requestedRooms;
        $nextRoomLabelNumber = min($selectedRoomIds->count() + 1, $requestedRooms);

        $baseParams = [];
        if (request('check_in'))  $baseParams['check_in'] = request('check_in');
        if (request('check_out')) $baseParams['check_out'] = request('check_out');
        if (request('guests'))    $baseParams['guests'] = request('guests');
        $baseParams['rooms'] = $requestedRooms;

        $nextSelectedRoomIds = $selectedRoomIds;
        if (!$isSelected && $canSelectMore) {
            $nextSelectedRoomIds = $selectedRoomIds->push((int) $room->id)->unique()->values();
        }

        $detailsParams = $baseParams;
        $detailsParams['origin'] = 'learn_more';
        $detailsParams['selection_action'] = 'view';
        $learnMoreSelectedIds = $selectedRoomIds
            ->reject(fn ($id) => (int) $id === (int) $room->id)
            ->values();

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

        $selectionComplete = $selectedRoomIds->count() >= $requestedRooms && $selectedRoomIds->isNotEmpty();
        $checkoutUrl = '';
        if ($selectionComplete) {
            $checkoutParams = [
                'check_in' => request('check_in'),
                'check_out' => request('check_out'),
                'guests' => request('guests'),
                'rooms' => $requestedRooms,
                'selected_rooms' => $selectedRoomIds->implode(','),
                'room_ids' => $selectedRoomIds->all(),
            ];
            $checkoutUrl = route('booking.checkout', $selectedRoomIds->first()) . '?' . http_build_query(array_filter($checkoutParams, fn ($value) => !is_null($value) && $value !== ''));
        }
    @endphp
    <div class="room-card">
        <div class="room-image">
            @if($room->photos->count() > 0)
                <img src="{{ asset('storage/' . $room->photos->first()->photo_path) }}" alt="{{ $room->roomType->name }}">
            @else
                <img src="https://via.placeholder.com/400x300/d4af37/2c2c2c?text={{ urlencode($room->roomType->name) }}" alt="{{ $room->roomType->name }}">
            @endif
            <div class="room-badge">{{ $room->status }}</div>
            @if($isSelected)
                <div style="position:absolute;top:10px;right:10px;background:#27ae60;color:#fff;padding:0.4rem 0.7rem;border-radius:6px;font-size:0.8rem;font-weight:700;">Selected</div>
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
                    <span class="price-label">From</span>
                    <span class="price-amount">₱{{ number_format($room->roomType->base_price, 2) }}</span>
                    <span class="price-period">/night</span>
                </div>
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap; justify-content:flex-end;">
                    <a href="{{ $btnUrl }}" class="book-btn">Learn More</a>
                    @if($selectionComplete)
                        <a href="{{ $checkoutUrl }}" class="book-btn" style="background: linear-gradient(135deg, #2c2c2c, #1f1f1f); color:#fff;">Proceed to Billing Details</a>
                    @elseif(!$isSelected && $canSelectMore)
                        <a href="{{ $selectUrl }}" class="book-btn" style="background: linear-gradient(135deg, #27ae60, #229954); color:#fff;">Select Room {{ $nextRoomLabelNumber }}</a>
                    @elseif($isSelected)
                        <a href="{{ $deselectUrl }}" class="book-btn" style="background:#fdeaea; color:#b42318;">Deselect Room</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
