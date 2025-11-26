<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;
use Tuezy\Admin\AdminCRUDHelper;
use Tuezy\Admin\AdminURLHelper;

/**
 * BaseAdminController - Base controller for all admin controllers
 * Provides common admin functionality and dependency injection
 */
abstract class BaseAdminController
{
    protected $db;
    protected $cache;
    protected $func;
    protected array $config;
    protected AdminAuthHelper $authHelper;
    protected AdminPermissionHelper $permissionHelper;
    protected ?AdminCRUDHelper $crudHelper = null;
    protected AdminURLHelper $urlHelper;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper
    ) {
        $this->db = $db;
        $this->cache = $cache;
        $this->func = $func;
        $this->config = $config;
        $this->authHelper = $authHelper;
        $this->permissionHelper = $permissionHelper;
        $this->urlHelper = new AdminURLHelper($config['database']['url'] ?? '');
    }

    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    protected function checkAuth(): bool
    {
        // Chỉ cần kiểm tra isLoggedIn, checkLogin() có thể quá strict
        return $this->authHelper->isLoggedIn();
    }

    /**
     * Check if user has permission
     * 
     * @param string $permission Permission name
     * @return bool
     */
    protected function hasPermission(string $permission): bool
    {
        return $this->permissionHelper->hasPermission($permission);
    }

    /**
     * Require authentication
     * 
     * @throws \RuntimeException If not authenticated
     */
    protected function requireAuth(): void
    {
        if (!$this->checkAuth()) {
            // Redirect to login page, not index.php
            $this->redirect($this->config['database']['url'] . ADMIN . '/index.php?com=user&act=login');
        }
    }

    /**
     * Require permission
     * 
     * @param string $permission Permission name
     * @throws \RuntimeException If no permission
     */
    protected function requirePermission(string $permission): void
    {
        if (!$this->hasPermission($permission)) {
            throw new \RuntimeException("Permission denied: $permission");
        }
    }

    /**
     * Get request parameter
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed
     */
    protected function getParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $_POST[$key] ?? $_REQUEST[$key] ?? $default;
    }

    /**
     * Redirect to URL
     * 
     * @param string $url URL to redirect to
     * @param int $code HTTP status code
     */
    protected function redirect(string $url, int $code = 302): void
    {
        header("Location: $url", true, $code);
        exit;
    }

    /**
     * Return JSON response
     * 
     * @param array $data Data to return
     * @param int $code HTTP status code
     */
    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get CRUD helper
     * 
     * @return AdminCRUDHelper
     */
    protected function getCRUDHelper(): AdminCRUDHelper
    {
        return $this->crudHelper;
    }

    /**
     * Get URL helper
     * 
     * @return AdminURLHelper
     */
    protected function getURLHelper(): AdminURLHelper
    {
        return $this->urlHelper;
    }
}

