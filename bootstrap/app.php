<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add response cache middleware for performance optimization
        $middleware->web(append: [
            \App\Http\Middleware\ResponseCache::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Genuinely unmatched routes (true 404s) never run LocaleMiddleware,
        // since it only fires for routes matched inside the {locale} group.
        // Detect the intended locale from the URL here so error pages
        // (404/500) render in the correct language.
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            $locale = (string) $request->segment(1);
            if (in_array($locale, ['ar', 'en'], true)) {
                app()->setLocale($locale);
            }

            return null;
        });
    })->create();
