@extends('layouts.app')

@section('title', 'Shopping Cart - '.env('APP_NAME', 'TechShop'))

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <style>
        .discount-input-group { display: flex; gap: 10px; margin-top: 10px; }
        .discount-input-group input { flex: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
        .discount-input-group button { padding: 10px 20px; background: var(--accent-color); color: white; border: none; border-radius: 5px; cursor: pointer; }
        .cart-item { margin-bottom: 20px; display: flex; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .cart-item img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; margin-right: 20px; }
        .cart-item .item-info { flex: 1; }
        .quantity-remove-wrapper { margin-top: 10px; display: flex; align-items: center; gap: 15px; }
        .remove-btn { background: none; border: none; color: #ff4757; cursor: pointer; font-size: 1.2rem; }
    </style>
@endsection

@section('content')
<main class="cart-container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
  <h1 class="page-title" aria-label="Shopping Cart" style="margin-bottom: 30px;">🛒 Your Shopping Cart</h1>

  <div class="cart-content" style="display: flex; gap: 40px;">
    @if ($cartItems->isEmpty())
        <div class="empty-cart" style="text-align: center; width: 100%; padding: 50px 0;">
            <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
            <h2>Your cart is currently empty</h2>
            <p style="color: #666; margin-bottom: 20px;">Add some products to your cart to start shopping now!</p>
            <a href="{{ url('shop') }}" class="btn" style="background: var(--accent-color); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Continue Shopping</a>
        </div>
    @else
    <section class="cart-items" aria-label="Items in your cart" style="flex: 2;">
      @foreach ($cartItems as $item)
      <div class="cart-item" data-cart-id="{{ $item->id }}">
        <img src="{{ asset('img/product/' . $item->product_image) }}" 
             alt="{{ $item->product_name }}" onerror="this.src='{{ asset('img/product/default.jpg') }}'">
        <div class="item-info">
          <h2>{{ $item->product_name }}</h2>
          <p class="price" style="font-weight: bold; color: var(--accent-color);">${{ number_format($item->current_price / 25000, 2) }}</p>
          <div class="quantity-remove-wrapper">
            <span class="quantity-label">Quantity:</span>
            <input 
              type="number" 
              value="{{ $item->quantity }}" 
              min="1" 
              max="{{ $item->stock_quantity ?? 10 }}"
              class="quantity-input"
              style="width: 60px; padding: 5px; border-radius: 4px; border: 1px solid #ddd;"
              onchange="updateQuantity({{ $item->id }}, this.value)"
            />
            <button class="remove-btn" onclick="removeItem({{ $item->id }})">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
      @endforeach
    </section>

    <aside class="cart-summary" aria-label="Order summary" style="flex: 1; background: #f9f9f9; padding: 25px; border-radius: 10px; align-self: flex-start;">
      <h2 style="margin-top:0;">Order Summary</h2>
      
      <div class="summary-row" style="display: flex; justify-content: space-between; margin: 15px 0;">
        <span>Subtotal:</span>
        <span id="subtotal" style="font-weight: bold;">${{ number_format($subtotal / 25000, 2) }}</span>
      </div>
      
      <div class="summary-row" style="display: flex; justify-content: space-between; margin: 15px 0;">
        <span>Shipping:</span>
        <span class="free-ship" style="color: #4CAF50;">Free 🚚</span>
      </div>

      <hr style="border:0; border-top: 1px solid #ddd; margin: 20px 0;">

      <div class="summary-total" style="display: flex; justify-content: space-between; font-size: 1.2rem; margin-bottom: 25px;">
        <span style="font-weight: bold;">Total:</span>
        <span id="total" style="font-weight: bold; color: var(--accent-color);">${{ number_format($subtotal / 25000, 2) }}</span>
      </div>

      <a href="{{ url('checkout') }}" style="display: block; width: 100%; text-decoration: none;">
          <button class="checkout-btn" style="width: 100%; padding: 15px; background: #4CAF50; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 1.1rem; margin-bottom: 15px;">🧾 Proceed to Checkout</button>
      </a>
      
      <a href="{{ url('shop') }}" class="continue-shopping" style="display: block; text-align: center; color: #666; text-decoration: none; margin-bottom: 25px;">← Continue Shopping</a>

      <div class="support-info" style="font-size: 0.9rem; color: #666; background: white; padding: 15px; border-radius: 5px; border: 1px solid #eee;">
        <p style="margin: 0;">
          📞 Need help? Call <a href="tel:0896492400" style="color: var(--accent-color);">0896 492 400</a> or email
          <a href="mailto:levahuy06042003@gmail.com" style="color: var(--accent-color);">Customer Support</a>
        </p>
      </div>
    </aside>
  </div>
    @endif
</main>
@endsection

@section('scripts')
<script>
    function updateQuantity(cartId, newQuantity) {
        if (newQuantity < 1) return;
        fetch('{{ url("cart/update") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ cart_id: cartId, quantity: newQuantity })
        }).then(r => r.json()).then(res => {
            if(res.success) location.reload();
            else alert(res.message || 'System error');
        });
    }

    function removeItem(cartId) {
        if(confirm('Are you sure you want to remove this item?')) {
            fetch('{{ url("cart/remove") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ cart_id: cartId })
            }).then(r => r.json()).then(res => {
                if(res.success) location.reload();
                else alert(res.message || 'Error');
            });
        }
    }
</script>
@endsection
