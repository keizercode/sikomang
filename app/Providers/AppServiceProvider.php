<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
    // sebelumnya:
    // public function boot(): void
    // {
    //     URL::forceScheme('https');
    // }

    public function boot(): void
{
    if (app()->isProduction()) {
        URL::forceScheme('https');
    }
}
}
