<?php

namespace App\Providers;

use App\Helpers\SiteHelper;
use App\Models\Category;
use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
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
        \Illuminate\Pagination\Paginator::useBootstrapFive();
        config(['app.default_locale' => config('app.locale', 'en')]);
        \Illuminate\Support\Facades\URL::defaults(['locale' => config('app.default_locale')]);

        view()->composer('*', function ($view) {
            $locale = App::getLocale();

            $view->with('htmlDir', $locale === 'ar' ? 'rtl' : 'ltr');
            $view->with('currentLocale', $locale);
            $view->with('supportedLocales', [
                'en' => ['label' => 'English', 'native' => 'English', 'flag' => '🇬🇧'],
                'ar' => ['label' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦'],
            ]);
        });

        view()->composer('layouts.app', function ($view) {
            // Cached for 5 minutes: runs on every storefront page render via
            // the layout composer, but the underlying category/product data
            // changes infrequently relative to page traffic.
            $navCategories = Cache::remember('layout.nav_categories', 300, fn () => Category::active()
                ->withCount('products')
                ->with([
                    'products' => fn ($query) => $query->active()
                        ->published()
                        ->orderByDesc('is_featured')
                        ->latest('published_at')
                        ->limit(2),
                ])
                ->orderByDesc('products_count')
                ->limit(8)
                ->get());

            $view->with('navSearchCategories', $navCategories);
            $view->with('navMegaCategories', $navCategories->take(6));
        });

        Product::observe(ProductObserver::class);
    }
}
