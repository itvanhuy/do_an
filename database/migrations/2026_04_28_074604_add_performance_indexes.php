<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // products
        Schema::table('products', function (Blueprint $table) {
            if (!$this->hasIndex('products', 'products_is_active_index'))
                $table->index('is_active');
            if (!$this->hasIndex('products', 'products_category_id_index'))
                $table->index('category_id');
            if (!$this->hasIndex('products', 'products_discount_index'))
                $table->index('discount');
            if (!$this->hasIndex('products', 'products_sold_index'))
                $table->index('sold');
        });

        // orders
        Schema::table('orders', function (Blueprint $table) {
            if (!$this->hasIndex('orders', 'orders_user_id_index'))
                $table->index('user_id');
            if (!$this->hasIndex('orders', 'orders_status_index'))
                $table->index('status');
            if (!$this->hasIndex('orders', 'orders_created_at_index'))
                $table->index('created_at');
        });

        // order_items
        Schema::table('order_items', function (Blueprint $table) {
            if (!$this->hasIndex('order_items', 'order_items_order_id_index'))
                $table->index('order_id');
            if (!$this->hasIndex('order_items', 'order_items_product_id_index'))
                $table->index('product_id');
        });

        // cart
        Schema::table('cart', function (Blueprint $table) {
            if (!$this->hasIndex('cart', 'cart_user_id_index'))
                $table->index('user_id');
        });

        // posts
        Schema::table('posts', function (Blueprint $table) {
            if (!$this->hasIndex('posts', 'posts_status_index'))
                $table->index('status');
            if (!$this->hasIndex('posts', 'posts_created_at_index'))
                $table->index('created_at');
        });

        // reviews
        Schema::table('reviews', function (Blueprint $table) {
            if (!$this->hasIndex('reviews', 'reviews_product_id_index'))
                $table->index('product_id');
            if (!$this->hasIndex('reviews', 'reviews_status_index'))
                $table->index('status');
        });

        // matches
        Schema::table('matches', function (Blueprint $table) {
            if (!$this->hasIndex('matches', 'matches_status_index'))
                $table->index('status');
            if (!$this->hasIndex('matches', 'matches_match_time_index'))
                $table->index('match_time');
            if (!$this->hasIndex('matches', 'matches_game_type_index'))
                $table->index('game_type');
        });

        // slides
        Schema::table('slides', function (Blueprint $table) {
            if (!$this->hasIndex('slides', 'slides_is_active_index'))
                $table->index('is_active');
        });

        // coupons
        Schema::table('coupons', function (Blueprint $table) {
            if (!$this->hasIndex('coupons', 'coupons_is_active_index'))
                $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['discount']);
            $table->dropIndex(['sold']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['product_id']);
        });
        Schema::table('cart', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['status']);
        });
        Schema::table('matches', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['match_time']);
            $table->dropIndex(['game_type']);
        });
        Schema::table('slides', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return collect(\Illuminate\Support\Facades\DB::select("SHOW INDEX FROM `$table`"))
            ->pluck('Key_name')
            ->contains($indexName);
    }
};
