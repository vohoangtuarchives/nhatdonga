<?php

namespace Tuezy\Middleware;

/**
 * LoggingMiddleware - Middleware for request logging
 */
class LoggingMiddleware
{
    private string $logPath;
    private bool $enabled;

    public function __construct(string $logPath = '', bool $enabled = true)
    {
        $this->logPath = $logPath ?: (defined('LOGS') ? LOGS : 'logs/');
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
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        $result = $next();

        if ($this->enabled) {
            $this->logRequest($startTime, $startMemory);
        }

        return $result;
    }

    /**
     * Log request
     * 
     * @param float $startTime Start time
     * @param int $startMemory Start memory
     */
    private function logRequest(float $startTime, int $startMemory): void
    {
        $logFile = $this->logPath . 'requests-' . date('Y-m-d') . '.txt';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $memoryUsed = round((memory_get_usage() - $startMemory) / 1024 / 1024, 2);

        $message = sprintf(
            "[%s] %s %s - Duration: %sms - Memory: %sMB - IP: %s\n",
            date('Y-m-d H:i:s'),
            $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
            $_SERVER['REQUEST_URI'] ?? 'UNKNOWN',
            $duration,
            $memoryUsed,
            $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN'
        );

        @file_put_contents($logFile, $message, FILE_APPEND);
    }
}

