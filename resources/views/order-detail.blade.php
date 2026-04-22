@extends('layouts.app')

@section('title', 'Order #'.str_pad($order->id, 6, '0', STR_PAD_LEFT).' - TechShop')

@section('styles')
    <style>
        .order-detail-container { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .order-meta-box { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        @media (max-width: 600px) { .order-meta-box { grid-template-columns: 1fr; gap: 20px; } }
        .info-group h4 { margin: 0 0 10px; color: #888; font-size: 0.85rem; text-transform: uppercase; }
        .info-group p { margin: 0; font-size: 1.1rem; font-weight: 500; }
        .order-items-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .item-row { display: flex; align-items: center; padding: 20px; border-bottom: 1px solid #f5f5f5; }
        .item-row:last-child { border-bottom: none; }
        .item-img { width: 80px; height: 80px; object-fit: contain; margin-right: 20px; border-radius: 8px; background: #f9f9f9; }
        .item-info { flex: 1; }
        .item-info h5 { margin: 0 0 5px; font-size: 1.05rem; }
        .order-summary-box { background: #f8f9fa; padding: 25px; border-top: 2px solid #eee; text-align: right; }
        .summary-row { margin-bottom: 10px; display: flex; justify-content: flex-end; gap: 50px; }
        .total-row { font-size: 1.3rem; font-weight: bold; margin-top: 15px; color: var(--accent-color); }
        .status-badge { display: inline-block; padding: 8px 15px; border-radius: 30px; font-weight: bold; font-size: 0.9rem; margin-top: 10px; text-transform: uppercase; }
        .status-pending { background: #fff3e0; color: #e65100; }
        .status-delivered { background: #e8f5e9; color: #2e7d32; }
    </style>
@endsection

@section('content')
<main class="order-detail-container">
    <div style="margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ url('orders') }}" style="text-decoration: none; color: #666;"><i class="fas fa-arrow-left"></i> Back to Orders</a>
        <h2 style="margin:0;">Order #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h2>
    </div>

    <div class="order-meta-box">
        <div class="info-group">
            <h4>Order Status</h4>
            <span class="status-badge status-{{ $order->status }}">{{ $order->status }}</span>
        </div>
        <div class="info-group">
            <h4>Placed On</h4>
            <p>{{ date('F d, Y H:i', strtotime($order->created_at)) }}</p>
        </div>
        <div class="info-group">
            <h4>Shipping Address</h4>
            <p>{{ $order->shipping_address }}</p>
        </div>
        <div class="info-group">
            <h4>Payment Method</h4>
            <p>{{ strtoupper($order->payment_method) }}</p>
        </div>
    </div>

    <div class="order-items-card">
        @foreach($items as $item)
            <div class="item-row">
                <img src="{{ asset('img/product/'.$item->image) }}" alt="{{ $item->name }}" class="item-img" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                <div class="item-info">
                    <h5>{{ $item->name }}</h5>
                    <p style="color: #666; margin:0;">Qty: {{ $item->quantity }}</p>
                </div>
                <p style="font-weight:bold; margin:0;">${{ number_format(($item->price * $item->quantity) / 25000, 2) }}</p>
            </div>
        @endforeach

        <div class="order-summary-box">
            <div class="summary-row">
                <span>Subtotal</span>
                <strong>${{ number_format($order->total / 25000, 2) }}</strong>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <strong>Free</strong>
            </div>
            <div class="summary-row total-row">
                <span>Total Amount</span>
                <span>${{ number_format($order->total / 25000, 2) }}</span>
            </div>
        </div>
    </div>
</main>
@endsection
