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
                    <div style="display:flex; gap:8px;">
                        <input type="text" name="coupon_code" id="coupon_input" value="{{ old('coupon_code') }}" placeholder="Enter coupon code" style="text-transform:uppercase; flex:1;">
                        <button type="button" onclick="applyCoupon()" style="padding:12px 18px; background:#4361ee; color:white; border:none; border-radius:6px; cursor:pointer; font-weight:600; white-space:nowrap;">Apply</button>
                    </div>
                    <div id="coupon-result" style="margin-top:8px; font-size:13px;"></div>
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
                        {{ $item->quantity }} x ${{ number_format(($item->current_price ?? $item->price) / 25000, 2) }}
                    </p>
                </div>
                <div style="font-weight:bold; font-size:14px;">
                    ${{ number_format($item->total / 25000, 2) }}
                </div>
            </div>
            @endforeach

            <div style="margin-top:15px;">
                <div class="total-row"><span>Subtotal</span><span id="summary-subtotal">${{ number_format($subtotal / 25000, 2) }}</span></div>
                <div class="total-row"><span>Shipping</span><span>${{ number_format($shippingFee / 25000, 2) }}</span></div>
                <div class="total-row" id="discount-row" style="display:none; color:#27ae60;">
                    <span>Discount <span id="discount-label"></span></span>
                    <span id="discount-amount">-$0.00</span>
                </div>
                <div class="total-row grand-total"><span>Total</span><span id="summary-total">${{ number_format($total / 25000, 2) }}</span></div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
<script>
    const subtotal = {{ $subtotal }};
    const shippingFee = {{ $shippingFee }};

    function formatVND(amount) {
        return '$' + (amount / 25000).toFixed(2);
    }

    function applyCoupon() {
        const code = document.getElementById('coupon_input').value.trim().toUpperCase();
        const resultEl = document.getElementById('coupon-result');

        if (!code) {
            resultEl.innerHTML = '<span style="color:#e74c3c;">Please enter a coupon code.</span>';
            return;
        }

        resultEl.innerHTML = '<span style="color:#888;">Checking...</span>';

        fetch('{{ route("coupon.apply") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ coupon_code: code, subtotal: subtotal })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const discount = data.discount_amount;
                const newTotal = subtotal + shippingFee - discount;

                document.getElementById('discount-row').style.display = 'flex';
                document.getElementById('discount-label').textContent = '(' + code + ')';
                document.getElementById('discount-amount').textContent = '-' + formatVND(discount);
                document.getElementById('summary-total').textContent = formatVND(newTotal);

                resultEl.innerHTML = '<span style="color:#27ae60;">✅ ' + data.message + '</span>';
            } else {
                document.getElementById('discount-row').style.display = 'none';
                document.getElementById('summary-total').textContent = formatVND(subtotal + shippingFee);
                resultEl.innerHTML = '<span style="color:#e74c3c;">❌ ' + data.message + '</span>';
            }
        })
        .catch(() => {
            resultEl.innerHTML = '<span style="color:#e74c3c;">Error checking coupon.</span>';
        });
    }

    // Apply on Enter key
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('coupon_input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); applyCoupon(); }
        });

        // Auto-apply if old value exists
        if (document.getElementById('coupon_input').value) applyCoupon();

        const checked = document.querySelector('input[name="payment_method"]:checked');
        if (checked) selectPayment(checked.value);
    });

    function selectPayment(method) {
        document.getElementById('label-cod').classList.toggle('selected', method === 'cod');
        document.getElementById('label-momo').classList.toggle('selected', method === 'momo');
    }
</script>
@endsection
