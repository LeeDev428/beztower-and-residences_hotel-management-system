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
            <li><a href="{{ route('about') }}">About</a></li>
            <li><a href="{{ route('rooms.index') }}">Rooms</a></li>
            <li><a href="{{ route('services') }}">Services</a></li>
            <li><a href="{{ route('contact') }}">Contact</a></li>
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
