<?php

namespace App\Services;

use App\Contracts\CartServiceInterface;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartService implements CartServiceInterface
{
    public function getCount(int $userId): int
    {
        return (int) DB::table('cart')->where('user_id', $userId)->sum('quantity');
    }

    public function getItems(int $userId): object
    {
        $items = DB::table('cart')
            ->join('products', 'cart.product_id', '=', 'products.id')
            ->where('cart.user_id', $userId)
            ->select('cart.*', 'products.discount', 'products.stock_quantity',
                     'products.price', 'products.name as product_name', 'products.image as product_image')
            ->get();

        foreach ($items as $item) {
            $item->current_price = $item->discount > 0
                ? $item->price * (1 - $item->discount / 100)
                : $item->price;
        }

        return $items;
    }

    public function add(int $userId, int $productId, int $quantity): array
    {
        $product = Product::where('id', $productId)->where('is_active', 1)->first();

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        if ($product->stock_quantity < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }

        $existing = DB::table('cart')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + $quantity;
            if ($product->stock_quantity < $newQty) {
                return ['success' => false, 'message' => 'Insufficient stock to update'];
            }
            DB::table('cart')->where('id', $existing->id)->update([
                'quantity'   => $newQty,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('cart')->insert([
                'user_id'    => $userId,
                'product_id' => $productId,
                'quantity'   => $quantity,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return [
            'success'      => true,
            'message'      => 'Added to cart',
            'cart_count'   => $this->getCount($userId),
            'product_name' => $product->name,
        ];
    }

    public function update(int $userId, int $cartId, int $quantity): array
    {
        $item = DB::table('cart')->where('id', $cartId)->where('user_id', $userId)->first();
        if (!$item) return ['success' => false, 'message' => 'Item not found'];

        $product = Product::find($item->product_id);
        if (!$product || $product->stock_quantity < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }

        if ($quantity > 0) {
            DB::table('cart')->where('id', $cartId)->update(['quantity' => $quantity, 'updated_at' => now()]);
        } else {
            DB::table('cart')->where('id', $cartId)->delete();
        }

        return ['success' => true, 'cart_count' => $this->getCount($userId)];
    }

    public function remove(int $userId, int $cartId): bool
    {
        return DB::table('cart')->where('id', $cartId)->where('user_id', $userId)->delete() > 0;
    }
}
