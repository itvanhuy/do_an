@extends('layouts.app')

@section('title', $product->name . ' - TechShop')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/detailproduct.css') }}">
    <style>
        .product-clickable {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .product-clickable:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .btn-wishlist {
            background: none;
            border: 1px solid #ddd;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 1.2rem;
            color: #ccc;
            transition: all 0.3s;
            margin-left: 10px;
        }
        .btn-wishlist:hover, .btn-wishlist.active {
            color: #ff4757;
            border-color: #ff4757;
            background: rgba(255, 71, 87, 0.1);
        }
    </style>
@endsection

@section('content')
    <nav class="breadcrumb">
        <ul>
            <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="{{ url('shop') }}">Shop</a></li>
            <li><a href="{{ route('category', ['slug' => $product->category_slug]) }}">{{ $product->category_name }}</a></li>
            <li class="current">{{ $product->name }}</li>
        </ul>
    </nav>

    <main class="product-container">
        <!-- Product Images -->
        <section class="product-image">
            <img id="mainImage" class="main-image" src="{{ asset('img/product/' . $images[0]) }}" 
                 alt="{{ $product->name }}" 
                 onerror="this.src='{{ asset('img/product/default.jpg') }}'" />
            <div class="thumbnail-gallery">
                @foreach ($images as $index => $image)
                <img src="{{ asset('img/product/' . $image) }}" 
                     alt="Thumbnail {{ $index + 1 }}" 
                     onclick="changeImage(this)"
                     onerror="this.src='{{ asset('img/product/default.jpg') }}'" />
                @endforeach
            </div>
        </section>

        <!-- Product Information -->
        <section class="product-info">
            <h1 class="product-title">{{ $product->name }}</h1>

            <div class="product-rating" aria-label="Product rating">
                <span class="stars" style="color: #f1c40f;">★★★★☆</span> 
                <span class="reviews-count">({{ count($reviews) }} reviews)</span>
            </div>

            <p class="product-price">
                @if ($product->discount > 0)
                <span style="text-decoration: line-through; color: #999; font-size: 16px; display: block;">
                    ${{ number_format($product->price / 25000, 2) }}
                </span>
                <span style="color: #e60023; font-weight: bold; font-size: 24px;">
                    ${{ number_format(($product->price * (1 - $product->discount/100)) / 25000, 2) }}
                </span>
                @else
                <span style="color: #333; font-weight: bold; font-size: 24px;">
                    ${{ number_format($product->price / 25000, 2) }}
                </span>
                @endif
            </p>

            <p class="product-description">
                {!! nl2br(e($product->description ?? 'No description available.')) !!}
            </p>

            <div class="product-actions">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock_quantity ?? 10 }}" />
                <button class="btn-add-cart" onclick="addToCart({{ $product->id }})">Add to Cart</button>
                <button class="btn-buy-now" onclick="buyNow({{ $product->id }})">Buy Now</button>
                <button class="btn-wishlist {{ $isInWishlist ? 'active' : '' }}" onclick="toggleWishlist({{ $product->id }})" title="{{ $isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                    <i class="{{ $isInWishlist ? 'fas' : 'far' }} fa-heart"></i>
                </button>
            </div>

            <!-- Technical Specifications -->
            <div class="tech-specs">
                <h2>Technical Specifications</h2>
                <table>
                    <tbody>
                        @php
                            $specs = !empty($product->specifications) ? json_decode($product->specifications, true) : null;
                        @endphp
                        
                        @if ($specs && is_array($specs))
                            @foreach ($specs as $key => $value)
                            <tr>
                                <th>{{ $key }}</th>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <th>Brand</th>
                                <td>{{ $product->brand ?? 'Unknown' }}</td>
                            </tr>
                            <tr>
                                <th>Model</th>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ $product->category_name }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Product Promotions -->
    <section class="product-promotions">
        <h2>Promotions when buying this product</h2>
        <ul class="promotion-list">
            <li>🚚 Free nationwide shipping</li>
            <li>🛡️ Official 12-month warranty</li>
            <li>💳 Cash on delivery available</li>
            <li>🎁 Comes with a premium carrying case</li>
            <li>🔥 5% off on your first order</li>
        </ul>
    </section>

    <!-- User Reviews -->
    <section class="review-section">
        <h2>⭐ Product Reviews</h2>

        <div class="review-list">
            @if ($reviews->isEmpty())
                <p style="color: #666; font-style: italic;">No reviews yet. Be the first to review this product!</p>
            @else
                @foreach ($reviews as $review)
                <div class="review-item">
                    <div class="review-header">
                        <span class="reviewer-name">{{ $review->full_name ?: $review->username }}</span>
                        <div class="review-stars" style="color: #f1c40f;">
                            {!! str_repeat('★', (int)$review->rating) . str_repeat('☆', 5 - (int)$review->rating) !!}
                        </div>
                        <span class="review-date">{{ date('M d, Y', strtotime($review->created_at)) }}</span>
                    </div>
                    <p class="review-text">{!! nl2br(e($review->comment)) !!}</p>
                </div>
                @endforeach
            @endif
        </div>

        @auth
        <!-- New review form -->
        <form class="review-form" id="reviewForm">
            @csrf
            <div class="rating-stars">
                <input type="radio" id="star5" name="rating" value="5" />
                <label for="star5" title="Excellent">&#9733;</label>

                <input type="radio" id="star4" name="rating" value="4" />
                <label for="star4" title="Good">&#9733;</label>

                <input type="radio" id="star3" name="rating" value="3" />
                <label for="star3" title="Average">&#9733;</label>

                <input type="radio" id="star2" name="rating" value="2" />
                <label for="star2" title="Poor">&#9733;</label>

                <input type="radio" id="star1" name="rating" value="1" />
                <label for="star1" title="Bad">&#9733;</label>
            </div>

            <textarea name="review" placeholder="Write your review..." rows="4" required></textarea>
            <button type="submit" class="btn-submit">Submit Review</button>
        </form>
        @else
        <p style="margin-top: 20px;"><a href="{{ route('login') }}">Log in</a> to submit a review.</p>
        @endauth
    </section>

    <!-- Related Products -->
    @if ($relatedProducts->isNotEmpty())
    <section class="related-products">
        <h2 class="section-title">🛒 Related Products</h2>
        <div class="product-grid">
            @foreach ($relatedProducts as $related)
            <div class="product-card product-clickable" onclick="window.location.href='{{ url('products/' . $related->id) }}'" style="position: relative;">
                @if (($related->discount ?? 0) > 0)
                    <div style="position: absolute; top: 10px; left: 10px; background: #e60023; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; z-index: 2;">-{{ $related->discount }}%</div>
                @endif
                
                <img src="{{ asset('img/product/' . $related->image) }}" 
                     alt="{{ $related->name }}"
                     onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                <div class="product-info">
                    <h3>{{ Str::limit($related->name, 30) }}</h3>
                    <div style="margin: 10px 0;">
                        @if (($related->discount ?? 0) > 0)
                            <span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;">
                                ${{ number_format($related->price / 25000, 2) }}
                            </span>
                            <span style="color: #e60023; font-weight: bold;">
                                ${{ number_format(($related->price * (1 - $related->discount/100)) / 25000, 2) }}
                            </span>
                        @else
                            <span style="color: #333; font-weight: bold;">${{ number_format($related->price / 25000, 2) }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="see-more-container">
            <a href="{{ route('category', ['slug' => $product->category_slug]) }}" class="btn-see-more">🔍 See More Products</a>
        </div>
    </section>
    @endif

    <!-- Recently Viewed Products -->
    @if (!empty($recentProducts))
    <section class="related-products" style="background-color: #f9f9f9; margin-top: 0; padding-top: 40px;">
        <h2 class="section-title">🕒 Recently Viewed Products</h2>
        <div class="product-grid">
            @foreach ($recentProducts as $recent)
            <div class="product-card product-clickable" onclick="window.location.href='{{ url('products/' . $recent->id) }}'" style="position: relative;">
                @if (($recent->discount ?? 0) > 0)
                    <div style="position: absolute; top: 10px; left: 10px; background: #e60023; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; z-index: 2;">-{{ $recent->discount }}%</div>
                @endif
                
                <img src="{{ asset('img/product/' . $recent->image) }}" 
                     alt="{{ $recent->name }}"
                     onerror="this.src='{{ asset('img/product/default.jpg') }}'">
                <div class="product-info">
                    <h3>{{ Str::limit($recent->name, 30) }}</h3>
                    <div style="margin: 10px 0;">
                        @if (($recent->discount ?? 0) > 0)
                            <span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;">
                                ${{ number_format($recent->price / 25000, 2) }}
                            </span>
                            <span style="color: #e60023; font-weight: bold;">
                                ${{ number_format(($recent->price * (1 - $recent->discount/100)) / 25000, 2) }}
                            </span>
                        @else
                            <span style="color: #333; font-weight: bold;">${{ number_format($recent->price / 25000, 2) }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

@endsection

@section('scripts')
    <script src="{{ asset('js/detailproduct.js') }}"></script>
    <script>
        function changeImage(img) {
            document.getElementById('mainImage').src = img.src;
        }

        function addToCart(productId, qty = null) {
            let quantity = qty;
            if (quantity === null) {
                quantity = document.getElementById('quantity').value;
            }
            
            @guest
                alert('Please log in to add products to your cart.');
                window.location.href = '{{ route("login") }}';
                return;
            @endguest
            
            fetch('{{ url("cart/add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    document.querySelectorAll('.cart-count, .mobile-cart-count').forEach(el => {
                        el.textContent = data.cart_count;
                    });
                } else {
                    alert(data.message || 'Could not add to cart.');
                }
            })
            .catch(error => alert('An error occurred. Please try again.'));
        }

        function buyNow(productId) {
            const quantity = document.getElementById('quantity').value;
            @guest
                alert('Please log in to buy items.');
                window.location.href = '{{ route("login") }}';
                return;
            @endguest
            
            fetch('{{ url("cart/add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ url("cart") }}';
                } else {
                    alert(data.message || 'Could not process order.');
                }
            });
        }

        function toggleWishlist(productId) {
            @guest
                alert('Please log in to use your wishlist.');
                window.location.href = '{{ route("login") }}';
                return;
            @endguest

            fetch('{{ url("wishlist/toggle") }}', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const btn = document.querySelector('.btn-wishlist');
                    const icon = btn.querySelector('i');
                    if (data.status === 'added') {
                        btn.classList.add('active');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        btn.title = 'Remove from Wishlist';
                    } else {
                        btn.classList.remove('active');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.title = 'Add to Wishlist';
                    }
                }
            });
        }

        const reviewForm = document.getElementById('reviewForm');
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const productId = {{ $product->id }};
                
                fetch(`{{ url('products') }}/${productId}/review`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Review submitted successfully!');
                        location.reload();
                    } else {
                        alert(data.message || 'Error submitting review.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        }
    </script>
@endsection
