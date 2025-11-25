<?php

use Tuezy\RequestHandler;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/* Request data handled by RequestHandler */
$params = RequestHandler::getParams();
$com = $params['com'];
$act = $params['act'];
$type = $params['type'];
$kind = $params['kind'];
$val = $params['val'];
$variant = $params['variant'];
$id_parent = $params['id_parent'];
$id = $params['id'];
$curPage = $params['curPage'];
$dfgallery = (!empty($params['kind']) && $params['kind'] == 'man_list') ? 'gallery_list' : 'gallery';

/* Admin Authentication Helper */
$adminAuth = new AdminAuthHelper($func, $d, $loginAdmin, $config);

/* Kiểm tra 2 máy đăng nhập cùng 1 tài khoản */
if ($adminAuth->isLoggedIn()) {
	$sessionValid = $adminAuth->validateSession();
	
	if (!$sessionValid) {
		if (!empty($_SESSION[TOKEN])) unset($_SESSION[TOKEN]);
		unset($_SESSION[$loginAdmin]);
		$func->redirect("index.php?com=user&act=login");
	}
	
	$userId = $adminAuth->getUserId();
	$user = $adminAuth->getUser();
	if ($user && $_SESSION[$loginAdmin]['login_token'] !== $user['user_token']) {
		$alertlogin = 'Có người đang đăng nhập tài khoản của bạn.';
	} else {
		$alertlogin = '';
	}
}

/* Kiểm tra phân quyền - Sử dụng AdminPermissionHelper */
$adminPermission = new AdminPermissionHelper($func, $config);

if ($adminPermission->isActive() && $adminAuth->isLoggedIn()) {
	/* Lấy quyền */
	$_SESSION[$loginAdmin]['permissions'] = [];
	$userId = $adminAuth->getUserId();
	if ($userId) {
		$id_permission = $d->rawQueryOne("SELECT id_permission FROM #_user WHERE id = ? AND find_in_set('hienthi',status) LIMIT 0,1", [$userId]);
		if (!empty($id_permission['id_permission'])) {
			$permission = $d->rawQueryOne("SELECT id FROM #_permission_group WHERE id = ? AND find_in_set('hienthi',status) LIMIT 0,1", [$id_permission['id_permission']]);
			if (!empty($permission['id'])) {
				$user_permission = $d->rawQuery("SELECT permission FROM #_permission WHERE id_permission_group = ?", [$permission['id']]);
				if (!empty($user_permission)) {
					foreach($user_permission as $value) {
						$_SESSION[$loginAdmin]['permissions'][] = $value['permission'];
					}
				}
			}
		}
	}
	
	/* Kiểm tra quyền - Sử dụng AdminPermissionHelper */
	if ($adminPermission->hasRole()) {
		$is_permission = true;
		
		// Actions không cần kiểm tra quyền
		$excludedActions = ['save', 'save_list', 'save_cat', 'save_item', 'save_sub', 'save_brand', 'save_color', 'save_size', 'saveImages', 'uploadExcel', 'save_static', 'save_photo'];
		$excludedComs = ['user', 'index'];
		
		if (!empty($com) && !in_array($com, $excludedComs) && !empty($act) && !in_array($act, $excludedActions)) {
			$sum_permission = $com . '_' . $act;
			$sum_permission .= (!empty($variant)) ? '_' . $variant : '';
			$sum_permission .= (!empty($type)) ? '_' . $type : '';
			
			if (isset($_SESSION[$loginAdmin]['permissions']) && !in_array($sum_permission, $_SESSION[$loginAdmin]['permissions'])) {
				$func->transfer("Bạn không có quyền truy cập vào khu vực này", "index.php", false);
			}
		}
	}
}

/* Kiểm tra đăng nhập */
if (!$adminAuth->isLoggedIn() && $act != "login") {
	$func->redirect("index.php?com=user&act=login");
}

/* Delete cache */
$cacheAction = [
	'save', 'save_copy', 'save_list', 'save_cat', 'save_item', 'save_sub',
	'save_brand', 'save_size', 'save_color', 'save_static', 'save_photo',
	'save_city', 'save_district', 'save_ward', 'update', 'delete',
	'delete_list', 'delete_cat', 'delete_item', 'delete_sub', 'delete_brand',
	'delete_city', 'delete_district', 'delete_ward'
];

if (isset($_POST) && !empty($cacheAction) && in_array($act, $cacheAction)) {
	$cache->delete();
}

/* Include sources */
if (file_exists(SOURCES . $com . '.php')) {
	include SOURCES . $com . ".php";
} else {
	$template = "index";
}

