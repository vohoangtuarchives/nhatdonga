<?php

namespace Tuezy;

/**
 * RequestHandler - Handles HTTP request parameters with sanitization
 * Refactored to improve code quality while maintaining exact functionality
 */
class RequestHandler
{
    /**
     * Get and sanitize request parameters
     * 
     * @return array<string, string> Sanitized request parameters
     */
    public static function getParams(): array
    {
        return [
            'com' => self::sanitizeRequest('com'),
            'act' => self::sanitizeRequest('act'),
            'type' => self::sanitizeRequest('type'),
            'kind' => self::sanitizeRequest('kind'),
            'val' => self::sanitizeRequest('val'),
            'variant' => self::sanitizeGet('variant'),
            'id_parent' => self::sanitizeRequest('id_parent'),
            'id' => self::sanitizeRequest('id'),
            'curPage' => self::sanitizeGet('p', '1')
        ];
    }

    /**
     * Sanitize REQUEST parameter
     * 
     * @param string $key Parameter key
     * @param string $default Default value if empty
     * @return string Sanitized value
     */
    private static function sanitizeRequest(string $key, string $default = ''): string
    {
        return !empty($_REQUEST[$key]) ? htmlspecialchars($_REQUEST[$key], ENT_QUOTES, 'UTF-8') : $default;
    }

    /**
     * Sanitize GET parameter
     * 
     * @param string $key Parameter key
     * @param string $default Default value if empty
     * @return string Sanitized value
     */
    private static function sanitizeGet(string $key, string $default = ''): string
    {
        return !empty($_GET[$key]) ? htmlspecialchars($_GET[$key], ENT_QUOTES, 'UTF-8') : $default;
    }
}
