<?php

namespace Tuezy\API\Controller;

use Tuezy\Config;
use Tuezy\SecurityHelper;

/**
 * BaseAPIController - Base controller for all API endpoints
 * Provides common API functionality
 */
abstract class BaseAPIController
{
    protected $db;
    protected $cache;
    protected $func;
    protected Config $config;
    protected string $lang;
    protected string $sluglang;

    public function __construct($db, $cache, $func, Config $config, string $lang = 'vi', string $sluglang = 'slugvi')
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->func = $func;
        $this->config = $config;
        $this->lang = $lang;
        $this->sluglang = $sluglang;
    }

    /**
     * Get POST parameter
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed
     */
    protected function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET parameter
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed
     */
    protected function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Send JSON response
     * 
     * @param array $data Response data
     * @param int $statusCode HTTP status code
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Send error response
     * 
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     */
    protected function error(string $message, int $statusCode = 400): void
    {
        $this->json(['error' => $message, 'success' => false], $statusCode);
    }

    /**
     * Send success response
     * 
     * @param array $data Response data
     */
    protected function success(array $data): void
    {
        $this->json(array_merge(['success' => true], $data));
    }

    /**
     * Validate required parameters
     * 
     * @param array $params Parameters to validate
     * @param array $required Required parameter keys
     * @return bool True if all required parameters are present
     */
    protected function validateRequired(array $params, array $required): bool
    {
        foreach ($required as $key) {
            if (!isset($params[$key]) || empty($params[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Sanitize input
     * 
     * @param mixed $value Input value
     * @return mixed Sanitized value
     */
    protected function sanitize($value)
    {
        if (is_string($value)) {
            return SecurityHelper::sanitize($value);
        } elseif (is_array($value)) {
            return SecurityHelper::sanitizeArray($value);
        }
        return $value;
    }
}

