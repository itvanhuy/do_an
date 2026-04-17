@extends('layouts.admin')
@section('title', 'Add New Product')
@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display:block; margin-bottom:5px;">Product Name</label>
                <input type="text" name="name" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Price (VNĐ)</label>
                <input type="number" name="price" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Category</label>
                <select name="category_id" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Brand</label>
                <select name="brand" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->name }}">{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Stock Quantity</label>
                <input type="number" name="stock_quantity" value="0" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Discount (%)</label>
                <input type="number" name="discount" value="0" min="0" max="100" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px;">Product Image</label>
            <input type="file" name="image" style="width:100%;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px;">Description</label>
            <textarea name="description" rows="5" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;"></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.products') }}" style="background:#eee; color:#333; text-decoration:none; padding:12px 25px; border-radius:5px;">Cancel</a>
            <button type="submit" style="background:var(--admin-accent); color:white; border:none; padding:12px 30px; border-radius:5px; cursor:pointer; font-weight:bold;">Create Product</button>
        </div>
    </form>
</div>
@endsection
