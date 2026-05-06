@extends('layouts.admin')
@section('title', 'Order #' . str_pad($order->id, 6, '0', STR_PAD_LEFT))
@section('content')
<div style="max-width:900px; margin:0 auto;">

    <div style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
        <a href="{{ route('admin.orders') }}" style="color:#3498db; text-decoration:none; font-size:14px;">
            ← Back to Orders
        </a>
        <span style="font-size:13px; color:#888;">Order placed: {{ date('d/m/Y H:i', strtotime($order->created_at)) }}</span>
    </div>

    @if(session('success'))
        <div style="background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:8px; margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div style="background:#fdecea; color:#c62828; padding:12px; border-radius:8px; margin-bottom:20px;">{{ session('error') }}</div>
    @endif

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">

        {{-- Order Info --}}
        <div class="card">
            <h3 style="margin:0 0 15px; font-size:1rem;">📦 Order Information</h3>
            <p><strong>Order ID:</strong> #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
            <p><strong>Payment:</strong> {{ strtoupper($order->payment_method) }}</p>
            <p><strong>Total:</strong> <span style="color:#e63946; font-weight:bold;">${{ number_format($order->total / 25000, 2) }}</span></p>
            <p style="margin-top:15px;"><strong>Status:</strong></p>
            @if($order->status === 'delivered')
                <div style="margin-top:8px; display:flex; align-items:center; gap:10px;">
                    <span style="display:inline-block; padding:8px 14px; background:#e8f5e9; color:#2e7d32; border-radius:6px; font-weight:600; font-size:14px;">✅ Delivered</span>
                    <span style="font-size:13px; color:#888; font-style:italic;">Đơn hàng đã giao, không thể thay đổi trạng thái.</span>
                </div>
            @else
            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" style="margin-top:8px;">
                @csrf
                <div style="display:flex; gap:10px; align-items:center;">
                    <select name="status" style="flex:1; padding:8px 12px; border-radius:6px; border:1px solid #ddd; font-size:14px;">
                        <option value="pending"    {{ $order->status == 'pending'    ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>🔄 Processing</option>
                        <option value="shipped"    {{ $order->status == 'shipped'    ? 'selected' : '' }}>🚚 Shipped</option>
                        <option value="delivered"  {{ $order->status == 'delivered'  ? 'selected' : '' }}>✅ Delivered</option>
                        <option value="cancelled"  {{ $order->status == 'cancelled'  ? 'selected' : '' }}>❌ Cancelled</option>
                    </select>
                    <button type="submit" style="padding:8px 16px; background:#3498db; color:white; border:none; border-radius:6px; cursor:pointer; font-weight:600;">Update</button>
                </div>
            </form>
            @endif
        </div>

        {{-- Customer Info --}}
        <div class="card">
            <h3 style="margin:0 0 15px; font-size:1rem;">👤 Customer Information</h3>
            <p><strong>Username:</strong> {{ $order->username ?? 'Guest' }}</p>
            <p><strong>Email:</strong> {{ $order->email ?? 'N/A' }}</p>
            <p><strong>Shipping Address:</strong></p>
            <p style="background:#f8f9fa; padding:10px; border-radius:6px; font-size:13px; line-height:1.6; margin-top:5px;">{{ $order->shipping_address }}</p>
        </div>
    </div>

    {{-- Order Items --}}
    <div class="card">
        <h3 style="margin:0 0 20px; font-size:1rem;">🛒 Order Items</h3>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#f8f9fa; font-size:0.8rem; text-transform:uppercase; color:#888;">
                    <th style="padding:12px; text-align:left;">Product</th>
                    <th style="padding:12px; text-align:center;">Qty</th>
                    <th style="padding:12px; text-align:right;">Unit Price</th>
                    <th style="padding:12px; text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr style="border-bottom:1px solid #f5f5f5;">
                    <td style="padding:14px; display:flex; align-items:center; gap:12px;">
                        <img src="{{ asset('img/product/' . $item->image) }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'"
                             style="width:50px; height:50px; object-fit:cover; border-radius:6px; border:1px solid #eee;">
                        <span style="font-weight:600; font-size:14px;">{{ $item->name }}</span>
                    </td>
                    <td style="padding:14px; text-align:center; color:#666;">{{ $item->quantity }}</td>
                    <td style="padding:14px; text-align:right; color:#666;">${{ number_format($item->price / 25000, 2) }}</td>
                    <td style="padding:14px; text-align:right; font-weight:600;">${{ number_format(($item->price * $item->quantity) / 25000, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f8f9fa;">
                    <td colspan="3" style="padding:14px; text-align:right; font-weight:700;">Total:</td>
                    <td style="padding:14px; text-align:right; font-weight:700; color:#e63946; font-size:1.1rem;">${{ number_format($order->total / 25000, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>
@endsection
