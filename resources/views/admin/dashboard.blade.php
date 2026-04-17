@extends('layouts.admin')

@section('title', 'Dashboard')

@section('styles')
    <style>
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .stat-icon { width: 60px; height: 60px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; }
        .stat-icon.users { background: #3498db; }
        .stat-icon.products { background: #e67e22; }
        .stat-icon.orders { background: #2ecc71; }
        .stat-icon.revenue { background: #9b59b6; }
        .stat-val { font-size: 1.8rem; font-weight: bold; margin: 0; }
        .stat-label { color: #888; font-size: 0.9rem; margin: 0; text-transform: uppercase; }
        
        .dashboard-main { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        @media (max-width: 1200px) { .dashboard-main { grid-template-columns: 1fr; } }
        
        .recent-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .recent-table th { text-align: left; padding: 15px; border-bottom: 2px solid #f5f5f5; color: #888; font-size: 0.85rem; text-transform: uppercase; }
        .recent-table td { padding: 15px; border-bottom: 1px solid #f5f5f5; font-size: 0.95rem; }
        
        .product-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #f5f5f5; }
        .product-item:last-child { border-bottom: none; }
        .product-name { font-weight: 600; font-size: 1rem; color: #333; margin: 0; }
        .product-meta { font-size: 0.85rem; color: #888; margin: 5px 0 0; }

        .status-delivered { background: #dff9fb; color: #130f40; }
        .status-shipped { background: #e3f2fd; color: #1976d2; }
        .status-pending { background: #fff3e0; color: #e65100; }
        .status-cancelled { background: #ffebee; color: #c62828; }
    </style>
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon users"><i class="fas fa-users"></i></div>
        <div>
            <p class="stat-val">{{ number_format($totalUsers) }}</p>
            <p class="stat-label">Total Users</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon products"><i class="fas fa-box"></i></div>
        <div>
            <p class="stat-val">{{ number_format($totalProducts) }}</p>
            <p class="stat-label">Total Products</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orders"><i class="fas fa-shopping-cart"></i></div>
        <div>
            <p class="stat-val">{{ number_format($totalOrders) }}</p>
            <p class="stat-label">Total Orders</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orders"><i class="fas fa-calendar-day"></i></div>
        <div>
            <p class="stat-val">{{ number_format($dailyOrders) }}</p>
            <p class="stat-label">Daily Orders</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon revenue"><i class="fas fa-money-bill-wave"></i></div>
        <div>
            <p class="stat-val">{{ number_format($monthlyRevenue, 0, ',', '.') }}₫</p>
            <p class="stat-label">Monthly Revenue</p>
        </div>
    </div>
</div>

<div class="dashboard-main">
    <div class="card">
        <h3 style="margin-top:0;">Recent Orders</h3>
        <table class="recent-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr>
                    <td style="font-weight:bold;">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $order->username }}</td>
                    <td style="font-weight:bold;">{{ number_format($order->total, 0, ',', '.') }}₫</td>
                    <td>
                        <span class="status-badge status-{{ $order->status }}">{{ $order->status }}</span>
                    </td>
                    <td style="color: #888;">{{ date('M d, Y', strtotime($order->created_at)) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3 style="margin-top:0;">Top Selling Products</h3>
        <div style="margin-top: 20px;">
            @foreach($topProducts as $product)
            <div class="product-item">
                <div>
                    <h4 class="product-name">{{ $product->name }}</h4>
                    <p class="product-meta">{{ number_format($product->sold) }} units sold</p>
                </div>
                <div style="text-align: right;">
                    <p style="font-weight:bold; margin:0;">{{ number_format($product->price * $product->sold, 0, ',', '.') }}₫</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
