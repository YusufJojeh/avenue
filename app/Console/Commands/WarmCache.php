<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EnhancedPerformanceService;
use App\Services\AdvancedCacheService;

class WarmCache extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:warm 
                            {--type=all : Type of cache to warm (all, products, categories, brands, navigation, settings)}
                            {--force : Force warm up even if cache exists}';

    /**
     * The console command description.
     */
    protected $description = 'Warm up application cache for better performance';

    /**
     * Execute the console command.
     */
    public function handle(EnhancedPerformanceService $performance, AdvancedCacheService $cache): int
    {
        $type = $this->option('type');
        $force = $this->option('force');
        
        $this->info("🔥 Warming up cache (type: {$type})...");
        
        $startTime = microtime(true);
        $warmedUp = [];
        
        try {
            switch ($type) {
                case 'products':
                    $warmedUp = array_merge($warmedUp, $this->warmProducts($performance, $force));
                    break;
                    
                case 'categories':
                    $warmedUp = array_merge($warmedUp, $this->warmCategories($performance, $force));
                    break;
                    
                case 'brands':
                    $warmedUp = array_merge($warmedUp, $this->warmBrands($performance, $force));
                    break;
                    
                case 'navigation':
                    $warmedUp = array_merge($warmedUp, $this->warmNavigation($performance, $force));
                    break;
                    
                case 'settings':
                    $warmedUp = array_merge($warmedUp, $this->warmSettings($performance, $force));
                    break;
                    
                case 'all':
                default:
                    $warmedUp = array_merge($warmedUp, $this->warmAll($performance, $force));
                    break;
            }
            
            $duration = microtime(true) - $startTime;
            
            $this->info("✅ Cache warmed up successfully!");
            $this->line("📊 Warmed up: " . implode(', ', $warmedUp));
            $this->line("⏱️  Duration: " . round($duration * 1000, 2) . "ms");
            
            // Show cache statistics
            $stats = $cache->getStats();
            if (isset($stats['redis'])) {
                $this->line("📈 Redis Hit Rate: " . $stats['redis']['hit_rate'] . "%");
                $this->line("💾 Memory Usage: " . $stats['redis']['memory_usage']);
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Cache warm-up failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Warm up products cache
     */
    private function warmProducts(EnhancedPerformanceService $performance, bool $force): array
    {
        $warmedUp = [];
        
        $this->line("🛍️  Warming up products cache...");
        
        // Main product listings
        $performance->getCachedProducts([], 12, 1);
        $performance->getCachedProducts([], 24, 1);
        $performance->getCachedProducts([], 48, 1);
        $warmedUp[] = 'products_main';
        
        // Featured products
        $performance->getCachedFeaturedProducts(8);
        $performance->getCachedFeaturedProducts(12);
        $warmedUp[] = 'products_featured';
        
        // Product filters
        $filters = [
            ['featured' => true],
            ['sort' => 'price_low'],
            ['sort' => 'price_high'],
            ['sort' => 'name'],
        ];
        
        foreach ($filters as $filter) {
            $performance->getCachedProducts($filter, 12, 1);
        }
        $warmedUp[] = 'products_filters';
        
        return $warmedUp;
    }
    
    /**
     * Warm up categories cache
     */
    private function warmCategories(EnhancedPerformanceService $performance, bool $force): array
    {
        $warmedUp = [];
        
        $this->line("📂 Warming up categories cache...");
        
        $performance->getCachedCategories();
        $warmedUp[] = 'categories_hierarchy';
        
        return $warmedUp;
    }
    
    /**
     * Warm up brands cache
     */
    private function warmBrands(EnhancedPerformanceService $performance, bool $force): array
    {
        $warmedUp = [];
        
        $this->line("🏷️  Warming up brands cache...");
        
        $performance->getCachedBrands();
        $warmedUp[] = 'brands_list';
        
        return $warmedUp;
    }
    
    /**
     * Warm up navigation cache
     */
    private function warmNavigation(EnhancedPerformanceService $performance, bool $force): array
    {
        $warmedUp = [];
        
        $this->line("🧭 Warming up navigation cache...");
        
        $performance->getCachedNavigation();
        $warmedUp[] = 'navigation_main';
        
        return $warmedUp;
    }
    
    /**
     * Warm up settings cache
     */
    private function warmSettings(EnhancedPerformanceService $performance, bool $force): array
    {
        $warmedUp = [];
        
        $this->line("⚙️  Warming up settings cache...");
        
        $performance->getCachedSiteSettings();
        $warmedUp[] = 'site_settings';
        
        return $warmedUp;
    }
    
    /**
     * Warm up all caches
     */
    private function warmAll(EnhancedPerformanceService $performance, bool $force): array
    {
        $warmedUp = [];
        
        $this->line("🚀 Warming up all caches...");
        
        // Warm up all individual caches
        $warmedUp = array_merge($warmedUp, $this->warmProducts($performance, $force));
        $warmedUp = array_merge($warmedUp, $this->warmCategories($performance, $force));
        $warmedUp = array_merge($warmedUp, $this->warmBrands($performance, $force));
        $warmedUp = array_merge($warmedUp, $this->warmNavigation($performance, $force));
        $warmedUp = array_merge($warmedUp, $this->warmSettings($performance, $force));
        
        // Warm up search suggestions for common terms
        $this->line("🔍 Warming up search suggestions...");
        $commonTerms = ['laptop', 'phone', 'watch', 'shirt', 'shoes', 'bag'];
        foreach ($commonTerms as $term) {
            $performance->getCachedSearchSuggestions($term, 5);
        }
        $warmedUp[] = 'search_suggestions';
        
        return $warmedUp;
    }
}
