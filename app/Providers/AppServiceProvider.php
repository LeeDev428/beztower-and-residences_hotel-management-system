<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
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
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.default');

        // Force HTTPS and proper URL when using tunneling services (ngrok, VS Code port forwarding, etc.)
        if ($this->app->environment('production') || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }
        
        // Use the forwarded host if available
        if (request()->header('X-Forwarded-Host')) {
            URL::forceRootUrl(request()->getScheme() . '://' . request()->header('X-Forwarded-Host'));
        }
    }
}
