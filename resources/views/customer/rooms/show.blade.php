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
            background: #fff;
            color: #2c2c2c;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 3rem;
            background: rgba(44, 44, 44, 0.98);
            backdrop-filter: blur(10px);
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: #d4af37;
            text-decoration: none;
            font-family: 'Georgia', serif;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
            font-weight: 500;
        }

        .nav-links a:hover {
            color: #d4af37;
        }

        /* Room Detail Section */
        .room-detail-container {
            max-width: 1400px;
            margin: 100px auto 0;
            padding: 3rem 3rem;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            color: #666;
            font-size: 0.95rem;
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
            font-size: 0.7rem;
        }

        /* Main Product Layout */
        .product-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-bottom: 4rem;
        }

        /* Image Gallery */
        .image-section {
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .main-image-container {
            background: #f9f9f9;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            border: 1px solid #eee;
            aspect-ratio: 4/3;
        }

        .main-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumbnail-gallery {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        .thumbnail {
            background: #f9f9f9;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s;
            aspect-ratio: 4/3;
        }

        .thumbnail:hover {
            border-color: #d4af37;
            transform: scale(1.05);
        }

        .thumbnail.active {
            border-color: #d4af37;
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Details Section */
        .details-section {
            padding-top: 1rem;
        }

        .room-category {
            color: #d4af37;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        .room-title {
            font-size: 2.8rem;
            font-family: 'Georgia', serif;
            color: #2c2c2c;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .room-number {
            color: #666;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .price-section {
            background: linear-gradient(135deg, #f9f5eb, #fff);
            border: 2px solid #d4af37;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .price-label {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .price-amount {
            font-size: 3.5rem;
            font-weight: 700;
            color: #d4af37;
            line-height: 1;
        }

        .price-period {
            color: #666;
            font-size: 1.1rem;
            margin-top: 0.3rem;
        }

        .price-note {
            color: #28a745;
            font-size: 0.85rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .price-note i {
            font-size: 1rem;
        }

        .stock-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #d4f4dd;
            color: #28a745;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .stock-status i {
            font-size: 0.7rem;
        }

        .room-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .meta-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .meta-icon i {
            color: #2c2c2c;
            font-size: 1.2rem;
        }

        .meta-content {
            flex: 1;
        }

        .meta-label {
            font-size: 0.85rem;
            color: #999;
            margin-bottom: 0.2rem;
        }

        .meta-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c2c2c;
        }

        .check-now-btn {
            width: 100%;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            padding: 1.5rem 2rem;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }

        .check-now-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.4);
        }

        .check-now-btn i {
            font-size: 1.3rem;
        }

        .guarantee-info {
            background: #f9f9f9;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .guarantee-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: #666;
            margin-bottom: 0.8rem;
        }

        .guarantee-item:last-child {
            margin-bottom: 0;
        }

        .guarantee-item i {
            color: #d4af37;
            font-size: 1.1rem;
        }

        /* Description Tabs */
        .description-section {
            margin-top: 4rem;
            padding-top: 3rem;
            border-top: 2px solid #eee;
        }

        .tabs {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #eee;
        }

        .tab {
            padding: 1rem 2rem;
            background: none;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            position: relative;
            transition: color 0.3s;
        }

        .tab:hover {
            color: #d4af37;
        }

        .tab.active {
            color: #d4af37;
        }

        .tab.active::after {
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
            padding: 2rem 0;
        }

        .tab-content.active {
            display: block;
        }

        .description-text {
            color: #666;
            line-height: 1.8;
            font-size: 1.05rem;
            margin-bottom: 2rem;
        }

        .amenities-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f9f9f9;
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .amenity-item:hover {
            transform: translateX(5px);
        }

        .amenity-item i {
            color: #d4af37;
            font-size: 1.3rem;
        }

        .amenity-item span {
            color: #2c2c2c;
            font-weight: 500;
        }

        .specs-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .spec-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .spec-label {
            color: #666;
            font-weight: 500;
        }

        .spec-value {
            color: #2c2c2c;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            background: #2c2c2c;
            color: white;
            padding: 3rem;
            margin-top: 5rem;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            text-align: center;
        }

        .footer-content p {
            color: #ccc;
            margin-bottom: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }

            .nav-links {
                display: none;
            }

            .room-detail-container {
                margin-top: 80px;
                padding: 0 1rem;
            }

            .image-gallery {
                grid-template-columns: 1fr;
                grid-template-rows: 300px;
            }

            .main-image {
                grid-row: span 1;
            }

            .thumbnail {
                display: none;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .booking-card {
                position: static;
            }

            .room-title {
                font-size: 1.8rem;
            }

            .room-title-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .amenities-grid {
                grid-template-columns: 1fr;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="{{ route('home') }}" class="logo">BEZ TOWER & RESIDENCES</a>
        <ul class="nav-links">
            <li><a href="{{ route('home') }}#home">Home</a></li>
            <li><a href="{{ route('home') }}#about">About</a></li>
            <li><a href="{{ route('home') }}#rooms">Rooms</a></li>
            <li><a href="{{ route('home') }}#services">Services</a></li>
            <li><a href="{{ route('home') }}#contact">Contact</a></li>
        </ul>
    </nav>

    <!-- Room Detail -->
    <div class="room-detail-container">
        <a href="{{ route('home') }}#rooms" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Rooms
        </a>

        <!-- Room Header -->
        <div class="room-header">
            <div class="room-title-section">
                <div>
                    <h1 class="room-title">{{ $room->roomType->name }}</h1>
                    <p class="room-subtitle">Room {{ $room->room_number }}</p>
                </div>
                <div class="room-status-badge">{{ $room->status }}</div>
            </div>
            <div class="room-meta">
                <span><i class="fas fa-bed"></i> {{ $room->roomType->bed_type }}</span>
                <span><i class="fas fa-users"></i> Up to {{ $room->roomType->max_guests }} Guests</span>
                <span><i class="fas fa-ruler-combined"></i> {{ $room->roomType->size_sqm }} m²</span>
            </div>
        </div>

        <!-- Image Gallery -->
        <div class="image-gallery">
            <div class="main-image">
                @if($room->photos->count() > 0)
                    <img src="{{ asset('storage/' . $room->photos->first()->photo_path) }}" alt="{{ $room->roomType->name }}">
                @else
                    <img src="https://via.placeholder.com/800x600/d4af37/2c2c2c?text={{ urlencode($room->roomType->name) }}" alt="{{ $room->roomType->name }}">
                @endif
            </div>
            @if($room->photos->count() > 1)
                @foreach($room->photos->skip(1)->take(2) as $photo)
                    <div class="thumbnail">
                        <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="{{ $room->roomType->name }}">
                    </div>
                @endforeach
            @else
                <div class="thumbnail">
                    <img src="https://via.placeholder.com/400x200/d4af37/2c2c2c?text=Gallery" alt="Gallery">
                </div>
                <div class="thumbnail">
                    <img src="https://via.placeholder.com/400x200/d4af37/2c2c2c?text=View" alt="View">
                </div>
            @endif
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <div>
                <!-- Description -->
                <div class="room-description-section">
                    <h2 class="section-title">Room Description</h2>
                    <p class="room-description">{{ $room->roomType->description }}</p>
                </div>

                <!-- Amenities -->
                @if($room->amenities->count() > 0)
                    <div class="amenities-section" style="margin-top: 2rem;">
                        <h2 class="section-title">Room Amenities</h2>
                        <div class="amenities-grid">
                            @foreach($room->amenities as $amenity)
                                <div class="amenity-item">
                                    <i class="{{ $amenity->icon }}"></i>
                                    <span>{{ $amenity->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Booking Card -->
            <div class="booking-card">
                <div class="price-section">
                    <div class="price-from">From</div>
                    <div class="price-amount">₱{{ number_format($room->roomType->base_price, 0) }}</div>
                    <div class="price-period">/night</div>
                </div>

                <form class="booking-form" action="{{ route('booking.create') }}" method="POST">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                    
                    <div class="form-group">
                        <label>Check-in Date</label>
                        <input type="date" name="check_in" required min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label>Check-out Date</label>
                        <input type="date" name="check_out" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>

                    <div class="form-group">
                        <label>Number of Guests</label>
                        <select name="number_of_guests" required>
                            @for($i = 1; $i <= $room->roomType->max_guests; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Guest' : 'Guests' }}</option>
                            @endfor
                        </select>
                    </div>

                    <button type="submit" class="book-now-btn">
                        <i class="fas fa-calendar-check"></i> Book Now
                    </button>

                    <p class="booking-info">
                        <i class="fas fa-info-circle"></i> Free cancellation up to 24 hours before check-in
                    </p>
                </form>
            </div>
        </div>

        <!-- Features Section -->
        <div class="features-section">
            <h2 class="section-title">Room Features</h2>
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <div class="feature-content">
                        <h4>High-Speed WiFi</h4>
                        <p>Complimentary high-speed internet access throughout your stay</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-tv"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Smart TV</h4>
                        <p>Large flat-screen TV with premium channels and streaming services</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-snowflake"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Climate Control</h4>
                        <p>Individual temperature control for your comfort</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-content">
                        <h4>Safety & Security</h4>
                        <p>Electronic safe and 24/7 security monitoring</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Bez Tower & Residences. All rights reserved.</p>
        <p>205 F. Blumentritt Street, Brgy. Pedro Cruz, San Juan City, Philippines</p>
    </footer>
</body>
</html>
