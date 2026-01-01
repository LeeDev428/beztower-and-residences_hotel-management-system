<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beztower & Residences - Luxury Hotel</title>
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            padding: 1rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 2px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #2c2c2c;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
            position: relative;
        }

        .nav-links a:hover {
            color: #d4af37;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 2rem;
            color: white;
        }

        .contact-number {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(212, 175, 55, 0.2);
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
            font-size: 0.9rem;
        }

        .cart-icon {
            font-size: 1.2rem;
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
            text-align: center;
            color: white;
            max-width: 1200px;
            padding: 2rem;
        }

        .hero-label {
            font-size: 0.9rem;
            letter-spacing: 3px;
            margin-bottom: 1rem;
            color: #d4af37;
            position: relative;
            top: -250px;
        }

        .hero-title {
            font-size: 5rem;
            font-weight: 300;
            letter-spacing: 0.05em;
            line-height: 1.2;
            margin-bottom: 2rem;
            font-family: 'Georgia', serif;
        }

        /* Booking Form */
        .booking-form {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 1.5rem 2.5rem;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 1200px;
            z-index: 20;
        }

        .booking-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 0.8fr 0.8fr 1fr auto;
            gap: 1.5rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            color: #666;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group input,
        .form-group select {
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.95rem;
            background: white;
            cursor: pointer;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #d4af37;
        }

        .check-btn {
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            font-size: 1rem;
        }

        .check-btn:hover {
            transform: translateY(-2px);
        }

        /* Map Section */
        .map-section {
            padding: 5rem 3rem;
            background: #f9f9f9;
        }

        .map-container {
            max-width: 1200px;
            margin: 3rem auto 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .map-container iframe {
            width: 100%;
            height: 500px;
            border: 0;
        }

        .section-title {
            font-size: 3rem;
            font-weight: 300;
            margin-bottom: 2rem;
            color: #2c2c2c;
            font-family: 'Georgia', serif;
            text-align: center;
        }

        .section-subtitle {
            color: #d4af37;
            font-size: 0.9rem;
            letter-spacing: 3px;
            margin-bottom: 1rem;
            text-align: center;
        }

        /* Content Sections */
        .content-section {
            padding: 5rem 3rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-section {
            background: #f9f9f9;
        }

        .about-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-top: 4rem;
        }

        .feature-item {
            text-align: center;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: #2c2c2c;
        }

        .feature-item h3 {
            font-size: 1.5rem;
            color: #2c2c2c;
            margin-bottom: 1rem;
        }

        .feature-item p {
            color: #666;
            line-height: 1.6;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .service-card {
            background: white;
            padding: 2.5rem;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .service-card:hover {
            transform: translateY(-10px);
        }

        .service-icon {
            font-size: 3rem;
            color: #d4af37;
            margin-bottom: 1.5rem;
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #2c2c2c;
        }

        .service-card p {
            color: #666;
            line-height: 1.6;
        }

        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2.5rem;
            margin-top: 3rem;
        }

        .room-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .room-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .room-image {
            position: relative;
            height: 250px;
            overflow: hidden;
        }

        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .room-card:hover .room-image img {
            transform: scale(1.1);
        }

        .room-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .room-details {
            padding: 1.5rem;
        }

        .room-details h3 {
            font-size: 1.5rem;
            color: #2c2c2c;
            margin-bottom: 0.5rem;
            font-family: 'Georgia', serif;
        }

        .room-number {
            color: #d4af37;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .room-info {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1rem;
            color: #666;
            font-size: 0.9rem;
        }

        .room-info i {
            color: #d4af37;
            margin-right: 0.3rem;
        }

        .room-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .room-amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .amenity-tag {
            background: #f9f9f9;
            padding: 0.4rem 0.8rem;
            border-radius: 5px;
            font-size: 0.85rem;
            color: #666;
        }

        .amenity-tag i {
            color: #d4af37;
            margin-right: 0.3rem;
        }

        .room-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
        }

        .room-price {
            display: flex;
            flex-direction: column;
        }

        .price-label {
            font-size: 0.85rem;
            color: #999;
        }

        .price-amount {
            font-size: 1.8rem;
            font-weight: 600;
            color: #d4af37;
        }

        .price-period {
            font-size: 0.85rem;
            color: #666;
        }

        .book-btn {
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            padding: 0.8rem 1.5rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s;
        }

        .book-btn:hover {
            transform: translateY(-2px);
        }

        .contact-section {
            background: #2c2c2c;
            color: white;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }

        .contact-info {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .contact-icon {
            font-size: 2rem;
            color: #d4af37;
        }

        .contact-details h4 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: #d4af37;
        }

        .contact-details p {
            color: #ccc;
            line-height: 1.6;
        }

        .contact-form-container {
            margin-top: 4rem;
            background: white;
            padding: 3rem;
            border-radius: 10px;
        }

        .contact-form-container h3 {
            color: #2c2c2c;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group-contact {
            margin-bottom: 1.5rem;
        }

        .form-group-contact label {
            display: block;
            color: #666;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group-contact input,
        .form-group-contact textarea {
            width: 100%;
            padding: 0.9rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            font-family: inherit;
        }

        .form-group-contact input:focus,
        .form-group-contact textarea:focus {
            outline: none;
            border-color: #d4af37;
        }

        .submit-btn {
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            padding: 1rem 3rem;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            font-size: 1rem;
            display: block;
            margin: 0 auto;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 3rem;
            }
            
            .navbar {
                padding: 1rem;
            }
            
            .nav-links {
                display: none;
            }
            
            .booking-grid {
                grid-template-columns: 1fr;
            }

            .booking-form {
                width: 95%;
                padding: 1rem;
                bottom: 1rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .content-section {
                padding: 3rem 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .contact-form-container {
                padding: 2rem 1.5rem;
            }

            .rooms-grid {
                grid-template-columns: 1fr;
            }

            .room-footer {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-gem"></i>
            </div>
            BEZTOWER
        </div>
        
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#rooms">Rooms</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
        
        <div class="nav-right">
            <div class="contact-number">
                <i class="fas fa-phone"></i>
                +1 234 567 8910
            </div>
            <i class="fas fa-shopping-cart cart-icon"></i>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <video class="hero-video" autoplay muted loop playsinline id="heroVideo">
            <source src="{{ asset('videos/bezHomePage.mp4') }}" type="video/mp4">
        </video>
        
        <div class="hero-overlay"></div>
        
        <div class="hero-content">
            <div class="hero-label">BEZTOWER AND RESIDENCES</div>
            {{-- <h1 class="hero-title">ENJOY A LUXURY<br>EXPERIENCE</h1> --}}
        </div>
        
        <!-- Booking Form -->
        <div class="booking-form">
            <form action="{{ route('rooms.index') }}" method="GET">
                <div class="booking-grid">
                    <div class="form-group">
                        <label><i class="far fa-calendar"></i> Check In</label>
                        <input type="date" name="check_in" id="checkIn" min="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="far fa-calendar"></i> Check Out</label>
                        <input type="date" name="check_out" id="checkOut" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Adult</label>
                        <select name="adults">
                            <option value="1">1</option>
                            <option value="2" selected>2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-child"></i> Children</label>
                        <select name="children">
                            <option value="0" selected>0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-bed"></i> Room</label>
                        <select name="room_type">
                            <option value="">All Rooms</option>
                            <option value="1">Standard</option>
                            <option value="2">Deluxe</option>
                            <option value="3">Suite</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="check-btn">Check Now</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="section-subtitle">FIND US</div>
        <h2 class="section-title">Our Location</h2>
        
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.3836567891644!2d121.03553631484284!3d14.603754389788835!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b7c3e0e0e0e1%3A0x0!2zMTTCsDM2JzEzLjUiTiAxMjHCsDAyJzE4LjMiRQ!5e0!3m2!1sen!2sph!4v1704067200000!5m2!1sen!2sph" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>

    <!-- About Section -->
    <section class="content-section about-section" id="about">
        <div class="section-subtitle">DISCOVER OUR HOTEL</div>
        <h2 class="section-title">Bez Tower & Residences</h2>
        <p class="section-description">
            Experience comfort and convenience in the heart of San Juan City. Located at 205 F. Blumentritt Street, Brgy. Pedro Cruz, we offer safe, secure accommodation with modern amenities and exceptional service.
        </p>
        
        <div class="about-features">
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Safe & Secure</h3>
                <p>24/7 security and surveillance for your peace of mind</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-map-marker-alt"></i></div>
                <h3>Prime Location</h3>
                <p>In the heart of San Juan City, close to major attractions</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-star"></i></div>
                <h3>Exceptional Service</h3>
                <p>Dedicated staff ensuring memorable experiences</p>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-home"></i></div>
                <h3>Modern Amenities</h3>
                <p>Contemporary facilities for comfort and convenience</p>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section class="content-section" id="rooms">
        <div class="section-subtitle">ACCOMMODATION</div>
        <h2 class="section-title">Our Luxury Rooms</h2>
        <p class="section-description">
            Discover our collection of elegantly designed rooms and suites, each offering the perfect blend of comfort and sophistication.
        </p>

        <div class="rooms-grid">
            @php
                $rooms = \App\Models\Room::with(['roomType', 'amenities', 'photos'])->get();
            @endphp
            
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
                        <p class="room-number">Room {{ $room->room_number }}</p>
                        
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
                            <a href="{{ route('rooms.show', $room) }}" class="book-btn">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Services Section -->
    <section class="content-section" id="services">
        <div class="section-subtitle">OUR FACILITIES</div>
        <h2 class="section-title">Premium Services</h2>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-concierge-bell"></i></div>
                <h3>24/7 Concierge</h3>
                <p>Our dedicated concierge team is available around the clock to assist with your every need.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-utensils"></i></div>
                <h3>Fine Dining</h3>
                <p>Experience culinary excellence at our signature restaurant with international and local cuisine.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-spa"></i></div>
                <h3>Spa & Wellness</h3>
                <p>Rejuvenate your body and mind with our premium spa treatments and wellness facilities.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-dumbbell"></i></div>
                <h3>Fitness Center</h3>
                <p>Stay active with our state-of-the-art gym equipment and personal training services.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-swimming-pool"></i></div>
                <h3>Swimming Pool</h3>
                <p>Relax and unwind at our infinity pool with stunning city views.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-wifi"></i></div>
                <h3>High-Speed WiFi</h3>
                <p>Stay connected with complimentary high-speed internet throughout the property.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-parking"></i></div>
                <h3>Parking</h3>
                <p>Secure parking facilities available for all our guests.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-luggage-cart"></i></div>
                <h3>Luggage Storage</h3>
                <p>Complimentary luggage storage service for your convenience.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-broom"></i></div>
                <h3>Housekeeping</h3>
                <p>Daily housekeeping services to ensure your comfort.</p>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="content-section contact-section" id="contact">
        <div class="section-subtitle" style="color: #d4af37;">GET IN TOUCH</div>
        <h2 class="section-title" style="color: white;">Contact Us</h2>
        
        <div class="contact-grid">
            <div class="contact-info">
                <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div class="contact-details">
                    <h4>Address</h4>
                    <p>205 F. Blumentritt Street<br>Brgy. Pedro Cruz<br>San Juan City, Philippines</p>
                </div>
            </div>
            
            <div class="contact-info">
                <div class="contact-icon"><i class="fas fa-phone"></i></div>
                <div class="contact-details">
                    <h4>Phone</h4>
                    <p>+1 234 567 8910<br>+1 234 567 8911</p>
                </div>
            </div>
            
            <div class="contact-info">
                <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                <div class="contact-details">
                    <h4>Email</h4>
                    <p>info@beztower.com<br>reservations@beztower.com</p>
                </div>
            </div>
        </div>
        
        <div class="contact-form-container">
            <h3>Send Us a Message</h3>
            <form class="contact-form">
                <div class="form-row">
                    <div class="form-group-contact">
                        <label>Name</label>
                        <input type="text" placeholder="Your Name" required>
                    </div>
                    <div class="form-group-contact">
                        <label>Email</label>
                        <input type="email" placeholder="your@email.com" required>
                    </div>
                </div>
                <div class="form-group-contact">
                    <label>Subject</label>
                    <input type="text" placeholder="Subject" required>
                </div>
                <div class="form-group-contact">
                    <label>Message</label>
                    <textarea rows="5" placeholder="Your message..." required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>
    </section>

    <script>
        // Date validation
        document.getElementById('checkIn').addEventListener('change', function() {
            const checkIn = new Date(this.value);
            const checkOut = document.getElementById('checkOut');
            const minCheckOut = new Date(checkIn);
            minCheckOut.setDate(minCheckOut.getDate() + 1);
            checkOut.min = minCheckOut.toISOString().split('T')[0];
            
            if (checkOut.value && new Date(checkOut.value) <= checkIn) {
                checkOut.value = '';
            }
        });
    </script>
</body>
</html>
