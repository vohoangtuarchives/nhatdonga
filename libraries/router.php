<?php

/**
 * router.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của libraries/router.php
 * Sử dụng RequestHandler, RouteHandler, và các class mới
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp libraries/router.php libraries/router.php.backup
 * 2. Copy file này: cp libraries/router-refactored.php libraries/router.php
 * 3. Hoặc include file này thay vì router.php cũ
 */

use Tuezy\RequestHandler;
use Tuezy\RouteHandler;
use Tuezy\RouterHelper;
use Tuezy\Config;
use Tuezy\Repository\PhotoRepository;
use Tuezy\SecurityHelper;
use Tuezy\Context;
use Tuezy\Helper\GlobalHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize RequestHandler
$params = RequestHandler::getParams();
$com = $params['com'];
$act = $params['act'];
$type = $params['type'];
$getPage = $params['curPage'];

// Ensure $http and $configUrl are defined (they should be from libraries/config.php)
if (!isset($http)) {
    if (
        (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
    ) {
        $http = 'https://';
    } else {
        $http = 'http://';
    }
}

if (!isset($configUrl)) {
    $configUrl = $config['database']['server-name'] . $config['database']['url'];
}

if (!isset($configBase)) {
    $configBase = $http . $configUrl;
}

/* Check HTTP */
$func->checkHTTP($http, $config['arrayDomainSSL'], $configBase, $configUrl);

/* Validate URL */
$func->checkUrl($config['website']['index']);

/* Check login */
$func->checkLoginMember();

/* Mobile detect */
if (!defined('TEMPLATE')) {
	define('TEMPLATE', './templates/');
}

/* Router */
$router->setBasePath($config['database']['url']);

$router->map('GET', array(ADMIN . '/', 'admin'), function () {
	$func = GlobalHelper::func();
	$config = GlobalHelper::config();
	$func->redirect($config['database']['url'] . ADMIN . "/index.php");
	exit;
});

$router->map('GET', array(ADMIN, 'admin'), function () {
	$func = GlobalHelper::func();
	$config = GlobalHelper::config();
	$func->redirect($config['database']['url'] . ADMIN . "/index.php");
	exit;
});

$router->map('GET|POST', '', 'index', 'home');
$router->map('GET|POST', 'index.php', 'index', 'index');
$router->map('GET|POST', 'sitemap.xml', 'sitemap', 'sitemap');
$router->map('GET|POST', '[a:com]', 'allpage', 'show');
$router->map('GET|POST', '[a:com]/[a:lang]/', 'allpagelang', 'lang');
$router->map('GET|POST', '[a:com]/[a:action]', 'account', 'account');

$router->map('GET', THUMBS . '/[i:w]x[i:h]x[i:z]/[**:src]', function ($w, $h, $z, $src) {
	$func = GlobalHelper::func();
	$config = GlobalHelper::config();
	// Convert URL path to file system path
	$src = str_replace('%20', ' ', $src);
	// Remove leading slash if present
	$src = ltrim($src, '/');
	// Build full file system path
	$filePath = ROOT . $src;
	// Normalize path separators for Windows
	$filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
	// If file doesn't exist, try alternative paths
	if (!file_exists($filePath)) {
		// Try with BASE_PATH
		$filePath = BASE_PATH . DIRECTORY_SEPARATOR . $src;
		$filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
	}
	// If still doesn't exist, try removing config URL prefix
	if (!file_exists($filePath) && !empty($config['database']['url'])) {
		$srcClean = str_replace($config['database']['url'], '', $src);
		$srcClean = ltrim($srcClean, '/');
		$filePath = ROOT . $srcClean;
		$filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
	}
	$func->createThumb($w, $h, $z, $filePath, null, THUMBS);
}, 'thumb');

$router->map('GET', WATERMARK . '/product/[i:w]x[i:h]x[i:z]/[**:src]', function ($w, $h, $z, $src) {
	$func = GlobalHelper::func();
	$cache = GlobalHelper::cache();
	$config = GlobalHelper::config();
	$d = GlobalHelper::db();
	$lang = $_SESSION['lang'] ?? 'vi';
	$sluglang = 'slugvi';
	
	// Convert URL path to file system path
	$src = str_replace('%20', ' ', $src);
	$src = ltrim($src, '/');
	$filePath = ROOT . $src;
	$filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
	if (!file_exists($filePath)) {
		$filePath = BASE_PATH . DIRECTORY_SEPARATOR . $src;
		$filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
	}
	
	// Sử dụng PhotoRepository thay vì cache trực tiếp
	$photoRepo = new PhotoRepository($d, $cache, $lang ?? 'vi', $sluglang ?? 'slugvi');
	$wtm = $photoRepo->getByTypeAndAct('watermark', 'photo_static');
	
	$func->createThumb($w, $h, $z, $filePath, $wtm, "product");
}, 'watermark');

$router->map('GET', WATERMARK . '/news/[i:w]x[i:h]x[i:z]/[**:src]', function ($w, $h, $z, $src) {
	$func = GlobalHelper::func();
	$cache = GlobalHelper::cache();
	$config = GlobalHelper::config();
	$d = GlobalHelper::db();
	$lang = $_SESSION['lang'] ?? 'vi';
	$sluglang = 'slugvi';
	
	// Convert URL path to file system path
	$src = str_replace('%20', ' ', $src);
	$src = ltrim($src, '/');
	$filePath = ROOT . $src;
	$filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
	if (!file_exists($filePath)) {
		$filePath = BASE_PATH . DIRECTORY_SEPARATOR . $src;
		$filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);
	}
	
	// Sử dụng PhotoRepository
	$photoRepo = new PhotoRepository($d, $cache, $lang ?? 'vi', $sluglang ?? 'slugvi');
	$wtm = $photoRepo->getByTypeAndAct('watermark-news', 'photo_static');
	
	$func->createThumb($w, $h, $z, $filePath, $wtm, "news");
}, 'watermarkNews');

/* Router match */
$match = $router->match();

/* Router check */
if (is_array($match)) {
	if (is_callable($match['target'])) {
		call_user_func_array($match['target'], $match['params']);
	} else {
		// Sử dụng SecurityHelper thay vì htmlspecialchars trực tiếp
		$com = !empty($match['params']['com']) ? $match['params']['com'] : $match['target'];
		$com = SecurityHelper::sanitize($com);
		$getPage = SecurityHelper::sanitizeGet('p', '1');
	}
} else {
	header('HTTP/1.0 404 Not Found', true, 404);
	include("404.php");
	exit;
}

/* Setting */
$sqlCache = "select * from #_setting";
$setting = $cache->get($sqlCache, null, 'fetch', 7200);
$optsetting = (!empty($setting['options'])) ? json_decode($setting['options'], true) : null;

/* Lang */
if (!empty($match['params']['lang'])) {
	$_SESSION['lang'] = $match['params']['lang'];
} else if (empty($_SESSION['lang']) && empty($match['params']['lang'])) {
	$_SESSION['lang'] = $optsetting['lang_default'] ?? 'vi';
}
$lang = $_SESSION['lang'];

/* Check lang */
$weblang = (!empty($config['website']['lang'])) ? array_keys($config['website']['lang']) : array();

if (!in_array($lang, $weblang)) {
	$_SESSION['lang'] = 'vi';
	$lang = $_SESSION['lang'];
}

$func->set_language($lang);
$func->set_comlang($config['website']['comlang']);

/* Slug lang */
$sluglang = 'slugvi';

/* SEO Lang */
$seolang = "vi";

/* Require datas lang */
require_once LIBRARIES . "lang/$lang.php";

/* Tối ưu link */
$requick = array(
	/* Sản phẩm */
	array("tbl" => "product_list", "field" => "idl", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
	array("tbl" => "product_cat", "field" => "idc", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
	array("tbl" => "product_item", "field" => "idi", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
	array("tbl" => "product_sub", "field" => "ids", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
	array("tbl" => "product_brand", "field" => "idb", "source" => "product", "com" => "thuong-hieu", "type" => "san-pham"),
	array("tbl" => "product", "field" => "id", "source" => "product", "com" => "san-pham", "type" => "san-pham", "menu" => true),

	/* Tags */
	array("tbl" => "tags", "tbltag" => "product", "field" => "id", "source" => "tags", "com" => "tags-san-pham", "type" => "san-pham", "menu" => true),
	array("tbl" => "tags", "tbltag" => "news", "field" => "id", "source" => "tags", "com" => "tags-tin-tuc", "type" => "tin-tuc", "menu" => true),

	/* Thư viện ảnh */
	array("tbl" => "product", "field" => "id", "source" => "product", "com" => "thu-vien-anh", "type" => "thu-vien-anh", "menu" => true),

	/* Video */
	array("tbl" => "photo", "field" => "id", "source" => "video", "com" => "video", "type" => "video", "menu" => true),

	array("tbl" => "news_list", "field" => "idl", "source" => "news", "com" => "dich-vu", "type" => "dich-vu"),
	array("tbl" => "news", "field" => "id", "source" => "news", "com" => "dich-vu", "type" => "dich-vu", "menu" => true),
	
	/* Tin tức */
	array("tbl" => "news_list", "field" => "idl", "source" => "news", "com" => "tin-tuc", "type" => "tin-tuc"),
	array("tbl" => "news_cat", "field" => "idc", "source" => "news", "com" => "tin-tuc", "type" => "tin-tuc"),
	array("tbl" => "news_item", "field" => "idi", "source" => "news", "com" => "tin-tuc", "type" => "tin-tuc"),
	array("tbl" => "news_sub", "field" => "ids", "source" => "news", "com" => "tin-tuc", "type" => "tin-tuc"),
	array("tbl" => "news", "field" => "id", "source" => "news", "com" => "tin-tuc", "type" => "tin-tuc", "menu" => true),

	array("tbl" => "news", "field" => "id", "source" => "news", "com" => "su-kien", "type" => "su-kien", "menu" => true),

	/* Bài viết */
	array("tbl" => "news", "field" => "id", "source" => "news", "com" => "tuyen-dung", "type" => "tuyen-dung", "menu" => true),
	array("tbl" => "news", "field" => "id", "source" => "news", "com" => "dao-tao", "type" => "dao-tao", "menu" => true),
	array("tbl" => "news", "field" => "id", "source" => "news", "com" => "chinh-sach", "type" => "chinh-sach", "menu" => false),
	array("tbl" => "news", "field" => "id", "source" => "news", "com" => "kien-thuc", "type" => "kien-thuc", "menu" => true),
	array("tbl" => "news", "field" => "id", "source" => "news", "com" => "thu-vien", "type" => "thu-vien", "menu" => true),
	
	/* Trang tĩnh */
	array("tbl" => "static", "field" => "id", "source" => "static", "com" => "gioi-thieu", "type" => "gioi-thieu", "menu" => true),

	/* Liên hệ */
	array("tbl" => "", "field" => "id", "source" => "", "com" => "lien-he", "type" => "", "menu" => true),
);

/* Find data */
if (!empty($com) && !in_array($com, ['tim-kiem', 'account', 'sitemap'])) {
	foreach ($requick as $k => $v) {
		$urlTbl = (!empty($v['tbl'])) ? $v['tbl'] : '';
		$urlTblTag = (!empty($v['tbltag'])) ? $v['tbltag'] : '';
		$urlType = (!empty($v['type'])) ? $v['type'] : '';
		$urlField = (!empty($v['field'])) ? $v['field'] : '';
		$urlCom = (!empty($v['com'])) ? $v['com'] : '';

		if (!empty($urlTbl) && !in_array($urlTbl, ['static', 'photo'])) {
			$row = $d->rawQueryOne("select id from #_$urlTbl where $sluglang = ? and type = ? and find_in_set('hienthi',status) limit 0,1", array($com, $urlType));

			if (!empty($row['id'])) {
				$_GET[$urlField] = $row['id'];
				$com = $urlCom;
				break;
			}
		}
	}
}

/* Switch coms - Sử dụng RouteHandler */
$routeHandler = new RouteHandler();
$routerHelper = new RouterHelper($routeHandler, $seo, $func);

// Xử lý các routes đặc biệt trước
$specialResult = $routerHelper->processRoute($com, $match['params']['lang'] ?? null, $urlType ?? null, $urlTblTag ?? null);

if ($specialResult && !empty($specialResult['exit'])) {
	// Route đã được xử lý (sitemap, ngon-ngu, etc.)
	exit;
}

// Lấy route config
$routeConfig = $routeHandler->getRouteConfig($com, [
	'hasId' => !empty($_GET['id']),
	'urlType' => $urlType ?? null,
	'urlTblTag' => $urlTblTag ?? null,
]);

if ($routeConfig) {
	// Sử dụng route config từ RouteHandler
	// Nếu processRoute đã trả về kết quả, sử dụng nó (đã resolve titleMain)
	if ($specialResult && !empty($specialResult['source'])) {
		$source = $specialResult['source'];
		$template = $specialResult['template'];
		$type = $specialResult['type'] ?? $com;
		$table = $specialResult['table'] ?? null;
		$titleMain = $specialResult['titleMain'] ?? null;
	} else {
		// Fallback: sử dụng trực tiếp từ routeConfig
		$source = $routeConfig['source'] ?? null;
		$template = $routeConfig['template'] ?? null;
		$type = $routeConfig['type'] ?? $com;
		$table = $routeConfig['table'] ?? null;
		$titleMain = $routeConfig['titleMain'] ?? null;
	}
	
	if (isset($routeConfig['seoType'])) {
		$seo->set('type', $routeConfig['seoType']);
	}
} else {
	// Fallback về switch statement cũ cho các routes chưa được định nghĩa
	switch ($com) {
		case 'bang-gia':
			$source = "static";
			$template = "static/static";
			$type = $com;
			$seo->set('type', 'article');
			$titleMain = "Bảng Giá";
			break;

		case 'su-kien':
			$source = "news";
			$template = isset($_GET['id']) ? "news/news_detail" : "news/news";
			$seo->set('type', isset($_GET['id']) ? "article" : "object");
			$type = $com;
			$titleMain = "Sự kiện";
			break;

		case 'kien-thuc':
			$source = "news";
			$template = isset($_GET['id']) ? "news/news_detail" : "news/news";
			$seo->set('type', isset($_GET['id']) ? "article" : "object");
			$type = $com;
			$titleMain = "Kiến Thức";
			break;

		case 'dich-vu':
			$source = "news";
			$template = isset($_GET['id']) ? "news/news_detail2" : "news/news";
			$seo->set('type', isset($_GET['id']) ? "article" : "object");
			$type = $com;
			$titleMain = "Dịch Vụ";
			break;

		case 'thu-vien':
			$source = "news";
			$template = isset($_GET['id']) ? "news/news_detail" : "news/news_dichvu";
			$seo->set('type', isset($_GET['id']) ? "article" : "object");
			$type = $com;
			$titleMain = "Thư Viện";
			break;

		case 'catalogue':
			$source = "news";
			$template = isset($_GET['id']) ? "news/news_detail" : "news/news";
			$seo->set('type', isset($_GET['id']) ? "article" : "object");
			$type = $com;
			$titleMain = "Catalogue";
			break;

		case 'chinh-sach':
			$source = "news";
			$template = isset($_GET['id']) ? "news/news_detail" : "news/news";
			$seo->set('type', 'article');
			$type = $com;
			$titleMain = "Chính Sách";
			break;

		case 'yeu-thich':
		case 'noi-bat':
		case 'khuyen-mai':
			$source = "product";
			$template = "product/product";
			$seo->set('type', 'object');
			$type = 'san-pham';
			$titleMain = null;
			break;

		case 'tim-kiem':
			$source = "search";
			$template = "product/product";
			$seo->set('type', 'object');
			$titleMain = timkiem;
			break;

		case '':
		case 'index':
			$source = "index";
			$template = "index/index";
			$seo->set('type', 'website');
			break;

		default:
			header('HTTP/1.0 404 Not Found', true, 404);
			include("404.php");
			exit();
	}
}

/* Require datas for all page */
require_once SOURCES . "allpage.php";

/* Include sources */
if (!empty($source)) {
	include SOURCES . $source . ".php";
}

/* Include sources */
if (empty($template)) {
	header('HTTP/1.0 404 Not Found', true, 404);
	include("404.php");
	exit;
}
