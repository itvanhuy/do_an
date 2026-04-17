@extends('layouts.app')

@section('title', 'My Profile - '.env('APP_NAME', 'TechShop'))

@section('styles')
    <style>
        .profile-container { max-width: 800px; margin: 40px auto; padding: 20px; display: grid; grid-template-columns: 250px 1fr; gap: 40px; }
        @media (max-width: 768px) { .profile-container { grid-template-columns: 1fr; } }
        .profile-sidebar { background: #f8f9fa; padding: 30px; border-radius: 10px; text-align: center; height: fit-content; }
        .avatar { width: 120px; height: 120px; border-radius: 50%; background: #ddd; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; }
        .sidebar-menu { list-style: none; padding: 0; margin-top: 30px; text-align: left; }
        .sidebar-menu li { margin-bottom: 15px; }
        .sidebar-menu a { text-decoration: none; color: #333; display: flex; align-items: center; gap: 10px; font-weight: 500; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { color: var(--accent-color); }
        .profile-content { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn-save { background: var(--accent-color); color: white; border: none; padding: 12px 30px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
    </style>
@endsection

@section('content')
<main class="profile-container">
    <div class="profile-sidebar">
        <div class="avatar"><i class="fas fa-user"></i></div>
        <h3>{{ $user->username }}</h3>
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 0;">{{ $user->email }}</p>
        
        <ul class="sidebar-menu">
            <li><a href="{{ url('profile') }}" class="active"><i class="fas fa-user-circle"></i> Profile Info</a></li>
            <li><a href="{{ url('orders') }}"><i class="fas fa-shopping-bag"></i> My Orders</a></li>
            <li><a href="{{ url('wishlist') }}"><i class="fas fa-heart"></i> My Wishlist</a></li>
            <li><a href="#" onclick="document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="profile-content">
        <h2 style="margin-top:0; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 25px;">Account Settings</h2>
        
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin:0; padding-left: 20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Enter your phone number">
            </div>
            <div class="form-group">
                <label>Shipping Address</label>
                <textarea name="address" rows="3" placeholder="Enter your default delivery address">{{ old('address', $user->address) }}</textarea>
            </div>
            <button type="submit" class="btn-save">Save Changes</button>
        </form>

        <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">
        
        <h3>Change Password</h3>
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="new_password_confirmation" required>
            </div>
            <button type="submit" class="btn-save" style="background:#4CAF50;">Update Password</button>
        </form>
    </div>
</main>
@endsection
