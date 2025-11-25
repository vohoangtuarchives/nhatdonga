<?php

namespace Tuezy\Middleware;

/**
 * Middleware - Base middleware interface
 * All middleware classes should implement this
 */
interface Middleware
{
    /**
     * Handle middleware
     * 
     * @param mixed $request Request object or data
     * @param callable $next Next middleware
     * @return mixed
     */
    public function handle($request, callable $next);
}

