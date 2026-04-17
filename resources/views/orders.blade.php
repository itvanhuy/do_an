@extends('layouts.app')

@section('title', 'My Orders - '.env('APP_NAME', 'TechShop'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/orders.css') }}">
    <style>
        .orders-container { max-width: 1000px; margin: 40px auto; padding: 0 20px; min-height: 60vh; }
        .page-title { margin-bottom: 30px; color: #333; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .no-orders { text-align: center; padding: 50px; color: #777; background: #f9f9f9; border-radius: 10px; }
        .no-orders i { font-size: 5rem; margin-bottom: 20px; color: #ddd; }
        .order-card { background: white; border: 1px solid #eee; border-radius: 10px; margin-bottom: 25px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: 0.3s; }
        .order-header { background: #f8f9fa; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; flex-wrap: wrap; }
        .order-status { padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .order-items { padding: 20px; }
        .order-item { display: flex; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f5f5f5; }
        .order-item:last-child { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
        .order-item img { width: 60px; height: 60px; object-fit: contain; margin-right: 15px; border-radius: 5px; }
        .item-info h4 { margin: 0 0 5px; font-size: 1rem; }
        .order-footer { padding: 15px 20px; background: white; border-top: 1px solid #eee; text-align: right; }
        .order-total span { color: var(--accent-color); font-weight: bold; font-size: 1.2rem; }
    </style>
@endsection

@section('content')
<main class="orders-container">
    <h1 class="page-title"><i class="fas fa-shopping-bag"></i> My Orders</h1>

    @if (empty($orders))
        <div class="no-orders">
            <i class="fas fa-box-open"></i>
            <p>You haven't placed any orders yet.</p>
            <a href="{{ url('shop') }}" class="btn" style="background: var(--accent-color); color: white; padding: 10px 25px; border-radius: 25px; text-decoration: none;">Start Shopping</a>
        </div>
    @else
        <div class="orders-list">
            @foreach ($orders as $order)
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span style="font-weight: bold;">Order #{{ str_pad($order['details']->id, 6, '0', STR_PAD_LEFT) }}</span>
                            <span style="margin-left: 15px; color: #666; font-size: 0.9rem;">
                                <i class="far fa-calendar-alt"></i> {{ date('d/m/Y H:i', strtotime($order['details']->created_at)) }}
                            </span>
                        </div>
                        <div class="order-status status-{{ $order['details']->status }}">
                            {{ ucfirst($order['details']->status) }}
                        </div>
                    </div>
                    
                    <div class="order-items">
                        @foreach ($order['items'] as $item)
                            <div class="order-item">
                                <img src="{{ asset('img/product/' . $item->image) }}" alt="{{ $item->name }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                                <div class="item-info">
                                    <h4>{{ $item->name }}</h4>
                                    <p>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}₫</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="order-footer">
                        <div class="order-total">
                            Total: <span>{{ number_format($order['details']->total, 0, ',', '.') }}₫</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</main>
@endsection
