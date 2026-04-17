@extends('layouts.admin')
@section('title', 'Manage Orders')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Order List</h3>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:15px;">Order #</th>
                <th style="padding:15px;">Customer</th>
                <th style="padding:15px;">Amount</th>
                <th style="padding:15px;">Status</th>
                <th style="padding:15px;">Date</th>
                <th style="padding:15px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td style="padding:15px; font-weight:bold;">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</td>
                <td style="padding:15px;">{{ $order->username }}</td>
                <td style="padding:15px; font-weight:bold;">{{ number_format($order->total, 0, ',', '.') }}₫</td>
                <td style="padding:15px;">
                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" onchange="this.submit()">
                        @csrf
                        <select name="status" style="padding:5px 10px; border-radius:20px; font-size:0.8rem; border:1px solid #ddd; background: #fafafa; cursor: pointer;">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </form>
                </td>
                <td style="padding:15px; color:#888;">{{ date('M d, Y', strtotime($order->created_at)) }}</td>
                <td style="padding:15px;">
                    <a href="{{ route('orders.show', $order->id) }}" style="text-decoration:none; color:#3498db;"><i class="fas fa-eye"></i> Details</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:20px;">
        {{ $orders->links() }}
    </div>
</div>
@endsection
