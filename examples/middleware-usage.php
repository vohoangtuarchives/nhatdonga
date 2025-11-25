<?php
/**
 * Example: How to use Middleware in the application
 * 
 * This file demonstrates how to integrate middleware into the router
 */

require_once __DIR__ . '/../bootstrap/app.php';

use Tuezy\Middleware\MiddlewareStack;
use Tuezy\Middleware\AuthMiddleware;
use Tuezy\Middleware\AdminAuthMiddleware;
use Tuezy\Middleware\LoggingMiddleware;
use Tuezy\Middleware\RateLimitingMiddleware;

// Example 1: Protected API endpoint with authentication
function protectedApiEndpoint()
{
    $stack = new MiddlewareStack();
    
    // Add middleware in order (executed first to last)
    $stack->add(new LoggingMiddleware());
    $stack->add(new RateLimitingMiddleware(100, 60)); // 100 requests per minute
    $stack->add(new AuthMiddleware('login_member', '/account/dang-nhap'));
    
    // Execute with handler
    return $stack->execute(function() {
        // Your API logic here
        return ['data' => 'Protected API response'];
    });
}

// Example 2: Admin endpoint with admin authentication
function adminEndpoint()
{
    $stack = new MiddlewareStack();
    
    $stack->add(new LoggingMiddleware());
    $stack->add(new AdminAuthMiddleware('login_admin'));
    
    return $stack->execute(function() {
        // Your admin logic here
        return ['data' => 'Admin response'];
    });
}

// Example 3: Public endpoint with rate limiting only
function publicApiEndpoint()
{
    $stack = new MiddlewareStack();
    
    $stack->add(new LoggingMiddleware());
    $stack->add(new RateLimitingMiddleware(200, 60)); // 200 requests per minute
    
    return $stack->execute(function() {
        // Your public API logic here
        return ['data' => 'Public API response'];
    });
}

// Example 4: Integration with router
/*
// In libraries/router.php or similar:

$route = $_GET['route'] ?? '';

switch ($route) {
    case 'api/protected':
        $stack = new MiddlewareStack();
        $stack->add(new LoggingMiddleware());
        $stack->add(new RateLimitingMiddleware());
        $stack->add(new AuthMiddleware('login_member'));
        
        $result = $stack->execute(function() {
            // Handle protected API
            return handleProtectedApi();
        });
        
        header('Content-Type: application/json');
        echo json_encode($result);
        break;
        
    case 'admin/dashboard':
        $stack = new MiddlewareStack();
        $stack->add(new LoggingMiddleware());
        $stack->add(new AdminAuthMiddleware('login_admin'));
        
        $stack->execute(function() {
            // Render admin dashboard
            include 'admin/templates/dashboard.php';
        });
        break;
}
*/

