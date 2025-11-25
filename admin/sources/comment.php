<?php

/**
 * admin/sources/comment.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của admin/sources/comment.php
 * Sử dụng SecurityHelper và Repository pattern
 * 
 * CÁCH SỬ DỤNG:
 * Có thể copy từng phần vào admin/sources/comment.php hoặc thay thế hoàn toàn
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

/* Kiểm tra active comment */
$variant = SecurityHelper::sanitizeGet('variant', '');
$type = SecurityHelper::sanitizeGet('type', '');

if (empty($config[$variant][$type]['comment'])) {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

switch($act) {
	case "man":
		viewMans();
		$template = "comment/man/mans";
		break;

	default:
		$template = "404";
}

function viewMans()
{
	global $d, $func, $cache, $comment, $item, $variant, $type;

	$id = (int)SecurityHelper::sanitizeGet('id', 0);

	if (!empty($id)) {
		/* Get data detail - Sử dụng SecurityHelper */
		$item = $d->rawQueryOne(
			"SELECT * FROM #_{$variant} WHERE id = ? AND type = ? LIMIT 0,1",
			[$id, $type]
		);

		/* Check data detail */
		if (!empty($item)) {
			/* Comment */
			$comment = new Comments($d, $func, $item['id'], $item['type'], true);
		} else {
			$func->transfer("Dữ liệu không có thực", "index.php", false);
		}
	} else {
		$func->transfer("Trang không tồn tại", "index.php", false);
	}
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

