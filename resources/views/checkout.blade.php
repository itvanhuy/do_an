@extends('layouts.app')

@section('title', 'Checkout - '.env('APP_NAME', 'TechShop'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
    <style>
        .checkout-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .checkout-content { display: flex; gap: 40px; }
        @media (max-width: 768px) { .checkout-content { flex-direction: column; } }
        .checkout-form { flex: 2; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .order-summary { flex: 1; background: #f9f9f9; padding: 30px; border-radius: 10px; height: fit-content; position: sticky; top: 90px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: border 0.3s; }
        .form-group input:focus, .form-group textarea:focus { border-color: #4361ee; outline: none; }
        .payment-option { display: flex; align-items: center; padding: 15px 20px; border: 2px solid #e0e0e0; border-radius: 10px; margin-bottom: 12px; cursor: pointer; transition: all 0.3s; }
        .payment-option:hover { border-color: #4361ee; background: #f8f9ff; }
        .payment-option input[type="radio"] { margin-right: 12px; width: 18px; height: 18px; accent-color: #4361ee; }
        .payment-option.selected { border-color: #4361ee; background: #f0f4ff; }
        .place-order-btn { width: 100%; padding: 15px; background: #4361ee; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: bold; cursor: pointer; margin-top: 20px; transition: background 0.3s; }
        .place-order-btn:hover { background: #3451d1; }
        .order-item { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .order-item img { width: 60px; height: 60px; object-fit: cover; border-radius: 6px; }
        .item-details { flex: 1; }
        .item-details h4 { margin: 0 0 4px; font-size: 0.9rem; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 12px; color: #555; }
        .grand-total { font-size: 1.2rem; font-weight: bold; color: #4361ee; border-top: 2px solid #ddd; padding-top: 15px; margin-top: 5px; }
        .error-message { background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
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
        {{-- LEFT: Form --}}
        <div class="checkout-form">
            <h2 style="margin-bottom:20px;">Shipping Information</h2>
            <form action="{{ url('checkout/process') }}" method="POST" id="checkoutForm">
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
                    <small style="color:#888;">Discount will be applied automatically.</small>
                </div>

                <h2 style="margin: 25px 0 15px;">Payment Method</h2>
                <div class="payment-methods">

                    {{-- COD --}}
                    <label class="payment-option" id="label-cod">
                        <input type="radio" name="payment_method" value="cod"
                            {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}
                            onchange="selectPayment('cod')">
                        <div>
                            <strong>💵 Cash on Delivery (COD)</strong>
                            <p style="margin:4px 0 0; font-size:13px; color:#666;">Pay when you receive your order.</p>
                        </div>
                    </label>

                    {{-- VNPay --}}
                    <label class="payment-option" id="label-momo">
                        <input type="radio" name="payment_method" value="vnpay"
                            {{ old('payment_method') === 'vnpay' ? 'checked' : '' }}
                            onchange="selectPayment('momo')">
                        <div style="display:flex; align-items:center; gap:12px; flex:1;">
                            <div>
                                <strong>💳 Online Payment (VNPay)</strong>
                                <p style="margin:4px 0 0; font-size:13px; color:#666;">Pay securely via VNPay gateway.</p>
                            </div>
                            <img src="https://vinadesign.vn/uploads/images/2023/05/vnpay-logo-vinadesign-25-12-57-55.jpg" alt="VNPay" style="height:32px; margin-left:auto; border-radius:4px;">
                        </div>
                    </label>

                </div>

                <button type="submit" class="place-order-btn">
                    <i class="fas fa-lock" style="margin-right:8px;"></i> Place Order Now
                </button>
            </form>
        </div>

        {{-- RIGHT: Order Summary --}}
        <div class="order-summary">
            <h2 style="margin-bottom:20px;">Order Summary</h2>
            @foreach ($cartItems as $item)
            <div class="order-item">
                <img src="{{ asset('img/product/' . $item->image) }}" alt="{{ $item->name }}"
                     onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                <div class="item-details">
                    <h4>{{ $item->name }}</h4>
                    <p style="color:#888; margin:0; font-size:13px;">
                        {{ $item->quantity }} x {{ number_format(($item->current_price ?? $item->price) / 1000, 0, ',', '.') }}đ
                    </p>
                </div>
                <div style="font-weight:bold; font-size:14px;">
                    {{ number_format($item->total / 1000, 0, ',', '.') }}đ
                </div>
            </div>
            @endforeach

            <div style="margin-top:15px;">
                <div class="total-row"><span>Subtotal</span><span>{{ number_format($subtotal / 1000, 0, ',', '.') }}đ</span></div>
                <div class="total-row"><span>Shipping</span><span>{{ number_format($shippingFee / 1000, 0, ',', '.') }}đ</span></div>
                <div class="total-row grand-total"><span>Total</span><span>{{ number_format($total / 1000, 0, ',', '.') }}đ</span></div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    function selectPayment(method) {
        document.getElementById('label-cod').classList.toggle('selected', method === 'cod');
        document.getElementById('label-momo').classList.toggle('selected', method === 'momo');
    }
    document.addEventListener('DOMContentLoaded', function() {
        const checked = document.querySelector('input[name="payment_method"]:checked');
        if (checked) selectPayment(checked.value);
    });
</script>
@endsection
