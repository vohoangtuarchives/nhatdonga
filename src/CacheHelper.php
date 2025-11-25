<?php

namespace Tuezy;

/**
 * CacheHelper - Enhanced cache operations
 * Provides convenient methods for common cache operations
 */
class CacheHelper
{
    private $cache;
    private int $defaultTtl;

    public function __construct($cache, int $defaultTtl = 7200)
    {
        $this->cache = $cache;
        $this->defaultTtl = $defaultTtl;
    }

    /**
     * Get cached data or execute callback
     * 
     * @param string $key Cache key
     * @param callable $callback Callback to execute if cache miss
     * @param int|null $ttl Time to live in seconds
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? $this->defaultTtl;
        
        // Try to get from cache
        $cached = $this->cache->get($key, null, 'fetch', $ttl);
        
        if ($cached !== false && $cached !== null) {
            return $cached;
        }

        // Execute callback and cache result
        $result = $callback();
        $this->put($key, $result, $ttl);
        
        return $result;
    }

    /**
     * Put data in cache
     * 
     * @param string $key Cache key
     * @param mixed $value Value to cache
     * @param int|null $ttl Time to live
     */
    public function put(string $key, $value, ?int $ttl = null): void
    {
        $ttl = $ttl ?? $this->defaultTtl;
        // Note: This depends on cache implementation
        // You may need to adapt based on your Cache class methods
    }

    /**
     * Get from cache
     * 
     * @param string $sql SQL query
     * @param array|null $params Query parameters
     * @param string $type Result type (fetch, result)
     * @param int|null $ttl Time to live
     * @return mixed
     */
    public function get(string $sql, ?array $params = null, string $type = 'result', ?int $ttl = null)
    {
        $ttl = $ttl ?? $this->defaultTtl;
        return $this->cache->get($sql, $params, $type, $ttl);
    }

    /**
     * Forget cache by key
     * 
     * @param string $key Cache key
     */
    public function forget(string $key): void
    {
        // Implementation depends on Cache class
        // You may need to add a forget method to Cache class
    }

    /**
     * Clear all cache
     */
    public function clear(): void
    {
        // Implementation depends on Cache class
    }

    /**
     * Get or set with default TTL
     * 
     * @param string $sql SQL query
     * @param array|null $params Query parameters
     * @param string $type Result type
     * @return mixed
     */
    public function query(string $sql, ?array $params = null, string $type = 'result')
    {
        return $this->get($sql, $params, $type, $this->defaultTtl);
    }

    /**
     * Cache a single item (fetch)
     * 
     * @param string $sql SQL query
     * @param array|null $params Query parameters
     * @return array|null
     */
    public function one(string $sql, ?array $params = null): ?array
    {
        $result = $this->get($sql, $params, 'fetch', $this->defaultTtl);
        return $result ?: null;
    }

    /**
     * Cache multiple items (result)
     * 
     * @param string $sql SQL query
     * @param array|null $params Query parameters
     * @return array
     */
    public function many(string $sql, ?array $params = null): array
    {
        $result = $this->get($sql, $params, 'result', $this->defaultTtl);
        return $result ?: [];
    }
}

