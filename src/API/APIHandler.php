<?php

namespace Tuezy\API;

use Tuezy\Config;

/**
 * APIHandler - Base class for API endpoints
 * Provides common functionality for all API endpoints
 */
abstract class APIHandler
{
    protected $d;
    protected $cache;
    protected $func;
    protected $custom;
    protected Config $config;
    protected string $lang;
    protected string $sluglang;
    protected array $setting;

    public function __construct($d, $cache, $func, $custom, Config $config, string $lang, string $sluglang, array $setting)
    {
        $this->d = $d;
        $this->cache = $cache;
        $this->func = $func;
        $this->custom = $custom;
        $this->config = $config;
        $this->lang = $lang;
        $this->sluglang = $sluglang;
        $this->setting = $setting;
    }

    /**
     * Get POST parameter
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed
     */
    protected function post(string $key, $default = '')
    {
        return !empty($_POST[$key]) ? htmlspecialchars($_POST[$key], ENT_QUOTES, 'UTF-8') : $default;
    }

    /**
     * Get GET parameter
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed
     */
    protected function get(string $key, $default = '')
    {
        return !empty($_GET[$key]) ? htmlspecialchars($_GET[$key], ENT_QUOTES, 'UTF-8') : $default;
    }

    /**
     * Send JSON response
     * 
     * @param array $data Response data
     * @param int $statusCode HTTP status code
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
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
        $this->jsonResponse(['error' => $message], $statusCode);
    }

    /**
     * Send success response
     * 
     * @param array $data Response data
     */
    protected function success(array $data): void
    {
        $this->jsonResponse($data);
    }

    /**
     * Handle API request - must be implemented by child classes
     */
    abstract public function handle(): void;
}

