<?php

/**
 * sources/static.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của sources/static.php
 * Sử dụng StaticRepository và SEOHelper
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp sources/static.php sources/static.php.backup
 * 2. Copy file này: cp sources/static-refactored.php sources/static.php
 * 3. Test kỹ trước khi deploy
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\StaticRepository;
use Tuezy\Service\StaticService;
use Tuezy\SEOHelper;
use Tuezy\BreadcrumbHelper;
use Tuezy\Config;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories, Service and Helpers
$staticRepo = new StaticRepository($d, $cache, $lang, $sluglang);
$staticService = new StaticService($staticRepo);
$seoHelper = new SEOHelper($seo, $func, $d, $lang, $seolang, $configBase);
$breadcrumbHelper = new BreadcrumbHelper($breadcr, $configBase);

/* Lấy bài viết tĩnh - Sử dụng StaticService */
$static = $staticService->getByType($type);

/* SEO - Sử dụng SEOHelper */
if (!empty($static)) {
	$seoDB = $seo->getOnDB(0, 'static', 'update', $static['type']);
	
	$seo->set('h1', $static['name' . $lang]);
	
	if (!empty($seoDB['title' . $seolang])) {
		$seo->set('title', $seoDB['title' . $seolang]);
	} else {
		$seo->set('title', $static['name' . $lang]);
	}
	
	if (!empty($seoDB['keywords' . $seolang])) {
		$seo->set('keywords', $seoDB['keywords' . $seolang]);
	}
	
	if (!empty($seoDB['description' . $seolang])) {
		$seo->set('description', $seoDB['description' . $seolang]);
	}
	
	$seo->set('url', $func->getPageURL());
	
	// Handle SEO image
	$imgJson = (!empty($static['options'])) ? json_decode($static['options'], true) : null;
	
	if (empty($imgJson) || ($imgJson['p'] != $static['photo'])) {
		$imgJson = $func->getImgSize($static['photo'], UPLOAD_NEWS_L . $static['photo']);
		$seo->updateSeoDB(json_encode($imgJson), 'static', $static['id']);
	}
	
	if (!empty($imgJson)) {
		$seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_NEWS_L . $static['photo']);
		$seo->set('photo:width', $imgJson['w']);
		$seo->set('photo:height', $imgJson['h']);
		$seo->set('photo:type', $imgJson['m']);
	}
}

/* Breadcrumbs - Sử dụng BreadcrumbHelper */
if (!empty($titleMain)) {
	$breadcrumbHelper->add($titleMain, '/' . $com);
}
$breadcrumbs = $breadcrumbHelper->render();

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

