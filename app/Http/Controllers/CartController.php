<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Contracts\CartServiceInterface;

class CartController extends Controller
{
    public function __construct(private CartServiceInterface $cartService) {}

    // Dùng cho Blade view (static helper)
    public static function getCartCount(): int
    {
        if (!Auth::check()) return 0;
        return (int) \Illuminate\Support\Facades\DB::table('cart')
            ->where('user_id', Auth::id())->sum('quantity');
    }

    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login to add to cart', 'cart_count' => 0]);
        }

        $result = $this->cartService->add(
            Auth::id(),
            (int) $request->product_id,
            max(1, (int) $request->input('quantity', 1))
        );

        return response()->json($result);
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $cartItems = $this->cartService->getItems(Auth::id());
        $subtotal  = $cartItems->sum(fn($item) => $item->current_price * $item->quantity);

        return view('cart', compact('cartItems', 'subtotal'));
    }

    public function update(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false]);

        $result = $this->cartService->update(
            Auth::id(),
            (int) $request->input('cart_id'),
            (int) $request->input('quantity', 1)
        );

        return response()->json($result);
    }

    public function remove(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false]);

        $this->cartService->remove(Auth::id(), (int) $request->input('cart_id'));

        return response()->json([
            'success'    => true,
            'cart_count' => $this->cartService->getCount(Auth::id()),
        ]);
    }
}
