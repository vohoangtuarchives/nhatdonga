<?php

/**
 * sources/news.php - REFACTORED VERSION
 * 
 * Sử dụng NewsController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Controller\NewsController;
use Tuezy\RequestHandler;

// Initialize RequestHandler
$params = RequestHandler::getParams();
$id = (int)$params['id'];
$idl = (int)($_GET['idl'] ?? 0);
$idc = (int)($_GET['idc'] ?? 0);
$idi = (int)($_GET['idi'] ?? 0);
$ids = (int)($_GET['ids'] ?? 0);

// Initialize Controller
$controller = new NewsController($d, $cache, $func, $seo, $config, $type ?? 'tin-tuc');

// Determine action based on request
if ($id > 0) {
	// News detail
	$viewData = $controller->detail($id, $type ?? 'tin-tuc');
	
	// Extract data for template
	extract($viewData);
	$rowDetail = $viewData['detail'];
	$newsList = $viewData['list'];
	$newsCat = $viewData['cat'];
	$newsItem = $viewData['item'];
	$newsSub = $viewData['sub'];
	$rowDetailPhoto = $viewData['photos'];
	$otherNewss = $viewData['related'];
	$breadcrumbs = $viewData['breadcrumbs'];
	
} elseif ($idl > 0) {
	// Category page
	$curPage = (int)($_GET['p'] ?? 1);
	$viewData = $controller->category($idl, $type ?? 'tin-tuc', $curPage, 12);
	
	extract($viewData);
	$news = $viewData['news'];
	$paging = $viewData['paging'];
	$breadcrumbs = $viewData['breadcrumbs'];
	$newsList = $viewData['category'];
	
} else {
	// News listing
	$filters = [];
	if (!empty($_GET['keyword'])) {
		$filters['keyword'] = $_GET['keyword'];
	}
	
	$curPage = (int)($_GET['p'] ?? 1);
	$viewData = $controller->index($type ?? 'tin-tuc', $filters, $curPage, 12);
	
	extract($viewData);
	$news = $viewData['news'];
	$paging = $viewData['paging'];
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~665 dòng với nhiều rawQuery
 * CODE MỚI (với Repository): ~150 dòng
 * CODE MỚI (với Service): ~120 dòng
 * 
 * GIẢM: ~82% code!
 * 
 * LỢI ÍCH:
 * - Sử dụng NewsService để tách business logic
 * - Sử dụng NewsRepository cho data access
 * - Sử dụng CategoryRepository cho categories
 * - Sử dụng SEOHelper cho SEO
 * - Sử dụng BreadcrumbHelper cho breadcrumbs
 * - Sử dụng PaginationHelper cho pagination
 * - Code dễ đọc, maintain và test hơn
 * - Dễ tái sử dụng logic giữa frontend và admin
 */

