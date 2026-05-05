<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Post;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Product::where('is_active', 1);

        if ($search) {
            $query->where('name', 'like', "%$search%");
        }

        $allProducts = $query->orderBy('created_at', 'desc')->paginate(3);

        // Slides
        $slides = DB::table('slides')->where('is_active', 1)->orderBy('sort_order')->get();

        // 1. Sản phẩm mới
        $newProducts = Product::where('is_active', 1)
                              ->orderBy('created_at', 'desc')
                              ->take(3)->get();
        
        // 2. Sản phẩm khuyến mãi
        $promoProducts = Product::where('is_active', 1)
                                ->where('discount', '>', 0)
                                ->orderBy('discount', 'desc')
                                ->take(3)->get();
        
        // 3. Sản phẩm đề nghị
        $recommendedProducts = Product::where('is_active', 1)
                                      ->orderBy('sold', 'desc')
                                      ->take(3)->get();

        // 4. Lấy 3 bài viết mới nhất
        $latestNews = Post::where('status', 'published')->orderBy('created_at', 'desc')->take(3)->get();
        
        // 5. Giải đấu sắp tới
        $upcomingMatch = DB::table('matches')->where('status', 'upcoming')->orderBy('match_time', 'asc')->first();
        
        return view('home', compact(
            'slides', 'newProducts', 'promoProducts', 'recommendedProducts', 
            'allProducts', 'latestNews', 'upcomingMatch', 'search'
        ));
    }

    public function newsletter(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $exists = DB::table('newsletters')->where('email', $request->email)->exists();
        if ($exists) {
            return back()->with('error', 'This email is already subscribed!');
        }

        DB::table('newsletters')->insert([
            'email' => $request->email,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}
