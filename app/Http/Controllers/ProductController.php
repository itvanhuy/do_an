<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Contracts\WishlistServiceInterface;

class ProductController extends Controller
{
    public function __construct(private WishlistServiceInterface $wishlistService) {}
    public function show(Request $request, $id)
    {
        // Chỉ lấy các cột cần thiết để tiết kiệm RAM server
        $product = Product::select('products.id', 'products.name', 'products.description', 'products.price', 'products.discount', 'products.image', 'products.stock_quantity', 'products.category_id', 'categories.name as category_name', 'categories.slug as category_slug')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('products.id', $id)
            ->where('products.is_active', 1)
            ->firstOrFail();

        $extraImages = [];
        // Kiểm tra bảng tồn tại trước khi truy vấn để tránh lỗi 500
        if (Schema::hasTable('product_images')) {
            $extraImages = DB::table('product_images')
                ->where('product_id', $id)
                ->pluck('image')
                ->toArray();
        }
            
        $images = array_values(array_filter(array_merge([$product->image], $extraImages)));
        if (empty($images)) {
            $images[] = 'default.jpg';
        }

        // Related Products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $id)
            ->where('is_active', 1)
            ->take(4)
            ->get();

        // Reviews
        $reviews = DB::table('reviews')
            ->join('users', 'reviews.user_id', '=', 'users.id')
            ->where('reviews.product_id', $id)
            ->where('reviews.status', 'approved')
            ->orderBy('reviews.created_at', 'desc')
            ->select('reviews.*', 'users.full_name', 'users.username')
            ->get();

        // Wishlist Status
        $isInWishlist = false;
        if (Auth::check()) {
            $isInWishlist = DB::table('wishlist')
                ->where('user_id', Auth::id())
                ->where('product_id', $id)
                ->exists();
        }

        // Recently Viewed Handling
        $recentIds = $request->session()->get('recently_viewed', []);
        
        // Cập nhật danh sách ID sản phẩm đã xem (giới hạn 6)
        $recentIds = array_values(array_unique(array_merge([$id], $recentIds)));
        $recentIds = array_slice($recentIds, 0, 7); // Lấy 7 để trừ đi sản phẩm hiện tại
        $request->session()->put('recently_viewed', $recentIds);

        // Lấy sản phẩm đã xem (loại trừ ID hiện tại)
        $displayRecentIds = array_diff($recentIds, [$id]);
        $recentProducts = Product::select('id', 'name', 'price', 'image', 'discount')
            ->whereIn('id', $displayRecentIds)
            ->where('is_active', 1)
            ->get()
            ->sortBy(function($model) use ($displayRecentIds) {
                return array_search($model->id, $displayRecentIds);
            });

        return view('product-detail', compact('product', 'images', 'relatedProducts', 'reviews', 'isInWishlist', 'recentProducts'));
    }

    public function submitReview(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Vui lòng đăng nhập để đánh giá']);
        }

        $request->validate([
            'rating' => 'required|min:1|max:5',
            'review' => 'required'
        ]);

        DB::table('reviews')->insert([
            'product_id' => $id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->review,
            'status' => 'pending', // Yêu cầu admin duyệt
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Đánh giá của bạn đã được gửi và đang chờ quản trị viên phê duyệt!'
        ]);
    }
}
