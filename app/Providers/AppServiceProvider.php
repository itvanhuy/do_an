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
