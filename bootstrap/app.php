<?php

use Tuezy\Application;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

if (!defined('APP_CONTEXT')) {
    define('APP_CONTEXT', 'web');
}

if (!defined('LIBRARIES')) {
    define('LIBRARIES', BASE_PATH . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR);
}

if (!defined('SOURCES')) {
    define('SOURCES', BASE_PATH . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR);
}

if (!defined('LAYOUT')) {
    define('LAYOUT', 'layout/');
}

if (!defined('THUMBS')) {
    define('THUMBS', 'thumbs');
}

if (!defined('WATERMARK')) {
    define('WATERMARK', 'watermark');
}

// Load helpers TRƯỚC autoloader để đảm bảo function env() local được định nghĩa trước
require_once BASE_PATH . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'helpers.php';
loadEnv(BASE_PATH);

$composerAutoload = BASE_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
    if (!defined('COMPOSER_AUTOLOADED')) {
        define('COMPOSER_AUTOLOADED', true);
    }
    $aliases = BASE_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Libraries' . DIRECTORY_SEPARATOR . 'aliases.php';
    if (file_exists($aliases)) {
        require_once $aliases;
    }
}

require_once LIBRARIES . 'config.php';

if (!defined('COMPOSER_AUTOLOADED')) {
    require_once LIBRARIES . 'autoload.php';
    // AutoLoad now supports both old classes and new namespace classes
    // AutoLoadRefactored is now just an alias, no need to load separately
    if (class_exists('AutoLoad')) {
        new AutoLoad();
    }
}

require_once BASE_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Application.php';
require_once BASE_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Context.php';
require_once BASE_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Helper' . DIRECTORY_SEPARATOR . 'GlobalHelper.php';

$app = new Application($config);

// Initialize Context for dependency management
use Tuezy\Context;
$context = new Context($app);
Context::setInstance($context);

// Set globals for backward compatibility (will be phased out gradually)
// Đảm bảo các biến global luôn được khởi tạo lại trong mỗi request
foreach ($app->getGlobals() as $name => $service) {
    $GLOBALS[$name] = $service;
}

return $app;

