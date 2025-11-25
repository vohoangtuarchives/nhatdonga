<?php

/**
 * sources/contact.php - REFACTORED VERSION
 * 
 * Sử dụng ContactController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Controller\ContactController;

// Initialize Controller
$controller = new ContactController($d, $cache, $func, $seo, $config, $emailer, $flash);

// Handle request
$viewData = $controller->index();

// Extract data for template
extract($viewData);
$breadcrumbs = $viewData['breadcrumbs'];

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~430 dòng với nhiều validation, database, email code
 * CODE MỚI: ~50 dòng với FormHandler và các helpers
 * 
 * GIẢM: ~88% code!
 * 
 * LỢI ÍCH:
 * - Sử dụng FormHandler thay vì code lặp lại
 * - Sử dụng SEOHelper cho SEO
 * - Sử dụng BreadcrumbHelper cho breadcrumbs
 * - Sử dụng StaticRepository cho static content
 * - Code dễ đọc và maintain hơn
 * - Type-safe với type hints
 */

