@extends('layouts.app')

@section('title', 'Order Success - '.env('APP_NAME', 'TechShop'))

@section('content')
<main class="success-container" style="max-width: 800px; margin: 80px auto; text-align: center; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
    <div class="success-icon" style="font-size: 5rem; color: #4CAF50; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i>
    </div>
    
    <h1 style="color: #2d3436; margin-bottom: 15px;">{{ $message ?? 'Thank You for Your Purchase!' }}</h1>
    
    @if(isset($orderId))
        <p style="font-size: 1.2rem; color: #636e72; margin-bottom: 30px;">
            Order ID: <strong style="color: var(--accent-color);">#{{ $orderId }}</strong>
        </p>
    @endif
    
    <p style="color: #636e72; line-height: 1.6; margin-bottom: 40px; max-width: 500px; margin-left: auto; margin-right: auto;">
        We have received your order and are processing it. You will receive an email confirmation shortly with your order details.
    </p>
    
    <div class="success-actions" style="display: flex; gap: 20px; justify-content: center;">
        <a href="{{ route('home') }}" class="btn" style="padding: 12px 30px; background: #f1f2f6; color: #2d3436; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s;">
            Back to Home
        </a>
        <a href="{{ url('shop') }}" class="btn" style="padding: 12px 30px; background: var(--accent-color); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3);">
            Continue Shopping
        </a>
    </div>
</main>
@endsection
