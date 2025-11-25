<?php

namespace Tuezy\Middleware;

/**
 * AuthMiddleware - Middleware for authentication
 */
class AuthMiddleware
{
    private string $loginSessionKey;
    private string $redirectUrl;

    public function __construct(string $loginSessionKey, string $redirectUrl = '')
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
            if ($this->redirectUrl) {
                header('Location: ' . $this->redirectUrl);
                exit;
            }
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        return $next();
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    private function isAuthenticated(): bool
    {
        return !empty($_SESSION[$this->loginSessionKey]['active']);
    }
}

