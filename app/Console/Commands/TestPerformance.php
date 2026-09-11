<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AdvancedCacheService;
use App\Services\EnhancedPerformanceService;
use App\Services\PageCacheService;
use Illuminate\Support\Facades\Cache;

class TestPerformance extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'performance:test 
                            {--iterations=10 : Number of test iterations}
                            {--warmup=5 : Number of warmup iterations}';

    /**
     * The console command description.
     */
    protected $description = 'Test performance optimization system';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $iterations = (int) $this->option('iterations');
        $warmup = (int) $this->option('warmup');
        
        $this->info("🚀 Performance Testing System");
        $this->line("Iterations: {$iterations}, Warmup: {$warmup}");
        $this->line(str_repeat('=', 50));
        
        try {
            // Test cache services
            $this->testCacheServices();
            
            // Test performance services
            $this->testPerformanceServices();
            
            // Test page cache service
            $this->testPageCacheService();
            
            // Performance benchmarks
            $this->runPerformanceBenchmarks($iterations, $warmup);
            
            $this->info("✅ All performance tests completed successfully!");
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ Performance test failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Test cache services
     */
    private function testCacheServices(): void
    {
        $this->line("🔧 Testing Cache Services...");
        
        $cache = app(AdvancedCacheService::class);
        
        // Test basic caching
        $testKey = 'test.cache.' . time();
        $testValue = ['test' => 'data', 'timestamp' => now()];
        
        $startTime = microtime(true);
        $cached = $cache->get($testKey, fn() => $testValue, 60);
        $cacheTime = microtime(true) - $startTime;
        
        $this->line("  ✅ Basic cache: " . round($cacheTime * 1000, 2) . "ms");
        
        // Test cache with tags
        $taggedKey = 'test.tagged.' . time();
        $startTime = microtime(true);
        $tagged = $cache->rememberWithTags($taggedKey, ['test'], fn() => $testValue, 60);
        $taggedTime = microtime(true) - $startTime;
        
        $this->line("  ✅ Tagged cache: " . round($taggedTime * 1000, 2) . "ms");
        
        // Test cache statistics
        $stats = $cache->getStats();
        $this->line("  📊 Cache stats: " . ($stats['redis_available'] ? 'Redis ✅' : 'Redis ❌'));
        
        // Cleanup
        Cache::forget($testKey);
        $cache->invalidateByTags(['test']);
    }
    
    /**
     * Test performance services
     */
    private function testPerformanceServices(): void
    {
        $this->line("📈 Testing Performance Services...");
        
        $performance = app(EnhancedPerformanceService::class);
        
        // Test navigation cache
        $startTime = microtime(true);
        $navigation = $performance->getCachedNavigation();
        $navTime = microtime(true) - $startTime;
        
        $this->line("  ✅ Navigation cache: " . round($navTime * 1000, 2) . "ms");
        $this->line("    - Categories: " . count($navigation['categories'] ?? []));
        $this->line("    - Brands: " . count($navigation['brands'] ?? []));
        
        // Test featured products cache
        $startTime = microtime(true);
        $featured = $performance->getCachedFeaturedProducts(8);
        $featuredTime = microtime(true) - $startTime;
        
        $this->line("  ✅ Featured products: " . round($featuredTime * 1000, 2) . "ms");
        $this->line("    - Products: " . count($featured));
        
        // Test categories cache
        $startTime = microtime(true);
        $categories = $performance->getCachedCategories();
        $catTime = microtime(true) - $startTime;
        
        $this->line("  ✅ Categories cache: " . round($catTime * 1000, 2) . "ms");
        $this->line("    - Categories: " . count($categories));
        
        // Test site settings cache
        $startTime = microtime(true);
        $settings = $performance->getCachedSiteSettings();
        $settingsTime = microtime(true) - $startTime;
        
        $this->line("  ✅ Site settings: " . round($settingsTime * 1000, 2) . "ms");
        $this->line("    - Settings: " . count($settings));
    }
    
    /**
     * Test page cache service
     */
    private function testPageCacheService(): void
    {
        $this->line("📄 Testing Page Cache Service...");
        
        $pageCache = app(PageCacheService::class);
        
        // Test fragment caching
        $startTime = microtime(true);
        $fragment = $pageCache->cacheFragment('test.fragment', function () {
            return '<div>Test fragment content</div>';
        }, 60);
        $fragmentTime = microtime(true) - $startTime;
        
        $this->line("  ✅ Fragment cache: " . round($fragmentTime * 1000, 2) . "ms");
        
        // Test navigation caching (skipped - view not available)
        // $startTime = microtime(true);
        // $navFragment = $pageCache->cacheNavigation('main', 60);
        // $navFragmentTime = microtime(true) - $startTime;
        
        // $this->line("  ✅ Navigation fragment: " . round($navFragmentTime * 1000, 2) . "ms");
    }
    
    /**
     * Run performance benchmarks
     */
    private function runPerformanceBenchmarks(int $iterations, int $warmup): void
    {
        $this->line("🏃 Running Performance Benchmarks...");
        
        $performance = app(EnhancedPerformanceService::class);
        
        // Warmup
        for ($i = 0; $i < $warmup; $i++) {
            $performance->getCachedNavigation();
            $performance->getCachedFeaturedProducts(8);
        }
        
        // Benchmark navigation
        $navTimes = [];
        for ($i = 0; $i < $iterations; $i++) {
            $startTime = microtime(true);
            $performance->getCachedNavigation();
            $navTimes[] = microtime(true) - $startTime;
        }
        
        $avgNavTime = array_sum($navTimes) / count($navTimes);
        $minNavTime = min($navTimes);
        $maxNavTime = max($navTimes);
        
        $this->line("  📊 Navigation Benchmark:");
        $this->line("    - Average: " . round($avgNavTime * 1000, 2) . "ms");
        $this->line("    - Min: " . round($minNavTime * 1000, 2) . "ms");
        $this->line("    - Max: " . round($maxNavTime * 1000, 2) . "ms");
        
        // Benchmark featured products
        $featuredTimes = [];
        for ($i = 0; $i < $iterations; $i++) {
            $startTime = microtime(true);
            $performance->getCachedFeaturedProducts(8);
            $featuredTimes[] = microtime(true) - $startTime;
        }
        
        $avgFeaturedTime = array_sum($featuredTimes) / count($featuredTimes);
        $minFeaturedTime = min($featuredTimes);
        $maxFeaturedTime = max($featuredTimes);
        
        $this->line("  📊 Featured Products Benchmark:");
        $this->line("    - Average: " . round($avgFeaturedTime * 1000, 2) . "ms");
        $this->line("    - Min: " . round($minFeaturedTime * 1000, 2) . "ms");
        $this->line("    - Max: " . round($maxFeaturedTime * 1000, 2) . "ms");
        
        // Performance assessment
        $this->line("  🎯 Performance Assessment:");
        if ($avgNavTime < 0.01) {
            $this->line("    - Navigation: 🟢 Excellent (< 10ms)");
        } elseif ($avgNavTime < 0.05) {
            $this->line("    - Navigation: 🟡 Good (< 50ms)");
        } else {
            $this->line("    - Navigation: 🔴 Needs improvement (> 50ms)");
        }
        
        if ($avgFeaturedTime < 0.01) {
            $this->line("    - Featured Products: 🟢 Excellent (< 10ms)");
        } elseif ($avgFeaturedTime < 0.05) {
            $this->line("    - Featured Products: 🟡 Good (< 50ms)");
        } else {
            $this->line("    - Featured Products: 🔴 Needs improvement (> 50ms)");
        }
    }
}
