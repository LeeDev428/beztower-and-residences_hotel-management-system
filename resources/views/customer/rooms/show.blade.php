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

        @media (max-width: 768px) {
            .navbar {
                padding: 1rem 1.5rem;
            }

            .nav-links {
                display: none;
            }

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
                <div class="rating-section">
                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="rating-text">4.8 (127 reviews)</span>
                    <div class="status-badge {{ strtolower($room->status) }}">{{ $room->status }}</div>
                </div>

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
                    <a href="{{ route('home') }}#rooms" class="check-now-btn">
                        <i class="fas fa-calendar-check"></i> Check Availability
                    </a>
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
                    <p>
                        Experience luxury and comfort in our beautifully designed {{ $room->roomType->name }}. 
                        Each room is thoughtfully crafted to provide you with a memorable stay, featuring modern amenities 
                        and elegant furnishings that create a warm and inviting atmosphere.
                    </p>
                    <p>
                        Located in the heart of San Juan City at 205 F. Blumentritt Street, Brgy. Pedro Cruz, 
                        our hotel offers easy access to major attractions while providing a peaceful retreat from the bustling city.
                    </p>
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

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 Bez Tower & Residences. All rights reserved.</p>
        <p>205 F. Blumentritt Street, Brgy. Pedro Cruz, San Juan City, Philippines</p>
    </footer>

    <script>
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
    </script>
</body>
</html>
