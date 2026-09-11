<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Setting;

class PerformanceOptimizationService
{
    /**
     * Cache configuration
     */
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_TTL_LONG = 86400; // 24 hours
    private const CACHE_TTL_SHORT = 300; // 5 minutes

    /**
     * Get cached products with optimization
     */
    public function getCachedProducts(string $locale, int $limit = 12, array $filters = []): array
    {
        $cacheKey = "products.{$locale}." . md5(serialize($filters)) . ".{$limit}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($locale, $limit, $filters) {
            $query = Product::active()
                ->with(['images', 'category', 'brand'])
                ->select(['id', 'name_' . $locale, 'short_description_' . $locale, 'slug_' . $locale, 'price', 'sale_price', 'category_id', 'brand_id']);

            // Apply filters
            if (!empty($filters['category'])) {
                $query->whereHas('category', function ($q) use ($filters, $locale) {
                    $q->where("slug_{$locale}", $filters['category']);
                });
            }

            if (!empty($filters['brand'])) {
                $query->whereHas('brand', function ($q) use ($filters, $locale) {
                    $q->where("slug_{$locale}", $filters['brand']);
                });
            }

            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters, $locale) {
                    $q->where("name_{$locale}", 'like', "%{$filters['search']}%")
                      ->orWhere("short_description_{$locale}", 'like', "%{$filters['search']}%");
                });
            }

            return $query->latest('published_at')
                ->take($limit)
                ->get()
                ->map(function ($product) use ($locale) {
                    return [
                        'id' => $product->id,
                        'name' => $product->getLocalizedAttribute('name', $locale),
                        'short_description' => $product->getLocalizedAttribute('short_description', $locale),
                        'slug' => $product->getLocalizedAttribute('slug', $locale),
                        'price' => $product->effective_price,
                        'image_url' => $product->primary_image_url,
                        'category' => $product->category ? [
                            'name' => $product->category->getLocalizedAttribute('name', $locale),
                            'slug' => $product->category->getLocalizedAttribute('slug', $locale),
                        ] : null,
                        'brand' => $product->brand ? [
                            'name' => $product->brand->getLocalizedAttribute('name', $locale),
                            'slug' => $product->brand->getLocalizedAttribute('slug', $locale),
                        ] : null,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get cached categories with optimization
     */
    public function getCachedCategories(string $locale): array
    {
        $cacheKey = "categories.{$locale}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () use ($locale) {
            return Category::active()
                ->select(['id', 'name_' . $locale, 'description_' . $locale, 'slug_' . $locale, 'image_path', 'sort_order'])
                ->orderBy('sort_order')
                ->get()
                ->map(function ($category) use ($locale) {
                    return [
                        'id' => $category->id,
                        'name' => $category->getLocalizedAttribute('name', $locale),
                        'description' => $category->getLocalizedAttribute('description', $locale),
                        'slug' => $category->getLocalizedAttribute('slug', $locale),
                        'image_url' => $category->image_url,
                        'sort_order' => $category->sort_order,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get cached brands with optimization
     */
    public function getCachedBrands(string $locale): array
    {
        $cacheKey = "brands.{$locale}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () use ($locale) {
            return Brand::active()
                ->select(['id', 'name_' . $locale, 'description_' . $locale, 'slug_' . $locale, 'logo_path'])
                ->orderBy("name_{$locale}")
                ->get()
                ->map(function ($brand) use ($locale) {
                    return [
                        'id' => $brand->id,
                        'name' => $brand->getLocalizedAttribute('name', $locale),
                        'description' => $brand->getLocalizedAttribute('description', $locale),
                        'slug' => $brand->getLocalizedAttribute('slug', $locale),
                        'logo_url' => $brand->logo_url,
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get cached site settings
     */
    public function getCachedSiteSettings(string $locale): array
    {
        $cacheKey = "site_settings.{$locale}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL_LONG, function () use ($locale) {
            $settings = Setting::where('group', 'site')->get();
            $localizedSettings = [];
            
            foreach ($settings as $setting) {
                $localizedSettings[$setting->key] = $setting->get($setting->key, null, $locale);
            }
            
            return $localizedSettings;
        });
    }

    /**
     * Warm up cache for common data
     */
    public function warmUpCache(): array
    {
        $startTime = microtime(true);
        $warmedUp = [];
        
        try {
            // Warm up products cache
            $this->getCachedProducts('en', 12);
            $this->getCachedProducts('ar', 12);
            $warmedUp[] = 'products';
            
            // Warm up categories cache
            $this->getCachedCategories('en');
            $this->getCachedCategories('ar');
            $warmedUp[] = 'categories';
            
            // Warm up brands cache
            $this->getCachedBrands('en');
            $this->getCachedBrands('ar');
            $warmedUp[] = 'brands';
            
            // Warm up site settings cache
            $this->getCachedSiteSettings('en');
            $this->getCachedSiteSettings('ar');
            $warmedUp[] = 'site_settings';
            
        } catch (\Exception $e) {
            Log::error('Cache warm-up failed: ' . $e->getMessage());
        }
        
        $duration = microtime(true) - $startTime;
        
        return [
            'warmed_up' => $warmedUp,
            'duration' => round($duration * 1000, 2), // milliseconds
            'timestamp' => now(),
        ];
    }

    /**
     * Clear specific cache types
     */
    public function clearCache(string $type = 'all'): array
    {
        $cleared = [];
        
        switch ($type) {
            case 'products':
                Cache::flush('products.*');
                $cleared[] = 'products';
                break;
                
            case 'categories':
                Cache::flush('categories.*');
                $cleared[] = 'categories';
                break;
                
            case 'brands':
                Cache::flush('brands.*');
                $cleared[] = 'brands';
                break;
                
            case 'settings':
                Cache::flush('site_settings.*');
                $cleared[] = 'site_settings';
                break;
                
            case 'all':
                Cache::flush();
                $cleared = ['all'];
                break;
        }
        
        return [
            'cleared' => $cleared,
            'timestamp' => now(),
        ];
    }

    /**
     * Get cache statistics
     */
    public function getCacheStatistics(): array
    {
        $stats = [
            'total_keys' => 0,
            'memory_usage' => 0,
            'hit_rate' => 0,
            'miss_rate' => 0,
        ];
        
        // Get cache driver info
        $driver = config('cache.default');
        $stats['driver'] = $driver;
        
        // For Redis, we can get more detailed stats
        if ($driver === 'redis') {
            try {
                $redis = Redis::connection();
                $info = $redis->info();
                $stats['memory_usage'] = $info['used_memory_human'] ?? 'Unknown';
                $stats['total_keys'] = $redis->dbsize();
            } catch (\Exception $e) {
                $stats['error'] = 'Redis connection failed: ' . $e->getMessage();
            }
        }
        
        return $stats;
    }

    /**
     * Optimize database queries
     */
    public function optimizeDatabaseQueries(): array
    {
        $optimizations = [];
        
        // Check for slow queries
        $slowQueries = DB::table('information_schema.processlist')
            ->where('command', '!=', 'Sleep')
            ->where('time', '>', 5)
            ->get();
            
        if ($slowQueries->count() > 0) {
            $optimizations[] = 'Found ' . $slowQueries->count() . ' slow queries';
        }
        
        // Check table sizes
        $tableSizes = DB::select("
            SELECT 
                table_name,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size_MB'
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
            ORDER BY (data_length + index_length) DESC
        ");
        
        foreach ($tableSizes as $table) {
            if ($table->Size_MB > 100) { // Tables larger than 100MB
                $optimizations[] = "Large table: {$table->table_name} ({$table->Size_MB}MB)";
            }
        }
        
        return [
            'optimizations' => $optimizations,
            'table_sizes' => $tableSizes,
            'timestamp' => now(),
        ];
    }

    /**
     * Generate performance report
     */
    public function generatePerformanceReport(): array
    {
        $startTime = microtime(true);
        
        $report = [
            'cache_stats' => $this->getCacheStatistics(),
            'database_optimizations' => $this->optimizeDatabaseQueries(),
            'cache_warmup' => $this->warmUpCache(),
            'generated_at' => now(),
        ];
        
        $report['generation_time'] = microtime(true) - $startTime;
        
        return $report;
    }

    /**
     * Preload common data for better performance
     */
    public function preloadCommonData(): void
    {
        // Preload common relationships
        Product::with(['category', 'brand', 'images'])->chunk(100, function ($products) {
            // This will populate the relationship cache
        });
        
        Category::with(['products'])->chunk(50, function ($categories) {
            // This will populate the relationship cache
        });
        
        Brand::with(['products'])->chunk(50, function ($brands) {
            // This will populate the relationship cache
        });
    }
}
