<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // View composer to share categories across all views that need it
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $globalCategories = \Illuminate\Support\Facades\DB::table('categories')->orderBy('name', 'asc')->get();
            $view->with('globalCategories', $globalCategories);
        });
    }
}
