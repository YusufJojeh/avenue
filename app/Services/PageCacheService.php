<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;

class PageCacheService
{
    private AdvancedCacheService $cache;
    
    public function __construct(AdvancedCacheService $cache)
    {
        $this->cache = $cache;
    }
    
    /**
     * Cache entire page response
     */
    public function cachePage(string $key, callable $callback, int $ttl = 3600): string
    {
        return $this->cache->get("page.{$key}", function () use ($callback) {
            ob_start();
            $callback();
            return ob_get_clean();
        }, $ttl);
    }
    
    /**
     * Cache page fragment
     */
    public function cacheFragment(string $key, callable $callback, int $ttl = 1800): string
    {
        return $this->cache->get("fragment.{$key}", $callback, $ttl);
    }
    
    /**
     * Cache view with data
     */
    public function cacheView(string $view, array $data = [], int $ttl = 3600): string
    {
        $cacheKey = "view.{$view}." . md5(serialize($data));
        
        return $this->cache->get($cacheKey, function () use ($view, $data) {
            return View::make($view, $data)->render();
        }, $ttl);
    }
    
    /**
     * Cache navigation menu
     */
    public function cacheNavigation(string $type = 'main', int $ttl = 86400): string
    {
        return $this->cacheFragment("navigation.{$type}", function () use ($type) {
            $data = $this->getNavigationData($type);
            return View::make("partials.navigation.{$type}", $data)->render();
        }, $ttl);
    }
    
    /**
     * Cache product listing
     */
    public function cacheProductListing(array $filters = [], int $page = 1, int $perPage = 12, int $ttl = 1800): string
    {
        $cacheKey = "product_listing." . md5(serialize($filters)) . ".{$page}.{$perPage}";
        
        return $this->cacheFragment($cacheKey, function () use ($filters, $page, $perPage) {
            $data = $this->getProductListingData($filters, $page, $perPage);
            return View::make('products.partials.listing', $data)->render();
        }, $ttl);
    }
    
    /**
     * Cache category sidebar
     */
    public function cacheCategorySidebar(int $ttl = 86400): string
    {
        return $this->cacheFragment('category_sidebar', function () {
            $data = $this->getCategorySidebarData();
            return View::make('partials.sidebar.categories', $data)->render();
        }, $ttl);
    }
    
    /**
     * Cache brand sidebar
     */
    public function cacheBrandSidebar(int $ttl = 86400): string
    {
        return $this->cacheFragment('brand_sidebar', function () {
            $data = $this->getBrandSidebarData();
            return View::make('partials.sidebar.brands', $data)->render();
        }, $ttl);
    }
    
    /**
     * Cache featured products section
     */
    public function cacheFeaturedProducts(int $limit = 8, int $ttl = 3600): string
    {
        return $this->cacheFragment("featured_products.{$limit}", function () use ($limit) {
            $data = $this->getFeaturedProductsData($limit);
            return View::make('partials.featured_products', $data)->render();
        }, $ttl);
    }
    
    /**
     * Cache footer
     */
    public function cacheFooter(int $ttl = 86400): string
    {
        return $this->cacheFragment('footer', function () {
            $data = $this->getFooterData();
            return View::make('partials.footer', $data)->render();
        }, $ttl);
    }
    
    /**
     * Cache search suggestions
     */
    public function cacheSearchSuggestions(string $query, int $limit = 10, int $ttl = 1800): string
    {
        $cacheKey = "search_suggestions." . md5($query) . ".{$limit}";
        
        return $this->cacheFragment($cacheKey, function () use ($query, $limit) {
            $data = $this->getSearchSuggestionsData($query, $limit);
            return View::make('partials.search_suggestions', $data)->render();
        }, $ttl);
    }
    
    /**
     * Clear page cache
     */
    public function clearPageCache(string $type = 'all'): array
    {
        $cleared = [];
        
        switch ($type) {
            case 'pages':
                $this->cache->invalidateByTags(['page']);
                $cleared[] = 'pages';
                break;
                
            case 'fragments':
                $this->cache->invalidateByTags(['fragment']);
                $cleared[] = 'fragments';
                break;
                
            case 'views':
                $this->cache->invalidateByTags(['view']);
                $cleared[] = 'views';
                break;
                
            case 'navigation':
                $this->cache->invalidateByTags(['navigation']);
                $cleared[] = 'navigation';
                break;
                
            case 'all':
                $this->cache->invalidateByTags(['page', 'fragment', 'view', 'navigation']);
                $cleared = ['pages', 'fragments', 'views', 'navigation'];
                break;
        }
        
        return $cleared;
    }
    
    /**
     * Get navigation data
     */
    private function getNavigationData(string $type): array
    {
        switch ($type) {
            case 'main':
                return [
                    'categories' => \App\Models\Category::active()
                        ->select(['id', 'name', 'slug', 'sort_order'])
                        ->orderBy('sort_order')
                        ->get(),
                    'brands' => \App\Models\Brand::active()
                        ->select(['id', 'name', 'slug'])
                        ->orderBy('name')
                        ->get(),
                ];
                
            case 'footer':
                return [
                    'categories' => \App\Models\Category::active()
                        ->select(['id', 'name', 'slug'])
                        ->orderBy('sort_order')
                        ->limit(10)
                        ->get(),
                    'brands' => \App\Models\Brand::active()
                        ->select(['id', 'name', 'slug'])
                        ->orderBy('name')
                        ->limit(10)
                        ->get(),
                ];
                
            default:
                return [];
        }
    }
    
    /**
     * Get product listing data
     */
    private function getProductListingData(array $filters, int $page, int $perPage): array
    {
        $query = \App\Models\Product::with(['images', 'category', 'brand'])
            ->active()
            ->select(['id', 'name', 'slug', 'short_description', 'price', 'sale_price', 'category_id', 'brand_id']);
        
        // Apply filters
        if (!empty($filters['category'])) {
            $query->whereHas('category', fn($c) => $c->where('slug', $filters['category']));
        }
        
        if (!empty($filters['brand'])) {
            $query->whereHas('brand', fn($b) => $b->where('slug', $filters['brand']));
        }
        
        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }
        
        // Apply sorting
        $sort = $filters['sort'] ?? 'latest';
        switch ($sort) {
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'name':
                $query->orderBy('name');
                break;
            default:
                $query->latest('published_at');
        }
        
        $products = $query->paginate($perPage, ['*'], 'page', $page);
        
        return [
            'products' => $products,
            'filters' => $filters,
        ];
    }
    
    /**
     * Get category sidebar data
     */
    private function getCategorySidebarData(): array
    {
        return [
            'categories' => \App\Models\Category::active()
                ->select(['id', 'name', 'slug', 'sort_order', 'parent_id'])
                ->orderBy('sort_order')
                ->get()
                ->groupBy('parent_id'),
        ];
    }
    
    /**
     * Get brand sidebar data
     */
    private function getBrandSidebarData(): array
    {
        return [
            'brands' => \App\Models\Brand::active()
                ->select(['id', 'name', 'slug'])
                ->orderBy('name')
                ->get(),
        ];
    }
    
    /**
     * Get featured products data
     */
    private function getFeaturedProductsData(int $limit): array
    {
        return [
            'products' => \App\Models\Product::featured()
                ->active()
                ->with(['images', 'category', 'brand'])
                ->select(['id', 'name', 'slug', 'short_description', 'price', 'sale_price', 'category_id', 'brand_id'])
                ->latest('published_at')
                ->limit($limit)
                ->get(),
        ];
    }
    
    /**
     * Get footer data
     */
    private function getFooterData(): array
    {
        return [
            'categories' => \App\Models\Category::active()
                ->select(['id', 'name', 'slug'])
                ->orderBy('sort_order')
                ->limit(10)
                ->get(),
            'brands' => \App\Models\Brand::active()
                ->select(['id', 'name', 'slug'])
                ->orderBy('name')
                ->limit(10)
                ->get(),
            'settings' => \App\Models\Setting::where('group', 'site')->get()->pluck('value', 'key'),
        ];
    }
    
    /**
     * Get search suggestions data
     */
    private function getSearchSuggestionsData(string $query, int $limit): array
    {
        $suggestions = [];
        
        // Product suggestions
        $products = \App\Models\Product::active()
            ->where('name', 'like', "%{$query}%")
            ->select(['name', 'slug'])
            ->limit($limit)
            ->get();
        
        foreach ($products as $product) {
            $suggestions[] = [
                'type' => 'product',
                'text' => $product->name,
                'url' => route('products.show', $product->slug),
            ];
        }
        
        // Category suggestions
        $categories = \App\Models\Category::active()
            ->where('name', 'like', "%{$query}%")
            ->select(['name', 'slug'])
            ->limit(5)
            ->get();
        
        foreach ($categories as $category) {
            $suggestions[] = [
                'type' => 'category',
                'text' => $category->name,
                'url' => route('categories.show', $category->slug),
            ];
        }
        
        return [
            'suggestions' => array_slice($suggestions, 0, $limit),
            'query' => $query,
        ];
    }
}
