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
            top: -200px;
        }

        .hero-title {
            font-size: 5rem;
            font-weight: 300;
            letter-spacing: 0.05em;
            line-height: 1.2;
            margin-bottom: 2rem;
            font-family: 'Georgia', serif;
        }

        /* Content Sections */
        .content-section {
            padding: 5rem 3rem;
            max-width: 1200px;
            margin: 0 auto;
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

        .section-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #666;
            text-align: center;
            max-width: 800px;
            margin: 0 auto 3rem;
        }

        .about-section {
            background: #f9f9f9;
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

        /* Play Button */
        .play-button {
            position: absolute;
            bottom: 3rem;
            right: 3rem;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid white;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            animation: pulse 2s infinite;
        }

        .play-button:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .play-button i {
            color: white;
            font-size: 1.5rem;
            margin-left: 5px;
        }

        .play-ring {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 100px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: ringPulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes ringPulse {
            0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
            100% { transform: translate(-50%, -50%) scale(1.3); opacity: 0; }
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
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="{{ route('rooms.index') }}">Rooms</a></li>
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
            <div class="hero-label">BEZTOWER LUXURY HOTEL</div>
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
        
        <!-- Play Button -->
        <div class="play-button" onclick="toggleVideo()">
            <div class="play-ring"></div>
            <i class="fas fa-play" id="playIcon"></i>
        </div>
    </section>

    <!-- About Section -->
    <section class="content-section about-section" id="about">
        <div class="section-subtitle">DISCOVER OUR HOTEL</div>
        <h2 class="section-title">Bez Tower & Residences</h2>
        <p class="section-description">
            Experience comfort and convenience in the heart of San Juan City. Located at 205 F. Blumentritt Street, Brgy. Pedro Cruz, we offer safe, secure accommodation with modern amenities and exceptional service.
        </p>
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

        // Video toggle
        function toggleVideo() {
            const video = document.getElementById('heroVideo');
            const icon = document.getElementById('playIcon');
            
            if (video.paused) {
                video.play();
                icon.classList.remove('fa-play');
                icon.classList.add('fa-pause');
            } else {
                video.pause();
                icon.classList.remove('fa-pause');
                icon.classList.add('fa-play');
            }
        }
    </script>
</body>
</html>
