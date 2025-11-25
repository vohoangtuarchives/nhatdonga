<?php

if (!defined('LIBRARIES')) die("Error");

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'helpers.php';
loadEnv(BASE_PATH);

$config = require BASE_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';

/* Timezone */
date_default_timezone_set($config['timezone'] ?? 'Asia/Ho_Chi_Minh');

/* Cấu hình coder */
$metadata = $config['metadata']['author'] ?? [];
define('NN_CONTRACT', $config['metadata']['contract'] ?? 'contract');
define('NN_AUTHOR', $metadata['email'] ?? 'support@example.com');

/* Error reporting */
error_reporting(!empty($config['website']['error-reporting']) ? E_ALL : 0);

/* Cấu hình http */
if (
    (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
) {
    $http = 'https://';
} else {
    $http = 'http://';
}

/* Redirect http/https */
if (!count($config['arrayDomainSSL']) && $http == 'https://') {
    $host = $_SERVER['HTTP_HOST'];
    $request_uri = $_SERVER['REQUEST_URI'];
    $good_url = "http://" . $host . $request_uri;
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: $good_url");
    exit;
}

/* CheckSSL */
if (count($config['arrayDomainSSL'])) {
    include LIBRARIES . "checkSSL.php";
}

/* Cấu hình base */
$configUrl = $config['database']['server-name'] . $config['database']['url'];
$config_base = $configBase = $http . $configUrl;

/* Token */
define('TOKEN', md5(NN_CONTRACT . $config['database']['url']));

/* Path */
define('ROOT', str_replace(basename(__DIR__), '', __DIR__));
define('ASSET', $http . $configUrl);
define('ADMIN', 'admin');

/* Cấu hình login */
$loginAdmin = $config['login']['admin'];
$loginMember = $config['login']['member'];

/* Cấu hình upload */
require_once LIBRARIES . "constant.php";

