<?php

/**
 * sources/video.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của sources/video.php
 * Sử dụng PhotoRepository, SEOHelper, PaginationHelper
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp sources/video.php sources/video.php.backup
 * 2. Copy file này: cp sources/video-refactored.php sources/video.php
 * 3. Test kỹ trước khi deploy
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\PhotoRepository;
use Tuezy\SEOHelper;
use Tuezy\BreadcrumbHelper;
use Tuezy\PaginationHelper;
use Tuezy\Config;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories and Helpers
$photoRepo = new PhotoRepository($d, $cache, $lang, $sluglang);
$seoHelper = new SEOHelper($seo, $func, $d, $lang, $seolang, $configBase);
$breadcrumbHelper = new BreadcrumbHelper($breadcr, $configBase);
$paginationHelper = new PaginationHelper($pagingAjax ?? null, $func);

/* Lấy videos - Sử dụng PhotoRepository */
$curPage = $paginationHelper->getCurrentPage();
$perPage = 10;
$start = $paginationHelper->getStartPoint($curPage, $perPage);

// Get all videos for count
$allVideos = $photoRepo->getVideos($type, 0, "numb,id desc");
$totalItems = count($allVideos);

// Get paginated videos
$video = array_slice($allVideos, $start, $perPage);

// Pagination
$url = $func->getCurrentPageURL();
$paging = $func->pagination($totalItems, $perPage, $curPage, $url);

/* SEO - Sử dụng SEOHelper */
$seoHelper->setupFromSeopage($type, $titleMain);

/* Breadcrumbs - Sử dụng BreadcrumbHelper */
if (!empty($titleMain)) {
	$breadcrumbHelper->add($titleMain, '/' . $com);
}
$breadcrumbs = $breadcrumbHelper->render();

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~97 dòng với rawQuery và SEO code lặp lại
 * CODE MỚI: ~45 dòng với Repositories và Helpers
 * 
 * GIẢM: ~54% code
 * 
 * LỢI ÍCH:
 * - Sử dụng PhotoRepository thay vì rawQuery
 * - Sử dụng SEOHelper cho SEO
 * - Sử dụng PaginationHelper
 * - Sử dụng BreadcrumbHelper
 * - Code dễ đọc và maintain hơn
 */

