<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Lấy 3 bài viết mới nhất
        $latestNews = Post::where('status', 'published')->orderBy('created_at', 'desc')->take(3)->get();
        
        // 2. Lấy 3 sản phẩm nổi bật
        $featuredProducts = Product::where('is_active', 1)->orderBy('id', 'desc')->take(3)->get();
        
        // 3. Lấy giải đấu sắp tới gần nhất (Upcoming Tournament)
        $upcomingMatch = DB::table('matches')->where('status', 'upcoming')->orderBy('match_time', 'asc')->first();
        
        return view('home', compact('latestNews', 'featuredProducts', 'upcomingMatch'));
    }

    public function newsletter(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $exists = DB::table('newsletters')->where('email', $request->email)->exists();
        if ($exists) {
            return back()->with('error', 'Email này đã đăng ký nhận tin rồi!');
        }

        DB::table('newsletters')->insert([
            'email' => $request->email,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đăng ký nhận bản tin!');
    }
}
