<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class CartController extends Controller
{
    // Cập nhật số lượng giỏ hàng hiển thị ở Navbar
    public static function getCartCount()
    {
        if (!Auth::check()) return 0;
        return DB::table('cart')->where('user_id', Auth::id())->sum('quantity');
    }

    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng',
                'cart_count' => 0
            ]);
        }

        $productId = (int) $request->product_id;
        $quantity = (int) $request->input('quantity', 1);
        if ($quantity < 1) $quantity = 1;

        $userId = Auth::id();

        $product = Product::where('id', $productId)->where('is_active', 1)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại'
            ]);
        }

        if ($product->stock_quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng tồn kho không đủ'
            ]);
        }

        $existingItem = DB::table('cart')->where('user_id', $userId)->where('product_id', $productId)->first();

        $finalPrice = $product->price;
        if ($product->discount > 0) {
            $finalPrice = $product->price * (1 - $product->discount / 100);
        }

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if ($product->stock_quantity < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng tồn kho không đủ để cập nhật'
                ]);
            }
            DB::table('cart')->where('id', $existingItem->id)->update([
                'quantity' => $newQuantity,
                'updated_at' => now()
            ]);
        } else {
            DB::table('cart')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $cartCount = self::getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm sản phẩm vào giỏ hàng',
            'cart_count' => $cartCount,
            'product_name' => $product->name
        ]);
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors(['email' => 'Vui lòng đăng nhập để xem giỏ hàng']);
        }

        $cartItems = DB::table('cart')
            ->join('products', 'cart.product_id', '=', 'products.id')
            ->where('cart.user_id', Auth::id())
            ->select('cart.*', 'products.discount', 'products.stock_quantity', 'products.price', 'products.name as product_name', 'products.image as product_image')
            ->get();

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $itemPrice = $item->price;
            // recalculate price based on current latest discount
            if ($item->discount > 0) {
                $itemPrice = $item->price * (1 - $item->discount/100);
            }
            $subtotal += $itemPrice * $item->quantity;
            $item->current_price = $itemPrice;
        }

        return view('cart', compact('cartItems', 'subtotal'));
    }

    public function update(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false]);
        
        $cartId = $request->input('cart_id');
        $quantity = (int) $request->input('quantity', 1);
        
        $item = DB::table('cart')->where('id', $cartId)->where('user_id', Auth::id())->first();
        if (!$item) return response()->json(['success' => false]);

        $product = Product::find($item->product_id);
        if (!$product || $product->stock_quantity < $quantity) {
             return response()->json([
                'success' => false,
                'message' => 'Tồn kho không đủ để đáp ứng'
            ]);
        }

        if ($quantity > 0) {
            DB::table('cart')->where('id', $cartId)->update(['quantity' => $quantity, 'updated_at' => now()]);
        } else {
            DB::table('cart')->where('id', $cartId)->delete();
        }

        return response()->json([
            'success' => true,
            'cart_count' => self::getCartCount()
        ]);
    }

    public function remove(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false]);
        
        $cartId = $request->input('cart_id');
        DB::table('cart')->where('id', $cartId)->where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'cart_count' => self::getCartCount()
        ]);
    }
}
