<?php

namespace Tuezy;

/**
 * ServiceContainer - Simple dependency injection container
 * Replaces global variables with a centralized service container
 */
class ServiceContainer
{
    private array $services = [];
    private array $singletons = [];

    /**
     * Register a service
     * 
     * @param string $name Service name
     * @param callable|object $service Service factory or instance
     * @param bool $singleton Whether to treat as singleton
     */
    public function register(string $name, $service, bool $singleton = true): void
    {
        $this->services[$name] = $service;
        
        if ($singleton && is_object($service) && !is_callable($service)) {
            $this->singletons[$name] = $service;
        }
    }

    /**
     * Get a service instance
     * 
     * @param string $name Service name
     * @return mixed
     * @throws \RuntimeException If service not found
     */
    public function get(string $name)
    {
        // Return singleton if already instantiated
        if (isset($this->singletons[$name])) {
            return $this->singletons[$name];
        }

        if (!isset($this->services[$name])) {
            throw new \RuntimeException("Service '{$name}' not found in container");
        }

        $service = $this->services[$name];

        // If it's a callable, execute it
        if (is_callable($service)) {
            $instance = $service($this);
        } else {
            $instance = $service;
        }

        // Store as singleton if it's an object
        if (is_object($instance)) {
            $this->singletons[$name] = $instance;
        }

        return $instance;
    }

    /**
     * Check if service is registered
     * 
     * @param string $name Service name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->services[$name]);
    }

    /**
     * Remove a service
     * 
     * @param string $name Service name
     */
    public function remove(string $name): void
    {
        unset($this->services[$name], $this->singletons[$name]);
    }
}

