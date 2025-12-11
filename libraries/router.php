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

if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] == 1)) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
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
$router->map('GET', 'sitemap-products.xml', function(){
    header('Content-Type: application/xml; charset=utf-8');
    $db = \Tuezy\Helper\GlobalHelper::db();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    $base = rtrim($config['database']['url'], '/');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    $rows = $db->rawQuery("SELECT {$sluglang} as slug, date_updated, type FROM #_product WHERE find_in_set('hienthi',status) ORDER BY date_updated DESC LIMIT 0, 5000");
    foreach($rows as $r){
        $loc = $base . '/' . ($r['slug'] ?? '');
        $lastmod = !empty($r['date_updated']) ? date('c', (int)$r['date_updated']) : date('c');
        echo "  <url><loc>{$loc}</loc><lastmod>{$lastmod}</lastmod><changefreq>weekly</changefreq><priority>0.7</priority></url>\n";
    }
    echo "</urlset>\n";
    exit;
}, 'sitemap_products');

$router->map('GET', 'sitemap-news.xml', function(){
    header('Content-Type: application/xml; charset=utf-8');
    $db = \Tuezy\Helper\GlobalHelper::db();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    $base = rtrim($config['database']['url'], '/');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    $rows = $db->rawQuery("SELECT {$sluglang} as slug, date_updated, type FROM #_news WHERE find_in_set('hienthi',status) ORDER BY date_updated DESC LIMIT 0, 5000");
    foreach($rows as $r){
        $loc = $base . '/' . ($r['slug'] ?? '');
        $lastmod = !empty($r['date_updated']) ? date('c', (int)$r['date_updated']) : date('c');
        echo "  <url><loc>{$loc}</loc><lastmod>{$lastmod}</lastmod><changefreq>weekly</changefreq><priority>0.6</priority></url>\n";
    }
    echo "</urlset>\n";
    exit;
}, 'sitemap_news');
$router->map('GET|POST', '[a:com]', 'allpage', 'show');
$router->map('GET|POST', '[a:com]/[a:lang]/', 'allpagelang', 'lang');
$router->map('GET|POST', '[a:com]/[a:action]', 'account', 'account');

/* API routes */
$router->map('GET|POST', 'api/product/list', function () {
    $db = \Tuezy\Helper\GlobalHelper::db();
    $cache = \Tuezy\Helper\GlobalHelper::cache();
    $func = \Tuezy\Helper\GlobalHelper::func();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    (new \Tuezy\API\Controller\ProductAPIController($db, $cache, $func, $config, $lang, $sluglang))->getList();
}, 'api_product_list');

$router->map('GET|POST', 'api/product/detail/[i:id]', function ($id) {
    $db = \Tuezy\Helper\GlobalHelper::db();
    $cache = \Tuezy\Helper\GlobalHelper::cache();
    $func = \Tuezy\Helper\GlobalHelper::func();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    (new \Tuezy\API\Controller\ProductAPIController($db, $cache, $func, $config, $lang, $sluglang))->getDetail((int)$id);
}, 'api_product_detail');

$router->map('GET|POST', 'api/product/list-by-hierarchy', function () {
    $db = \Tuezy\Helper\GlobalHelper::db();
    $cache = \Tuezy\Helper\GlobalHelper::cache();
    $func = \Tuezy\Helper\GlobalHelper::func();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    (new \Tuezy\API\Controller\ProductAPIController($db, $cache, $func, $config, $lang, $sluglang))->getListByHierarchy();
}, 'api_product_list_hierarchy');

$router->map('GET|POST', 'api/news/list', function () {
    $db = \Tuezy\Helper\GlobalHelper::db();
    $cache = \Tuezy\Helper\GlobalHelper::cache();
    $func = \Tuezy\Helper\GlobalHelper::func();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    (new \Tuezy\API\Controller\NewsAPIController($db, $cache, $func, $config, $lang, $sluglang))->getList();
}, 'api_news_list');

$router->map('GET|POST', 'api/news/detail/[i:id]', function ($id) {
    $db = \Tuezy\Helper\GlobalHelper::db();
    $cache = \Tuezy\Helper\GlobalHelper::cache();
    $func = \Tuezy\Helper\GlobalHelper::func();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    (new \Tuezy\API\Controller\NewsAPIController($db, $cache, $func, $config, $lang, $sluglang))->getDetail((int)$id);
}, 'api_news_detail');

$router->map('GET|POST', 'api/news/list-by-hierarchy', function () {
    $db = \Tuezy\Helper\GlobalHelper::db();
    $cache = \Tuezy\Helper\GlobalHelper::cache();
    $func = \Tuezy\Helper\GlobalHelper::func();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    (new \Tuezy\API\Controller\NewsAPIController($db, $cache, $func, $config, $lang, $sluglang))->getListByHierarchy();
}, 'api_news_list_hierarchy');

$router->map('GET|POST', 'api/search/products', function () {
    $db = \Tuezy\Helper\GlobalHelper::db();
    $cache = \Tuezy\Helper\GlobalHelper::cache();
    $func = \Tuezy\Helper\GlobalHelper::func();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    (new \Tuezy\API\Controller\SearchAPIController($db, $cache, $func, $config, $lang, $sluglang))->products();
}, 'api_search_products');

$router->map('GET|POST', 'api/search/articles', function () {
    $db = \Tuezy\Helper\GlobalHelper::db();
    $cache = \Tuezy\Helper\GlobalHelper::cache();
    $func = \Tuezy\Helper\GlobalHelper::func();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    (new \Tuezy\API\Controller\SearchAPIController($db, $cache, $func, $config, $lang, $sluglang))->articles();
}, 'api_search_articles');

$router->map('GET|POST', 'api/search/suggest-products', function () {
    $db = \Tuezy\Helper\GlobalHelper::db();
    $cache = \Tuezy\Helper\GlobalHelper::cache();
    $func = \Tuezy\Helper\GlobalHelper::func();
    $config = \Tuezy\Helper\GlobalHelper::config();
    $lang = $_SESSION['lang'] ?? 'vi';
    $sluglang = 'slugvi';
    (new \Tuezy\API\Controller\SearchAPIController($db, $cache, $func, $config, $lang, $sluglang))->suggestProducts();
}, 'api_search_suggest_products');

/* Admin API (for AstroJS admin) */
$router->map('GET', 'api/admin/redirects', function () {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    $db = \Tuezy\Helper\GlobalHelper::db();
    $items = [];
    try {
        $items = $db->rawQuery("SELECT id, `from`, `to`, status_code, status FROM #_redirects ORDER BY id DESC LIMIT 0, 200");
    } catch (\Throwable $e) {
        $items = [];
    }
    echo json_encode($items);
    exit;
}, 'api_admin_redirects');

$router->map('GET', 'api/admin/products', function () {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    $db = \Tuezy\Helper\GlobalHelper::db();
    $items = [];
    try {
        $type = $_GET['type'] ?? null;
        if ($type) {
            $items = $db->rawQuery(
                "SELECT id, namevi, slugvi, type, date_updated FROM #_product WHERE type = ? ORDER BY id DESC LIMIT 0, 200",
                [$type]
            );
        } else {
            $items = $db->rawQuery(
                "SELECT id, namevi, slugvi, type, date_updated FROM #_product ORDER BY id DESC LIMIT 0, 200"
            );
        }
    } catch (\Throwable $e) {
        $items = [];
    }
    echo json_encode($items);
    exit;
}, 'api_admin_products');

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
	
	// Build thumb file path
	$folder_old = dirname($src) . '/';
	$folder_old = str_replace('\\', '/', $folder_old);
	if (strpos($folder_old, $_SERVER['DOCUMENT_ROOT']) === 0) {
		$folder_old = str_replace($_SERVER['DOCUMENT_ROOT'], '', $folder_old);
	}
	$folder_old = ltrim($folder_old, '/\\');
	if (!empty($folder_old) && substr($folder_old, -1) !== '/') {
		$folder_old .= '/';
	}
	
	$image_name = basename($filePath);
	$thumb_dir = THUMBS . '/' . $w . 'x' . $h . 'x' . $z . '/' . $folder_old;
	$thumb_dir = str_replace('\\', '/', $thumb_dir);
	$thumb_dir = str_replace('//', '/', $thumb_dir);
	$thumb_dir = rtrim($thumb_dir, '/');
	$thumb_file = $thumb_dir . '/' . $image_name;
	
	// Check if thumb file already exists
	if (file_exists($thumb_file)) {
		// Serve existing file with cache headers
		$mime_type = 'jpeg';
		$ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
		if ($ext == 'png') $mime_type = 'png';
		elseif ($ext == 'gif') $mime_type = 'gif';
		
		$lastModified = filemtime($thumb_file);
		$etag = md5_file($thumb_file);
		
		// Check if client has cached version
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304);
			exit;
		}
		
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304);
			exit;
		}
		
		// Set cache headers
		header('Content-Type: image/' . $mime_type);
		header('Cache-Control: public, max-age=31536000, immutable');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
		header('ETag: ' . $etag);
		header('Content-Length: ' . filesize($thumb_file));
		
		// Output file
		readfile($thumb_file);
		exit;
	}
	
	// Thumb doesn't exist, create it
	// Lưu $folder_old vào biến global hoặc tính lại từ $src (đường dẫn tương đối)
	// Sửa: Tính $folder_old từ $src (đường dẫn tương đối) thay vì từ $filePath
	$relativeFolder = dirname($src);
	$relativeFolder = str_replace('\\', '/', $relativeFolder);
	$relativeFolder = ltrim($relativeFolder, '/\\');
	if (!empty($relativeFolder) && substr($relativeFolder, -1) !== '/') {
		$relativeFolder .= '/';
	}
	// Gọi createThumb với đường dẫn đầy đủ, nhưng sẽ tính lại folder_old từ $src
    (new \Tuezy\Infrastructure\Media\ThumbService())->generate($w, $h, $z, $filePath, null, THUMBS);
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
	
	// Build watermark thumb file path
	$folder_old = dirname($src) . '/';
	$folder_old = str_replace('\\', '/', $folder_old);
	if (strpos($folder_old, $_SERVER['DOCUMENT_ROOT']) === 0) {
		$folder_old = str_replace($_SERVER['DOCUMENT_ROOT'], '', $folder_old);
	}
	$folder_old = ltrim($folder_old, '/\\');
	if (!empty($folder_old) && substr($folder_old, -1) !== '/') {
		$folder_old .= '/';
	}
	
	$image_name = basename($filePath);
	$thumb_dir = WATERMARK . '/product/' . $w . 'x' . $h . 'x' . $z . '/' . $folder_old;
	$thumb_dir = str_replace('\\', '/', $thumb_dir);
	$thumb_dir = str_replace('//', '/', $thumb_dir);
	$thumb_dir = rtrim($thumb_dir, '/');
	$thumb_file = $thumb_dir . '/' . $image_name;
	
	// Check if watermark thumb file already exists
	if (file_exists($thumb_file)) {
		// Serve existing file with cache headers
		$mime_type = 'jpeg';
		$ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
		if ($ext == 'png') $mime_type = 'png';
		elseif ($ext == 'gif') $mime_type = 'gif';
		
		$lastModified = filemtime($thumb_file);
		$etag = md5_file($thumb_file);
		
		// Check if client has cached version
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304);
			exit;
		}
		
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304);
			exit;
		}
		
		// Set cache headers
		header('Content-Type: image/' . $mime_type);
		header('Cache-Control: public, max-age=31536000, immutable');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
		header('ETag: ' . $etag);
		header('Content-Length: ' . filesize($thumb_file));
		
		// Output file
		readfile($thumb_file);
		exit;
	}
	
	// Thumb doesn't exist, create it
	// Sử dụng PhotoRepository thay vì cache trực tiếp
	$photoRepo = new PhotoRepository($d, $lang ?? 'vi', $sluglang ?? 'slugvi');
	$wtm = $photoRepo->getByTypeAndAct('watermark', 'photo_static');
	
    (new \Tuezy\Infrastructure\Media\ThumbService())->generate($w, $h, $z, $filePath, $wtm, "product");
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
	
	// Build watermark thumb file path
	$folder_old = dirname($src) . '/';
	$folder_old = str_replace('\\', '/', $folder_old);
	if (strpos($folder_old, $_SERVER['DOCUMENT_ROOT']) === 0) {
		$folder_old = str_replace($_SERVER['DOCUMENT_ROOT'], '', $folder_old);
	}
	$folder_old = ltrim($folder_old, '/\\');
	if (!empty($folder_old) && substr($folder_old, -1) !== '/') {
		$folder_old .= '/';
	}
	
	$image_name = basename($filePath);
	$thumb_dir = WATERMARK . '/news/' . $w . 'x' . $h . 'x' . $z . '/' . $folder_old;
	$thumb_dir = str_replace('\\', '/', $thumb_dir);
	$thumb_dir = str_replace('//', '/', $thumb_dir);
	$thumb_dir = rtrim($thumb_dir, '/');
	$thumb_file = $thumb_dir . '/' . $image_name;
	
	// Check if watermark thumb file already exists
	if (file_exists($thumb_file)) {
		// Serve existing file with cache headers
		$mime_type = 'jpeg';
		$ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
		if ($ext == 'png') $mime_type = 'png';
		elseif ($ext == 'gif') $mime_type = 'gif';
		
		$lastModified = filemtime($thumb_file);
		$etag = md5_file($thumb_file);
		
		// Check if client has cached version
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304);
			exit;
		}
		
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304);
			exit;
		}
		
		// Set cache headers
		header('Content-Type: image/' . $mime_type);
		header('Cache-Control: public, max-age=31536000, immutable');
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
		header('ETag: ' . $etag);
		header('Content-Length: ' . filesize($thumb_file));
		
		// Output file
		readfile($thumb_file);
		exit;
	}
	
	// Thumb doesn't exist, create it
	// Sử dụng PhotoRepository
	$photoRepo = new PhotoRepository($d, $lang ?? 'vi', $sluglang ?? 'slugvi');
	$wtm = $photoRepo->getByTypeAndAct('watermark-news', 'photo_static');
	
    (new \Tuezy\Infrastructure\Media\ThumbService())->generate($w, $h, $z, $filePath, $wtm, "news");
}, 'watermarkNews');

/* Router match */
$match = $router->match();

/* Router check */
if (is_array($match)) {
	if (is_callable($match['target'])) {
		call_user_func_array($match['target'], $match['params']);
	} else {
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
	/* Sản phẩm - ưu tiên product trước các bảng category */
	array("tbl" => "product", "field" => "id", "source" => "product", "com" => "san-pham", "type" => "san-pham", "menu" => true),
	array("tbl" => "product_list", "field" => "idl", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
	array("tbl" => "product_cat", "field" => "idc", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
	array("tbl" => "product_item", "field" => "idi", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
	array("tbl" => "product_sub", "field" => "ids", "source" => "product", "com" => "san-pham", "type" => "san-pham"),
	array("tbl" => "product_brand", "field" => "idb", "source" => "product", "com" => "thuong-hieu", "type" => "san-pham"),

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

	array("tbl" => "news", "field" => "id", "source" => "news", "com" => "tai-sao-chon", "type" => "tai-sao-chon", "menu" => true),

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
    if(isset($_GET['debug_routing'])) {
        echo "<div style='background:lightblue;padding:10px;margin:5px;'>";
        echo "<strong>Router Debug (Before SlugResolver):</strong><br>";
        echo "Com: $com<br>";
        echo "Slug lang: $sluglang<br>";
        echo "</div>";
    }
    
    $resolver = \Tuezy\Application\Routing\SlugResolver::resolve($com, $d, $sluglang, $requick);
    
    if(isset($_GET['debug_routing'])) {
        echo "<div style='background:lightgreen;padding:10px;margin:5px;'>";
        echo "<strong>Router Debug (After SlugResolver):</strong><br>";
        echo "Resolved com: " . ($resolver['com'] ?? 'null') . "<br>";
        echo "Resolved type: " . ($resolver['type'] ?? 'null') . "<br>";
        echo "Resolved table: " . ($resolver['table'] ?? 'null') . "<br>";
        echo "\$_GET['id']: " . ($_GET['id'] ?? 'not set') . "<br>";
        echo "</div>";
    }
    
    $com = $resolver['com'] ?? $com;
    if (!empty($resolver['type'])) { $type = $resolver['type']; }
}

/* Redirect manager: handle 301/302 when slug changes */
if (!headers_sent()) {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (!empty($path)) {
        $row = null;
        try {
            $row = $d->rawQueryOne("SELECT `to`, status_code FROM #_redirects WHERE `from` = ? AND find_in_set('hienthi',status) LIMIT 0,1", [$path]);
        } catch (\Throwable $e) {
            $row = null;
        }
        if (!empty($row['to'])) {
            $code = (int)($row['status_code'] ?? 301);
            header("Location: " . $row['to'], true, ($code >= 300 && $code < 400) ? $code : 301);
            exit;
        }
    }
}

/* Switch coms - Sử dụng RouteHandler */
$routeHandler = new RouteHandler();
$routerHelper = new RouterHelper($routeHandler, $seo, $func);

// Xử lý các routes đặc biệt trước
$specialResult = $routerHelper->processRoute($com, $match['params']['lang'] ?? null, $urlType ?? null, $urlTblTag ?? null);

if ($specialResult && !empty($specialResult['exit'])) {
	exit;
}

// Lấy route config
$routeConfig = $routeHandler->getRouteConfig($com, [
	'hasId' => !empty($_GET['id']),
	'urlType' => $urlType ?? null,
	'urlTblTag' => $urlTblTag ?? null,
]);

if(isset($_GET['debug_routing'])) {
    echo "<div style='background:orange;padding:10px;margin:5px;'>";
    echo "<strong>Route Config Debug:</strong><br>";
    echo "Com: $com<br>";
    echo "Has ID: " . (!empty($_GET['id']) ? 'YES' : 'NO') . "<br>";
    echo "Route config found: " . ($routeConfig ? 'YES' : 'NO') . "<br>";
    if($routeConfig) {
        echo "Controller: " . ($routeConfig['controller'] ?? 'null') . "<br>";
        echo "Action: " . ($routeConfig['action'] ?? 'null') . "<br>";
        echo "Template: " . ($routeConfig['template'] ?? 'null') . "<br>";
    }
    echo "</div>";
}

// Determine View Variables
$source = null;
$template = null;
$viewData = [];

if ($routeConfig && isset($routeConfig['controller'])) {
    $controllerClass = $routeConfig['controller'];
    $controllerAction = $routeConfig['action'] ?? 'index';
    
    // Dynamic Action Resolution
    if ($controllerAction === 'index') {
        if (!empty($_GET['id'])) {
            $controllerAction = 'detail';
        } elseif (!empty($_GET['idc'])) {
            $controllerAction = 'category'; // cat
        } elseif (!empty($_GET['cat'])) { // Some routes might use 'cat' param?
            $controllerAction = 'category';
        } elseif (!empty($_GET['idl'])) {
            $controllerAction = 'list';
        } elseif (!empty($_GET['idi'])) {
            $controllerAction = 'item';
        } elseif (!empty($_GET['ids'])) {
            $controllerAction = 'sub';
        } elseif (!empty($_GET['idb'])) {
            $controllerAction = 'brand';
        }
    }
    
    if(isset($_GET['debug_routing'])) {
        echo "<div style='background:pink;padding:10px;margin:5px;'>";
        echo "<strong>Controller Action Debug:</strong><br>";
        echo "Controller class: $controllerClass<br>";
        echo "Action (resolved): $controllerAction<br>";
        echo "</div>";
    }
    
    // Handle 'account' specific actions
    if ($com === 'account' && !empty($match['params']['action'])) {
        $userAction = $match['params']['action'];
        switch ($userAction) {
            case 'dang-nhap': $controllerAction = 'login'; break;
            case 'dang-ky': $controllerAction = 'register'; break;
            case 'dang-xuat': $controllerAction = 'logout'; break;
            case 'thong-tin': $controllerAction = 'profile'; break;
            case 'quen-mat-khau': $controllerAction = 'forgotPassword'; break;
            default: $controllerAction = 'login'; break;
        }
    }

    // Handle 'tim-kiem'
    if ($com === 'tim-kiem') {
        $controllerAction = 'search';
    }

    // Set template and SEO type from config if available (RouteHandler logic handled this somewhat)
    $source = $routeConfig['source'] ?? $com; // Legacy source fallback for templates checking $source
    $template = $routeConfig['template'] ?? null;
    $type = $routeConfig['type'] ?? $com;
    
    // DEBUG: Log template after setting from routeConfig
    if(isset($_GET['debug_routing']) || isset($_GET['debug_template'])) {
        error_log("DEBUG [router.php:743] Template set from routeConfig: " . ($template ?? 'NULL'));
        error_log("DEBUG [router.php:743] RouteConfig template value: " . ($routeConfig['template'] ?? 'NOT SET'));
        error_log("DEBUG [router.php:743] RouteConfig keys: " . implode(', ', array_keys($routeConfig)));
        error_log("DEBUG [router.php:743] Com: $com, Has ID: " . (!empty($_GET['id']) ? 'YES' : 'NO'));
    }
    
    // Pass global dependencies
    // For simple DI, we just instantiate manually here as we have all globals within scope
    // Ideally use ServiceContainer, but let's wire it directly for stability first
    
    if (class_exists($controllerClass)) {
        $controller = null;
        
        switch ($controllerClass) {
            case \Tuezy\Controller\ProductController::class:
                $controller = new \Tuezy\Controller\ProductController($d, $cache, $func, $seo, $config, $type);
                break;
            case \Tuezy\Controller\NewsController::class:
                $controller = new \Tuezy\Controller\NewsController($d, $cache, $func, $seo, $config, $type);
                break;
            case \Tuezy\Controller\StaticController::class:
                $controller = new \Tuezy\Controller\StaticController($d, $cache, $func, $seo, $config);
                break;
            case \Tuezy\Controller\ContactController::class:
                $controller = new \Tuezy\Controller\ContactController($d, $cache, $func, $seo, $config, $emailer, $flash ?? null);
                break;
            case \Tuezy\Controller\HomeController::class:
                $controller = new \Tuezy\Controller\HomeController($d, $cache, $func, $seo, $config);
                break;
            case \Tuezy\Controller\VideoController::class:
                $controller = new \Tuezy\Controller\VideoController($d, $cache, $func, $seo, $config);
                break;
            case \Tuezy\Controller\OrderController::class:
                $controller = new \Tuezy\Controller\OrderController($d, $cache, $func, $seo, $config, $cart, $emailer, $flash ?? null);
                break;
            case \Tuezy\Controller\UserController::class:
                $loginMember = $loginMember ?? 'member'; // Default
                $controller = new \Tuezy\Controller\UserController($d, $cache, $func, $seo, $config, $flash ?? null, $loginMember);
                break;
            default:
                // Not mapped specifically in switch, try basic if possible or fail safely
                break;
        }

        if ($controller) {
            // Execute Action
            if (method_exists($controller, $controllerAction)) {
                $args = [];
                // Argument population logic
                if ($controllerAction === 'detail') $args[] = (int)($_GET['id'] ?? 0);
                if ($controllerAction === 'category') $args[] = (int)($_GET['idc'] ?? 0);
                if ($controllerAction === 'list') $args[] = (int)($_GET['idl'] ?? 0);
                if ($controllerAction === 'item') $args[] = (int)($_GET['idi'] ?? 0);
                if ($controllerAction === 'sub') $args[] = (int)($_GET['ids'] ?? 0);
                if ($controllerAction === 'brand') $args[] = (int)($_GET['idb'] ?? 0);
                
                if (in_array($controllerAction, ['detail', 'category', 'list', 'item', 'sub', 'brand'])) {
                    $args[] = $type;
                }
                
                if ($controllerAction === 'search' && $controllerClass === \Tuezy\Controller\ProductController::class) {
                    $args[] = $_GET['keyword'] ?? '';
                    $args[] = $type;
                }
                
                // StaticController::index() requires $type parameter
                if ($controllerAction === 'index' && $controllerClass === \Tuezy\Controller\StaticController::class) {
                    $args[] = $type;
                }
                
                // NewsController::index() and ProductController::index() require $type parameter
                if ($controllerAction === 'index' && in_array($controllerClass, [\Tuezy\Controller\NewsController::class, \Tuezy\Controller\ProductController::class])) {
                    $args[] = $type;
                }

                if(isset($_GET['debug_routing'])) {
                    echo "<div style='background:cyan;padding:10px;margin:5px;'>";
                    echo "<strong>Calling Controller:</strong><br>";
                    echo "Controller: " . get_class($controller) . "<br>";
                    echo "Action: $controllerAction<br>";
                    echo "Args: " . json_encode($args) . "<br>";
                    echo "</div>";
                }

                try {
                    $viewData = call_user_func_array([$controller, $controllerAction], $args);
                    
                    if(isset($_GET['debug_routing'])) {
                        echo "<div style='background:lightgreen;padding:10px;margin:5px;'>";
                        echo "<strong>Controller Returned:</strong><br>";
                        echo "View data keys: " . (is_array($viewData) ? implode(', ', array_keys($viewData)) : 'NOT ARRAY') . "<br>";
                        echo "</div>";
                    }
                } catch (\Throwable $e) {
                    if(isset($_GET['debug_routing'])) {
                        echo "<div style='background:red;color:white;padding:10px;margin:5px;'>";
                        echo "<strong>Controller Error:</strong><br>";
                        echo "Error: " . $e->getMessage() . "<br>";
                        echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
                        echo "</div>";
                    }
                    throw $e;
                }
                
                if (isset($viewData['titleMain'])) {
                    $titleMain = $viewData['titleMain'];
                } elseif (isset($routeConfig['titleMain'])) {
                    $titleMain = $routeConfig['titleMain'];
                }
                
            } else {
                 if(DEV_MODE) throw new Exception("Action $controllerAction not found in $controllerClass");
            }
        }
    }
} else {
    // If no route config found, 404.
	header('HTTP/1.0 404 Not Found', true, 404);
	include("404.php");
	exit;
}

/* Require datas for all page */
require_once SOURCES . "allpage.php";

/* Extract view data */
// DEBUG: Log template before extract
if(isset($_GET['debug_routing']) || isset($_GET['debug_template'])) {
    error_log("DEBUG [router.php:856] Template BEFORE extract: " . ($template ?? 'NULL'));
    error_log("DEBUG [router.php:856] ViewData keys: " . (is_array($viewData) ? implode(', ', array_keys($viewData)) : 'NOT ARRAY'));
    if(is_array($viewData) && isset($viewData['template'])) {
        error_log("DEBUG [router.php:856] WARNING: viewData contains 'template' key with value: " . $viewData['template']);
    }
}

if (!empty($viewData) && is_array($viewData)) {
    extract($viewData);
}

// DEBUG: Log template after extract
if(isset($_GET['debug_routing']) || isset($_GET['debug_template'])) {
    error_log("DEBUG [router.php:860] Template AFTER extract: " . ($template ?? 'NULL'));
}

/* Include template */
// Determine actual template file path (with _tpl.php suffix as per templates/index.php line 33)
$templateFile = TEMPLATE . $template . "_tpl.php";
$templateFileAlt = TEMPLATE . $template . ".php"; // Fallback for templates without _tpl suffix

// DEBUG: Final template check
if(isset($_GET['debug_routing']) || isset($_GET['debug_template'])) {
    error_log("DEBUG [router.php:882] FINAL Template check: " . ($template ?? 'NULL'));
    error_log("DEBUG [router.php:882] Template empty check: " . (empty($template) ? 'YES (WILL 404!)' : 'NO'));
    error_log("DEBUG [router.php:882] Template file path (_tpl.php): " . $templateFile);
    error_log("DEBUG [router.php:882] Template file exists (_tpl.php): " . (file_exists($templateFile) ? 'YES' : 'NO'));
    error_log("DEBUG [router.php:882] Template file path (.php): " . $templateFileAlt);
    error_log("DEBUG [router.php:882] Template file exists (.php): " . (file_exists($templateFileAlt) ? 'YES' : 'NO'));
    
    echo "<div style='background:purple;color:white;padding:10px;margin:5px;'>";
    echo "<strong>Template Debug:</strong><br>";
    echo "Template var: " . ($template ?? 'NULL') . "<br>";
    echo "Template file (_tpl.php): " . $templateFile . "<br>";
    echo "File exists (_tpl.php): " . (file_exists($templateFile) ? 'YES' : 'NO') . "<br>";
    echo "Template file (.php): " . $templateFileAlt . "<br>";
    echo "File exists (.php): " . (file_exists($templateFileAlt) ? 'YES' : 'NO') . "<br>";
    echo "RouteConfig template: " . ($routeConfig['template'] ?? 'NOT SET') . "<br>";
    echo "Com: $com<br>";
    echo "Has ID: " . (!empty($_GET['id']) ? 'YES' : 'NO') . "<br>";
    echo "</div>";
}

if (empty($template)) {
    // DEBUG: Log why template is empty
    if(isset($_GET['debug_routing']) || isset($_GET['debug_template'])) {
        error_log("ERROR [router.php:901] Template is EMPTY - causing 404!");
        error_log("ERROR [router.php:901] RouteConfig was: " . ($routeConfig ? 'SET' : 'NULL'));
        if($routeConfig) {
            error_log("ERROR [router.php:901] RouteConfig template: " . ($routeConfig['template'] ?? 'NOT SET'));
            error_log("ERROR [router.php:901] RouteConfig templateDetail: " . ($routeConfig['templateDetail'] ?? 'NOT SET'));
            error_log("ERROR [router.php:901] RouteConfig templateList: " . ($routeConfig['templateList'] ?? 'NOT SET'));
        }
    }
	header('HTTP/1.0 404 Not Found', true, 404);
	include("404.php");
	exit;
}

// Verify template file exists (with _tpl.php suffix as used in templates/index.php)
if (!file_exists($templateFile) && !file_exists($templateFileAlt)) {
    // DEBUG: Log template file not found
    if(isset($_GET['debug_routing']) || isset($_GET['debug_template'])) {
        error_log("ERROR [router.php:915] Template file NOT FOUND!");
        error_log("ERROR [router.php:915] Tried: " . $templateFile);
        error_log("ERROR [router.php:915] Tried: " . $templateFileAlt);
    }
	header('HTTP/1.0 404 Not Found', true, 404);
	include("404.php");
	exit;
}
