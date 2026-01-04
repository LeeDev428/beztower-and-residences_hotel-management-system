<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $room->roomType->name }} - Bez Tower & Residences</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            color: #2c2c2c;
        }

        /* Navbar - moved to component */

        /* Container */
        .container {
            max-width: 1400px;
            margin: 100px auto 50px;
            padding: 0 3rem;
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            color: #666;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: #666;
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: #d4af37;
        }

        .breadcrumb i {
            font-size: 0.6rem;
            color: #999;
        }

        /* Product Layout - Two Columns */
        .product-layout {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 5rem;
            margin-bottom: 4rem;
        }

        /* Left Column - Image Gallery */
        .image-section {
            position: sticky;
            top: 120px;
            height: fit-content;
        }

        .main-image-wrapper {
            width: 100%;
            height: 500px;
            background: #f8f8f8;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e5e5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            cursor: zoom-in;
        }

        .thumbnail-gallery {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .thumbnail {
            width: 100%;
            height: 100px;
            background: #f8f8f8;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .thumbnail:hover,
        .thumbnail.active {
            border-color: #d4af37;
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Right Column - Room Details */
        .details-section {
            padding-top: 1rem;
        }

        .room-type-badge {
            display: inline-block;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .room-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c2c2c;
            margin-bottom: 1rem;
            font-family: 'Georgia', serif;
            line-height: 1.2;
        }

        .room-number {
            color: #999;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        /* Rating Section */
        .rating-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e5e5e5;
        }

        .stars {
            color: #d4af37;
            font-size: 1.1rem;
        }

        .rating-text {
            color: #666;
            font-size: 0.95rem;
        }

        .status-badge {
            margin-left: auto;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-badge.available {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-badge.occupied {
            background: #ffebee;
            color: #c62828;
        }

        /* Price Section */
        .price-section {
            margin-bottom: 2rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .price-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .price-wrapper {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
        }

        .price-amount {
            font-size: 2.5rem;
            font-weight: 700;
            color: #d4af37;
        }

        .price-period {
            font-size: 1.1rem;
            color: #666;
        }

        /* Room Specs */
        .room-specs {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
            padding: 1.5rem 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .spec-item {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .spec-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .spec-icon i {
            color: #2c2c2c;
            font-size: 1.2rem;
        }

        .spec-details span {
            display: block;
        }

        .spec-label {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 0.2rem;
        }

        .spec-value {
            font-size: 1rem;
            font-weight: 600;
            color: #2c2c2c;
        }

        /* Action Buttons */
        .action-section {
            margin-bottom: 2rem;
        }

        .check-now-btn {
            width: 100%;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            padding: 1.2rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .check-now-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
        }

        .check-now-btn i {
            margin-right: 0.5rem;
        }

        /* Additional Info */
        .additional-info {
            background: #f8f8f8;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            color: #666;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item i {
            color: #d4af37;
            font-size: 1.1rem;
        }

        /* Description Tabs */
        .description-section {
            margin-top: 4rem;
        }

        .tabs {
            display: flex;
            gap: 2rem;
            border-bottom: 2px solid #e5e5e5;
            margin-bottom: 2rem;
        }

        .tab-button {
            padding: 1rem 0;
            background: none;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            position: relative;
            transition: color 0.3s;
        }

        .tab-button.active {
            color: #d4af37;
        }

        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: #d4af37;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Description Content */
        .description-content {
            line-height: 1.8;
            color: #555;
            font-size: 1rem;
        }

        .description-content p {
            margin-bottom: 1rem;
        }

        /* Amenities Grid */
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .amenity-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.2rem;
            background: #f8f8f8;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .amenity-card:hover {
            background: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .amenity-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .amenity-icon i {
            color: #2c2c2c;
            font-size: 1.1rem;
        }

        .amenity-name {
            font-weight: 500;
            color: #2c2c2c;
        }

        /* Features Section */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
        }

        .feature-card {
            display: flex;
            gap: 1.5rem;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-icon i {
            color: #2c2c2c;
            font-size: 1.5rem;
        }

        .feature-content h4 {
            font-size: 1.2rem;
            color: #2c2c2c;
            margin-bottom: 0.5rem;
        }

        .feature-content p {
            color: #666;
            line-height: 1.6;
        }

        /* Footer */
        .footer {
            background: #2c2c2c;
            color: white;
            text-align: center;
            padding: 3rem 2rem;
            margin-top: 5rem;
        }

        .footer p {
            margin-bottom: 0.5rem;
            color: #ccc;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .product-layout {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .image-section {
                position: static;
            }
        }

        /* Booking Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auxxxto;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 2% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 900px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #2c2c2c, #1a1a1a);
            color: white;
            padding: 2rem;
            border-radius: 15px 15px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.8rem;
            font-family: 'Georgia', serif;
        }

        .modal-header .gold-accent {
            color: #d4af37;
        }

        .close {
            color: #d4af37;
            font-size: 2rem;
            font-weight: 300;
            cursor: pointer;
            transition: all 0.3s;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close:hover {
            background: rgba(212, 175, 55, 0.2);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 2.5rem;
        }

        .booking-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group-full {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c2c2c;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-label .required {
            color: #d4af37;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.9rem 1.2rem;
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s;
            background: white;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .section-divider {
            grid-column: 1 / -1;
            border-top: 2px solid #f0f0f0;
            margin: 1.5rem 0;
            padding-top: 1.5rem;
        }

        .section-title-modal {
            font-size: 1.3rem;
            color: #2c2c2c;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .section-title-modal i {
            color: #d4af37;
            font-size: 1.1rem;
        }

        .extras-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-top: 1rem;
        }

        .extra-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem;
            background: #f8f8f8;
            border-radius: 8px;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .extra-item:hover {
            background: #fff;
            border-color: #d4af37;
        }

        .extra-info {
            flex: 1;
        }

        .extra-name {
            font-weight: 600;
            color: #2c2c2c;
            margin-bottom: 0.3rem;
        }

        .extra-desc {
            font-size: 0.85rem;
            color: #666;
        }

        .extra-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: #d4af37;
            margin: 0 1rem;
        }

        .extra-checkbox {
            width: 22px;
            height: 22px;
            cursor: pointer;
            accent-color: #d4af37;
        }

        .booking-summary {
            background: linear-gradient(135deg, #f8f8f8, #fff);
            padding: 1.5rem;
            border-radius: 10px;
            border: 2px solid #e5e5e5;
            margin-top: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .summary-row:last-child {
            border-bottom: none;
            font-size: 1.2rem;
            font-weight: 700;
            color: #d4af37;
            padding-top: 1rem;
        }

        .submit-booking-btn {
            width: 100%;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            padding: 1.3rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1.5rem;
        }

        .submit-booking-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
        }

        .submit-booking-btn:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1.5rem;
                margin-top: 80px;
            }

            .room-title {
                font-size: 1.8rem;
            }

            .main-image-wrapper {
                height: 350px;
            }

            .thumbnail-gallery {
                grid-template-columns: repeat(3, 1fr);
            }

            .room-specs {
                grid-template-columns: 1fr;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .tabs {
                gap: 1rem;
                overflow-x: auto;
            }

            .tab-button {
                font-size: 1rem;
                white-space: nowrap;
            }

            .modal-content {
                width: 95%;
                margin: 5% auto;
            }

            .booking-form-grid {
                grid-template-columns: 1fr;
            }

            .modal-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    @include('components.navbar')

    <!-- Main Container -->
    <div class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('home') }}#rooms">Rooms</a>
            <i class="fas fa-chevron-right"></i>
            <span>{{ $room->roomType->name }}</span>
        </div>

        <!-- Product Layout -->
        <div class="product-layout">
            <!-- Left Column - Image Gallery -->
            <div class="image-section">
                <div class="main-image-wrapper">
                    @if($room->photos->count() > 0)
                        <img src="{{ asset('storage/' . $room->photos->first()->photo_path) }}" alt="{{ $room->roomType->name }}" class="main-image" id="mainImage">
                    @else
                        <img src="https://via.placeholder.com/800x600/d4af37/2c2c2c?text={{ urlencode($room->roomType->name) }}" alt="{{ $room->roomType->name }}" class="main-image" id="mainImage">
                    @endif
                </div>

                <div class="thumbnail-gallery">
                    @if($room->photos->count() > 0)
                        @foreach($room->photos->take(4) as $index => $photo)
                            <div class="thumbnail {{ $index === 0 ? 'active' : '' }}" onclick="changeImage('{{ asset('storage/' . $photo->photo_path) }}', this)">
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="{{ $room->roomType->name }}">
                            </div>
                        @endforeach
                        @for($i = $room->photos->count(); $i < 4; $i++)
                            <div class="thumbnail">
                                <img src="https://via.placeholder.com/150x100/d4af37/2c2c2c?text=Room+{{ $i + 1 }}" alt="Room View {{ $i + 1 }}">
                            </div>
                        @endfor
                    @else
                        @for($i = 0; $i < 4; $i++)
                            <div class="thumbnail {{ $i === 0 ? 'active' : '' }}">
                                <img src="https://via.placeholder.com/150x100/d4af37/2c2c2c?text=View+{{ $i + 1 }}" alt="Room View {{ $i + 1 }}">
                            </div>
                        @endfor
                    @endif
                </div>
            </div>

            <!-- Right Column - Room Details -->
            <div class="details-section">
                <div class="room-type-badge">{{ $room->roomType->name }}</div>
                
                <h1 class="room-title">{{ $room->roomType->name }}</h1>
                <p class="room-number">Room #{{ $room->room_number }}</p>

                <!-- Rating Section -->
                {{-- <div class="rating-section">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="rating-text">4.8 (127 reviews)</span>
                    <div class="status-badge {{ strtolower($room->status) }}">{{ $room->status }}</div>
                </div> --}}

                <!-- Price Section -->
                <div class="price-section">
                    <div class="price-label">Price:</div>
                    <div class="price-wrapper">
                        <span class="price-amount">₱{{ number_format($room->roomType->base_price, 2) }}</span>
                        <span class="price-period">/night</span>
                    </div>
                </div>

                <!-- Room Specifications -->
                <div class="room-specs">
                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div class="spec-details">
                            <span class="spec-label">Bed Type</span>
                            <span class="spec-value">{{ $room->roomType->bed_type }}</span>
                        </div>
                    </div>

                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="spec-details">
                            <span class="spec-label">Max Guests</span>
                            <span class="spec-value">{{ $room->roomType->max_guests }} Guests</span>
                        </div>
                    </div>

                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="fas fa-ruler-combined"></i>
                        </div>
                        <div class="spec-details">
                            <span class="spec-label">Room Size</span>
                            <span class="spec-value">{{ $room->roomType->size_sqm }} m²</span>
                        </div>
                    </div>

                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="spec-details">
                            <span class="spec-label">Floor</span>
                            <span class="spec-value">{{ $room->floor_number }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-section">
                    <button onclick="openBookingModal()" class="check-now-btn">
                        <i class="fas fa-calendar-check"></i> Book Now
                    </button>
                </div>

                <!-- Additional Information -->
                <div class="additional-info">
                    <div class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Free cancellation up to 24 hours before check-in</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure and safe accommodation</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span>Check-in: 2:00 PM | Check-out: 12:00 PM</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Tabs Section -->
        <div class="description-section">
            <div class="tabs">
                <button class="tab-button active" onclick="openTab(event, 'description')">Description</button>
                <button class="tab-button" onclick="openTab(event, 'amenities')">Amenities</button>
                <button class="tab-button" onclick="openTab(event, 'features')">Features</button>
            </div>

            <!-- Description Tab -->
            <div id="description" class="tab-content active">
                <div class="description-content">
                    <p>{{ $room->roomType->description }}</p>
                </div>
            </div>

            <!-- Amenities Tab -->
            <div id="amenities" class="tab-content">
                @if($room->amenities->count() > 0)
                    <div class="amenities-grid">
                        @foreach($room->amenities as $amenity)
                            <div class="amenity-card">
                                <div class="amenity-icon">
                                    <i class="{{ $amenity->icon }}"></i>
                                </div>
                                <span class="amenity-name">{{ $amenity->name }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No amenities listed for this room.</p>
                @endif
            </div>

            <!-- Features Tab -->
            <div id="features" class="tab-content">
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <div class="feature-content">
                            <h4>High-Speed WiFi</h4>
                            <p>Stay connected with complimentary high-speed internet access throughout your stay.</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tv"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Smart Entertainment</h4>
                            <p>Large flat-screen TV with premium channels and streaming services for your entertainment.</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-snowflake"></i>
                        </div>
                        <div class="feature-content">
                            <h4>Climate Control</h4>
                            <p>Individual temperature control to ensure your comfort at all times.</p>
                        </div>
                    </div>

                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <div class="feature-content">
                            <h4>24/7 Room Service</h4>
                            <p>Around-the-clock room service to cater to your every need at any time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Book <span class="gold-accent">{{ $room->roomType->name }}</span></h2>
                <span class="close" onclick="closeBookingModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form action="{{ route('booking.create') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    
                    <!-- Guest Information Section -->
                    <div class="section-title-modal">
                        <i class="fas fa-user"></i> Guest Information
                    </div>
                    
                    <div class="booking-form-grid">
                        <div>
                            <label class="form-label">First Name <span class="required">*</span></label>
                            <input type="text" name="first_name" class="form-input" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Last Name <span class="required">*</span></label>
                            <input type="text" name="last_name" class="form-input" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" name="email" class="form-input" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Phone <span class="required">*</span></label>
                            <input type="tel" name="phone" class="form-input" required>
                        </div>
                        
                        <div>
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-input" placeholder="e.g., Philippines">
                        </div>
                        
                        <div>
                            <label class="form-label">Upload ID Photo (Passport/Driver's License) <span class="required">*</span></label>
                            <input type="file" name="id_photo" class="form-input" accept="image/jpeg,image/png,image/jpg,application/pdf" required>
                            <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 0.3rem;">Accepted formats: JPG, PNG, PDF (Max: 5MB)</small>
                        </div>
                        
                        <div class="form-group-full">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-input" placeholder="Street Address, City, State/Province">
                        </div>
                        
                        <!-- Booking Details Section -->
                        <div class="form-group-full section-divider">
                            <div class="section-title-modal">
                                <i class="fas fa-calendar-alt"></i> Booking Details
                            </div>
                        </div>
                        
                        <div>
                            <label class="form-label">Check-In Date <span class="required">*</span></label>
                            <input type="date" name="check_in_date" class="form-input" id="checkInDate" 
                                   min="{{ date('Y-m-d') }}" required onchange="calculateNights()">
                        </div>
                        
                        <div>
                            <label class="form-label">Check-Out Date <span class="required">*</span></label>
                            <input type="date" name="check_out_date" class="form-input" id="checkOutDate" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" required onchange="calculateNights()">
                        </div>
                        
                        <div>
                            <label class="form-label">Number of Guests <span class="required">*</span></label>
                            <select name="number_of_guests" class="form-select" required>
                                @for($i = 1; $i <= $room->roomType->max_guests; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}</option>
                                @endfor
                            </select>
                        </div>
                        
                        <div>
                            <label class="form-label">Total Nights</label>
                            <input type="number" name="total_nights" class="form-input" id="totalNights" readonly value="1">
                        </div>
                        
                        <!-- Extras Section -->
                        <div class="form-group-full section-divider">
                            <div class="section-title-modal">
                                <i class="fas fa-plus-circle"></i> Additional Services (Optional)
                            </div>
                            
                            @php
                                $extras = \App\Models\Extra::where('is_active', true)->get();
                            @endphp
                            
                            @if($extras->count() > 0)
                                <div class="extras-grid">
                                    @foreach($extras as $extra)
                                        <div class="extra-item">
                                            <div class="extra-info">
                                                <div class="extra-name">{{ $extra->name }}</div>
                                                <div class="extra-desc">{{ $extra->description }}</div>
                                            </div>
                                            <div class="extra-price">₱{{ number_format($extra->price, 2) }}</div>
                                            <input type="checkbox" name="extras[]" value="{{ $extra->id }}" 
                                                   class="extra-checkbox" data-price="{{ $extra->price }}" 
                                                   onchange="updateTotal()">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="color: #666; font-style: italic;">No additional services available at this time.</p>
                            @endif
                        </div>
                        
                        <!-- Special Requests -->
                        <div class="form-group-full">
                            <label class="form-label">Special Requests or Preferences</label>
                            <textarea name="special_requests" class="form-textarea" 
                                      placeholder="Any special requests or dietary requirements..."></textarea>
                        </div>
                        
                        <!-- Booking Summary -->
                        <div class="form-group-full">
                            <div class="booking-summary">
                                <h3 style="margin-bottom: 1rem; color: #2c2c2c;">
                                    <i class="fas fa-receipt"></i> Booking Summary
                                </h3>
                                <div class="summary-row">
                                    <span>Room Rate (per night)</span>
                                    <span>₱{{ number_format($room->roomType->base_price, 2) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span>Number of Nights</span>
                                    <span id="nightsDisplay">1</span>
                                </div>
                                <div class="summary-row">
                                    <span>Subtotal</span>
                                    <span id="subtotalDisplay">₱{{ number_format($room->roomType->base_price, 2) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span>Additional Services</span>
                                    <span id="extrasDisplay">₱0.00</span>
                                </div>
                                <div class="summary-row">
                                    <span>Tax (12%)</span>
                                    <span id="taxDisplay">₱{{ number_format($room->roomType->base_price * 0.12, 2) }}</span>
                                </div>
                                <div class="summary-row">
                                    <span>Total Amount</span>
                                    <span id="totalDisplay">₱{{ number_format($room->roomType->base_price * 1.12, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-booking-btn">
                        <i class="fas fa-check-circle"></i> Confirm Booking
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Bez Tower & Residences. All rights reserved.</p>
        <p>205 F. Blumentritt Street, Brgy. Pedro Cruz, San Juan City, Philippines</p>
    </footer>

    <script>
        const basePrice = {{ $room->roomType->base_price }};
        const taxRate = 0.12;

        // Change main image when clicking thumbnails
        function changeImage(src, element) {
            document.getElementById('mainImage').src = src;
            
            // Remove active class from all thumbnails
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });
            
            // Add active class to clicked thumbnail
            element.classList.add('active');
        }

        // Tab functionality
        function openTab(evt, tabName) {
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }

            // Remove active class from all tab buttons
            const tabButtons = document.getElementsByClassName('tab-button');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }

            // Show selected tab content and mark button as active
            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');
        }

        // Booking Modal Functions
        function openBookingModal() {
            document.getElementById('bookingModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('bookingModal');
            if (event.target == modal) {
                closeBookingModal();
            }
        }

        // Calculate nights between check-in and check-out
        function calculateNights() {
            const checkIn = document.getElementById('checkInDate').value;
            const checkOut = document.getElementById('checkOutDate').value;
            
            if (checkIn && checkOut) {
                const date1 = new Date(checkIn);
                const date2 = new Date(checkOut);
                const diffTime = Math.abs(date2 - date1);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays > 0) {
                    document.getElementById('totalNights').value = diffDays;
                    document.getElementById('nightsDisplay').textContent = diffDays;
                    updateTotal();
                }
            }
        }

        // Update total amount calculation
        function updateTotal() {
            const nights = parseInt(document.getElementById('totalNights').value) || 1;
            const subtotal = basePrice * nights;
            
            // Calculate extras total
            let extrasTotal = 0;
            const checkedExtras = document.querySelectorAll('.extra-checkbox:checked');
            checkedExtras.forEach(checkbox => {
                extrasTotal += parseFloat(checkbox.dataset.price);
            });
            
            // Calculate tax on subtotal + extras
            const taxableAmount = subtotal + extrasTotal;
            const tax = taxableAmount * taxRate;
            const total = taxableAmount + tax;
            
            // Update displays
            document.getElementById('subtotalDisplay').textContent = '₱' + subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('extrasDisplay').textContent = '₱' + extrasTotal.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('taxDisplay').textContent = '₱' + tax.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('totalDisplay').textContent = '₱' + total.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        // Set minimum check-out date when check-in changes
        document.addEventListener('DOMContentLoaded', function() {
            const checkInInput = document.getElementById('checkInDate');
            const checkOutInput = document.getElementById('checkOutDate');
            
            checkInInput.addEventListener('change', function() {
                const checkInDate = new Date(this.value);
                checkInDate.setDate(checkInDate.getDate() + 1);
                const minCheckOut = checkInDate.toISOString().split('T')[0];
                checkOutInput.min = minCheckOut;
                
                if (checkOutInput.value && checkOutInput.value <= this.value) {
                    checkOutInput.value = minCheckOut;
                }
            });
        });
    </script>
</body>
</html>
