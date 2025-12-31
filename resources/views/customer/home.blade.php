@extends('layouts.customer')

@section('title', 'Home - Beztower & Residences Hotel')

@section('content')
<!-- Hero Section with Video -->
<section class="relative h-screen">
    <div class="absolute inset-0 bg-black">
        <video autoplay muted loop playsinline class="w-full h-full object-cover opacity-60">
            <source src="{{ asset('videos/bezHomePage.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    
    <div class="relative z-10 flex items-center justify-center h-full">
        <div class="text-center text-white px-4">
            <h1 class="text-5xl md:text-7xl font-bold mb-4" style="font-family: 'Georgia', serif;">
                ENJOY A LUXURY EXPERIENCE
            </h1>
            <p class="text-xl md:text-2xl mb-8">BEZTOWER & RESIDENCES HOTEL</p>
            
            <!-- Booking Widget -->
            <div class="bg-white/95 backdrop-blur-sm rounded-lg shadow-2xl p-8 max-w-5xl mx-auto mt-12">
                <form action="{{ route('booking.checkAvailability') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div class="text-left">
                        <label class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-calendar-alt mr-2"></i>Check In
                        </label>
                        <input type="date" name="check_in" id="check_in" required 
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="text-left">
                        <label class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-calendar-alt mr-2"></i>Check Out
                        </label>
                        <input type="date" name="check_out" id="check_out" required 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="text-left">
                        <label class="block text-gray-700 font-medium mb-2">
                            <i class="fas fa-users mr-2"></i>Guests
                        </label>
                        <select name="guests" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="1">1 Guest</option>
                            <option value="2" selected>2 Guests</option>
                            <option value="3">3 Guests</option>
                            <option value="4">4 Guests</option>
                            <option value="5">5+ Guests</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-300">
                            <i class="fas fa-search mr-2"></i>Check Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Scroll Down Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-10">
        <a href="#features" class="text-white animate-bounce">
            <i class="fas fa-chevron-down fa-2x"></i>
        </a>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center mb-12">Why Choose Beztower?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6">
                <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-concierge-bell text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Premium Service</h3>
                <p class="text-gray-600">24/7 concierge service to make your stay unforgettable</p>
            </div>
            <div class="text-center p-6">
                <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-wifi text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Modern Amenities</h3>
                <p class="text-gray-600">High-speed WiFi, Smart TVs, and luxury comfort</p>
            </div>
            <div class="text-center p-6">
                <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marker-alt text-blue-600 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-3">Prime Location</h3>
                <p class="text-gray-600">Located in the heart of the city with easy access</p>
            </div>
        </div>
    </div>
</section>

<!-- Location Map Section -->
<section id="location" class="py-20 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold text-center mb-12">Our Location</h2>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="h-96">
                <!-- Replace with actual Google Maps embed or coordinates -->
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.3158!2d121.0244!3d14.5995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTTCsDM1JzU4LjIiTiAxMjHCsDAxJzI3LjgiRQ!5e0!3m2!1sen!2sph!4v1234567890"
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-2">Beztower & Residences Hotel</h3>
                <p class="text-gray-600"><i class="fas fa-map-marker-alt mr-2"></i>123 Main Street, City Center</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold mb-8">Get In Touch</h2>
        <p class="text-xl text-gray-600 mb-8">Have questions? We're here to help!</p>
        <div class="flex flex-col md:flex-row justify-center items-center space-y-4 md:space-y-0 md:space-x-8">
            <a href="tel:+1234567890" class="flex items-center text-lg text-gray-700 hover:text-blue-600">
                <i class="fas fa-phone-alt mr-3 text-blue-600"></i>
                +1 234 567 8910
            </a>
            <a href="mailto:info@beztower.com" class="flex items-center text-lg text-gray-700 hover:text-blue-600">
                <i class="fas fa-envelope mr-3 text-blue-600"></i>
                info@beztower.com
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Ensure check-out is always after check-in
    document.getElementById('check_in').addEventListener('change', function() {
        const checkIn = new Date(this.value);
        const checkOut = document.getElementById('check_out');
        const minCheckOut = new Date(checkIn);
        minCheckOut.setDate(minCheckOut.getDate() + 1);
        checkOut.min = minCheckOut.toISOString().split('T')[0];
        
        if (checkOut.value && new Date(checkOut.value) <= checkIn) {
            checkOut.value = minCheckOut.toISOString().split('T')[0];
        }
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>
@endpush
