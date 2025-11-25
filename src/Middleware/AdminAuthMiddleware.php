<?php

namespace Tuezy\Middleware;

/**
 * AdminAuthMiddleware - Middleware for admin authentication
 */
class AdminAuthMiddleware
{
    private string $loginSessionKey;
    private string $redirectUrl;

    public function __construct(string $loginSessionKey, string $redirectUrl = 'admin/index.php?com=user&act=login')
    {
        $this->loginSessionKey = $loginSessionKey;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Handle middleware
     * 
     * @param callable $next Next middleware or handler
     * @return mixed
     */
    public function handle(callable $next)
    {
        if (!$this->isAuthenticated()) {
            header('Location: ' . $this->redirectUrl);
            exit;
        }

        return $next();
    }

    /**
     * Check if admin is authenticated
     * 
     * @return bool
     */
    private function isAuthenticated(): bool
    {
        return !empty($_SESSION[$this->loginSessionKey]['active']);
    }
}

