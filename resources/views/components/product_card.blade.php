<div class="card">
    <img src="{{ asset('img/product/' . $product->image) }}" 
         alt="{{ $product->name }}"
         onerror="this.src='{{ asset('img/product/default.jpg') }}'">
    <h3>{{ Str::limit($product->name, 25) }}</h3>
    @if (($product->discount ?? 0) > 0)
        <p style="margin-bottom: 10px;">
            <span style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-right: 5px;">${{ number_format($product->price / 25000, 2) }}</span>
            <span style="color: #e63946; font-weight: bold;">${{ number_format(($product->price * (1 - $product->discount/100)) / 25000, 2) }}</span>
        </p>
    @else
        <p style="color: #e63946; font-weight: bold; margin-bottom: 10px;">${{ number_format($product->price / 25000, 2) }}</p>
    @endif
    <a href="{{ url('products/' . $product->id) }}"><button>Buy Now</button></a>
</div>
