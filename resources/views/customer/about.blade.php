@extends('customer.layout')

@section('title', 'About Us - Beztower & Residences')

@section('content')
<section class="content-section about-section">
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

<style>
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
</style>
@endsection
