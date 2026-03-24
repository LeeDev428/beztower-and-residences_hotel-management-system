@extends('customer.layout')

@section('title', 'Services - Beztower & Residences')

@section('content')
<section class="content-section">
    <div class="section-subtitle">OUR FACILITIES</div>
    <h2 class="section-title">Premium Services</h2>

    <div class="services-grid">
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-id-card"></i></div>
                <h3>Secure Keycard Access</h3>
                <p>Access to floors is restricted via keycard, ensuring privacy and safety for all guests.</p>
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

            <div class="service-card">
                <div class="service-icon"><i class="fas fa-building"></i></div>
                <h3>Rooftop Event Space</h3>
                <p>A scenic rooftop venue perfect for private events and celebrations, accommodating up to 200 guests.</p>
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
