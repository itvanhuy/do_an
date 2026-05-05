<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Contracts\WishlistServiceInterface;

class WishlistController extends Controller
{
    public function __construct(private WishlistServiceInterface $wishlistService) {}

    public function index()
    {
        if (!Auth::check()) return redirect()->route('login');

        $wishlistItems = $this->wishlistService->getByUser(Auth::id());

        return view('wishlist', compact('wishlistItems'));
    }

    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login']);
        }

        $result = $this->wishlistService->toggle(Auth::id(), (int) $request->product_id);

        return response()->json($result);
    }
}
