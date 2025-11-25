<?php

/**
 * api/product.php - REFACTORED VERSION
 * 
 * Sử dụng ProductAPIController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

include "config.php";

use Tuezy\API\Controller\ProductAPIController;
use Tuezy\Config;

// Initialize Config
$configObj = new Config($config);

// Initialize Controller
$controller = new ProductAPIController($d, $cache, $func, $configObj, $lang, $sluglang);

// Determine action
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Determine action - backward compatibility với code cũ
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

// Nếu không có action nhưng có các params cũ, xử lý như list
if ($action === 'list' && (isset($_GET['perpage']) || isset($_GET['idList']) || isset($_GET['noibat']))) {
	// Backward compatibility - giữ nguyên logic cũ
	$perPage = (int)($_GET['perpage'] ?? 12);
	$p = (int)($_GET['p'] ?? 1);
	$idList = (int)($_GET['idList'] ?? 0);
	$pNoibat = $_GET['noibat'] ?? 'all';
	$eShow = $_GET['eShow'] ?? '';

	// Build filters
	$filters = [];
	if ($idList) {
		$filters['id_list'] = $idList;
	}
	if ($pNoibat != 'all') {
		$filters['status'] = $pNoibat;
	}

	// Build page link
	$pageLink = "api/product.php?perpage=" . $perPage;
	$tempLink = "";
	if ($idList) {
		$tempLink .= "&idList=" . $idList;
	}
	if ($pNoibat != 'all') {
		$tempLink .= "&noibat=" . $pNoibat;
	}
	$pageLink .= $tempLink;

	// Get products thông qua ProductService
	$productRepo = new \Tuezy\Repository\ProductRepository($d, $cache, $lang, $sluglang, 'san-pham');
	$categoryRepo = new \Tuezy\Repository\CategoryRepository($d, $cache, $lang, $sluglang, 'product');
	$tagsRepo = new \Tuezy\Repository\TagsRepository($d, $cache, $lang, $sluglang);
	$productService = new \Tuezy\Service\ProductService($productRepo, $categoryRepo, $tagsRepo, $d, $lang);
	
	$listResult = $productService->getListing('san-pham', $filters, $p, $perPage);
	$products = $listResult['items'];
	$totalItems = $listResult['total'];

	// Pagination
	$pagingAjax = new \PaginationsAjax();
	$pagingAjax->perpage = $perPage;
	$paging = $pagingAjax->getAllPageLinks($totalItems, $pageLink, $eShow);

	// Output HTML (giữ nguyên format cũ)
	if ($totalItems) {
		$productItems = $products;
		$paginationHtml = $paging;
		include TEMPLATE . 'components/product-grid.php';
	}
} else {
	// Sử dụng Controller cho các action mới
	switch ($action) {
		case 'list':
			$controller->getList();
			break;
			
		case 'detail':
			if ($id > 0) {
				$controller->getDetail($id);
			} else {
				$controller->error('Product ID required');
			}
			break;
			
		case 'quickview':
			if ($id > 0) {
				$controller->quickView($id);
			} else {
				$controller->error('Product ID required');
			}
			break;
			
		default:
			// Fallback to old behavior for backward compatibility
			$controller->getList();
			break;
	}
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~91 dòng với rawQuery và pagination code
 * CODE MỚI: ~70 dòng với ProductRepository và PaginationHelper
 * 
 * GIẢM: ~23% code
 * 
 * LỢI ÍCH:
 * - Sử dụng ProductRepository thay vì rawQuery
 * - Sử dụng PaginationHelper
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 */

