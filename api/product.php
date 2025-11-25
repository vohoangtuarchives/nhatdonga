<?php

/**
 * api/product.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của api/product.php
 * Sử dụng ProductRepository và PaginationHelper
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp api/product.php api/product.php.backup
 * 2. Copy file này: cp api/product-refactored.php api/product.php
 * 3. Test kỹ trước khi deploy
 */

include "config.php";

use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\TagsRepository;
use Tuezy\PaginationHelper;
use Tuezy\Config;
use Tuezy\SecurityHelper;
use Tuezy\Service\ProductService;

// Initialize Config
$configObj = new Config($config);

// Initialize PaginationsAjax
include LIBRARIES . "class/class.PaginationsAjax.php";
$pagingAjax = new PaginationsAjax();

// Initialize Repositories and Helpers
$productRepo = new ProductRepository($d, $cache, $lang, $sluglang);
$categoryRepo = new CategoryRepository($d, $cache, $lang, $sluglang, 'product');
$tagsRepo = new TagsRepository($d, $cache, $lang, $sluglang);
$productService = new ProductService($productRepo, $categoryRepo, $tagsRepo, $d, $lang);
$paginationHelper = new PaginationHelper($pagingAjax, $func);

// Get parameters
$perPage = (int)($_GET['perpage'] ?? 12);
$eShow = SecurityHelper::sanitizeGet('eShow', '');
$idList = (int)($_GET['idList'] ?? 0);
$pNoibat = SecurityHelper::sanitizeGet('noibat', 'all');
$p = (int)($_GET['p'] ?? 1);

$pagingAjax->perpage = $perPage;
$start = $paginationHelper->getStartPoint($p, $perPage);

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
$filters['status'] = 'noibat';
$listResult = $productService->getListing('san-pham', $filters, $p, $perPage);
$products = $listResult['items'];
$totalItems = $listResult['total'];

// Pagination
$paging = $pagingAjax->getAllPageLinks($totalItems, $pageLink, $eShow);

// Output HTML (giữ nguyên format cũ)
if ($totalItems) {
	$productItems = $products;
	$paginationHtml = $paging;
	include TEMPLATE . 'components/product-grid.php';
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

