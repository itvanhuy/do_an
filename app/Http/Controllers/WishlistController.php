<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        if (!Auth::check()) return redirect()->route('login');

        $userId = Auth::id();
        $wishlistItems = DB::table('wishlist')
            ->join('products', 'wishlist.product_id', '=', 'products.id')
            ->where('wishlist.user_id', $userId)
            ->select('products.*', 'wishlist.created_at as added_at')
            ->orderBy('wishlist.created_at', 'desc')
            ->get();

        return view('wishlist', compact('wishlistItems'));
    }

    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập']);
        }

        $productId = $request->product_id;
        $userId = Auth::id();

        $exists = DB::table('wishlist')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            DB::table('wishlist')
                ->where('id', $exists->id)
                ->delete();
            return response()->json(['success' => true, 'status' => 'removed']);
        } else {
            DB::table('wishlist')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return response()->json(['success' => true, 'status' => 'added']);
        }
    }
}
