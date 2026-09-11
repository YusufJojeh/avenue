<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AdvancedCacheService;
use App\Services\EnhancedPerformanceService;

class CacheMonitor extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:monitor 
                            {--watch : Watch mode - continuously monitor cache stats}
                            {--interval=5 : Interval in seconds for watch mode}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor cache performance and statistics';

    /**
     * Execute the console command.
     */
    public function handle(AdvancedCacheService $cache, EnhancedPerformanceService $performance): int
    {
        $watch = $this->option('watch');
        $interval = (int) $this->option('interval');
        
        if ($watch) {
            return $this->watchMode($cache, $performance, $interval);
        } else {
            return $this->singleReport($cache, $performance);
        }
    }
    
    /**
     * Single report mode
     */
    private function singleReport(AdvancedCacheService $cache, EnhancedPerformanceService $performance): int
    {
        $this->info("📊 Cache Performance Report");
        $this->line("Generated at: " . now()->format('Y-m-d H:i:s'));
        $this->line(str_repeat('=', 50));
        
        // Cache statistics
        $stats = $cache->getStats();
        $this->displayCacheStats($stats);
        
        // Performance statistics
        $perfStats = $performance->getPerformanceStats();
        $this->displayPerformanceStats($perfStats);
        
        return Command::SUCCESS;
    }
    
    /**
     * Watch mode - continuously monitor
     */
    private function watchMode(AdvancedCacheService $cache, EnhancedPerformanceService $performance, int $interval): int
    {
        $this->info("👀 Starting cache monitoring (interval: {$interval}s)");
        $this->info("Press Ctrl+C to stop");
        $this->line(str_repeat('=', 50));
        
        while (true) {
            // Clear screen (works on most terminals)
            system('clear');
            
            $this->info("📊 Cache Performance Monitor - " . now()->format('Y-m-d H:i:s'));
            $this->line(str_repeat('=', 50));
            
            // Cache statistics
            $stats = $cache->getStats();
            $this->displayCacheStats($stats);
            
            // Performance statistics
            $perfStats = $performance->getPerformanceStats();
            $this->displayPerformanceStats($perfStats);
            
            $this->line(str_repeat('-', 50));
            $this->info("Next update in {$interval} seconds...");
            
            sleep($interval);
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Display cache statistics
     */
    private function displayCacheStats(array $stats): void
    {
        $this->line("🔧 Cache Configuration:");
        $this->line("  Default Store: " . ($stats['default_store'] ?? 'Unknown'));
        $this->line("  Redis Available: " . ($stats['redis_available'] ? '✅ Yes' : '❌ No'));
        $this->line("  Memcached Available: " . ($stats['memcached_available'] ? '✅ Yes' : '❌ No'));
        
        if (isset($stats['redis'])) {
            $this->line("\n📈 Redis Statistics:");
            $redis = $stats['redis'];
            $this->line("  Memory Usage: " . ($redis['memory_usage'] ?? 'Unknown'));
            $this->line("  Connected Clients: " . ($redis['connected_clients'] ?? 0));
            $this->line("  Total Commands: " . number_format($redis['total_commands'] ?? 0));
            $this->line("  Keyspace Hits: " . number_format($redis['keyspace_hits'] ?? 0));
            $this->line("  Keyspace Misses: " . number_format($redis['keyspace_misses'] ?? 0));
            $this->line("  Hit Rate: " . ($redis['hit_rate'] ?? 0) . "%");
            $this->line("  Total Keys: " . number_format($redis['total_keys'] ?? 0));
            
            // Performance indicators
            $hitRate = $redis['hit_rate'] ?? 0;
            if ($hitRate >= 90) {
                $this->line("  Performance: 🟢 Excellent");
            } elseif ($hitRate >= 80) {
                $this->line("  Performance: 🟡 Good");
            } elseif ($hitRate >= 70) {
                $this->line("  Performance: 🟠 Fair");
            } else {
                $this->line("  Performance: 🔴 Poor");
            }
        }
        
        if (isset($stats['redis_error'])) {
            $this->error("  Redis Error: " . $stats['redis_error']);
        }
    }
    
    /**
     * Display performance statistics
     */
    private function displayPerformanceStats(array $perfStats): void
    {
        if (isset($perfStats['database_stats']['table_sizes'])) {
            $this->line("\n🗄️  Database Statistics:");
            $this->line("  Largest Tables:");
            
            $tables = array_slice($perfStats['database_stats']['table_sizes'], 0, 5);
            foreach ($tables as $table) {
                $size = $table->Size_MB ?? 0;
                $rows = number_format($table->table_rows ?? 0);
                $this->line("    {$table->table_name}: {$size}MB ({$rows} rows)");
            }
        }
        
        if (isset($perfStats['database_stats']['slow_queries'])) {
            $slowQueries = $perfStats['database_stats']['slow_queries'];
            if (!empty($slowQueries)) {
                $this->line("\n🐌 Slow Queries (" . count($slowQueries) . "):");
                foreach (array_slice($slowQueries, 0, 3) as $query) {
                    $time = $query->query_time ?? 0;
                    $this->line("    {$time}s - " . substr($query->sql_text ?? '', 0, 50) . "...");
                }
            } else {
                $this->line("\n✅ No slow queries detected");
            }
        }
        
        if (isset($perfStats['database_stats']['error'])) {
            $this->error("  Database Error: " . $perfStats['database_stats']['error']);
        }
    }
}
