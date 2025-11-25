<?php

namespace Tuezy\Middleware;

/**
 * RateLimitingMiddleware - Middleware for rate limiting
 * Prevents abuse by limiting requests per IP address
 */
class RateLimitingMiddleware
{
    private int $maxRequests;
    private int $timeWindow; // in seconds
    private string $cacheDir;
    private bool $enabled;

    public function __construct(int $maxRequests = 100, int $timeWindow = 60, string $cacheDir = '', bool $enabled = true)
    {
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
        $this->cacheDir = $cacheDir ?: (defined('CACHE') ? CACHE : 'cache/');
        $this->enabled = $enabled;
    }

    /**
     * Handle middleware
     * 
     * @param callable $next Next middleware or handler
     * @return mixed
     */
    public function handle(callable $next)
    {
        if (!$this->enabled) {
            return $next();
        }

        $ip = $this->getClientIp();
        $key = $this->getCacheKey($ip);

        if (!$this->checkRateLimit($key)) {
            $this->sendRateLimitExceeded();
        }

        $this->incrementRequest($key);

        return $next();
    }

    /**
     * Get client IP address
     * 
     * @return string
     */
    private function getClientIp(): string
    {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get cache key for IP
     * 
     * @param string $ip IP address
     * @return string
     */
    private function getCacheKey(string $ip): string
    {
        $timeSlot = floor(time() / $this->timeWindow);
        return 'ratelimit_' . md5($ip . $timeSlot);
    }

    /**
     * Check rate limit
     * 
     * @param string $key Cache key
     * @return bool
     */
    private function checkRateLimit(string $key): bool
    {
        $file = $this->cacheDir . $key . '.txt';
        
        if (!file_exists($file)) {
            return true;
        }

        $data = @file_get_contents($file);
        if ($data === false) {
            return true;
        }

        $count = (int)$data;
        return $count < $this->maxRequests;
    }

    /**
     * Increment request count
     * 
     * @param string $key Cache key
     */
    private function incrementRequest(string $key): void
    {
        $file = $this->cacheDir . $key . '.txt';
        
        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0755, true);
        }

        $count = 1;
        if (file_exists($file)) {
            $count = (int)@file_get_contents($file) + 1;
        }

        @file_put_contents($file, (string)$count);
    }

    /**
     * Send rate limit exceeded response
     */
    private function sendRateLimitExceeded(): void
    {
        http_response_code(429);
        header('Content-Type: application/json');
        header('Retry-After: ' . $this->timeWindow);
        
        echo json_encode([
            'success' => false,
            'error' => 'Too many requests. Please try again later.',
            'retry_after' => $this->timeWindow
        ]);
        
        exit;
    }

    /**
     * Clean old rate limit files
     * 
     * @param int $maxAge Maximum age in seconds
     */
    public function cleanOldFiles(int $maxAge = 3600): void
    {
        if (!is_dir($this->cacheDir)) {
            return;
        }

        $files = glob($this->cacheDir . 'ratelimit_*.txt');
        $now = time();

        foreach ($files as $file) {
            if (filemtime($file) < ($now - $maxAge)) {
                @unlink($file);
            }
        }
    }
}

