<?php

/**
 * sources/search.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của sources/search.php
 * Sử dụng ProductRepository và PaginationHelper
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp sources/search.php sources/search.php.backup
 * 2. Copy file này: cp sources/search-refactored.php sources/search.php
 * 3. Test kỹ trước khi deploy
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\RequestHandler;
use Tuezy\Repository\ProductRepository;
use Tuezy\PaginationHelper;
use Tuezy\Config;
use Tuezy\SecurityHelper;
use Tuezy\Service\ProductService;

// Initialize Config
$configObj = new Config($config);

// Initialize RequestHandler
$params = RequestHandler::getParams();

// Initialize Repositories
$productRepo = new ProductRepository($d, $cache, $lang, $sluglang, 'san-pham');
$productService = new ProductService($productRepo, null, null, $d, $lang);
$paginationHelper = new PaginationHelper($pagingAjax ?? null, $func);

/* Tìm kiếm sản phẩm */
if (!empty($_GET['keyword'])) {
	$tukhoa = SecurityHelper::sanitizeGet('keyword');
	$tukhoa = $func->changeTitle($tukhoa);

	if ($tukhoa) {
		// Sử dụng ProductRepository với keyword filter
		$filters = ['keyword' => $tukhoa];
		
		$curPage = $paginationHelper->getCurrentPage();
		$perPage = 12;
		$start = $paginationHelper->getStartPoint($curPage, $perPage);
		
		$listResult = $productService->getListing('san-pham', $filters, $curPage, $perPage);
		$product = $listResult['items'];
		$totalItems = $listResult['total'];

		// Pagination
		$url = $func->getCurrentPageURL();
		$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
	}
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~67 dòng với rawQuery
 * CODE MỚI: ~35 dòng với ProductRepository
 * 
 * GIẢM: ~48% code
 * 
 * LỢI ÍCH:
 * - Sử dụng ProductRepository thay vì rawQuery
 * - Sử dụng SecurityHelper cho sanitization
 * - Sử dụng PaginationHelper
 * - Code dễ đọc và maintain hơn
 */

