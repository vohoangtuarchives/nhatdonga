<?php

namespace Tuezy\Middleware;

/**
 * MiddlewareStack - Manages middleware execution
 * Allows chaining multiple middleware
 */
class MiddlewareStack
{
    private array $middlewares = [];

    /**
     * Add middleware to stack
     * 
     * @param Middleware|callable $middleware Middleware instance or callable
     */
    public function add($middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Execute middleware stack
     * 
     * @param mixed $request Request object or data
     * @return mixed Final result
     */
    public function execute($request)
    {
        return $this->executeMiddleware($request, 0);
    }

    /**
     * Execute middleware at index
     * 
     * @param mixed $request Request object
     * @param int $index Current middleware index
     * @return mixed
     */
    private function executeMiddleware($request, int $index)
    {
        if ($index >= count($this->middlewares)) {
            return $request;
        }

        $middleware = $this->middlewares[$index];

        if ($middleware instanceof Middleware) {
            return $middleware->handle($request, function($req) use ($index) {
                return $this->executeMiddleware($req, $index + 1);
            });
        }

        if (is_callable($middleware)) {
            return $middleware($request, function($req) use ($index) {
                return $this->executeMiddleware($req, $index + 1);
            });
        }

        return $request;
    }

    /**
     * Clear all middleware
     */
    public function clear(): void
    {
        $this->middlewares = [];
    }
}

