<?php

if (!defined('LIBRARIES')) die("Error");



/* Timezone */

date_default_timezone_set('Asia/Ho_Chi_Minh');



/* Cấu hình coder */

define('NN_CONTRACT', '788922w');

define('NN_AUTHOR', 'hoangnguyenminh.nina@gmail.com');





/* Cấu hình chung */

$config = array(

	'author' => array(

		'name' => 'Nguyễn Minh Hoàng',

		'email' => 'hoangnguyenminh.nina@gmail.com',

		'timefinish' => '27/05/2022'

	),

	'arrayDomainSSL' => array(),

	'database' => array(

		'server-name' => $_SERVER["SERVER_NAME"],

		'url' => '/',

		'type' => 'mysql',

		'host' => 'localhost',

		'username' => 'root',

		'password' => '',

		'dbname' => 'cuuho',

		'port' => 3306,

		'prefix' => 'table_',

		'charset' => 'utf8mb4'

	),

	'website' => array(

		'error-reporting' => false,

		'secret' => '$nina@',

		'salt' => 'swKJjeS!t',

		'debug-developer' => true,

		'debug-css' => true,

		'debug-js' => true,

		'index' => false,

		'image' => array(

			'hasWebp' => false,

		),

		'video' => array(

			'extension' => array('mp4', 'mkv'),

			'poster' => array(

				'width' => 700,

				'height' => 610,

				'extension' => '.jpg|.png|.jpeg'

			),

			'allow-size' => '100Mb',

			'max-size' => 100 * 1024 * 1024

		),

		'upload' => array(

			'max-width' => 1600,

			'max-height' => 1600

		),

		'lang' => array(

			'vi' => 'Tiếng Việt',

		),

		'lang-doc' => 'vi',

		'slug' => array(

			'vi' => 'Tiếng Việt'

		),

		'seo' => array(

			'vi' => 'Tiếng Việt'

		),

		'comlang' => array(

			"gioi-thieu" => array("vi" => "gioi-thieu", "en" => "about-us"),

			"san-pham" => array("vi" => "san-pham", "en" => "product"),

			"tin-tuc" => array("vi" => "tin-tuc", "en" => "news"),

			"dich-vu" => array("vi" => "dich-vu", "en" => "news"),

			"dao-tao" => array("vi" => "dao-tao", "en" => "news"),

			"tuyen-dung" => array("vi" => "tuyen-dung", "en" => "recruitment"),

			"thu-vien-anh" => array("vi" => "thu-vien-anh", "en" => "gallery"),

			"video" => array("vi" => "video", "en" => "video"),

			"lien-he" => array("vi" => "lien-he", "en" => "contact")

		)

	),

	'order' => array(

		'ship' => false

	),

	'cart' => array(

		"active" => false,

	),

	'coppy' => array(

		"lock" => false,

	),

	'careers' => [

		'casi' => "Ca sĩ",

		'youtuber' => "Youtuber",

		'kysu' => "Kỹ Sư",

		'cogiao' => "Cô Giáo",

		'laptrinhvien' => "Lập Trình Viên",

		'mauanh' => "Mẫu Ảnh",

		'tiktoker' => "Tiktoker"

	],

	'login' => array(

		'admin' => 'LoginAdmin' . NN_CONTRACT,

		'member' => 'LoginMember' . NN_CONTRACT,

		'attempt' => 5,

		'delay' => 15

	),

	'googleAPI' => array(

		'recaptcha' => array(

			'active' => true,

			'urlapi' => 'https://www.google.com/recaptcha/api/siteverify',

			'sitekey' => '6Ld_l2clAAAAAG9amJdEX-ghOQ3PdOyAXBPk88ib',

			'secretkey' => '6Ld_l2clAAAAAH_xLNyfa2UuuiprJquQSmBD8prg'

		)

	),

	'oneSignal' => array(

		'active' => false,

		'id' => '',

		'restId' => ''

	),

	'license' => array(

		'version' => "7.1.0",

	)

);



/* Error reporting */

error_reporting(($config['website']['error-reporting']) ? E_ALL : 0);



/* Cấu hình http */

/* Cấu hình SSL */

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

