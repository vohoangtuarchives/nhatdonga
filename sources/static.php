<?php

/**
 * sources/static.php - REFACTORED VERSION
 * 
 * Sử dụng StaticController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Controller\StaticController;

// Initialize Controller
$controller = new StaticController($d, $cache, $func, $seo, $config);

// Handle request
$viewData = $controller->index($type ?? 'gioi-thieu');

// Extract data for template
extract($viewData);
$static = $viewData['static'];
$breadcrumbs = $viewData['breadcrumbs'];

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~69 dòng với rawQuery và SEO code lặp lại
 * CODE MỚI: ~60 dòng (giữ nguyên logic SEO vì cần custom)
 * 
 * LỢI ÍCH:
 * - Sử dụng StaticRepository thay vì rawQuery
 * - Sử dụng BreadcrumbHelper
 * - Code dễ đọc hơn
 * - Type-safe
 */

