<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class UrlCorrectionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Force the correct URL scheme and host for local development
        if (request()->getHost() === '127.0.0.1') {
            URL::forceRootUrl('http://127.0.0.1:8000');
        }
    }
}
