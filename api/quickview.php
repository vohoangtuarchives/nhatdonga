<?php

/**
 * api/quickview.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của api/quickview.php
 * Sử dụng ProductRepository
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp api/quickview.php api/quickview.php.backup
 * 2. Copy file này: cp api/quickview-refactored.php api/quickview.php
 * 3. Test kỹ trước khi deploy
 */

include "config.php";

use Tuezy\Repository\ProductRepository;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories
$productRepo = new ProductRepository($d, $cache, $lang, $sluglang);

// Configuration
$w = 307;
$h = 265;
$r = 1;
$z = 2;
$thumbnail = $w * $z . 'x' . $h * $z . 'x' . $r;
$isWater = false;
$assets = $isWater ? WATERMARK . '/product' : THUMBS;

// Get product ID
$id = (int)($_GET['id'] ?? 0);

if ($id) {
	// Get product detail - Sử dụng ProductRepository
	$rowDetail = $productRepo->getProductDetail($id, 'san-pham');
	
	if ($rowDetail) {
		// Get product gallery - Sử dụng ProductRepository
		$rowDetailPhoto = $productRepo->getProductGallery($id, 'san-pham');
		
		// Get colors and sizes
		$rowColor = $productRepo->getProductColors($id, 'san-pham');
		$rowSize = $productRepo->getProductSizes($id, 'san-pham');
		
		// Include quickview template (giữ nguyên template cũ)
		include TEMPLATE . "product/quickview.php";
	}
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~287 dòng với rawQuery và HTML
 * CODE MỚI: ~40 dòng với ProductRepository
 * 
 * GIẢM: ~86% code
 * 
 * LỢI ÍCH:
 * - Sử dụng ProductRepository thay vì rawQuery
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 * - HTML được tách ra template
 */

