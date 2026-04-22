@extends('layouts.admin')
@section('title', 'Edit Product: ' . $product->name)
@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display:block; margin-bottom:5px;">Product Name</label>
                <input type="text" name="name" value="{{ $product->name }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Price (VNĐ)</label>
                <input type="number" name="price" value="{{ $product->price }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Category</label>
                <select name="category_id" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Brand</label>
                <select name="brand" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
                    <option value="">Select Brand</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand->name }}" {{ $product->brand == $brand->name ? 'selected' : '' }}>{{ $brand->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Stock Quantity</label>
                <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
            </div>
            <div>
                <label style="display:block; margin-bottom:5px;">Discount (%)</label>
                <input type="number" name="discount" value="{{ $product->discount }}" min="0" max="100" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 20px;">
            <div>
                <label style="display:block; margin-bottom:10px; font-weight:600;">Main Image</label>
                <div style="display:flex; align-items:center; gap:15px;">
                    <img id="mainPreview" src="{{ asset('img/product/'.$product->image) }}" style="width:120px; height:120px; object-fit:contain; border-radius:10px; background:#f9f9f9; border:1px solid #eee;" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                    <div style="flex-grow:1;">
                        <input type="file" name="image" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:5px;" onchange="previewMain(this)">
                        <small style="color:#888; display:block; margin-top:5px;">Change product cover image</small>
                    </div>
                </div>
            </div>
            
            <div>
                <label style="display:block; margin-bottom:10px; font-weight:600;">Add to Gallery</label>
                <input type="file" name="images[]" multiple style="width:100%; padding:8px; border:1px solid #ddd; border-radius:5px;" onchange="previewGallery(this)">
                <div id="newGalleryPreview" style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;"></div>
            </div>
        </div>

        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom:10px; font-weight:600;">Existing Gallery</label>
            <div style="display:flex; gap:15px; flex-wrap:wrap; background:#f8f9fa; padding:15px; border-radius:10px;">
                @forelse($gallery as $img)
                    <div style="position:relative; width:80px; height:80px;">
                        <img src="{{ asset('img/product/'.$img->image) }}" style="width:100%; height:100%; object-fit:cover; border-radius:5px; border:1px solid #ddd;">
                    </div>
                @empty
                    <p style="color:#888; margin:0; font-size:0.9rem; font-style:italic;">No extra images in gallery.</p>
                @endforelse
            </div>
        </div>

        <script>
            function previewMain(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.querySelector('#mainPreview').src = e.target.result;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function previewGallery(input) {
                const preview = document.querySelector('#newGalleryPreview');
                preview.innerHTML = '';
                if (input.files) {
                    Array.from(input.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.width = '60px';
                            img.style.height = '60px';
                            img.style.objectFit = 'cover';
                            img.style.borderRadius = '5px';
                            preview.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    });
                }
            }
        </script>

        <div style="margin-bottom: 20px;">
            <label style="display:block; margin-bottom:5px;">Description</label>
            <textarea name="description" rows="5" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;">{{ $product->description }}</textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px;">
            <a href="{{ route('admin.products') }}" style="background:#eee; color:#333; text-decoration:none; padding:12px 25px; border-radius:5px;">Cancel</a>
            <button type="submit" style="background:var(--admin-accent); color:white; border:none; padding:12px 30px; border-radius:5px; cursor:pointer; font-weight:bold;">Update Product</button>
        </div>
    </form>
</div>
@endsection
