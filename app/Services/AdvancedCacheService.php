<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Predis\Client as PredisClient;

class AdvancedCacheService
{
    /**
     * Cache TTL constants
     */
    private const CACHE_TTL_SHORT = 300;      // 5 minutes
    private const CACHE_TTL_MEDIUM = 3600;    // 1 hour
    private const CACHE_TTL_LONG = 86400;     // 24 hours
    private const CACHE_TTL_VERY_LONG = 604800; // 7 days

    /**
     * Cache stores
     */
    private const STORE_REDIS = 'redis';
    private const STORE_MEMCACHED = 'memcached';
    private const STORE_DATABASE = 'database';

    /**
     * Multi-level caching with fallback
     */
    public function get(string $key, callable $callback, int $ttl = self::CACHE_TTL_MEDIUM, string $store = self::STORE_REDIS)
    {
        try {
            // Try Redis first (L1) if requested and available
            if ($store === self::STORE_REDIS && $this->isRedisAvailable()) {
                try {
                    return Cache::store('redis')->remember($key, $ttl, $callback);
                } catch (\Exception $redisError) {
                    Log::warning("Redis cache failed for key {$key}, falling back: " . $redisError->getMessage());
                    // Fall through to next cache level
                }
            }

            // Fallback to Memcached (L2)
            if ($this->isMemcachedAvailable()) {
                try {
                    return Cache::store('memcached')->remember($key, $ttl, $callback);
                } catch (\Exception $memcachedError) {
                    Log::warning("Memcached cache failed for key {$key}, falling back: " . $memcachedError->getMessage());
                    // Fall through to database cache
                }
            }

            // Fallback to database cache (L3)
            return Cache::store('database')->remember($key, $ttl, $callback);

        } catch (\Exception $e) {
            Log::warning("All cache stores failed for key {$key}: " . $e->getMessage());

            // Execute callback directly if all caches fail
            return $callback();
        }
    }

    /**
     * Cache with tags for group invalidation
     */
    public function rememberWithTags(string $key, array $tags, callable $callback, int $ttl = self::CACHE_TTL_MEDIUM)
    {
        try {
            // Only use Redis if it's available and working
            if ($this->isRedisAvailable()) {
                try {
                    return Cache::store('redis')->tags($tags)->remember($key, $ttl, $callback);
                } catch (\Exception $redisError) {
                    Log::warning("Redis tagged cache failed, falling back to database: " . $redisError->getMessage());
                    // Fall through to database cache
                }
            }

            // Fallback to database cache without tags
            return Cache::store('database')->remember($key, $ttl, $callback);

        } catch (\Exception $e) {
            Log::warning("Tagged cache error for key {$key}: " . $e->getMessage());
            return $callback();
        }
    }

    /**
     * Invalidate cache by tags
     */
    public function invalidateByTags(array $tags): bool
    {
        try {
            if ($this->isRedisAvailable()) {
                Cache::store('redis')->tags($tags)->flush();
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Cache invalidation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cache with automatic key generation
     */
    public function remember(string $prefix, array $params, callable $callback, int $ttl = self::CACHE_TTL_MEDIUM): mixed
    {
        $key = $prefix . '.' . md5(serialize($params));
        return $this->get($key, $callback, $ttl);
    }

    /**
     * Cache paginated results
     */
    public function rememberPaginated(string $key, int $page, int $perPage, callable $callback, int $ttl = self::CACHE_TTL_MEDIUM): array
    {
        $paginatedKey = "{$key}.page.{$page}.per.{$perPage}";

        return $this->get($paginatedKey, function () use ($callback, $page, $perPage) {
            $result = $callback();

            // Ensure result has pagination structure
            if (is_array($result) && isset($result['data'])) {
                return $result;
            }

            // Convert to paginated format if needed
            return [
                'data' => $result,
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => count($result),
                'last_page' => ceil(count($result) / $perPage),
            ];
        }, $ttl);
    }

    /**
     * Cache with compression for large data
     */
    public function rememberCompressed(string $key, callable $callback, int $ttl = self::CACHE_TTL_LONG): mixed
    {
        return $this->get($key, function () use ($callback) {
            $data = $callback();

            // Compress if data is large
            if (is_string($data) && strlen($data) > 1024) {
                return gzcompress($data, 6);
            }

            return $data;
        }, $ttl);
    }

    /**
     * Get compressed data
     */
    public function getCompressed(string $key): mixed
    {
        $data = Cache::get($key);

        if (is_string($data) && substr($data, 0, 2) === "\x1f\x8b") {
            return gzuncompress($data);
        }

        return $data;
    }

    /**
     * Cache with automatic refresh
     */
    public function rememberWithRefresh(string $key, callable $callback, int $ttl = self::CACHE_TTL_MEDIUM, int $refreshThreshold = 300): mixed
    {
        $data = Cache::get($key);
        $metaKey = "{$key}.meta";
        $meta = Cache::get($metaKey);

        // Check if cache needs refresh
        if (!$data || !$meta || (time() - $meta['created_at']) > ($ttl - $refreshThreshold)) {
            // Refresh in background if data exists
            if ($data) {
                dispatch(function () use ($key, $callback, $ttl, $metaKey) {
                    $newData = $callback();
                    Cache::put($key, $newData, $ttl);
                    Cache::put($metaKey, ['created_at' => time()], $ttl);
                });
            } else {
                // Synchronous refresh if no data
                $data = $callback();
                Cache::put($key, $data, $ttl);
                Cache::put($metaKey, ['created_at' => time()], $ttl);
            }
        }

        return $data;
    }

    /**
     * Batch cache operations
     */
    public function rememberMany(array $keys, callable $callback, int $ttl = self::CACHE_TTL_MEDIUM): array
    {
        $results = [];
        $missingKeys = [];

        // Try to get existing cached values
        foreach ($keys as $key) {
            $value = Cache::get($key);
            if ($value !== null) {
                $results[$key] = $value;
            } else {
                $missingKeys[] = $key;
            }
        }

        // Fetch missing values
        if (!empty($missingKeys)) {
            $newValues = $callback($missingKeys);

            foreach ($newValues as $key => $value) {
                Cache::put($key, $value, $ttl);
                $results[$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Cache statistics
     */
    public function getStats(): array
    {
        $stats = [
            'redis_available' => $this->isRedisAvailable(),
            'memcached_available' => $this->isMemcachedAvailable(),
            'default_store' => config('cache.default'),
        ];

        if ($this->isRedisAvailable()) {
            try {
                // Try Predis first
                if (class_exists('Predis\Client')) {
                    $redis = new PredisClient([
                        'host' => config('database.redis.default.host', '127.0.0.1'),
                        'port' => config('database.redis.default.port', 6379),
                        'timeout' => 2,
                    ]);
                    $info = $redis->info();

                    $stats['redis'] = [
                        'client' => 'Predis',
                        'memory_usage' => $info['used_memory_human'] ?? 'Unknown',
                        'connected_clients' => $info['connected_clients'] ?? 0,
                        'total_commands' => $info['total_commands_processed'] ?? 0,
                        'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                        'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                        'hit_rate' => $this->calculateHitRate($info),
                        'total_keys' => $redis->dbsize(),
                    ];
                } else {
                    // Fallback to Redis extension
                    $redis = Redis::connection();
                    $info = $redis->info();

                    $stats['redis'] = [
                        'client' => 'Redis Extension',
                        'memory_usage' => $info['used_memory_human'] ?? 'Unknown',
                        'connected_clients' => $info['connected_clients'] ?? 0,
                        'total_commands' => $info['total_commands_processed'] ?? 0,
                        'keyspace_hits' => $info['keyspace_hits'] ?? 0,
                        'keyspace_misses' => $info['keyspace_misses'] ?? 0,
                        'hit_rate' => $this->calculateHitRate($info),
                        'total_keys' => $redis->dbsize(),
                    ];
                }
            } catch (\Exception $e) {
                $stats['redis_error'] = $e->getMessage();
            }
        } else {
            $stats['redis_error'] = 'Redis not available (neither Predis nor Redis extension)';
        }

        if (!$this->isMemcachedAvailable()) {
            $stats['memcached_error'] = 'Memcached extension not available or not configured';
        }

        return $stats;
    }

    /**
     * Clear all caches
     */
    public function clearAll(): array
    {
        $cleared = [];

        try {
            if ($this->isRedisAvailable()) {
                Cache::store('redis')->flush();
                $cleared[] = 'redis';
            }
        } catch (\Exception $e) {
            Log::error("Redis flush error: " . $e->getMessage());
        }

        try {
            if ($this->isMemcachedAvailable()) {
                Cache::store('memcached')->flush();
                $cleared[] = 'memcached';
            }
        } catch (\Exception $e) {
            Log::error("Memcached flush error: " . $e->getMessage());
        }

        try {
            Cache::store('database')->flush();
            $cleared[] = 'database';
        } catch (\Exception $e) {
            Log::error("Database cache flush error: " . $e->getMessage());
        }

        return $cleared;
    }

    /**
     * Check if Redis is available
     */
    public function isRedisAvailable(): bool
    {
        try {
            // Check if Redis server is actually running
            if (class_exists('Predis\Client')) {
                $redis = new PredisClient([
                    'host' => config('database.redis.default.host', '127.0.0.1'),
                    'port' => config('database.redis.default.port', 6379),
                    'timeout' => 2,
                ]);
                $redis->ping();
                return true;
            }

            return false;
        } catch (\Exception $e) {
            // Redis server is not running or not accessible
            return false;
        }
    }

    /**
     * Check if Memcached is available
     */
    public function isMemcachedAvailable(): bool
    {
        try {
            // Check if Memcached extension is loaded
            if (!extension_loaded('memcached')) {
                return false;
            }

            Cache::store('memcached')->get('test');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Calculate cache hit rate
     */
    private function calculateHitRate(array $info): float
    {
        $hits = $info['keyspace_hits'] ?? 0;
        $misses = $info['keyspace_misses'] ?? 0;
        $total = $hits + $misses;

        return $total > 0 ? round(($hits / $total) * 100, 2) : 0;
    }

    /**
     * Get cache key with prefix
     */
    public function getKey(string $key): string
    {
        $prefix = config('cache.prefix', 'ecommerce-cache-');
        return $prefix . $key;
    }

    /**
     * Cache with lock to prevent cache stampede
     */
    public function rememberWithLock(string $key, callable $callback, int $ttl = self::CACHE_TTL_MEDIUM, int $lockTimeout = 10): mixed
    {
        $lockKey = "lock.{$key}";

        // Try to acquire lock
        if (Cache::store('redis')->add($lockKey, 1, $lockTimeout)) {
            try {
                // Check if data exists after acquiring lock
                $data = Cache::get($key);
                if ($data !== null) {
                    return $data;
                }

                // Generate new data
                $data = $callback();
                Cache::put($key, $data, $ttl);

                return $data;
            } finally {
                // Release lock
                Cache::store('redis')->forget($lockKey);
            }
        } else {
            // Wait for other process to finish
            $attempts = 0;
            while ($attempts < 50) { // 5 seconds max wait
                usleep(100000); // 100ms
                $data = Cache::get($key);
                if ($data !== null) {
                    return $data;
                }
                $attempts++;
            }

            // Fallback to direct execution
            return $callback();
        }
    }
}
