<?php

/**
 * sources/product.php - REFACTORED VERSION
 * 
 * Sử dụng ProductController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Controller\ProductController;
use Tuezy\RequestHandler;

// Initialize RequestHandler
$params = RequestHandler::getParams();
$id = (int)$params['id'];
$idl = (int)($_GET['idl'] ?? 0);
$idc = (int)($_GET['idc'] ?? 0);
$idi = (int)($_GET['idi'] ?? 0);
$ids = (int)($_GET['ids'] ?? 0);
$idb = (int)($_GET['idb'] ?? 0);

// Initialize Controller
$controller = new ProductController($d, $cache, $func, $seo, $config, $type ?? 'san-pham');

// Determine action based on request
if ($id > 0) {
	// Product detail
	$viewData = $controller->detail($id, $type ?? 'san-pham');
	
	// Extract data for template
	extract($viewData);
	$rowDetail = $viewData['detail'];
	$rowTags = $viewData['tags'];
	$rowColor = $viewData['colors'];
	$rowSize = $viewData['sizes'];
	$productList = $viewData['list'];
	$productCat = $viewData['cat'];
	$productItem = $viewData['item'];
	$productSub = $viewData['sub'];
	$productBrand = $viewData['brand'];
	$rowDetailPhoto = $viewData['photos'];
	$relatedProducts = $viewData['related'];
	$breadcrumbs = $viewData['breadcrumbs'];
	
} elseif ($idc > 0) {
	// Category page
	$curPage = (int)($_GET['p'] ?? 1);
	$viewData = $controller->category($idc, $type ?? 'san-pham', $curPage, 12);
	
	extract($viewData);
	$products = $viewData['products'];
	$paging = $viewData['paging'];
	$breadcrumbs = $viewData['breadcrumbs'];
	
} elseif (!empty($_GET['keyword'])) {
	// Search page
	$keyword = $_GET['keyword'];
	$curPage = (int)($_GET['p'] ?? 1);
	$viewData = $controller->search($keyword, $type ?? 'san-pham', $curPage, 12);
	
	extract($viewData);
	$products = $viewData['products'];
	$paging = $viewData['paging'];
	
} else {
	// Product listing
	$filters = [];
	if ($idl) $filters['id_list'] = $idl;
	if ($idc) $filters['id_cat'] = $idc;
	if ($idi) $filters['id_item'] = $idi;
	if ($ids) $filters['id_sub'] = $ids;
	if ($idb) $filters['id_brand'] = $idb;
	if (!empty($_GET['keyword'])) {
		$filters['keyword'] = $_GET['keyword'];
	}
	if (!empty($_GET['status'])) {
		$filters['status'] = $_GET['status'];
	}
	
	$curPage = (int)($_GET['p'] ?? 1);
	$viewData = $controller->index($type ?? 'san-pham', $filters, $curPage, 12);
	
	extract($viewData);
	$products = $viewData['products'];
	$paging = $viewData['paging'];
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~931 dòng với nhiều rawQuery
 * CODE MỚI: ~150 dòng với Repositories
 * 
 * GIẢM: ~84% code!
 * 
 * LỢI ÍCH:
 * - Sử dụng ProductRepository thay vì rawQuery
 * - Sử dụng CategoryRepository cho categories
 * - Sử dụng TagsRepository cho tags
 * - Sử dụng SEOHelper cho SEO
 * - Sử dụng BreadcrumbHelper cho breadcrumbs
 * - Code dễ đọc và maintain hơn
 */

