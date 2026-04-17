<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // 1. Featured Products
        $featuredProducts = Product::where('is_active', 1)->orderBy('price', 'desc')->take(9)->get();

        // 2. Flash Sale
        $fsProducts = Product::where('is_active', 1)
            ->whereNotNull('discount')
            ->where('discount', '>', 0)
            ->orderBy('discount', 'desc')
            ->take(4)
            ->get();

        if ($fsProducts->isEmpty()) {
            $fsProducts = Product::where('is_active', 1)->inRandomOrder()->take(4)->get();
        }

        // 3. Recommendations
        $recProducts = Product::where('is_active', 1)->inRandomOrder()->take(12)->get();

        // 4. Sidebar Categories
        // Handle columns safely since schema might differ slightly (like 'status' vs 'is_active')
        // We'll just get all categories as simple fallback
        $categories = Category::orderBy('name')->get();

        // Brands - Skip since table doesn't exist
        $brands = [];

        return view('shop', compact('featuredProducts', 'fsProducts', 'recProducts', 'categories', 'brands'));
    }
}
