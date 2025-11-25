<?php

namespace Tuezy\Admin;

use Tuezy\Config;
use Tuezy\ResponseHelper;
use Tuezy\SecurityHelper;

/**
 * AdminController - Base controller for admin panel
 * Provides common functionality for all admin controllers
 */
abstract class AdminController
{
    protected $d;
    protected $func;
    protected $flash;
    protected $cache;
    protected Config $config;
    protected ResponseHelper $response;
    protected string $com;
    protected string $act;
    protected string $type;
    protected string $template;
    protected array $configType;

    public function __construct($d, $func, $flash, $cache, Config $config, string $com, string $act, string $type, array $configType)
    {
        $this->d = $d;
        $this->func = $func;
        $this->flash = $flash;
        $this->cache = $cache;
        $this->config = $config;
        $this->com = $com;
        $this->act = $act;
        $this->type = $type;
        $this->configType = $configType;
        
        $configBase = $this->config->get('database.url', '/');
        $this->response = new ResponseHelper($func, $configBase);
    }

    /**
     * Handle request - main entry point
     * 
     * @return string|null Template name or null
     */
    public function handle(): ?string
    {
        // Check if module is active
        if (!$this->checkModuleActive()) {
            $this->response->transfer("Trang không tồn tại", "index.php", false);
            return null;
        }

        // Route to action
        return $this->route();
    }

    /**
     * Route to appropriate action
     * Must be implemented by child classes
     * 
     * @return string|null Template name
     */
    abstract protected function route(): ?string;

    /**
     * Check if module is active in config
     * 
     * @return bool
     */
    protected function checkModuleActive(): bool
    {
        $moduleConfig = $this->config->get($this->com);
        if (empty($moduleConfig)) {
            return false;
        }

        $arrCheck = array_keys($moduleConfig);
        return in_array($this->type, $arrCheck);
    }

    /**
     * Get POST data
     * 
     * @param string $key Data key
     * @param mixed $default Default value
     * @return mixed
     */
    protected function post(string $key, $default = '')
    {
        return SecurityHelper::sanitizePost($key, $default);
    }

    /**
     * Get GET data
     * 
     * @param string $key Data key
     * @param mixed $default Default value
     * @return mixed
     */
    protected function get(string $key, $default = '')
    {
        return SecurityHelper::sanitizeGet($key, $default);
    }

    /**
     * Get REQUEST data
     * 
     * @param string $key Data key
     * @param mixed $default Default value
     * @return mixed
     */
    protected function request(string $key, $default = '')
    {
        return SecurityHelper::sanitizeRequest($key, $default);
    }

    /**
     * Transfer (redirect with message)
     * 
     * @param string $message Message
     * @param string $url URL
     * @param bool $success Success status
     */
    protected function transfer(string $message, string $url, bool $success = true): void
    {
        $this->response->transfer($message, $url, $success);
    }

    /**
     * Redirect
     * 
     * @param string $url URL
     */
    protected function redirect(string $url): void
    {
        $this->response->redirect($url);
    }
}

