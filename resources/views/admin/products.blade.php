@extends('layouts.admin')
@section('title', 'Manage Products')
@section('content')
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 25px;">
        <h3 style="margin:0;">Product List</h3>
        <a href="{{ route('admin.products.create') }}" class="btn" style="background:var(--admin-accent); color:white; border:none; padding:10px 20px; border-radius:5px; text-decoration:none; display:inline-block;">+ Add New Product</a>
    </div>
    @if(session('success')) <div style="background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:5px; margin-bottom:15px;">{{ session('success') }}</div> @endif
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr style="text-align:left; border-bottom:2px solid #f5f5f5; color:#888; font-size:0.85rem; text-transform:uppercase;">
                <th style="padding:15px;">Image</th>
                <th style="padding:15px;">Name</th>
                <th style="padding:15px;">Category</th>
                <th style="padding:15px;">Price</th>
                <th style="padding:15px;">Stock</th>
                <th style="padding:15px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr style="border-bottom:1px solid #f5f5f5;">
                <td style="padding:15px;"><img src="{{ asset('img/product/'.$product->image) }}" style="width:40px; height:40px; object-fit:contain;" onerror="this.src='{{ asset('img/product/default.jpg') }}'"></td>
                <td style="padding:15px; font-weight:600;">{{ $product->name }}</td>
                <td style="padding:15px; color:#666;">{{ $product->category_name }}</td>
                <td style="padding:15px; font-weight:bold;">{{ number_format($product->price, 0, ',', '.') }}₫</td>
                <td style="padding:15px;">{{ $product->stock_quantity }}</td>
                <td style="padding:15px;">
                    <a href="{{ route('admin.products.edit', $product->id) }}" style="border:none; background:none; color:#3498db; cursor:pointer; margin-right:15px; text-decoration:none;"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="border:none; background:none; color:#e74c3c; cursor:pointer;"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="margin-top:20px;">
        {{ $products->links() }}
    </div>
</div>
@endsection
