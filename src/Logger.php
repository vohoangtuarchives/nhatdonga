<?php

namespace Tuezy;

/**
 * Logger - Simple logging utility
 * Provides logging functionality for the application
 */
class Logger
{
    private string $logPath;
    private string $logFile;
    private bool $enabled;

    public function __construct(string $logPath = 'logs', bool $enabled = true)
    {
        $this->logPath = rtrim($logPath, '/');
        $this->logFile = $this->logPath . '/' . date('d-m-Y') . '.txt';
        $this->enabled = $enabled;

        // Create log directory if it doesn't exist
        if ($this->enabled && !is_dir($this->logPath)) {
            mkdir($this->logPath, 0777, true);
        }
    }

    /**
     * Log info message
     * 
     * @param string $message Message
     * @param array $context Additional context
     */
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }

    /**
     * Log warning message
     * 
     * @param string $message Message
     * @param array $context Additional context
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }

    /**
     * Log error message
     * 
     * @param string $message Message
     * @param array $context Additional context
     */
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }

    /**
     * Log debug message
     * 
     * @param string $message Message
     * @param array $context Additional context
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }

    /**
     * Write log entry
     * 
     * @param string $level Log level
     * @param string $message Message
     * @param array $context Additional context
     */
    private function log(string $level, string $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logEntry = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;

        file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log exception
     * 
     * @param \Throwable $exception Exception
     * @param array $context Additional context
     */
    public function exception(\Throwable $exception, array $context = []): void
    {
        $message = $exception->getMessage();
        $context['file'] = $exception->getFile();
        $context['line'] = $exception->getLine();
        $context['trace'] = $exception->getTraceAsString();
        
        $this->error($message, $context);
    }

    /**
     * Enable logging
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * Disable logging
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Check if logging is enabled
     * 
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}

