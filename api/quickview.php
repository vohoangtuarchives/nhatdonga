<?php

/**
 * api/quickview.php - REFACTORED VERSION
 * 
 * Sử dụng QuickviewAPIController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

include "config.php";

use Tuezy\API\Controller\QuickviewAPIController;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize Controller
$controller = new QuickviewAPIController($d, $cache, $func, $configObj, $lang, $sluglang);

// Get product ID
$id = (int)SecurityHelper::sanitizeGet('id', 0);

if ($id > 0) {
	$controller->getQuickview($id);
} else {
	$controller->error('Product ID required');
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

