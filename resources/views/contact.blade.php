@extends('layouts.app')
@section('title', 'Contact Us - TechShop')
@section('content')
<main style="max-width: 1000px; margin: 40px auto; padding: 0 20px;">
    <div style="text-align:center; margin-bottom: 50px;">
        <h1 style="font-size: 2.5rem; margin-bottom: 15px;">Get in Touch</h1>
        <p style="color:#666; font-size: 1.1rem;">Have a question? We'd love to hear from you.</p>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 50px;">
        <div>
            <h3 style="margin-bottom:25px;">Contact Information</h3>
            <div style="margin-bottom:20px;">
                <p style="font-weight:bold; margin-bottom:5px;"><i class="fas fa-map-marker-alt" style="color:var(--accent-color); margin-right:10px;"></i> Address</p>
                <p style="color:#666;">99 To Hien Thanh, Son Tra, Da Nang, Vietnam</p>
            </div>
            <div style="margin-bottom:20px;">
                <p style="font-weight:bold; margin-bottom:5px;"><i class="fas fa-phone" style="color:var(--accent-color); margin-right:10px;"></i> Phone</p>
                <p style="color:#666;">(+84) 0896 492 400</p>
            </div>
            <div style="margin-bottom:20px;">
                <p style="font-weight:bold; margin-bottom:5px;"><i class="fas fa-envelope" style="color:var(--accent-color); margin-right:10px;"></i> Email</p>
                <p style="color:#666;">levanhuy06042003@gmail.com</p>
            </div>
            <div style="margin-top:40px;">
                <h4 style="margin-bottom:15px;">Follow Us</h4>
                <div style="display:flex; gap:15px; font-size:1.5rem;">
                    <a href="https://www.facebook.com/huylv.2k3" target="_blank" style="color:#3b5998;"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="color:#1da1f2;"><i class="fab fa-twitter"></i></a>
                    <a href="#" style="color:#e1306c;"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        <div style="background:#f9f9f9; padding:30px; border-radius:15px;">
            @if(session('success'))
                <div style="background:#e8f5e9; color:#2e7d32; padding:15px; border-radius:8px; margin-bottom:20px;">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('contact.send') }}" method="POST">
                @csrf
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Full Name</label>
                    <input type="text" name="name" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Email Address</label>
                    <input type="email" name="email" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Subject</label>
                    <input type="text" name="subject" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                </div>
                <div style="margin-bottom:20px;">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Message</label>
                    <textarea name="message" required rows="5" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;"></textarea>
                </div>
                <button type="submit" style="background:#e63946; color:white; border:none; padding:14px 30px; border-radius:8px; font-weight:bold; cursor:pointer; width:100%; font-size:15px; transition: background 0.3s;">
                    <i class="fas fa-paper-plane" style="margin-right:8px;"></i> Send Message
                </button>
            </form>
        </div>
    </div>
</main>
@endsection
