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
// Lấy ID trực tiếp từ $_GET vì router set $_GET['id'] sau khi RequestHandler được khởi tạo
$id = (int)($_GET['id'] ?? $params['id'] ?? 0);
$idl = (int)($_GET['idl'] ?? 0);
$idc = (int)($_GET['idc'] ?? 0);
$idi = (int)($_GET['idi'] ?? 0);
$ids = (int)($_GET['ids'] ?? 0);
$idb = (int)($_GET['idb'] ?? 0);

// Đảm bảo $type được set đúng từ router
if (empty($type)) {
	$type = 'san-pham';
}

// Initialize Controller
$controller = new ProductController($d, $cache, $func, $seo, $config, $type);

// Initialize default variables
$rowDetail = null;
$rowTags = [];
$rowColor = [];
$rowSize = [];
$productList = null;
$productCat = null;
$productItem = null;
$productSub = null;
$productBrand = null;
$rowDetailPhoto = [];
$relatedProducts = [];
$breadcrumbs = '';
$products = []; // Initialize products array
$paging = ''; // Initialize paging
// Determine action based on request
if ($id > 0) {
	// Product detail - đảm bảo type được truyền đúng
	$productType = !empty($type) ? $type : 'san-pham';
	$viewData = $controller->detail($id, $productType);
	
	// Extract data for template
	extract($viewData);
	$rowDetail = $viewData['detail'] ?? null;
	$rowTags = $viewData['tags'] ?? [];
	$rowColor = $viewData['colors'] ?? [];
	$rowSize = $viewData['sizes'] ?? [];
	$productList = $viewData['list'] ?? null;
	$productCat = $viewData['cat'] ?? null;
	$productItem = $viewData['item'] ?? null;
	$productSub = $viewData['sub'] ?? null;
	$productBrand = $viewData['brand'] ?? null;
	$rowDetailPhoto = $viewData['photos'] ?? [];
	$relatedProducts = $viewData['related'] ?? [];
	$breadcrumbs = $viewData['breadcrumbs'] ?? '';
	
} elseif ($idc > 0) {
	// Category page
	$curPage = (int)($_GET['p'] ?? 1);
	$viewData = $controller->category($idc, $type ?? 'san-pham', $curPage, 12);
	
	extract($viewData);
	$products = $viewData['products'];
	$paging = $viewData['paging'];
	$breadcrumbs = $viewData['breadcrumbs'];
	$categoriesTree = $viewData['categoriesTree'] ?? [];
	$brands = $viewData['brands'] ?? [];
	$total = $viewData['total'] ?? 0;
	
} elseif (!empty($_GET['keyword'])) {
	// Search page
	$keyword = $_GET['keyword'];
	$curPage = (int)($_GET['p'] ?? 1);
	$viewData = $controller->search($keyword, $type ?? 'san-pham', $curPage, 12);
	
	extract($viewData);
	$products = $viewData['products'];
	$paging = $viewData['paging'];
	$categoriesTree = $viewData['categoriesTree'] ?? [];
	$brands = $viewData['brands'] ?? [];
	$total = $viewData['total'] ?? 0;
	
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
	
	// Handle filter params
	if (!empty($_GET['brand'])) {
		$filters['id_brand'] = (int)$_GET['brand'];
	}
	if (!empty($_GET['status_filter'])) {
		$filters['status'] = $_GET['status_filter'];
	}
	if (!empty($_GET['has_discount']) && $_GET['has_discount'] == '1') {
		$filters['has_discount'] = true;
	}
	if (!empty($_GET['price_min'])) {
		$filters['price_min'] = (float)$_GET['price_min'];
	}
	if (!empty($_GET['price_max'])) {
		$filters['price_max'] = (float)$_GET['price_max'];
	}
	
	$curPage = (int)($_GET['p'] ?? 1);
	$perPage = (int)($_GET['per_page'] ?? 12);
	$sortBy = $_GET['sort'] ?? 'default';
	$sortOrder = $_GET['order'] ?? 'desc';
	$viewData = $controller->index($type ?? 'san-pham', $filters, $curPage, $perPage, $sortBy, $sortOrder);
	
	extract($viewData);
	$products = $viewData['products'];
	$paging = $viewData['paging'];
	$categoriesTree = $viewData['categoriesTree'] ?? [];
	$brands = $viewData['brands'] ?? [];
	$total = $viewData['total'] ?? 0;
}

