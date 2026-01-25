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

        /* Navigation - moved to component */

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
            top: -240px;
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            font-weight: 600;
        }

        .form-group label i {
            color: #d4af37;
        }

        .form-group input,
        .form-group select {
            padding: 0.9rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 0.95rem;
            background: white;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #d4af37;
        }

        /* Guest Selector Styles */
        .guest-selector {
            position: relative;
        }

        .guest-display {
            padding: 0.9rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            background: white;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: border-color 0.3s;
        }

        .guest-display:hover {
            border-color: #d4af37;
        }

        .guest-display i {
            color: #d4af37;
            transition: transform 0.3s;
        }

        .guest-selector.active .guest-display i {
            transform: rotate(180deg);
        }

        .guest-dropdown {
            position: absolute;
            top: calc(100% + 0.5rem);
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #d4af37;
            border-radius: 5px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            display: none;
            z-index: 1000;
        }

        .guest-selector.active .guest-dropdown {
            display: block;
        }

        .guest-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eee;
        }

        .guest-option:last-of-type {
            border-bottom: none;
        }

        .guest-option label {
            font-weight: 600;
            color: #2c2c2c;
            margin: 0;
        }

        .counter {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .counter-btn {
            width: 35px;
            height: 35px;
            border: 2px solid #d4af37;
            background: white;
            color: #d4af37;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .counter-btn:hover:not(:disabled) {
            background: #d4af37;
            color: white;
        }

        .counter-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .counter input {
            width: 50px;
            text-align: center;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            color: #2c2c2c;
        }

        .guest-done-btn {
            width: 100%;
            margin-top: 1rem;
            padding: 0.8rem;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .guest-done-btn:hover {
            transform: translateY(-2px);
        }

        .book-now-btn {
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            padding: 1rem 2rem;
            border-radius: 5px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .book-now-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
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
            left: 1rem;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
            z-index: 2;
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
        
        .discount-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
            z-index: 2;
        }
        
        .original-price {
            font-size: 1.2rem;
            color: #999;
            text-decoration: line-through;
            margin: 0.25rem 0;
        }

        .discounted-price {
            color: #dc3545 !important;
            font-weight: 700;
        }
        
        .discounted-price {
            color: #dc3545 !important;
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

        .browse-all-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            padding: 1.2rem 3rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s;
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.3);
        }

        .browse-all-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.5);
        }

        .browse-all-btn i {
            font-size: 1.2rem;
        }

        /* Pagination Styles */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin: 3rem 0 2rem;
        }

        .pagination-nav {
            display: inline-block;
        }

        .pagination {
            display: flex;
            list-style: none;
            gap: 0.5rem;
            margin: 0;
            padding: 0;
        }

        .page-item {
            display: inline-block;
        }

        .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0.5rem 1rem;
            background: white;
            border: 2px solid #ddd;
            color: #2c2c2c;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }

        .page-link:hover {
            background: #f9f9f9;
            border-color: #d4af37;
            color: #d4af37;
            transform: translateY(-2px);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            border-color: #d4af37;
            color: #2c2c2c;
            cursor: default;
        }

        .page-item.disabled .page-link {
            background: #f5f5f5;
            border-color: #e0e0e0;
            color: #999;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .page-item.disabled .page-link:hover {
            transform: none;
            background: #f5f5f5;
            border-color: #e0e0e0;
        }

        #roomsGrid {
            position: relative;
            min-height: 400px;
        }

        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3rem;
            color: #d4af37;
            z-index: 10;
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
    @include('components.navbar')

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
            <form action="{{ route('rooms.index') }}" method="GET" id="heroBookingForm">
                <div class="booking-grid">
                    <div class="form-group">
                        <label><i class="far fa-calendar"></i> Check-In Date *</label>
                        <input type="date" name="check_in" id="heroCheckIn" min="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="far fa-calendar"></i> Check-Out Date *</label>
                        <input type="date" name="check_out" id="heroCheckOut" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-users"></i> Guests *</label>
                        <div class="guest-selector" id="guestSelector">
                            <div class="guest-display">
                                <span id="guestCount">1 Room, 1 Adult, 0 Child</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="guest-dropdown" id="guestDropdown">
                                <div class="guest-option">
                                    <label>Rooms</label>
                                    <div class="counter">
                                        <button type="button" class="counter-btn" data-action="decrement" data-target="rooms">-</button>
                                        <input type="number" name="rooms" id="rooms" value="1" min="1" max="5" readonly>
                                        <button type="button" class="counter-btn" data-action="increment" data-target="rooms">+</button>
                                    </div>
                                </div>
                                <div class="guest-option">
                                    <label>Adults</label>
                                    <div class="counter">
                                        <button type="button" class="counter-btn" data-action="decrement" data-target="adults">-</button>
                                        <input type="number" name="adults" id="adults" value="1" min="1" max="10" readonly>
                                        <button type="button" class="counter-btn" data-action="increment" data-target="adults">+</button>
                                    </div>
                                </div>
                                <div class="guest-option">
                                    <label>Children</label>
                                    <div class="counter">
                                        <button type="button" class="counter-btn" data-action="decrement" data-target="children">-</button>
                                        <input type="number" name="children" id="children" value="0" min="0" max="10" readonly>
                                        <button type="button" class="counter-btn" data-action="increment" data-target="children">+</button>
                                    </div>
                                </div>
                                <button type="button" class="guest-done-btn" id="guestDoneBtn">Done</button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="book-now-btn">
                        <i class="fas fa-search"></i> BOOK NOW
                    </button>
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
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.9!2d121.038!3d14.5986!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTTCsDM1JzU1LjAiTiAxMjHCsDAyJzE2LjgiRQ!5e0!3m2!1sen!2sph!4v1704067200000!5m2!1sen!2sph&q=205+F+Blumentritt+Street+Brgy+Pedro+Cruz+San+Juan+City+Philippines" 
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

        <div class="rooms-grid" id="roomsGrid">
            @include('customer.home.partials.room-cards', ['rooms' => $rooms])
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper" id="paginationWrapper">
            @include('customer.home.partials.pagination', ['rooms' => $rooms])
        </div>

        <!-- Browse All Rooms Button -->
        <div style="text-align: center; margin-top: 3rem;">
            <a href="{{ route('rooms.index') }}" class="browse-all-btn">
                <i class="fas fa-th-large"></i> Browse All Rooms
            </a>
            <p style="color: #666; margin-top: 1rem; font-size: 0.95rem;">
                Use advanced filters, search, and check real-time availability
            </p>
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
                    <p>(02) 88075046 or 09171221429</p>
                </div>
            </div>
            
            <div class="contact-info">
                <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                <div class="contact-details">
                    <h4>Email</h4>
                    <p>beztower05@gmail.com</p>
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

    <!-- Calendar Modal -->
    <div class="calendar-modal" id="calendarModal">
        <div class="calendar-content">
            <div class="calendar-header">
                <h3><i class="fas fa-calendar-alt"></i> Room Availability Calendar</h3>
                <button class="close-calendar" id="closeCalendar">&times;</button>
            </div>
            <div class="calendar-controls">
                <button class="calendar-nav" id="prevMonth"><i class="fas fa-chevron-left"></i></button>
                <h4 id="calendarMonthYear">Loading...</h4>
                <button class="calendar-nav" id="nextMonth"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="calendar-legend">
                <span class="legend-item"><span class="legend-dot available"></span> Available</span>
                <span class="legend-item"><span class="legend-dot partial"></span> Partially Booked</span>
                <span class="legend-item"><span class="legend-dot full"></span> Fully Booked</span>
            </div>
            <div id="calendarGrid" class="calendar-grid">
                <!-- Calendar will be populated here -->
            </div>
        </div>
    </div>

    <!-- Floating Calendar Button -->
    {{-- <button class="floating-calendar-btn" id="showCalendar">
        <i class="fas fa-calendar-check"></i>
        <span>Check Availability</span>
    </button> --}}

    <style>
        /* Calendar Modal Styles */
        .calendar-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            overflow-y: auto;
            padding: 2rem;
        }

        .calendar-modal.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .calendar-content {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            max-width: 800px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }

        .calendar-header h3 {
            color: #2c2c2c;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .calendar-header h3 i {
            color: #d4af37;
        }

        .close-calendar {
            background: none;
            border: none;
            font-size: 2rem;
            color: #999;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close-calendar:hover {
            color: #f44336;
        }

        .calendar-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .calendar-nav {
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            border: none;
            color: #2c2c2c;
            padding: 0.8rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.3s;
        }

        .calendar-nav:hover {
            transform: scale(1.05);
        }

        .calendar-legend {
            display: flex;
            gap: 2rem;
            justify-content: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }

        .legend-dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
        }

        .legend-dot.available {
            background: #4caf50;
        }

        .legend-dot.partial {
            background: #ff9800;
        }

        .legend-dot.full {
            background: #f44336;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 600;
            color: #d4af37;
            padding: 0.5rem;
            font-size: 0.9rem;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px solid #eee;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .calendar-day.empty {
            border: none;
            cursor: default;
        }

        .calendar-day.past {
            background: #f5f5f5;
            color: #ccc;
            cursor: not-allowed;
        }

        .calendar-day.available {
            border-color: #4caf50;
        }

        .calendar-day.available:hover {
            background: #e8f5e9;
            transform: scale(1.05);
        }

        .calendar-day.partial {
            border-color: #ff9800;
        }

        .calendar-day.partial:hover {
            background: #fff3e0;
        }

        .calendar-day.full {
            border-color: #f44336;
            background: #ffebee;
            cursor: not-allowed;
        }

        .calendar-day-number {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c2c2c;
        }

        .calendar-day-info {
            font-size: 0.7rem;
            color: #666;
            margin-top: 0.2rem;
        }

        /* Floating Calendar Button */
        .floating-calendar-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 50px;
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 999;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .floating-calendar-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.6);
        }

        @media (max-width: 768px) {
            .floating-calendar-btn {
                bottom: 1rem;
                right: 1rem;
                padding: 0.8rem 1.2rem;
                font-size: 0.9rem;
            }

            .calendar-content {
                padding: 1rem;
            }

            .calendar-grid {
                gap: 0.3rem;
            }

            .calendar-day-number {
                font-size: 0.9rem;
            }

            .calendar-day-info {
                font-size: 0.6rem;
            }
        }
    </style>

    <script>
        // Calendar functionality
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let calendarData = null;

        const calendarModal = document.getElementById('calendarModal');
        const showCalendarBtn = document.getElementById('showCalendar');
        const closeCalendarBtn = document.getElementById('closeCalendar');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');
        const calendarGrid = document.getElementById('calendarGrid');
        const calendarMonthYear = document.getElementById('calendarMonthYear');

        showCalendarBtn.addEventListener('click', () => {
            calendarModal.classList.add('active');
            loadCalendar(currentMonth, currentYear);
        });

        closeCalendarBtn.addEventListener('click', () => {
            calendarModal.classList.remove('active');
        });

        calendarModal.addEventListener('click', (e) => {
            if (e.target === calendarModal) {
                calendarModal.classList.remove('active');
            }
        });

        prevMonthBtn.addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            loadCalendar(currentMonth, currentYear);
        });

        nextMonthBtn.addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            loadCalendar(currentMonth, currentYear);
        });

        function loadCalendar(month, year) {
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            
            calendarMonthYear.textContent = `${monthNames[month]} ${year}`;
            calendarGrid.innerHTML = '<div style="text-align:center;padding:2rem;grid-column:1/-1;"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';

            fetch(`{{ route('calendar.availability') }}?month=${month + 1}&year=${year}`)
                .then(response => response.json())
                .then(data => {
                    calendarData = data;
                    renderCalendar(month, year, data);
                })
                .catch(error => {
                    console.error('Error loading calendar:', error);
                    calendarGrid.innerHTML = '<div style="text-align:center;padding:2rem;grid-column:1/-1;color:#f44336;">Error loading calendar</div>';
                });
        }

        function renderCalendar(month, year, data) {
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            let html = '';
            
            // Day headers
            const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            dayHeaders.forEach(day => {
                html += `<div class="calendar-day-header">${day}</div>`;
            });

            // Empty cells before first day
            for (let i = 0; i < firstDay; i++) {
                html += '<div class="calendar-day empty"></div>';
            }

            // Days
            for (let day = 1; day <= daysInMonth; day++) {
                const currentDate = new Date(year, month, day);
                const dateString = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                
                const bookedInfo = data.booked_dates.find(d => d.date === dateString);
                const blockedInfo = data.block_dates.find(d => d.date === dateString);
                
                let className = 'calendar-day';
                let statusText = 'Available';
                
                if (currentDate < today) {
                    className += ' past';
                    statusText = '';
                } else if (blockedInfo) {
                    className += ' full';
                    statusText = 'Blocked';
                } else if (bookedInfo) {
                    const bookedCount = bookedInfo.booked_rooms;
                    if (bookedCount >= data.total_rooms) {
                        className += ' full';
                        statusText = 'Full';
                    } else {
                        className += ' partial';
                        statusText = `${data.total_rooms - bookedCount} left`;
                    }
                } else {
                    className += ' available';
                    statusText = 'Available';
                }

                html += `
                    <div class="${className}" data-date="${dateString}">
                        <div class="calendar-day-number">${day}</div>
                        <div class="calendar-day-info">${statusText}</div>
                    </div>
                `;
            }

            calendarGrid.innerHTML = html;
        }

        // Auto-refresh calendar every 30 seconds if modal is open
        setInterval(() => {
            if (calendarModal.classList.contains('active')) {
                loadCalendar(currentMonth, currentYear);
            }
        }, 30000);

        // Date validation - only run if elements exist
        const checkInElement = document.getElementById('checkIn');
        const checkOutElement = document.getElementById('checkOut');
        
        if (checkInElement && checkOutElement) {
            checkInElement.addEventListener('change', function() {
                const checkIn = new Date(this.value);
                const minCheckOut = new Date(checkIn);
                minCheckOut.setDate(minCheckOut.getDate() + 1);
                checkOutElement.min = minCheckOut.toISOString().split('T')[0];
                
                if (checkOutElement.value && new Date(checkOutElement.value) <= checkIn) {
                    checkOutElement.value = '';
                }
            });
        }

        // AJAX Pagination for Rooms Section
        const roomsGrid = document.getElementById('roomsGrid');
        const paginationWrapper = document.getElementById('paginationWrapper');

        function attachPaginationHandlers() {
            const paginationLinks = paginationWrapper.querySelectorAll('a.page-link');
            
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    loadRooms(url);
                });
            });
        }

        function loadRooms(url) {
            // Show loading state
            roomsGrid.style.opacity = '0.5';
            roomsGrid.style.pointerEvents = 'none';
            
            // Add spinner
            const spinner = document.createElement('div');
            spinner.className = 'loading-spinner';
            spinner.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            roomsGrid.appendChild(spinner);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update rooms grid
                roomsGrid.innerHTML = data.rooms;
                
                // Update pagination
                paginationWrapper.innerHTML = data.pagination;
                
                // Remove loading state
                roomsGrid.style.opacity = '1';
                roomsGrid.style.pointerEvents = 'auto';
                
                // Reattach pagination handlers
                attachPaginationHandlers();
                
                // Scroll to rooms section
                document.getElementById('rooms').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            })
            .catch(error => {
                console.error('Error loading rooms:', error);
                roomsGrid.style.opacity = '1';
                roomsGrid.style.pointerEvents = 'auto';
                const spinner = roomsGrid.querySelector('.loading-spinner');
                if (spinner) spinner.remove();
            });
        }

        // Initialize pagination handlers on page load
        attachPaginationHandlers();

        // ===== HERO BOOKING FORM - GUEST SELECTOR =====
        // Elements should be available since script is at bottom of page
        const guestSelector = document.getElementById('guestSelector');
        const guestDisplay = guestSelector?.querySelector('.guest-display');
        const guestDoneBtn = document.getElementById('guestDoneBtn');
        const roomsInput = document.getElementById('rooms');
        const adultsInput = document.getElementById('adults');
        const childrenInput = document.getElementById('children');
        const guestCountDisplay = document.getElementById('guestCount');

        // Toggle guest dropdown
        if (guestDisplay) {
            guestDisplay.addEventListener('click', (e) => {
                e.stopPropagation();
                guestSelector.classList.toggle('active');
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (guestSelector && !guestSelector.contains(e.target)) {
                guestSelector.classList.remove('active');
            }
        });

        // Counter buttons functionality
        document.querySelectorAll('.counter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const action = btn.dataset.action;
                const target = btn.dataset.target;
                const input = document.getElementById(target);
                let value = parseInt(input.value);
                const min = parseInt(input.min);
                const max = parseInt(input.max);

                if (action === 'increment' && value < max) {
                    input.value = value + 1;
                } else if (action === 'decrement' && value > min) {
                    input.value = value - 1;
                }

                updateGuestDisplay();
            });
        });

        // Update guest display text
        function updateGuestDisplay() {
            const rooms = parseInt(roomsInput?.value || 1);
            const adults = parseInt(adultsInput?.value || 1);
            const children = parseInt(childrenInput?.value || 0);

            const roomText = rooms === 1 ? '1 Room' : `${rooms} Rooms`;
            const adultText = adults === 1 ? '1 Adult' : `${adults} Adults`;
            const childText = children === 0 ? '0 Child' : children === 1 ? '1 Child' : `${children} Children`;

            if (guestCountDisplay) {
                guestCountDisplay.textContent = `${roomText}, ${adultText}, ${childText}`;
            }
        }

        // Done button closes dropdown
        if (guestDoneBtn) {
            guestDoneBtn.addEventListener('click', (e) => {
                e.preventDefault();
                guestSelector.classList.remove('active');
            });
        }

        // Hero form date validation
        const heroCheckIn = document.getElementById('heroCheckIn');
        const heroCheckOut = document.getElementById('heroCheckOut');
        
        if (heroCheckIn && heroCheckOut) {
            heroCheckIn.addEventListener('change', function() {
                const checkIn = new Date(this.value);
                const minCheckOut = new Date(checkIn);
                minCheckOut.setDate(minCheckOut.getDate() + 1);
                heroCheckOut.min = minCheckOut.toISOString().split('T')[0];
                
                if (heroCheckOut.value && new Date(heroCheckOut.value) <= checkIn) {
                    heroCheckOut.value = '';
                }
            });
        }
    </script>
</body>
</html>
