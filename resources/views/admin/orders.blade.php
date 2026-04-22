@extends('layouts.admin')
@section('title', 'Order Management')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Order Management</h3>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif

    {{-- Status Filter Tabs --}}
    <div style="display:flex; gap:5px; margin-bottom:25px; flex-wrap:wrap; border-bottom:2px solid #f0f0f0; padding-bottom:0;">
        @php
        $tabs = [
            'all'        => ['label' => 'All', 'color' => '#333'],
            'pending'    => ['label' => 'Pending', 'color' => '#f39c12'],
            'processing' => ['label' => 'Processing', 'color' => '#3498db'],
            'shipped'    => ['label' => 'Shipped', 'color' => '#9b59b6'],
            'delivered'  => ['label' => 'Delivered', 'color' => '#27ae60'],
            'cancelled'  => ['label' => 'Cancelled', 'color' => '#e74c3c'],
        ];
        @endphp
        @foreach($tabs as $key => $tab)
        <a href="{{ route('admin.orders', ['status' => $key]) }}" style="padding:8px 16px; text-decoration:none; border-radius:8px 8px 0 0; font-size:0.85rem; font-weight:600; border:2px solid transparent; border-bottom:none; transition:all 0.2s;
            {{ $statusFilter === $key ? 'background:white; color:' . $tab['color'] . '; border-color:#f0f0f0; border-bottom-color:white; margin-bottom:-2px;' : 'background:#f8f9fa; color:#888;' }}">
            {{ $tab['label'] }}
            <span style="background:{{ $statusFilter === $key ? $tab['color'] : '#ccc' }}; color:white; padding:2px 7px; border-radius:20px; font-size:0.75rem; margin-left:4px;">{{ $statusCounts[$key] }}</span>
        </a>
        @endforeach
    </div>

    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:15px;">Order ID</th>
                <th style="padding:15px;">Customer</th>
                <th style="padding:15px;">Total</th>
                <th style="padding:15px;">Status</th>
                <th style="padding:15px;">Order Date</th>
                <th style="padding:15px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td style="padding:15px; font-weight:bold;">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td style="padding:15px;">{{ $order->username ?? 'Guest' }}</td>
                <td style="padding:15px; font-weight:bold; color:#e63946;">${{ number_format($order->total / 25000, 2) }}</td>
                <td style="padding:15px;">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" onchange="this.submit()">
                        @csrf
                        <select name="status" style="padding:6px 12px; border-radius:20px; font-size:0.8rem; border:1px solid #ddd; background:#fafafa; cursor:pointer;">
                            <option value="pending"    {{ $order->status == 'pending'    ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>🔄 Processing</option>
                            <option value="shipped"    {{ $order->status == 'shipped'    ? 'selected' : '' }}>🚚 Shipped</option>
                            <option value="delivered"  {{ $order->status == 'delivered'  ? 'selected' : '' }}>✅ Delivered</option>
                            <option value="cancelled"  {{ $order->status == 'cancelled'  ? 'selected' : '' }}>❌ Cancelled</option>
                        </select>
                    </form>
                </td>
                <td style="padding:15px; color:#888;">{{ date('M d, Y H:i', strtotime($order->created_at)) }}</td>
                <td style="padding:15px;">
                    <a href="{{ route('orders.show', $order->id) }}" style="text-decoration:none; color:#3498db; font-size:0.85rem;">
                        <i class="fas fa-eye"></i> Details
                    </a>
                </td>
            </tr>
            @endforeach
            @if($orders->isEmpty())
            <tr><td colspan="6" style="padding:30px; text-align:center; color:#888;">No orders found.</td></tr>
            @endif
        </tbody>
    </table>
    <div style="margin-top:20px;">
        {{ $orders->appends(['status' => $statusFilter])->links() }}
    </div>
</div>
@endsection
