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

<style>
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

    @media (max-width: 768px) {
        .navbar {
            padding: 1rem 1.5rem;
        }

        .nav-links {
            display: none;
        }
    }
</style>
