<?php

/**
 * admin/sources/contact.php - REFACTORED VERSION (Partial)
 * 
 * File này là phiên bản refactored của admin/sources/contact.php
 * Sử dụng AdminCRUDHelper và ContactRepository
 * 
 * CÁCH SỬ DỤNG:
 * Có thể copy từng phần vào admin/sources/contact.php hoặc thay thế hoàn toàn
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\AdminCRUDHelper;
use Tuezy\Repository\ContactRepository;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize ContactRepository
$contactRepo = new ContactRepository($d, $cache);

// Initialize AdminCRUDHelper for contact
// Note: Contact không có type, nên cần custom một chút
$adminCRUD = new AdminCRUDHelper(
	$d, 
	$func, 
	$flash, 
	'contact', 
	'', 
	'contact', 
	UPLOAD_FILE_L, 
	$lang, 
	$sluglang
);

switch($act) {
	case "man":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}
		if (!empty($_REQUEST['status'])) {
			$filters['status'] = SecurityHelper::sanitize($_REQUEST['status']);
		}

		// Get contacts using ContactRepository
		$perPage = 10;
		$start = ($curPage - 1) * $perPage;
		$items = $contactRepo->getAll($filters, $start, $perPage);
		$totalItems = $contactRepo->count($filters);
		
		$url = "index.php?com=contact&act=man";
		$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
		$template = "contact/man/mans";
		break;
		
	case "edit":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			$item = $contactRepo->getById($id);
			if (!$item) {
				$func->transfer("Dữ liệu không có thực", "index.php?com=contact&act=man", false);
			}
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=contact&act=man", false);
		}
		$template = "contact/man/man_add";
		break;
		
	case "save":
		// Save logic - có thể sử dụng ContactRepository->update()
		// Giữ nguyên logic cũ cho phần này vì phức tạp
		saveMan();
		break;
		
	case "delete":
		$id = (int)($_GET['id'] ?? 0);
		if ($id && $contactRepo->delete($id)) {
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=contact&act=man");
		} else {
			$func->transfer("Xóa dữ liệu thất bại", "index.php?com=contact&act=man", false);
		}
		break;
		
	default:
		$template = "404";
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~228 dòng với nhiều functions
 * CODE MỚI: ~90 dòng với ContactRepository và AdminCRUDHelper
 * 
 * GIẢM: ~61% code
 * 
 * LỢI ÍCH:
 * - Sử dụng ContactRepository
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 */

