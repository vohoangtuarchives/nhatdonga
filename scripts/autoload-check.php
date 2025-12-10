<?php
// Simple autoload verification for legacy libraries classes

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('LIBRARIES')) {
    define('LIBRARIES', BASE_PATH . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR);
}

require BASE_PATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$aliases = BASE_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Libraries' . DIRECTORY_SEPARATOR . 'aliases.php';
if (file_exists($aliases)) {
    require $aliases;
}

$ok = true;

// Check legacy class autoload via Composer classmap and aliases
if (!class_exists('Functions')) { $ok = false; }
if (!class_exists('Email')) { $ok = false; }
if (!class_exists('Seo')) { $ok = false; }
if (!class_exists('Cache')) { $ok = false; }
if (!class_exists('Cart')) { $ok = false; }
if (!class_exists('BreadCrumbs')) { $ok = false; }
if (!class_exists('Statistic')) { $ok = false; }
if (!class_exists('PaginationsAjax')) { $ok = false; }
if (!class_exists('PDODb')) { $ok = false; }
if (!class_exists('JsMinify')) { $ok = false; }
if (!class_exists('CssMinify')) { $ok = false; }
if (!class_exists('MobileDetect')) { $ok = false; }
if (!class_exists('Comments')) { $ok = false; }

// Verify migrated namespaced classes
if (!class_exists('Tuezy\Libraries\Functions')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\Email')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\Seo')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\Cache')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\Cart')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\BreadCrumbs')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\Statistic')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\PaginationsAjax')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\PDODb')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\JsMinify')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\CssMinify')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\MobileDetect')) { $ok = false; }
if (!class_exists('Tuezy\Libraries\Comments')) { $ok = false; }

echo $ok ? "Autoload OK\n" : "Autoload FAIL\n";
exit($ok ? 0 : 1);
