<?php

namespace Tuezy;

/**
 * SessionHelper - Enhanced session management
 * Provides convenient methods for session operations
 */
class SessionHelper
{
    private bool $started = false;

    public function __construct()
    {
        $this->start();
    }

    /**
     * Start session if not already started
     */
    public function start(): void
    {
        if (!$this->started) {
            if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
                session_start();
            }
            $this->started = true;
        }
    }

    /**
     * Get session value
     * 
     * @param string $key Session key
     * @param mixed $default Default value
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set session value
     * 
     * @param string $key Session key
     * @param mixed $value Value
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if session key exists
     * 
     * @param string $key Session key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session key
     * 
     * @param string $key Session key
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Get and remove session value (flash)
     * 
     * @param string $key Session key
     * @param mixed $default Default value
     * @return mixed
     */
    public function pull(string $key, $default = null)
    {
        $value = $this->get($key, $default);
        $this->remove($key);
        return $value;
    }

    /**
     * Set flash message
     * 
     * @param string $key Flash key
     * @param mixed $value Flash value
     */
    public function flash(string $key, $value): void
    {
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Get flash message
     * 
     * @param string $key Flash key
     * @param mixed $default Default value
     * @return mixed
     */
    public function getFlash(string $key, $default = null)
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * Check if flash exists
     * 
     * @param string $key Flash key
     * @return bool
     */
    public function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    /**
     * Clear all flash messages
     */
    public function clearFlash(): void
    {
        unset($_SESSION['_flash']);
    }

    /**
     * Regenerate session ID
     * 
     * @param bool $deleteOldSession Delete old session
     */
    public function regenerate(bool $deleteOldSession = true): void
    {
        session_regenerate_id($deleteOldSession);
    }

    /**
     * Destroy session
     */
    public function destroy(): void
    {
        session_destroy();
        $this->started = false;
    }

    /**
     * Get all session data
     * 
     * @return array
     */
    public function all(): array
    {
        return $_SESSION;
    }

    /**
     * Clear all session data (except flash)
     */
    public function clear(): void
    {
        $flash = $_SESSION['_flash'] ?? null;
        $_SESSION = [];
        if ($flash !== null) {
            $_SESSION['_flash'] = $flash;
        }
    }
}

