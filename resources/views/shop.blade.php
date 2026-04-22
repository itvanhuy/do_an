@extends('layouts.app')

@section('title', 'Shop - TechShop')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
@endsection

@section('content')
<div class="container" style="display: flex; gap: 30px; margin-top: 30px;">
    <!-- Sidebar -->
    <div class="sidebar">
        <form action="{{ url('search') }}" method="GET" class="search-box">
            <input type="text" name="q" placeholder="Search products...">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
        
        <h4>Categories</h4>
        <ul class="category-list">
            @foreach ($categories as $cat)
            <li class="category-item">
                <a href="{{ url('products?category_id=' . $cat->id) }}" style="color: inherit; text-decoration: none; flex: 1;">
                    {{ $cat->name }}
                </a>
            </li>
            @endforeach
        </ul>

        <h4>Filter by Brand</h4>
        @forelse ($brands as $brand)
            <label>
                <input type="checkbox" onclick="window.location.href='{{ url('products?brand=' . urlencode($brand->name)) }}'"> 
                {{ $brand->name }}
            </label>
        @empty
            <p style="color: #666; font-size: 0.9em; margin-bottom: 20px;">No brands available.</p>
        @endforelse
    </div>

    <!-- Content -->
    <div class="content">
        @if(isset($isSearch) && $isSearch)
            <section class="search-results-section" style="margin-bottom: 50px;">
                <h2 style="margin-bottom: 10px;">Search Results for "{{ $searchQuery }}"</h2>
                <p style="color: #666; margin-bottom: 30px;">Found {{ $featuredProducts->count() }} product(s)</p>
                
                @if($featuredProducts->isEmpty())
                    <div class="no-results" style="text-align: center; padding: 50px 0; background: #f9f9f9; border-radius: 10px;">
                        <i class="fas fa-search" style="font-size: 3rem; color: #ccc; margin-bottom: 20px;"></i>
                        <h3>No products found</h3>
                        <p>We couldn't find any products matching your search terms.</p>
                        <a href="{{ url('shop') }}" class="btn-see-more" style="display:inline-block; margin-top:20px; text-decoration:none;">Back to Shop</a>
                    </div>
                @else
                    <div class="product-grid" style="display: flex; flex-wrap: wrap; gap: 20px;">
                        @foreach ($featuredProducts as $p)
                        <div class="card" style="width: calc(33.333% - 14px); min-width: 250px;">
                            <a href="{{ url('products/' . $p->id) }}">
                                <img src="{{ asset('img/product/' . $p->image) }}" alt="{{ $p->name }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                            </a>
                            <div class="card-content">
                                <a href="{{ url('products/' . $p->id) }}" style="text-decoration:none;">
                                    <h4>{{ Str::limit($p->name, 40) }}</h4>
                                </a>
                                @if (($p->discount ?? 0) > 0)
                                    <p class="price">
                                        <span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;">${{ number_format($p->price / 25000, 2) }}</span>
                                        <span style="color: #e60023; font-weight: bold;">${{ number_format(($p->price * (1 - $p->discount/100)) / 25000, 2) }}</span>
                                    </p>
                                @else
                                    <p class="price">${{ number_format($p->price / 25000, 2) }}</p>
                                @endif
                                <button class="buy-button" onclick="addToCart({{ $p->id }})">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @else
            <!-- Featured Products Slider -->
            <section class="featured-section">
                <h2>Featured Products</h2>
                <div class="carousel-container">
                    <div class="carousel-track">
                        @php $chunks = $featuredProducts->chunk(3); @endphp
                        @foreach ($chunks as $index => $chunk)
                            <div class="carousel-slide">
                                @foreach ($chunk as $p)
                                <div class="card">
                                    <span class="badge" style="position:absolute; top:10px; left:10px; background:var(--accent-color); color:white; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:bold;">HOT</span>
                                    <a href="{{ url('products/' . $p->id) }}">
                                        <img src="{{ asset('img/product/' . $p->image) }}" alt="{{ $p->name }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                                    </a>
                                    <div class="card-content">
                                        <a href="{{ url('products/' . $p->id) }}" style="text-decoration:none;">
                                            <h4>{{ Str::limit($p->name, 40) }}</h4>
                                        </a>
                                        @if (($p->discount ?? 0) > 0)
                                            <p class="price">
                                                <span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;">${{ number_format($p->price / 25000, 2) }}</span>
                                                <span style="color: #e60023; font-weight: bold;">${{ number_format(($p->price * (1 - $p->discount/100)) / 25000, 2) }}</span>
                                            </p>
                                        @else
                                            <p class="price">${{ number_format($p->price / 25000, 2) }}</p>
                                        @endif
                                        <button class="buy-button" onclick="addToCart({{ $p->id }})">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="carousel-dots">
                    @for($i = 0; $i < $chunks->count(); $i++)
                        <span class="dot {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"></span>
                    @endfor
                </div>
            </section>

            <!-- Flash Sale -->
            <section class="fs-section">
                <div class="fs-header">
                    <h2 class="fs-title"><i class="fas fa-bolt"></i> Flash Sale</h2>
                    <!-- ... rest of flash sale ... -->
                    <div class="fs-countdown">
                        <div class="fs-countdown__item">
                            <div id="hours" class="fs-countdown__value">00</div>
                            <div class="fs-countdown__label">Hours</div>
                        </div>
                        <div class="fs-countdown__item">
                            <div id="minutes" class="fs-countdown__value">00</div>
                            <div class="fs-countdown__label">Minutes</div>
                        </div>
                        <div class="fs-countdown__item">
                            <div id="seconds" class="fs-countdown__value">00</div>
                            <div class="fs-countdown__label">Seconds</div>
                        </div>
                    </div>
                </div>
                <div class="fs-products">
                    @foreach ($fsProducts as $p)
                    <div class="fs-card" onclick="window.location.href='{{ url('products/' . $p->id) }}'">
                        <img src="{{ asset('img/product/' . $p->image) }}" alt="{{ $p->name }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                        <div class="fs-card__content">
                            <h4>{{ Str::limit($p->name, 30) }}</h4>
                            <p class="fs-price">
                                <span style="text-decoration: line-through; color: #999; font-size: 0.8em; display: block;">${{ number_format($p->price / 25000, 2) }}</span>
                                <span style="color: #e60023; font-weight: bold;">${{ number_format(($p->price * (1 - ($p->discount ?? 0)/100)) / 25000, 2) }}</span>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>

            <!-- Recommendations -->
            <div class="recommendation-section" style="margin-top: 50px;">
                <div class="header-bar" style="margin-bottom:20px;">
                    <h3>Today's Recommendations</h3>
                </div>
                <div class="product-list">
                    @foreach ($recProducts as $p)
                    <div class="item-card" onclick="window.location.href='{{ url('products/' . $p->id) }}'">
                        <img src="{{ asset('img/product/' . $p->image) }}" alt="{{ $p->name }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                        <p class="item-name">{{ Str::limit($p->name, 40) }}</p>
                        @if (($p->discount ?? 0) > 0)
                            <p class="item-price">
                                <span style="text-decoration: line-through; color: #999; font-size: 0.8em; margin-right: 5px;">${{ number_format($p->price / 25000, 2) }}</span>
                                <span style="color: #e63946; font-weight: bold;">${{ number_format(($p->price * (1 - $p->discount/100)) / 25000, 2) }}</span>
                            </p>
                        @else
                            <p class="item-price">${{ number_format($p->price / 25000, 2) }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/shop.js') }}"></script>
<script>
    // Toast Notification Function
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${type === 'success' ? '#4CAF50' : '#f44336'};
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease-out forwards;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        `;
        toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-in forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Add to Cart Function (AJAX - expects standard JSON response)
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
        .then(data => showToast(data.message || 'Added to cart!', data.success ? 'success' : 'error'))
        .catch(() => showToast('Log in to continue!', 'error'));
    }
</script>
@endsection
