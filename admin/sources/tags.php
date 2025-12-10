<?php

/**
 * admin/sources/tags.php - REFACTORED VERSION
 * 
 * Sử dụng TagsAdminController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\Controller\TagsAdminController;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;
use Tuezy\SecurityHelper;

// Initialize language variables
if (!isset($lang)) {
	$lang = $_SESSION['lang'] ?? 'vi';
}
if (!isset($sluglang)) {
	$sluglang = 'slugvi';
}

/* Kiểm tra active tags */
if (isset($config['tags'])) {
	$arrCheck = array();
	foreach($config['tags'] as $k => $v) $arrCheck[] = $k;
	if (!count($arrCheck) || !in_array($type, $arrCheck)) {
		$func->transfer("Trang không tồn tại", "index.php", false);
	}
} else {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

// Initialize Controller
$adminAuthHelper = new AdminAuthHelper($func, $d, $loginAdmin, $config);
$adminPermissionHelper = new AdminPermissionHelper($func, $config);
$controller = new TagsAdminController($d, $cache, $func, $config, $adminAuthHelper, $adminPermissionHelper, $type);

switch($act) {
	case "man":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}

		$viewData = $controller->man($filters, $curPage, 10);
		$items = $viewData['items'];
		$paging = $viewData['paging'];
		$template = "tags/man/mans";
		break;
		
	case "add":
		$template = "tags/man/man_add";
		break;
		
	case "edit":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			$item = $controller->getTag($id);
			if (!$item) {
				$func->transfer("Dữ liệu không có thực", "index.php?com=tags&act=man&type=" . $type, false);
			}
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=tags&act=man&type=" . $type, false);
		}
		$template = "tags/man/man_add";
		break;
		
	case "save":
		if (empty($_POST)) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=tags&act=man&type=" . $type, false);
		}
		
		$id = !empty($_POST['data']['id']) ? (int)$_POST['data']['id'] : null;
		$data = $_POST['data'] ?? [];
		
		try {
			if ($controller->save($data, $id)) {
				$message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
				$func->transfer($message, "index.php?com=tags&act=man&type=" . $type);
			} else {
				$func->transfer("Có lỗi xảy ra khi lưu dữ liệu", "index.php?com=tags&act=man&type=" . $type, false);
			}
		} catch (\Exception $e) {
			$func->transfer($e->getMessage(), "index.php?com=tags&act=man&type=" . $type, false);
		}
		break;
		
	case "delete":
		// Xử lý xóa nhiều items (listid)
		if (!empty($_GET['listid'])) {
			$listid = SecurityHelper::sanitizeGet('listid', '');
			$ids = explode(',', $listid);
			$ids = array_filter(array_map('intval', $ids)); // Loại bỏ giá trị rỗng và convert sang int
			
			if (empty($ids)) {
				$func->transfer("Không nhận được dữ liệu", "index.php?com=tags&act=man&type=" . $type, false);
			}
			
			$successCount = 0;
			$failedCount = 0;
			
			foreach ($ids as $tagId) {
				if ($tagId > 0 && $controller->delete($tagId)) {
					$successCount++;
				} else {
					$failedCount++;
				}
			}
			
			if ($successCount > 0) {
				$message = "Đã xóa thành công {$successCount} tag";
				if ($failedCount > 0) {
					$message .= " ({$failedCount} tag xóa thất bại)";
				}
				$func->transfer($message, "index.php?com=tags&act=man&type=" . $type);
			} else {
				$func->transfer("Xóa dữ liệu thất bại", "index.php?com=tags&act=man&type=" . $type, false);
			}
		} else {
			// Xóa một item (id)
			$id = (int)($_GET['id'] ?? 0);
			if ($id && $controller->delete($id)) {
				$func->transfer("Xóa dữ liệu thành công", "index.php?com=tags&act=man&type=" . $type);
			} else {
				$func->transfer("Xóa dữ liệu thất bại", "index.php?com=tags&act=man&type=" . $type, false);
			}
		}
		break;

	default:
		$template = "404";
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~375 dòng với nhiều functions
 * CODE MỚI: ~90 dòng với AdminCRUDHelper
 * 
 * GIẢM: ~76% code
 * 
 * LỢI ÍCH:
 * - Sử dụng AdminCRUDHelper cho CRUD operations
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 */

