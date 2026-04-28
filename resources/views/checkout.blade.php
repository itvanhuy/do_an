@extends('layouts.app')

@section('title', 'Checkout - '.env('APP_NAME', 'TechShop'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
    <style>
        .error-message { background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        .checkout-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .checkout-content { display: flex; gap: 40px; }
        @media (max-width: 768px) { .checkout-content { flex-direction: column; } }
        .checkout-form { flex: 2; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .order-summary { flex: 1; background: #f9f9f9; padding: 30px; border-radius: 10px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; }
        .payment-methods { margin: 20px 0; }
        .payment-option { display: block; padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; }
        .payment-option:hover { border-color: var(--accent-color); }
        .payment-option input { margin-right: 15px; }
        .place-order-btn { width: 100%; padding: 15px; background: #4361ee; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: bold; cursor: pointer; display: block; margin-top: 20px; }
        .order-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .order-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 15px; }
        .item-details { flex: 1; }
        .item-details h4 { margin: 0 0 5px; font-size: 0.95rem; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .grand-total { font-size: 1.2rem; font-weight: bold; color: var(--accent-color); border-top: 2px solid #ddd; padding-top: 15px; }
    </style>
@endsection

@section('content')
<main class="checkout-container">
    <h1 style="margin-bottom: 30px;">Checkout</h1>

    @if ($errors->any())
        <div class="error-message">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="checkout-content">
        <div class="checkout-form">
            <h2>Shipping Information</h2>
            <form action="{{ url('checkout/process') }}" method="POST">
                @csrf
                <input type="hidden" name="is_buy_now" value="{{ $isBuyNow ? '1' : '0' }}">
                @if($isBuyNow)
                    <input type="hidden" name="buy_now_product_id" value="{{ $cartItems[0]->product_id }}">
                    <input type="hidden" name="buy_now_quantity" value="{{ $cartItems[0]->quantity }}">
                @endif
                
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" required value="{{ old('name', $user->full_name ?? $user->username) }}" placeholder="Enter your full name">
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="text" name="phone" required value="{{ old('phone', $user->phone) }}" placeholder="Enter your phone number">
                </div>
                <div class="form-group">
                    <label>Shipping Address *</label>
                    <textarea name="shipping_address" required rows="3" placeholder="Enter your detailed delivery address">{{ old('shipping_address', $user->address) }}</textarea>
                </div>
                
                <div class="form-group">
                    <label>Coupon Code</label>
                    <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="Enter coupon code (if any)" style="text-transform:uppercase;">
                    <small style="color: #666;">(Discount will be automatically calculated when you place the order)</small>
                </div>
                
                <h2>Payment Method</h2>
                <div class="payment-methods">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="cod" {{ old('payment_method') !== 'vnpay' ? 'checked' : '' }}> 
                        <span>Cash on Delivery (COD)</span>
                    </label>
                    
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="momo" {{ old('payment_method') === 'momo' ? 'checked' : '' }}> 
                        <span>Pay via MoMo E-Wallet</span>
                        <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="MoMo" style="height: 28px; margin-left: 10px;">
                    </label>
                </div>
                
                <button type="submit" class="place-order-btn">Place Order Now</button>
            </form>
        </div>
        
        <div class="order-summary">
            <h2>Order Summary</h2>
            <div class="order-items">
                @foreach ($cartItems as $item)
                <div class="order-item">
                    <img src="{{ asset('img/product/' . $item->image) }}" alt="{{ $item->name }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                    <div class="item-details">
                        <h4>{{ $item->name }}</h4>
                        <p style="color: #666; margin:0;">{{ $item->quantity }} x ${{ number_format(($item->current_price ?? $item->price) / 25000, 2) }}</p>
                    </div>
                    <div style="font-weight:bold;">${{ number_format($item->total / 25000, 2) }}</div>
                </div>
                @endforeach
            </div>
            <div class="order-totals" style="margin-top:20px;">
                <div class="total-row"><span>Subtotal</span> <span>${{ number_format($subtotal / 25000, 2) }}</span></div>
                <div class="total-row"><span>Shipping Fee</span> <span>${{ number_format($shippingFee / 25000, 2) }}</span></div>
                <div class="total-row grand-total"><span>Total</span> <span>${{ number_format($total / 25000, 2) }}</span></div>
            </div>
        </div>
    </div>
</main>
@endsection
