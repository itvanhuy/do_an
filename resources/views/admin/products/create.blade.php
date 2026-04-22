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

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display:block; margin-bottom:5px; font-weight:600;">Main Image</label>
                <input type="file" name="image" id="mainImageInput" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" onchange="previewMainImage(this)">
                <div id="mainImagePreview" style="margin-top:10px; display:none;">
                    <img src="" style="max-width:150px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                </div>
            </div>
            <div>
                <label style="display:block; margin-bottom:5px; font-weight:600;">Gallery Images</label>
                <input type="file" name="images[]" multiple style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px;" onchange="previewGalleryImages(this)">
                <div id="galleryPreview" style="margin-top:10px; display:flex; gap:10px; flex-wrap:wrap;"></div>
            </div>
        </div>

        <script>
            function previewMainImage(input) {
                const preview = document.querySelector('#mainImagePreview');
                const img = preview.querySelector('img');
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function previewGalleryImages(input) {
                const preview = document.querySelector('#galleryPreview');
                preview.innerHTML = '';
                if (input.files) {
                    Array.from(input.files).forEach(file => {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '80px';
                            img.style.maxHeight = '80px';
                            img.style.borderRadius = '5px';
                            img.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
                            preview.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    });
                }
            }
        </script>

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
