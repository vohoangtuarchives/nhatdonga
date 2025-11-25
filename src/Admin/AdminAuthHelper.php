<?php

namespace Tuezy\Admin;

use Tuezy\SecurityHelper;

/**
 * AdminAuthHelper - Authentication and authorization for admin
 * Centralizes admin authentication logic
 */
class AdminAuthHelper
{
    private $func;
    private $d;
    private string $loginAdmin;
    private array $config;

    public function __construct($func, $d, string $loginAdmin, array $config)
    {
        $this->func = $func;
        $this->d = $d;
        $this->loginAdmin = $loginAdmin;
        $this->config = $config;
    }

    /**
     * Check if admin is logged in
     * 
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return !empty($_SESSION[$this->loginAdmin]['active']);
    }

    /**
     * Check admin login (with session validation)
     * 
     * @return bool
     */
    public function checkLogin(): bool
    {
        return $this->func->checkLoginAdmin();
    }

    /**
     * Get current admin user ID
     * 
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $_SESSION[$this->loginAdmin]['id'] ?? null;
    }

    /**
     * Get current admin user data
     * 
     * @return array|null
     */
    public function getUser(): ?array
    {
        $userId = $this->getUserId();
        if (!$userId) {
            return null;
        }

        return $this->d->rawQueryOne(
            "SELECT * FROM #_user WHERE id = ? LIMIT 0,1",
            [$userId]
        );
    }

    /**
     * Check user role/permission
     * 
     * @return bool True if user has permission
     */
    public function checkRole(): bool
    {
        return $this->func->checkRole();
    }

    /**
     * Check if user has permission for action
     * 
     * @param string $action Action name
     * @return bool
     */
    public function hasPermission(string $action): bool
    {
        // Check if permission system is active
        if (empty($this->config['permission']['active'])) {
            return true; // No permission system, allow all
        }

        // Check role
        if ($this->checkRole()) {
            return false; // User doesn't have role
        }

        // Additional permission checks can be added here
        return true;
    }

    /**
     * Require login - redirect if not logged in
     */
    public function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            $this->func->redirect("index.php?com=user&act=login");
            exit;
        }
    }

    /**
     * Require permission - redirect if no permission
     * 
     * @param string $action Action name
     */
    public function requirePermission(string $action): void
    {
        if (!$this->hasPermission($action)) {
            $this->func->transfer("Bạn không có quyền truy cập vào khu vực này", "index.php", false);
            exit;
        }
    }

    /**
     * Login user
     * 
     * @param array $userData User data
     */
    public function login(array $userData): void
    {
        $_SESSION[$this->loginAdmin] = [
            'active' => true,
            'id' => $userData['id'],
            'username' => $userData['username'],
            'login_session' => md5(sha1($userData['password'] . $userData['username'])),
            'login_token' => md5(time()),
        ];
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        unset($_SESSION[$this->loginAdmin]);
        if (isset($_SESSION[TOKEN])) {
            unset($_SESSION[TOKEN]);
        }
    }

    /**
     * Validate session
     * 
     * @return bool True if session is valid
     */
    public function validateSession(): bool
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userId = $this->getUserId();
        $timenow = time();

        $row = $this->d->rawQueryOne(
            "SELECT username, password, lastlogin, user_token FROM #_user WHERE id = ? LIMIT 0,1",
            [$userId]
        );

        if (empty($row)) {
            return false;
        }

        $sessionhash = md5(sha1($row['password'] . $row['username']));

        // Check session hash, timeout, and token
        if ($_SESSION[$this->loginAdmin]['login_session'] != $sessionhash ||
            ($timenow - $row['lastlogin']) > 3600 ||
            !isset($_SESSION[TOKEN])) {
            return false;
        }

        // Check login token
        if ($_SESSION[$this->loginAdmin]['login_token'] !== $row['user_token']) {
            return false; // Someone else is logged in
        }

        // Update session
        $token = md5(time());
        $_SESSION[$this->loginAdmin]['login_token'] = $token;
        $this->d->rawQuery(
            "UPDATE #_user SET lastlogin = ?, user_token = ? WHERE id = ?",
            [$timenow, $token, $userId]
        );

        return true;
    }
}

