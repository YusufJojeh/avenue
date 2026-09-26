<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
        // Share site settings with all views
        View::composer('*', function ($view) {
            try {
                // Memoized per request: View::composer('*') fires on every Blade
                // view/partial/component render, not once per HTTP response. Without
                // this cache, an admin list page rendering hundreds of row/cell
                // partials issued hundreds of identical `settings` table scans.
                static $settings = null;
                if ($settings === null) {
                    $settings = Setting::query()->pluck('value', 'key')->all();
                }
                $siteName = $settings['site.name'] ?? 'MyStore';
                
                // Parse JSON settings
                if (isset($settings['site.social_media']) && $settings['site.social_media']) {
                    $settings['social_media'] = json_decode($settings['site.social_media'], true) ?: [];
                } else {
                    $settings['social_media'] = [];
                }
                
                if (isset($settings['home.limits']) && $settings['home.limits']) {
                    $settings['limits'] = json_decode($settings['home.limits'], true) ?: [];
                } else {
                    $settings['limits'] = [];
                }
                
                $view->with([
                    'siteName' => $siteName,
                    'settings' => $settings,
                ]);
            } catch (\Exception $e) {
                // Fallback if settings fail to load
                $view->with([
                    'siteName' => 'MyStore',
                    'settings' => [
                        'social_media' => [],
                        'limits' => [],
                    ],
                ]);
            }
        });
    }
}
