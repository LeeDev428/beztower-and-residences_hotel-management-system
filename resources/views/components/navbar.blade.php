<nav class="navbar">
    <div class="logo">
        <div class="logo-icon">
            <i class="fas fa-gem"></i>
        </div>
        BEZTOWER
    </div>
    
    <ul class="nav-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('home') }}#about">About</a></li>
        <li><a href="{{ route('home') }}#rooms">Rooms</a></li>
        <li><a href="{{ route('home') }}#services">Services</a></li>
        <li><a href="{{ route('home') }}#contact">Contact</a></li>
    </ul>
    
    <div class="nav-right">
        {{-- <div class="contact-number">
            <i class="fas fa-phone"></i>
            +1 234 567 8910
        </div> --}}
        <a href="{{ route('home') }}#rooms" class="check-now-btn">
            <i class="fas fa-search"></i> Check Now
        </a>
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
