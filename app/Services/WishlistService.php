<?php

namespace App\Services;

use App\Contracts\WishlistServiceInterface;
use Illuminate\Support\Facades\DB;

class WishlistService implements WishlistServiceInterface
{
    public function getByUser(int $userId): object
    {
        return DB::table('wishlist')
            ->join('products', 'wishlist.product_id', '=', 'products.id')
            ->where('wishlist.user_id', $userId)
            ->select('products.*', 'wishlist.created_at as added_at')
            ->orderBy('wishlist.created_at', 'desc')
            ->get();
    }

    public function toggle(int $userId, int $productId): array
    {
        $exists = DB::table('wishlist')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            DB::table('wishlist')->where('id', $exists->id)->delete();
            return ['success' => true, 'status' => 'removed'];
        }

        DB::table('wishlist')->insert([
            'user_id'    => $userId,
            'product_id' => $productId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['success' => true, 'status' => 'added'];
    }

    public function isInWishlist(int $userId, int $productId): bool
    {
        return DB::table('wishlist')
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }
}
