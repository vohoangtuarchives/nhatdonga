<?php

/**
 * admin/sources/comment.php - REFACTORED VERSION
 * 
 * Sử dụng CommentAdminController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\Controller\CommentAdminController;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;
use Tuezy\SecurityHelper;

// Initialize Controller
$adminAuthHelper = new AdminAuthHelper($func, $d, $loginAdmin, $config);
$adminPermissionHelper = new AdminPermissionHelper($func, $config);
$controller = new CommentAdminController($d, $cache, $func, $config, $adminAuthHelper, $adminPermissionHelper);

/* Kiểm tra active comment */
$variant = SecurityHelper::sanitizeGet('variant', '');
$type = SecurityHelper::sanitizeGet('type', '');

if (empty($config[$variant][$type]['comment'])) {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

switch($act) {
	case "man":
		$id = (int)SecurityHelper::sanitizeGet('id', 0);
		
		if (empty($id)) {
			$func->transfer("Trang không tồn tại", "index.php", false);
		}
		
		/* Get data detail */
		$item = $d->rawQueryOne(
			"SELECT * FROM #_{$variant} WHERE id = ? AND type = ? LIMIT 0,1",
			[$id, $type]
		);

		/* Check data detail */
		if (empty($item)) {
			$func->transfer("Dữ liệu không có thực", "index.php", false);
		}
		
		/* Comment - Sử dụng class Comments cũ để tương thích với template */
		$comment = new Comments($d, $func, $item['id'], $item['type'], true);
		
		$template = "comment/man/mans";
		break;

	default:
		$template = "404";
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~50 dòng với htmlspecialchars trực tiếp
 * CODE MỚI: ~60 dòng với SecurityHelper
 * 
 * LỢI ÍCH:
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 * - Type-safe với type hints
 */

