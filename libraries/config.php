<?php

if (!defined('LIBRARIES')) die("Error");

date_default_timezone_set('Asia/Ho_Chi_Minh');
const NN_CONTRACT = '';

define('ROOT', 
    str_replace(basename(__DIR__), 
    '', 
    __DIR__));

$config = array(
    'arrayDomainSSL' => array(), 
    'cache' => ['default' => 'file', 
    'stores' => ['array' => ['driver' => 'array', 
    'serialize' => false,], 
    'file' => ['driver' => 'file', 
    'path' => ROOT . "/storage/icache", 
    'lock_path' => ROOT . "/storage/icache",], 
    'redis' => ['driver' => 'redis', 
    'connection' => 'cache', 
    'lock_connection' => 'default',],], 
    'prefix' => '_cache_',], 
    'database' => array('server-name' => $_SERVER["SERVER_NAME"], 
    'url' => '/', 
    'type' => 'mysql', 
    'host' => 'localhost', 
    'username' => 'root', 
    'password' => '12345678@', 
    'dbname' => 'database', 
    'port' => 3306, 
    'prefix' => 'table_', 
    'charset' => 'utf8mb4'),

    'menu' => ['index' => [], 
        'gioi-thieu' => [], 
        'san-pham' => [], 
        'dich-vu' => [], 
        'tin-tuc' => [], 
        'tuyen-dung' => [], 
        'lien-he' => [],],

    'website' => array(
		'error-reporting' => true,
        'secret' => '$nina@', 
        'salt' => 'swKJjeS!t', 
        'scss' => FALSE,
        'debug-developer' => true, 
        'debug-css' => true,
        'debug-js' => true, 
        'index' => false, 
        'image' => array('hasWebp' => false,), 
        'video' => array('extension' => array('mp4', 
        'mkv'), 
        'poster' => array('width' => 700, 
        'height' => 610, 
        'extension' => '.jpg|.png|.jpeg'), 
        'allow-size' => '100Mb', 
        'max-size' => 100 * 1024 * 1024),

        'upload' => array('max-width' => 1600, 
            'max-height' => 1600),

        'lang' => array(

            'vi' => 'Tiếng Việt',

//            'en' => 'Tiếng Anh'

        ),

        'lang-doc' => 'vi|en', 
            'slug' => array('vi' => 'Tiếng Việt',//            'en' => 'Tiếng Anh'
        ), 
            'seo' => array('vi' => 'Tiếng Việt',//            'en' => 'Tiếng Anh'

        ),

        'comlang' => array("gioi-thieu" => array("vi" => "gioi-thieu", 
            "en" => "about-us"), 
            "san-pham" => array("vi" => "san-pham", 
            "en" => "product"), 
            "tin-tuc" => array("vi" => "tin-tuc", 
            "en" => "news"), 
            "tuyen-dung" => array("vi" => "tuyen-dung", 
            "en" => "recruitment"), 
            "thu-vien-anh" => array("vi" => "thu-vien-anh", 
            "en" => "gallery"), 
            "video" => array("vi" => "video", 
            "en" => "video"), 
            "lien-he" => array("vi" => "lien-he", 
            "en" => "contact"))

    ),

    'order' => array(

        'ship' => true

    ),

    'login' => array(

        'admin' => 'LoginAdmin' . NN_CONTRACT,

        'member' => 'LoginMember' . NN_CONTRACT,

        'attempt' => 5,

        'delay' => 15

    ),

    'googleAPI' => array(

        'recaptcha' => array(

            'active' => false,

            'urlapi' => 'https://www.google.com/recaptcha/api/siteverify',

            'sitekey' => '6LezS5kUAAAAAF2A6ICaSvm7R5M-BUAcVOgJT_31',

            'secretkey' => '6LezS5kUAAAAAGCGtfV7C1DyiqlPFFuxvacuJfdq'

        )

    ),

    'oneSignal' => array(

        'active' => false,

        'id' => 'af12ae0e-cfb7-41d0-91d8-8997fca889f8',

        'restId' => 'MWFmZGVhMzYtY2U0Zi00MjA0LTg0ODEtZWFkZTZlNmM1MDg4'

    ),

    'license' => array(

        'version' => "7.1.0",

        'powered' => "phuctai.nina@gmail.com"

    )

);


/* Error reporting */

error_reporting(($config['website']['error-reporting']) ? E_ALL : 0);


/* Cấu hình http */

if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
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
$configBase = $http . $configUrl;

/* Token */

define('TOKEN', 
    md5(NN_CONTRACT . $config['database']['url']));

/* Path */

define('ASSET', 
    $http . $configUrl);
const ADMIN = 'admin';

/* Cấu hình login */
$loginAdmin = $config['login']['admin'];
$loginMember = $config['login']['member'];

/* Cấu hình upload */
require_once LIBRARIES . "constant.php";





