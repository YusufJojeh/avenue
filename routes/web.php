<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HomeVisibilityController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\LegacyPublicRedirectController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ThemeCssController;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('{locale}')->where(['locale' => 'ar|en'])->middleware(LocaleMiddleware::class)->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/search', [HomeController::class, 'search'])->name('search');
    Route::get('/products', [CatalogController::class, 'index'])->name('products.index');
    Route::get('/product/{slug}', [CatalogController::class, 'show'])->name('products.show');
    Route::get('/wishlist', [CatalogController::class, 'wishlist'])->name('wishlist.index');
    Route::get('/categories', [CatalogController::class, 'categories'])->name('categories.index');
    Route::get('/category/{slug}', [CatalogController::class, 'category'])->name('categories.show');
    Route::get('/brands', [CatalogController::class, 'brands'])->name('brands.index');
    Route::get('/brand/{slug}', [CatalogController::class, 'brand'])->name('brands.show');
    Route::view('/about', 'about')->name('about');
    Route::get('/contact', [ContactController::class, 'show'])->name('contact');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::view('/faq', 'faq')->name('faq');
    Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
    Route::view('/terms-of-service', 'terms-of-service')->name('terms-of-service');
});

$defaultLocale = fn (): string => in_array(config('app.default_locale'), ['ar', 'en'], true) ? config('app.default_locale') : 'en';
$redirect = fn (string $route, array $query = []) => redirect()->route($route, ['locale' => $defaultLocale()] + $query, 301);

Route::get('/', fn () => $redirect('home'));
Route::get('/search', fn (Request $request) => $redirect('search', $request->only('q', 'page')));
Route::get('/products', fn (Request $request) => $redirect('products.index', $request->only('q', 'brand', 'category', 'sort', 'page')));
Route::get('/product/{slug}', [LegacyPublicRedirectController::class, 'product']);
Route::get('/wishlist', fn () => $redirect('wishlist.index'));
Route::get('/categories', fn () => $redirect('categories.index'));
Route::get('/category/{slug}', [LegacyPublicRedirectController::class, 'category']);
Route::get('/brands', fn () => $redirect('brands.index'));
Route::get('/brand/{slug}', [LegacyPublicRedirectController::class, 'brand']);
foreach (['about', 'contact', 'faq', 'privacy-policy', 'terms-of-service'] as $page) {
    Route::get('/'.$page, fn () => $redirect($page));
}

Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');
Route::get('/language/switch', fn () => redirect()->route('language.toggle'));
Route::get('/language/toggle', [LanguageController::class, 'toggle'])->name('language.toggle');
Route::get('/api/search/suggestions', [HomeController::class, 'searchSuggestions'])->name('api.search.suggestions');
Route::get('/theme.css', ThemeCssController::class)->name('theme.css');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::prefix('api/home')->group(function () {
    Route::get('/settings', [HomeVisibilityController::class, 'getAllSettings'])->name('api.home.settings');
    Route::middleware('auth')->group(function () {
        Route::post('/visibility/update', [HomeVisibilityController::class, 'updateVisibility'])->name('api.home.visibility.update');
        Route::post('/limits/update', [HomeVisibilityController::class, 'updateLimits'])->name('api.home.limits.update');
        Route::post('/visibility/bulk', [HomeVisibilityController::class, 'bulkUpdateVisibility'])->name('api.home.visibility.bulk');
        Route::post('/visibility/toggle/{section}', [HomeVisibilityController::class, 'toggleVisibility'])->name('api.home.visibility.toggle');
    });
});
