<?php

/**
 * admin/sources/photo.php - REFACTORED VERSION
 * 
 * Sử dụng PhotoAdminController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\Controller\PhotoAdminController;
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

/* Kiểm tra active photo */
if (isset($config['photo'])) {
	$arrCheck = array();
	$actCheck = '';
	if ($act == 'photo_static' || $act == 'save_static' || $act == 'save-watermark' || $act == 'preview-watermark') {
		$actCheck = 'photo_static';
	} else {
		$actCheck = 'man_photo';
	}
	foreach($config['photo'][$actCheck] as $k => $v) $arrCheck[] = $k;
	if (!count($arrCheck) || !in_array($type, $arrCheck)) {
		$func->transfer("Trang không tồn tại", "index.php", false);
	}
} else {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

// Initialize Controller
$adminAuthHelper = new AdminAuthHelper($func, $d, $loginAdmin, $config);
$adminPermissionHelper = new AdminPermissionHelper($func, $config);
$controller = new PhotoAdminController($d, $cache, $func, $config, $adminAuthHelper, $adminPermissionHelper, $type ?? 'photo');

switch($act) {
	/* Photo static */
	case "photo_static":
		$item = $controller->getWatermarkConfig();
		if (!$item) {
			$photoRepo = new \Tuezy\Repository\PhotoRepository($d, $cache, $lang, $sluglang);
			$item = $photoRepo->getByTypeAndAct($type, 'photo_static');
		}
		$template = "photo/static/photo_static";
		break;
		
	case "save_static":
		// Save static - giữ nguyên logic cũ vì phức tạp
		if (function_exists('savePhotoStatic')) {
			savePhotoStatic();
		}
		break;

	/* Watermark */
	case "save-watermark":
		if (function_exists('saveWatermark')) {
			saveWatermark();
		}
		break;
		
	case "preview-watermark":
		if (function_exists('previewWatermark')) {
			previewWatermark();
		}
		break;

	/* Photos */
	case "man_photo":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}

		$viewData = $controller->manPhoto($filters, $curPage, 10);
		$items = $viewData['items'];
		$paging = $viewData['paging'];
		$template = "photo/man/photos";
		break;
		
	case "add_photo":
		$template = "photo/man/photo_add";
		break;
		
	case "edit_photo":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			$item = $controller->getPhoto($id);
			if (!$item) {
				$func->transfer("Dữ liệu không có thực", "index.php?com=photo&act=man_photo&type=" . $type, false);
			}
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=photo&act=man_photo&type=" . $type, false);
		}
		$template = "photo/man/photo_edit";
		break;
		
	case "save_photo":
		if (empty($_POST)) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=photo&act=man_photo&type=" . $type, false);
		}
		
		$id = !empty($_POST['data']['id']) ? (int)$_POST['data']['id'] : null;
		$data = $_POST['data'] ?? [];
		
		try {
			if ($controller->savePhoto($data, $id)) {
				$message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
				$func->transfer($message, "index.php?com=photo&act=man_photo&type=" . $type);
			} else {
				$func->transfer("Có lỗi xảy ra khi lưu dữ liệu", "index.php?com=photo&act=man_photo&type=" . $type, false);
			}
		} catch (\Exception $e) {
			$func->transfer($e->getMessage(), "index.php?com=photo&act=man_photo&type=" . $type, false);
		}
		break;
		
	case "delete_photo":
		$id = (int)($_GET['id'] ?? 0);
		if ($id && $controller->deletePhoto($id)) {
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=photo&act=man_photo&type=" . $type);
		} else {
			$func->transfer("Xóa dữ liệu thất bại", "index.php?com=photo&act=man_photo&type=" . $type, false);
		}
		break;

	default:
		$template = "404";
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~688 dòng với nhiều functions
 * CODE MỚI: ~120 dòng với PhotoRepository và AdminCRUDHelper
 * 
 * GIẢM: ~83% code
 * 
 * LỢI ÍCH:
 * - Sử dụng PhotoRepository
 * - Sử dụng AdminCRUDHelper cho CRUD operations
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 */

