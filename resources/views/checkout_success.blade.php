@extends('layouts.app')

@section('title', 'Order Success - '.env('APP_NAME', 'TechShop'))

@section('content')
<main style="max-width: 800px; margin: 80px auto; text-align: center; padding: 40px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">

    @if(request('momo_error') || request('vnpay_error'))
        {{-- MoMo thất bại --}}
        <div style="font-size: 5rem; color: #e74c3c; margin-bottom: 20px;">
            <i class="fas fa-times-circle"></i>
        </div>
        <h1 style="color: #e74c3c; margin-bottom: 15px;">Payment Failed</h1>
        <p style="color: #636e72; margin-bottom: 10px;">
            Your MoMo payment was cancelled or failed.
        </p>
        @if(isset($orderId))
            <p style="font-size: 1.1rem; color: #636e72; margin-bottom: 30px;">
                Order <strong>#{{ $orderId }}</strong> has been cancelled.
            </p>
        @endif
        <div style="display: flex; gap: 20px; justify-content: center;">
            <a href="{{ url('checkout') }}" style="padding: 12px 30px; background: #e94560; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                Try Again
            </a>
            <a href="{{ route('home') }}" style="padding: 12px 30px; background: #f1f2f6; color: #2d3436; border-radius: 8px; text-decoration: none; font-weight: 600;">
                Back to Home
            </a>
        </div>
    @else
        {{-- Thành công --}}
        <div style="font-size: 5rem; color: #4CAF50; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 style="color: #2d3436; margin-bottom: 15px;">Order Placed Successfully!</h1>

        @if(isset($orderId))
            <p style="font-size: 1.2rem; color: #636e72; margin-bottom: 10px;">
                Order ID: <strong style="color: #e94560;">#{{ $orderId }}</strong>
            </p>
        @endif

        @php
            $paymentMethod = isset($orderId) ? \Illuminate\Support\Facades\DB::table('orders')->where('id', $orderId)->value('payment_method') : null;
        @endphp

        @if($paymentMethod === 'banking')
            <div style="background:#fff3f8; border:1px solid #d63384; border-radius:8px; padding:20px; margin:20px auto; max-width:500px; text-align:left;">
                <h3 style="color:#d63384; margin:0 0 15px;">📱 Complete Payment via MoMo</h3>
                <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:flex-start;">
                    <div>
                        <p><strong>MoMo Number:</strong> 0896492400</p>
                        <p><strong>Account Name:</strong> LE VAN HUY</p>
                        <p style="margin-top:10px; color:#e74c3c; font-size:13px;">
                            Transfer note: <strong>Order {{ $orderId }}</strong>
                        </p>
                        <p style="font-size:12px; color:#666; margin-top:8px;">
                            Your order will be confirmed automatically after payment.
                        </p>
                    </div>
                    <div style="text-align:center;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=2|99|0896492400|LE+VAN+HUY||0|0|0|TechShop+Order+{{ $orderId }}|transfer_myqr" 
                             alt="MoMo QR" style="width:150px; border-radius:8px; border:1px solid #ddd;">
                        <p style="font-size:11px; color:#666; margin-top:5px;">Scan to pay via MoMo</p>
                    </div>
                </div>

                {{-- Auto check payment status --}}
                <div id="payment-status" style="margin-top:15px; padding:10px; background:#fff; border-radius:6px; text-align:center;">
                    <span id="status-text" style="color:#f39c12;">⏳ Waiting for payment...</span>
                </div>
            </div>

            @auth
            <script>
                const orderId = {{ $orderId }};
                let checkCount = 0;
                const maxChecks = 60; // check 5 phút

                function checkPayment() {
                    fetch(`/api/order-status/${orderId}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.confirmed) {
                                document.getElementById('status-text').innerHTML =
                                    '✅ <strong style="color:#27ae60;">Payment confirmed! Your order is being processed.</strong>';
                                clearInterval(timer);
                            } else if (checkCount >= maxChecks) {
                                document.getElementById('status-text').innerHTML =
                                    '⚠️ Payment not detected yet. Please contact us if you have transferred.';
                                clearInterval(timer);
                            }
                            checkCount++;
                        })
                        .catch(() => {});
                }

                const timer = setInterval(checkPayment, 5000); // check mỗi 5 giây
                checkPayment(); // check ngay lập tức
            </script>
            @endauth
        @else
            <div style="background: #f0fff4; border: 1px solid #b2dfdb; border-radius: 8px; padding: 15px; margin: 20px auto; max-width: 400px;">
                <p style="color: #27ae60; font-weight: 600; margin: 0;">💵 Cash on Delivery — Pay when you receive your order.</p>
            </div>
        @endif

        <p style="color: #636e72; line-height: 1.6; margin-bottom: 40px; max-width: 500px; margin-left: auto; margin-right: auto;">
            We have received your order and are processing it. A confirmation email will be sent to you shortly.
        </p>

        <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ url('orders') }}" style="padding: 12px 30px; background: #e94560; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 15px rgba(233,69,96,0.3);">
                View My Orders
            </a>
            <a href="{{ url('shop') }}" style="padding: 12px 30px; background: #f1f2f6; color: #2d3436; border-radius: 8px; text-decoration: none; font-weight: 600;">
                Continue Shopping
            </a>
        </div>
    @endif
</main>
@endsection
