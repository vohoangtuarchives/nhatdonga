<?php

namespace Tuezy\Helper;

/**
 * FlashHelper - Flash message management
 * Refactored from class.Flash.php
 * 
 * Manages flash messages stored in session for one-time display
 */
class FlashHelper
{
    private const SESSION_KEY = 'flash';

    /**
     * Initialize session if not already started
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialize session
     */
    private function init(): void
    {
        if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
            session_start();
        }
    }

    /**
     * Set flash message
     * 
     * @param string $key Message key
     * @param mixed $value Message value
     */
    public function set(string $key, $value): void
    {
        if (!empty($key) && !empty($value)) {
            $_SESSION[self::SESSION_KEY][$key] = $value;
        }
    }

    /**
     * Get and remove flash message
     * 
     * @param string $key Message key
     * @return mixed|null Message value or null if not found
     */
    public function get(string $key)
    {
        $data = !empty($_SESSION[self::SESSION_KEY][$key]) 
            ? $_SESSION[self::SESSION_KEY][$key] 
            : null;
        
        unset($_SESSION[self::SESSION_KEY][$key]);
        
        return $data;
    }

    /**
     * Check if flash message exists
     * 
     * @param string $key Message key
     * @return bool True if message exists
     */
    public function has(string $key): bool
    {
        return !empty($_SESSION[self::SESSION_KEY][$key]);
    }

    /**
     * Get formatted messages HTML
     * 
     * @param string $type Message type ('admin' or 'frontend')
     * @return string HTML string
     */
    public function getMessages(string $type = ''): string
    {
        if (empty($type)) {
            return '';
        }

        $message = $this->get('message');
        if (empty($message)) {
            return '';
        }

        $result = json_decode(base64_decode($message), true);
        if (empty($result)) {
            return '';
        }

        $class = 'info';
        if (!empty($result['status'])) {
            $class = $result['status'] === 'danger' ? 'danger' : 'info';
        }

        if (!empty($result['messages'])) {
            return $this->messagesHtml($result['messages'], $class, $type);
        }

        return '';
    }

    /**
     * Generate messages HTML
     * 
     * @param array $messages Array of message strings
     * @param string $class CSS class (danger, info, etc.)
     * @param string $type Message type ('admin' or 'frontend')
     * @return string HTML string
     */
    private function messagesHtml(array $messages, string $class, string $type): string
    {
        if (empty($messages) || empty($class) || empty($type)) {
            return '';
        }

        $str = '';

        if ($type === 'admin') {
            $str .= '<div class="card bg-gradient-' . htmlspecialchars($class) . '">';
            $str .= '<div class="card-header">';
            $str .= '<h3 class="card-title">Thông báo</h3>';
            $str .= '<div class="card-tools">';
            $str .= '<button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>';
            $str .= '<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>';
            $str .= '</div>';
            $str .= '</div>';
            $str .= '<div class="card-body">';
            
            foreach ($messages as $v) {
                $str .= '<p class="mb-1">- ' . htmlspecialchars($v) . '</p>';
            }
            
            $str .= '</div>';
            $str .= '</div>';
        } elseif ($type === 'frontend') {
            $str .= '<div class="alert alert-' . htmlspecialchars($class) . '">';
            
            foreach ($messages as $v) {
                $str .= '<p class="mb-1">- ' . htmlspecialchars($v) . '</p>';
            }
            
            $str .= '</div>';
        }

        return $str;
    }
}

