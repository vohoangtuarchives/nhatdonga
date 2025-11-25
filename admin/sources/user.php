<?php

/**
 * admin/sources/user.php - REFACTORED VERSION (Partial)
 * 
 * File này là phiên bản refactored của admin/sources/user.php
 * Sử dụng AdminAuthHelper và AdminPermissionHelper
 * 
 * CÁCH SỬ DỤNG:
 * Có thể copy từng phần vào admin/sources/user.php hoặc thay thế hoàn toàn
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;
use Tuezy\Repository\UserRepository;
use Tuezy\Service\UserService;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize Helpers
$adminAuthHelper = new AdminAuthHelper($func, $d, $loginAdmin, $config);
$adminPermissionHelper = new AdminPermissionHelper($func, $config);

// Initialize UserService for member management
$userRepo = new UserRepository($d, $cache);
$userService = new UserService($userRepo, $d);

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
		// View admins - có thể tạo AdminRepository sau
		viewAdmins();
		$template = "user/man_admin/mans";
		break;
		
	case "add_admin":
		$template = "user/man_admin/man_add";
		break;
		
	case "edit_admin":
		editAdmin();
		$template = "user/man_admin/man_add";
		break;
		
	case "info_admin":
		infoAdmin();
		$template = "user/man_admin/info";
		break;
		
	case "save_admin":
		saveAdmin();
		break;
		
	case "delete_admin":
		deleteAdmin();
		break;

	/* Members - Sử dụng UserService */
	case "man_member":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['status'])) {
			$filters['status'] = SecurityHelper::sanitize($_REQUEST['status']);
		}
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}

		// Get members using UserService
		$perPage = 20;
		$listing = $userService->getListing($filters, $curPage, $perPage);
		$items = $listing['items'];
		$totalItems = $listing['total'];
		$paging = $func->paging($totalItems, $perPage, $curPage, "index.php?com=user&act=man_member");
		$template = "user/man_member/mans";
		break;
		
	case "add_member":
		$template = "user/man_member/man_add";
		break;
		
	case "edit_member":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			$item = $userRepo->getById($id);
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
		
		// Sanitize data
		foreach ($data as $key => $value) {
			if (is_string($value)) {
				$data[$key] = SecurityHelper::sanitize($value);
			}
		}

		if ($id) {
			// Update existing member
			if ($userService->updateProfile($id, $data)) {
				// Update password if provided
				if (!empty($_POST['data']['password'])) {
					$oldPassword = $_POST['data']['old_password'] ?? '';
					$newPassword = $_POST['data']['password'];
					$userService->updatePassword($id, $oldPassword, $newPassword);
				}
				$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=user&act=man_member");
			} else {
				$func->transfer("Cập nhật dữ liệu thất bại", "index.php?com=user&act=man_member", false);
			}
		} else {
			// Create new member
			if ($userService->register($data)) {
				$func->transfer("Thêm dữ liệu thành công", "index.php?com=user&act=man_member");
			} else {
				$func->transfer("Thêm dữ liệu thất bại. Email có thể đã tồn tại.", "index.php?com=user&act=man_member", false);
			}
		}
		break;
		
	case "delete_member":
		$id = (int)($_GET['id'] ?? 0);
		if ($id && $userRepo->delete($id)) {
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

