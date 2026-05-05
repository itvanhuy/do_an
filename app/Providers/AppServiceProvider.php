<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\CartServiceInterface;
use App\Contracts\WishlistServiceInterface;
use App\Services\CartService;
use App\Services\WishlistService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CartServiceInterface::class, CartService::class);
        $this->app->bind(WishlistServiceInterface::class, WishlistService::class);
    }

    public function boot(): void
    {
        // Force HTTPS trên production
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Share categories across all views
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $globalCategories = \Illuminate\Support\Facades\DB::table('categories')->orderBy('name', 'asc')->get();
            $view->with('globalCategories', $globalCategories);
        });
    }
}
