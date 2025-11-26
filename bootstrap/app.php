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

$composerAutoload = BASE_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

require_once LIBRARIES . 'config.php';
require_once LIBRARIES . 'autoload.php';

$refactoredAutoload = LIBRARIES . 'autoload-refactored.php';
if (file_exists($refactoredAutoload)) {
    require_once $refactoredAutoload;
}

if (class_exists('AutoLoad')) {
    new AutoLoad();
}

if (class_exists('AutoLoadRefactored')) {
    new AutoLoadRefactored();
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
foreach ($app->getGlobals() as $name => $service) {
    if (!array_key_exists($name, $GLOBALS)) {
        $GLOBALS[$name] = $service;
    }
}

return $app;

