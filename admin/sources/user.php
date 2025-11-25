<?php

/**
 * admin/sources/user.php - REFACTORED VERSION
 * 
 * Sử dụng UserAdminController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\Controller\UserAdminController;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;
use Tuezy\SecurityHelper;

// Initialize Helpers
$adminAuthHelper = new AdminAuthHelper($func, $d, $loginAdmin, $config);
$adminPermissionHelper = new AdminPermissionHelper($func, $config);

// Initialize Controller
$controller = new UserAdminController($d, $cache, $func, $config, $adminAuthHelper, $adminPermissionHelper, $loginAdmin);

/* Check access user - Sử dụng AdminPermissionHelper */
$restrictedActions = ['man_admin', 'add_admin', 'edit_admin', 'delete_admin', 'man_member', 'add_member', 'edit_member', 'delete_member', 'permission_group', 'add_permission_group', 'edit_permission_group', 'delete_permission_group'];
if (!empty($act) && !in_array($act, array('login', 'info_admin')) && in_array($act, $restrictedActions) && !$adminPermissionHelper->hasRole()) {
	$func->transfer("Bạn không có quyền truy cập vào khu vực này", "index.php", false);
	exit;
}

switch($act) {
	/* Admins */
	case "login":
		if (!empty($_SESSION[$loginAdmin]['active'])) {
			$func->transfer("Trang không tồn tại", "index.php", false);
		} else {
			$template = "user/login";
		}
		break;
		
	case "logout":
		// Sử dụng AdminAuthHelper
		$adminAuthHelper->logout();
		break;
		
	case "man_admin":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}
		
		$viewData = $controller->manAdmin($filters, $curPage, 20);
		$items = $viewData['items'];
		$paging = $viewData['paging'];
		$template = "user/man_admin/mans";
		break;
		
	case "add_admin":
		$template = "user/man_admin/man_add";
		break;
		
	case "edit_admin":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			$item = $controller->getUser($id);
			if (!$item) {
				$func->transfer("Dữ liệu không có thực", "index.php?com=user&act=man_admin", false);
			}
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=user&act=man_admin", false);
		}
		$template = "user/man_admin/man_add";
		break;
		
	case "info_admin":
		if (function_exists('infoAdmin')) {
			infoAdmin();
		}
		$template = "user/man_admin/info";
		break;
		
	case "save_admin":
		if (empty($_POST)) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=user&act=man_admin", false);
		}
		
		$id = !empty($_POST['data']['id']) ? (int)$_POST['data']['id'] : null;
		$data = $_POST['data'] ?? [];
		
		if ($controller->save($data, $id, 'admin')) {
			$message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
			$func->transfer($message, "index.php?com=user&act=man_admin");
		} else {
			$func->transfer("Có lỗi xảy ra khi lưu dữ liệu", "index.php?com=user&act=man_admin", false);
		}
		break;
		
	case "delete_admin":
		$id = (int)($_GET['id'] ?? 0);
		if ($id && $controller->delete($id, 'admin')) {
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=user&act=man_admin");
		} else {
			$func->transfer("Xóa dữ liệu thất bại", "index.php?com=user&act=man_admin", false);
		}
		break;

	/* Members - Sử dụng UserAdminController */
	case "man_member":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['status'])) {
			$filters['status'] = SecurityHelper::sanitize($_REQUEST['status']);
		}
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}

		$viewData = $controller->manMember($filters, $curPage, 20);
		$items = $viewData['items'];
		$paging = $viewData['paging'];
		$template = "user/man_member/mans";
		break;
		
	case "add_member":
		$template = "user/man_member/man_add";
		break;
		
	case "edit_member":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			$item = $controller->getUser($id);
			if (!$item) {
				$func->transfer("Dữ liệu không có thực", "index.php?com=user&act=man_member", false);
			}
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=user&act=man_member", false);
		}
		$template = "user/man_member/man_add";
		break;
		
	case "save_member":
		if (empty($_POST)) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=user&act=man_member", false);
		}

		$id = !empty($_POST['data']['id']) ? (int)$_POST['data']['id'] : null;
		$data = $_POST['data'] ?? [];

		if ($controller->save($data, $id, 'member')) {
			$message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
			$func->transfer($message, "index.php?com=user&act=man_member");
		} else {
			$func->transfer("Có lỗi xảy ra khi lưu dữ liệu", "index.php?com=user&act=man_member", false);
		}
		break;
		
	case "delete_member":
		$id = (int)($_GET['id'] ?? 0);
		if ($id && $controller->delete($id, 'member')) {
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=user&act=man_member");
		} else {
			$func->transfer("Xóa dữ liệu thất bại", "index.php?com=user&act=man_member", false);
		}
		break;
		
	/* Permission */
	case "permission_group":
		viewPermissionGroups();
		$template = "user/permission/permission_groups";
		break;
		
	case "add_permission_group":
		$template = "user/permission/permission_group";
		break;
		
	case "edit_permission_group":
		editPermissionGroup();
		$template = "user/permission/permission_group";
		break;
		
	case "save_permission_group":
		savePermissionGroup();
		break;
		
	case "delete_permission_group":
		deletePermissionGroup();
		break;

	default:
		$template = "404";
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~1125 dòng với nhiều functions
 * CODE MỚI: ~120 dòng với AdminAuthHelper và AdminPermissionHelper
 * 
 * GIẢM: ~89% code
 * 
 * LỢI ÍCH:
 * - Sử dụng AdminAuthHelper cho authentication
 * - Sử dụng AdminPermissionHelper cho permissions
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 * 
 * LƯU Ý:
 * - Các functions như viewAdmins, saveAdmin, etc. vẫn giữ nguyên
 * - Có thể refactor thêm sau để tạo UserRepository
 */

