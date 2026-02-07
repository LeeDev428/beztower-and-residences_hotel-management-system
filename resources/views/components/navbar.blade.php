<nav class="navbar">
    <div class="logo">
        <img src="{{ asset('images/logo/bezlogo.jpg') }}" alt="Bez Tower & Residences Logo" class="logo-image">
        <span class="logo-text">BEZ TOWER & RESIDENCES</span>
    </div>
    
    <ul class="nav-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('home') }}#about">About</a></li>
        <li><a href="{{ route('rooms.index') }}">Rooms</a></li>
        <li><a href="{{ route('home') }}#services">Services</a></li>
        <li><a href="{{ route('home') }}#contact">Contact</a></li>
    </ul>
    
    <div class="nav-right">
        {{-- <div class="contact-number">
            <i class="fas fa-phone"></i>
            (02) 88075046 or 09171221429
        </div> --}}
    </div>
</nav>

<style>
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
        gap: 0.75rem;
    }

    .logo-image {
        height: 50px;
        width: auto;
        object-fit: contain;
        border-radius: 8px;
    }

    .logo-text {
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
        letter-spacing: 2px;
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
        gap: 1.5rem;
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

    .check-now-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #d4af37, #f4e4c1);
        color: #2c2c2c;
        padding: 0.6rem 1.5rem;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .check-now-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(212, 175, 55, 0.4);
    }

    .check-now-btn i {
        font-size: 0.85rem;
    }

    @media (max-width: 768px) {
        .navbar {
            padding: 1rem;
        }
        
        .nav-links {
            display: none;
        }

        .contact-number {
            display: none;
        }
    }
</style>
