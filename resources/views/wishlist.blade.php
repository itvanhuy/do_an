@extends('layouts.app')

@section('title', 'My Wishlist - '.env('APP_NAME', 'TechShop'))

@section('styles')
    <style>
        .wishlist-container { max-width: 1200px; margin: 40px auto; padding: 0 20px; min-height: 60vh; }
        .wishlist-header { margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 15px; display: flex; align-items: center; gap: 15px; }
        .wishlist-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; }
        .empty-wishlist { text-align: center; padding: 80px 0; color: #666; }
        .empty-wishlist i { font-size: 5rem; color: #ddd; margin-bottom: 20px; }
        .product-card { background: white; border: 1px solid #eee; border-radius: 12px; overflow: hidden; transition: 0.3s; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .product-card img { width: 100%; height: 200px; object-fit: contain; background: #f9f9f9; padding: 15px; }
        .product-info { padding: 20px; text-align: center; }
        .product-info h3 { margin: 0 0 10px; font-size: 1.1rem; }
        .product-info .price { color: var(--accent-color); font-weight: bold; font-size: 1.2rem; }
        .card-actions { padding: 0 20px 20px; display: flex; gap: 10px; }
        .btn-cart { flex: 2; background: var(--accent-color); color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-remove { flex: 1; background: #fff; color: #ff4757; border: 1px solid #ff4757; padding: 10px; border-radius: 6px; cursor: pointer; }
        .btn-remove:hover { background: #ff4757; color: white; }
    </style>
@endsection

@section('content')
<main class="wishlist-container">
    <div class="wishlist-header">
        <h1>My Wishlist</h1>
        <i class="fas fa-heart" style="color: #ff4757; font-size: 1.5rem;"></i>
    </div>

    @if($wishlistItems->isEmpty())
        <div class="empty-wishlist">
            <i class="far fa-heart"></i>
            <h2>Your wishlist is empty</h2>
            <p>Save items you love to buy later!</p>
            <a href="{{ url('shop') }}" class="btn" style="background: var(--accent-color); color: white; display: inline-block; margin-top: 25px; padding: 12px 30px; border-radius: 30px; text-decoration: none;">Explore Shop</a>
        </div>
    @else
        <div class="wishlist-grid">
            @foreach($wishlistItems as $product)
                <div class="product-card" id="wishlist-item-{{ $product->id }}">
                    <a href="{{ url('products/' . $product->id) }}" style="text-decoration: none; color: inherit;">
                        <img src="{{ asset('img/product/' . $product->image) }}" alt="{{ $product->name }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                        <div class="product-info">
                            <h3>{{ Str::limit($product->name, 40) }}</h3>
                            <p class="price">${{ number_format($product->price / 25000, 2) }}</p>
                        </div>
                    </a>
                    <div class="card-actions">
                        <button class="btn-cart" onclick="addToCart({{ $product->id }})">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <button class="btn-remove" onclick="toggleWishlist({{ $product->id }})" title="Remove from Wishlist">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</main>
@endsection

@section('scripts')
<script>
    function toggleWishlist(productId) {
        if(!confirm('Are you sure you want to remove this item?')) return;
        
        fetch('{{ url("wishlist/toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.status === 'removed') {
                const item = document.getElementById('wishlist-item-' + productId);
                if (item) item.remove();
                if (document.querySelectorAll('.product-card').length === 0) location.reload();
            }
        });
    }

    function addToCart(productId) {
        fetch('{{ url("cart/add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ product_id: productId, quantity: 1 })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) alert('Product added to cart!');
            else alert(data.message || 'Error occurred.');
        });
    }
</script>
@endsection
