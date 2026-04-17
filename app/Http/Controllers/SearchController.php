<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query)) {
            return redirect()->route('shop');
        }

        $products = Product::where('is_active', 1)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->get();

        return view('shop', [
            'featuredProducts' => $products, // Reusing shop view with search results as featured
            'isSearch' => true,
            'searchQuery' => $query,
            'categories' => \App\Models\Category::all(),
            'fsProducts' => collect(), // Empty for search
            'recProducts' => collect(), // Empty for search
            'brands' => []
        ]);
    }
}
