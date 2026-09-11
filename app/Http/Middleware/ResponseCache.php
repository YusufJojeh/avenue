<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class ResponseCache
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }
        
        // Skip caching for authenticated users
        if ($request->user()) {
            return $next($request);
        }
        
        // Skip caching for admin routes
        if ($request->is('admin/*') || $request->is('platform/*')) {
            return $next($request);
        }

        // Skip caching for language toggle/switch routes
        if ($request->is('language/*')) {
            return $next($request);
        }
        
        // Generate cache key
        $cacheKey = $this->generateCacheKey($request);
        
        // Try to get cached response
        $cachedResponse = Cache::get($cacheKey);
        if ($cachedResponse) {
            $response = response($cachedResponse['content'])
                ->setStatusCode($cachedResponse['status_code']);

            // Never replay Set-Cookie headers from cache (breaks locale/session switching).
            $headers = $cachedResponse['headers'] ?? [];
            if (is_array($headers)) {
                unset($headers['set-cookie'], $headers['Set-Cookie']);
                $response->withHeaders($headers);
            }

            // Add cache headers (ETag/Expires) for cached responses too.
            $this->addCacheHeaders($response, $request);

            return $response;
        }
        
        // Process request
        $response = $next($request);
        
        // Cache successful responses
        if ($response->getStatusCode() === 200) {
            $this->cacheResponse($cacheKey, $response);
        }
        
        // Add cache headers
        $this->addCacheHeaders($response, $request);
        
        return $response;
    }
    
    /**
     * Generate cache key for request
     */
    private function generateCacheKey(Request $request): string
    {
        // Cache must vary by locale, otherwise language switching appears broken.
        $key = 'response_cache.' . app()->getLocale() . '.' . md5($request->fullUrl());
        
        // Add route name if available
        if ($routeName = Route::currentRouteName()) {
            $key .= '.' . $routeName;
        }
        
        return $key;
    }
    
    /**
     * Cache response
     */
    private function cacheResponse(string $cacheKey, Response $response): void
    {
        $ttl = $this->getCacheTTL($response);
        
        if ($ttl > 0) {
            $headers = $response->headers->all();
            unset($headers['set-cookie'], $headers['Set-Cookie']);

            $cachedData = [
                'content' => $response->getContent(),
                'headers' => $headers,
                'status_code' => $response->getStatusCode(),
                'cached_at' => now()->timestamp,
            ];
            
            Cache::put($cacheKey, $cachedData, $ttl);
        }
    }
    
    /**
     * Get cache TTL based on response
     */
    private function getCacheTTL(Response $response): int
    {
        $routeName = Route::currentRouteName();
        
        // Different TTL for different types of content
        switch ($routeName) {
            case 'home':
                return 3600; // 1 hour for home page
                
            case 'products.index':
            case 'products.show':
                return 1800; // 30 minutes for product pages
                
            case 'categories.index':
            case 'categories.show':
                return 3600; // 1 hour for category pages
                
            case 'brands.index':
            case 'brands.show':
                return 3600; // 1 hour for brand pages
                
            default:
                // Check if it's a static page
                if (str_contains($response->getContent(), 'static-content')) {
                    return 7200; // 2 hours for static content
                }
                
                return 900; // 15 minutes default
        }
    }
    
    /**
     * Add cache headers to response
     */
    private function addCacheHeaders(Response $response, Request $request): void
    {
        $ttl = $this->getCacheTTL($response);
        
        if ($ttl > 0) {
            // Add cache control headers
            $response->headers->set('Cache-Control', "public, max-age={$ttl}");
            $response->headers->set('Expires', gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT');
            $response->headers->set('Vary', 'Accept-Language, Cookie');
            
            // Add ETag for conditional requests
            $etag = md5($response->getContent());
            $response->headers->set('ETag', $etag);
            
            // Check if client has cached version
            if ($request->headers->get('If-None-Match') === $etag) {
                $response->setStatusCode(304);
                $response->setContent('');
            }
        }
    }
}
