@extends('customer.layout')

@section('title', 'Services - Beztower & Residences')

@section('content')
<section class="content-section">
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

<style>
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
</style>
@endsection
