<?php

namespace Tuezy;

/**
 * Config - Centralized configuration management
 * Refactored from global $config array to improve maintainability
 * Supports loading from .env file and config/app.php
 */
class Config
{
    private array $config;
    private static ?array $env = null;

    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $this->loadConfig();
        } else {
            $this->config = $config;
        }
    }

    /**
     * Load configuration from config/app.php and .env
     */
    private function loadConfig(): void
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__);
        
        // Load .env file if exists (use bootstrap helper if available)
        if (function_exists('loadEnv')) {
            loadEnv($basePath);
        } else {
            $this->loadEnv($basePath);
        }
        
        // Load config/app.php
        $configFile = $basePath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        } else {
            // Fallback to libraries/config.php for backward compatibility
            $legacyConfigFile = $basePath . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'config.php';
            if (file_exists($legacyConfigFile)) {
                // This will set $config global variable
                require $legacyConfigFile;
                $this->config = $GLOBALS['config'] ?? [];
            } else {
                $this->config = [];
            }
        }
    }

    /**
     * Load environment variables from .env file
     * 
     * @param string $basePath Base path of application
     */
    private function loadEnv(string $basePath): void
    {
        if (self::$env !== null) {
            return; // Already loaded
        }

        $envFile = $basePath . DIRECTORY_SEPARATOR . '.env';
        self::$env = [];

        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                // Parse KEY=VALUE
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Remove quotes if present
                    if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                        (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                        $value = substr($value, 1, -1);
                    }
                    
                    if (!array_key_exists($key, $_ENV)) {
                        $_ENV[$key] = $value;
                    }
                    if (!array_key_exists($key, $_SERVER)) {
                        $_SERVER[$key] = $value;
                    }
                    self::$env[$key] = $value;
                }
            }
        }
    }

    /**
     * Get environment variable
     * 
     * @param string $key Environment key
     * @param mixed $default Default value
     * @return mixed
     */
    public static function env(string $key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    /**
     * Get configuration value by key path (dot notation)
     * 
     * @param string $key Configuration key (e.g., 'website.debug-css')
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Set configuration value
     * 
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     */
    public function set(string $key, $value): void
    {
        $keys = explode('.', $key);
        $config = &$this->config;

        foreach ($keys as $k) {
            if (!isset($config[$k]) || !is_array($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }

    /**
     * Get all configuration
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * Check if configuration key exists
     * 
     * @param string $key Configuration key
     * @return bool
     */
    public function has(string $key): bool
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return false;
            }
            $value = $value[$k];
        }

        return true;
    }
}

