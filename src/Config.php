<?php

namespace Tuezy;

/**
 * Config - Centralized configuration management
 * Refactored from global $config array to improve maintainability
 */
class Config
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
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

