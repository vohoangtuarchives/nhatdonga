<?php

use Tuezy\Application;

if (!function_exists('bootstrap_context')) {
    /**
     * Khởi tạo ứng dụng với context cụ thể (web/admin/api).
     */
    function bootstrap_context(string $context, array $paths = []): Application
    {
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', dirname(__DIR__));
        }

        if (!defined('APP_CONTEXT')) {
            define('APP_CONTEXT', $context);
        }

        $defaults = [
            'libraries' => BASE_PATH . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR,
            'sources' => BASE_PATH . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR,
            'templates' => BASE_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
            'layout' => 'layout/',
            'thumbs' => 'thumbs',
            'watermark' => 'watermark',
        ];

        $paths = array_merge($defaults, $paths);

        $define = static function (string $name, string $value): void {
            if (!defined($name)) {
                define($name, $value);
            }
        };

        $define('LIBRARIES', $paths['libraries']);
        $define('SOURCES', $paths['sources']);
        $define('TEMPLATE', $paths['templates']);
        $define('LAYOUT', $paths['layout']);
        $define('THUMBS', $paths['thumbs']);
        $define('WATERMARK', $paths['watermark']);

        return require BASE_PATH . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php';
    }
}

