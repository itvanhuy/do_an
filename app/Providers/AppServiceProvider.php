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
        if (config('app.env') === 'production' || config('app.url') && str_starts_with(config('app.url'), 'https')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // View composer to share categories across all views that need it
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            $globalCategories = \Illuminate\Support\Facades\DB::table('categories')->orderBy('name', 'asc')->get();
            $view->with('globalCategories', $globalCategories);
        });
    }
}
