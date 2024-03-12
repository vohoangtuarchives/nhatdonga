<?php

namespace NN;

use Closure;
use Illuminate\Cache\CacheManager;

class Storage
{
    protected CacheManager $cache;

    public function __construct($cache)
    {
        $this->cache = $cache;
    }

    public function put($key, $value, $ttl = null)
    {
        return $this->cache->put($key, $value, $ttl);
    }

    public function get($key)
    {
        return $this->cache->get($key);
    }

    public function remember($key, $ttl, Closure $callback)
    {
        return $this->cache->remember($key, $ttl, $callback);
    }

    public function clear()
    {
        return $this->cache->clear();
    }
}

