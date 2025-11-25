<?php

namespace Tuezy;

/**
 * ResponseHelper - Handles HTTP responses (redirects, transfers, JSON)
 * Centralizes response handling logic
 */
class ResponseHelper
{
    private $func;
    private string $configBase;

    public function __construct($func, string $configBase)
    {
        $this->func = $func;
        $this->configBase = $configBase;
    }

    /**
     * Redirect to URL
     * 
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code
     */
    public function redirect(string $url, int $statusCode = 302): void
    {
        // If relative URL, prepend configBase
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = $this->configBase . ltrim($url, '/');
        }

        $this->func->redirect($url);
    }

    /**
     * Transfer (redirect with message)
     * 
     * @param string $message Message to display
     * @param string|null $url URL to redirect to (default: configBase)
     * @param bool $success Success status (true = success, false = error)
     */
    public function transfer(string $message, ?string $url = null, bool $success = true): void
    {
        $url = $url ?? $this->configBase;
        $this->func->transfer($message, $url, $success);
    }

    /**
     * Send JSON response
     * 
     * @param array $data Response data
     * @param int $statusCode HTTP status code
     */
    public function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Send JSON success response
     * 
     * @param array $data Response data
     * @param string|null $message Success message
     */
    public function jsonSuccess(array $data = [], ?string $message = null): void
    {
        $response = ['success' => true];
        if ($message) {
            $response['message'] = $message;
        }
        $response = array_merge($response, $data);
        $this->json($response);
    }

    /**
     * Send JSON error response
     * 
     * @param string $message Error message
     * @param array $errors Additional errors
     * @param int $statusCode HTTP status code
     */
    public function jsonError(string $message, array $errors = [], int $statusCode = 400): void
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        $this->json($response, $statusCode);
    }

    /**
     * Send 404 Not Found response
     */
    public function notFound(): void
    {
        header('HTTP/1.0 404 Not Found', true, 404);
        if (file_exists('404.php')) {
            include '404.php';
        } else {
            echo '404 Not Found';
        }
        exit;
    }

    /**
     * Send 403 Forbidden response
     */
    public function forbidden(): void
    {
        http_response_code(403);
        header('HTTP/1.0 403 Forbidden', true, 403);
        echo '403 Forbidden';
        exit;
    }

    /**
     * Send 500 Internal Server Error response
     */
    public function serverError(): void
    {
        http_response_code(500);
        header('HTTP/1.0 500 Internal Server Error', true, 500);
        echo '500 Internal Server Error';
        exit;
    }

    /**
     * Set HTTP header
     * 
     * @param string $name Header name
     * @param string $value Header value
     */
    public function header(string $name, string $value): void
    {
        header("$name: $value");
    }

    /**
     * Set multiple HTTP headers
     * 
     * @param array $headers Headers array ['name' => 'value']
     */
    public function headers(array $headers): void
    {
        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }
    }
}

