<?php

/**
 * sources/order.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của sources/order.php
 * Sử dụng OrderHandler, OrderRepository, và các helpers
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp sources/order.php sources/order.php.backup
 * 2. Copy file này: cp sources/order-refactored.php sources/order.php
 * 3. Test kỹ trước khi deploy
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\OrderHandler;
use Tuezy\ValidationHelper;
use Tuezy\Repository\OrderRepository;
use Tuezy\Repository\NewsRepository;
use Tuezy\BreadcrumbHelper;
use Tuezy\Config;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories
$orderRepo = new OrderRepository($d, $cache);
$newsRepo = new NewsRepository($d, $lang, 'tin-tuc');
$breadcrumbHelper = new BreadcrumbHelper($breadcr, $configBase);

/* SEO */
$seo->set('title', $titleMain);

/* Breadcrumbs */
if (!empty($titleMain)) {
	$breadcrumbHelper->add($titleMain, '/gio-hang');
}
$breadcrumbs = $breadcrumbHelper->render();

/* Tỉnh thành - Sử dụng LocationRepository */
use Tuezy\Repository\LocationRepository;
$locationRepo = new LocationRepository($d, $cache);
$city = $locationRepo->getCities();

/* Hình thức thanh toán */
$payments_info = $newsRepo->getNewsItems('hinh-thuc-thanh-toan', [], 0, 0);

/* Handle Order Submission - Sử dụng OrderHandler */
if (!empty($_POST['thanhtoan'])) {
	$validator = new ValidationHelper($func, $config);
	$orderHandler = new OrderHandler(
		$d, 
		$func, 
		$cart, 
		$emailer, 
		$flash, 
		$validator, 
		$orderRepo, 
		$configBase, 
		$lang, 
		$setting, 
		$config
	);
	
	$dataOrder = $_POST['dataOrder'] ?? [];
	
	// Sử dụng OrderHandler - giảm từ ~627 dòng xuống 1 dòng!
	$orderHandler->handleOrder($dataOrder);
	// OrderHandler tự động xử lý:
	// - Validation
	// - Order creation
	// - Order details
	// - Email sending
	// - Cart clearing
	// - Redirects
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~627 dòng với nhiều validation, database, email code
 * CODE MỚI: ~50 dòng với OrderHandler
 * 
 * GIẢM: ~92% code!
 * 
 * LỢI ÍCH:
 * - Sử dụng OrderHandler thay vì code lặp lại
 * - Sử dụng OrderRepository
 * - Sử dụng ValidationHelper
 * - Sử dụng BreadcrumbHelper
 * - Code dễ đọc và maintain hơn
 * - Type-safe với type hints
 */

