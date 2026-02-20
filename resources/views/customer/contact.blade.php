@extends('customer.layout')

@section('title', 'Contact Us - Beztower & Residences')

@section('content')
<section class="content-section contact-section">
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

        @if(session('success'))
            <div style="background: #d4edda; color: #155724; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #c3e6cb;">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border: 1px solid #f5c6cb;">
                @foreach($errors->all() as $error)
                    <p><i class="fas fa-exclamation-circle"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form class="contact-form" action="{{ route('contact.send') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group-contact">
                    <label>Name</label>
                    <input type="text" name="name" placeholder="Your Name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group-contact">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="your@email.com" value="{{ old('email') }}" required>
                </div>
            </div>
            <div class="form-group-contact">
                <label>Subject</label>
                <input type="text" name="subject" placeholder="Subject" value="{{ old('subject') }}" required>
            </div>
            <div class="form-group-contact">
                <label>Message</label>
                <textarea name="message" rows="5" placeholder="Your message..." required>{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </div>
</section>

<style>
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
    
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .contact-form-container {
            padding: 2rem 1.5rem;
        }
    }
</style>
@endsection

@section('scripts')
<div id="toastContainer" style="position:fixed;top:1.25rem;right:1.25rem;z-index:99999;display:flex;flex-direction:column;gap:0.6rem;pointer-events:none;max-width:380px;"></div>
<script>
function showContactToast(message, type) {
    const configs = {
        success: { bg:'#d4edda', border:'#28a745', icon:'✓', text:'#155724', label:'Success' },
        error:   { bg:'#f8d7da', border:'#dc3545', icon:'✕', text:'#721c24', label:'Error' },
        warning: { bg:'#fff3cd', border:'#ffc107', icon:'⚠', text:'#856404', label:'Warning' },
    };
    const c = configs[type] || configs.success;
    const t = document.createElement('div');
    t.style.cssText = 'pointer-events:all;background:'+c.bg+';border-left:5px solid '+c.border+';border-radius:10px;padding:1rem 1.25rem;display:flex;align-items:flex-start;gap:0.75rem;box-shadow:0 8px 24px rgba(0,0,0,0.18);transform:translateX(120%);transition:transform 0.35s cubic-bezier(0.34,1.56,0.64,1),opacity 0.3s ease;opacity:0;max-width:380px;width:100%;';
    t.innerHTML = '<div style="width:30px;height:30px;border-radius:50%;background:'+c.border+';color:white;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:700;flex-shrink:0;">'+c.icon+'</div><div style="flex:1;"><div style="font-weight:700;font-size:0.8rem;text-transform:uppercase;letter-spacing:0.5px;color:'+c.border+';margin-bottom:0.2rem;">'+c.label+'</div><div style="color:'+c.text+';font-size:0.9rem;line-height:1.4;">'+message+'</div></div><button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:'+c.text+';font-size:1.2rem;padding:0;flex-shrink:0;opacity:0.6;">&times;</button>';
    document.getElementById('toastContainer').appendChild(t);
    requestAnimationFrame(()=>requestAnimationFrame(()=>{t.style.transform='translateX(0)';t.style.opacity='1';}));
    setTimeout(()=>{t.style.transform='translateX(120%)';t.style.opacity='0';setTimeout(()=>t.remove(),350);}, 5000);
}
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showContactToast('{{ addslashes(session('success')) }}', 'success');
    @endif
    @if(session('warning'))
        showContactToast('{{ addslashes(session('warning')) }}', 'warning');
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
        showContactToast('{{ addslashes($error) }}', 'error');
        @endforeach
    @endif
});
</script>
@endsection
