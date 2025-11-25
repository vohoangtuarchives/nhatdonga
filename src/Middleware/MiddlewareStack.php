<?php

namespace Tuezy\Middleware;

/**
 * MiddlewareStack - Manages and executes middleware stack
 */
class MiddlewareStack
{
    private array $middlewares = [];

    /**
     * Add middleware to stack
     * 
     * @param object $middleware Middleware instance
     * @return self
     */
    public function add(object $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Execute middleware stack
     * 
     * @param callable $handler Final handler
     * @return mixed
     */
    public function execute(callable $handler)
    {
        $next = $handler;

        // Build middleware chain in reverse order
        for ($i = count($this->middlewares) - 1; $i >= 0; $i--) {
            $middleware = $this->middlewares[$i];
            $next = function () use ($middleware, $next) {
                return $middleware->handle($next);
            };
        }

        return $next();
    }

    /**
     * Clear middleware stack
     */
    public function clear(): void
    {
        $this->middlewares = [];
    }
}
