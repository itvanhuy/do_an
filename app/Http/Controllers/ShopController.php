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
        $search = $request->get('search');
        $categorySlug = $request->get('category');
        $sort = $request->get('sort', 'newest');

        $query = Product::where('is_active', 1);

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        if ($categorySlug) {
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        switch ($sort) {
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'name': $query->orderBy('name', 'asc'); break;
            default: $query->orderBy('created_at', 'desc'); break;
        }

        $featuredProducts = $query->paginate(12);

        // Flash Sale products
        $fsProducts = Product::where('is_active', 1)
            ->whereNotNull('discount')
            ->where('discount', '>', 0)
            ->orderBy('discount', 'desc')
            ->take(4)
            ->get();

        if ($fsProducts->isEmpty()) {
            $fsProducts = Product::where('is_active', 1)->orderBy('sold', 'desc')->take(4)->get();
        }

        // Recommendations
        $recProducts = Product::where('is_active', 1)->orderBy('sold', 'desc')->take(12)->get();

        // Sidebar Categories
        $categories = Category::orderBy('name')->get();
        $brands = DB::table('brands')->orderBy('name')->get();
        $isCategory = false;

        return view('shop', compact('featuredProducts', 'fsProducts', 'recProducts', 'categories', 'brands', 'search', 'sort', 'isCategory'));
    }

    /**
     * Show products by category slug
     */
    public function category($slug)
    {
        $category = DB::table('categories')->where('slug', $slug)->first();
        if (!$category) abort(404);

        $products = Product::where('is_active', 1)
            ->where('category_id', $category->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = Category::orderBy('name')->get();
        $brands = DB::table('brands')->orderBy('name')->get();

        return view('shop', [
            'featuredProducts' => $products,
            'fsProducts' => collect([]),
            'recProducts' => collect([]),
            'categories' => $categories,
            'brands' => $brands,
            'currentCategory' => $category,
            'search' => null,
            'sort' => 'newest',
            'isCategory' => true,
        ]);
    }
}
