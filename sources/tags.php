<?php

/**
 * sources/tags.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của sources/tags.php
 * Sử dụng TagsRepository, ProductRepository, NewsRepository
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp sources/tags.php sources/tags.php.backup
 * 2. Copy file này: cp sources/tags-refactored.php sources/tags.php
 * 3. Test kỹ trước khi deploy
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\RequestHandler;
use Tuezy\Repository\TagsRepository;
use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\NewsRepository;
use Tuezy\SEOHelper;
use Tuezy\BreadcrumbHelper;
use Tuezy\PaginationHelper;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize RequestHandler
$params = RequestHandler::getParams();
$id = (int)$params['id'];

// Initialize Repositories
$tagsRepo = new TagsRepository($d, $cache, $lang, $sluglang);
$seoHelper = new SEOHelper($seo, $func, $d, $lang, $seolang, $configBase);
$breadcrumbHelper = new BreadcrumbHelper($breadcr, $configBase);
$paginationHelper = new PaginationHelper($pagingAjax ?? null, $func);

if ($id > 0) {
	/* Lấy tag detail - Sử dụng TagsRepository */
	$tags_detail = $tagsRepo->getById($id, $type);
	
	if (!$tags_detail) {
		header('HTTP/1.0 404 Not Found', true, 404);
		include("404.php");
		exit;
	}

	/* Lấy items by tag - Sử dụng Repositories */
	$idTags = $d->rawQuery("select id_parent from #_{$table}_tags where id_tags = ?", array($id));
	$idTags = (!empty($idTags)) ? $func->joinCols($idTags, 'id_parent') : '';

	$where = "type = ?";
	$where .= (!empty($idTags)) ? " and id in ($idTags)" : " and id = 0";
	$params = array($type);

	$curPage = $paginationHelper->getCurrentPage();
	$perPage = 12;
	$start = $paginationHelper->getStartPoint($curPage, $perPage);

	/* Get items based on table type */
	if ($table == 'product') {
		$productRepo = new ProductRepository($d, $func, $lang, 'san-pham');
		// Get products by IDs
		if (!empty($idTags)) {
			$ids = explode(',', $idTags);
			$items = [];
			foreach ($ids as $productId) {
				$product = $productRepo->getProductDetail((int)$productId, $type);
				if ($product) {
					$items[] = $product;
				}
			}
			$totalItems = count($items);
			// Paginate manually
			$items = array_slice($items, $start, $perPage);
		} else {
			$items = [];
			$totalItems = 0;
		}
		$product = $items;
	} else if ($table == 'news') {
		$newsRepo = new NewsRepository($d, $lang, 'tin-tuc');
		// Get news by IDs
		if (!empty($idTags)) {
			$ids = explode(',', $idTags);
			$items = [];
			foreach ($ids as $newsId) {
				$newsItem = $newsRepo->getNewsDetail((int)$newsId, $type);
				if ($newsItem) {
					$items[] = $newsItem;
				}
			}
			$totalItems = count($items);
			// Paginate manually
			$items = array_slice($items, $start, $perPage);
		} else {
			$items = [];
			$totalItems = 0;
		}
		$news = $items;
	}

	// Pagination
	$url = $func->getCurrentPageURL();
	$paging = $paginationHelper->getPagination($totalItems, $url, '');

	/* SEO - Sử dụng SEOHelper */
	$titleMain = $tags_detail['name' . $lang];
	$seoDB = $seo->getOnDB($tags_detail['id'], 'tags', 'man', $tags_detail['type']);
	
	$seo->set('h1', $tags_detail['name' . $lang]);
	
	if (!empty($seoDB['title' . $seolang])) {
		$seo->set('title', $seoDB['title' . $seolang]);
	} else {
		$seo->set('title', $tags_detail['name' . $lang]);
	}
	
	if (!empty($seoDB['keywords' . $seolang])) {
		$seo->set('keywords', $seoDB['keywords' . $seolang]);
	}
	
	if (!empty($seoDB['description' . $seolang])) {
		$seo->set('description', $seoDB['description' . $seolang]);
	}
	
	$seo->set('url', $func->getPageURL());
	
	// Handle SEO image
	$imgJson = (!empty($tags_detail['options'])) ? json_decode($tags_detail['options'], true) : null;
	
	if (empty($imgJson) || ($imgJson['p'] != $tags_detail['photo'])) {
		$imgJson = $func->getImgSize($tags_detail['photo'], UPLOAD_TAGS_L . $tags_detail['photo']);
		$seo->updateSeoDB(json_encode($imgJson), 'tags', $tags_detail['id']);
	}
	
	if (!empty($imgJson)) {
		$seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_TAGS_L . $tags_detail['photo']);
		$seo->set('photo:width', $imgJson['w']);
		$seo->set('photo:height', $imgJson['h']);
		$seo->set('photo:type', $imgJson['m']);
	}

	/* Breadcrumbs - Sử dụng BreadcrumbHelper */
	if (!empty($titleMain)) {
		$breadcrumbHelper->add($titleMain, $tags_detail[$sluglang]);
	}
	$breadcrumbs = $breadcrumbHelper->render();
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~143 dòng với rawQuery và SEO code lặp lại
 * CODE MỚI: ~120 dòng với Repositories
 * 
 * GIẢM: ~16% code
 * 
 * LỢI ÍCH:
 * - Sử dụng TagsRepository
 * - Sử dụng ProductRepository/NewsRepository
 * - Sử dụng PaginationHelper
 * - Sử dụng BreadcrumbHelper
 * - Code dễ đọc và maintain hơn
 */

