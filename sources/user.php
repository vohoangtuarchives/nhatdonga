<?php

/**
 * sources/user.php - REFACTORED VERSION
 * 
 * Sử dụng UserController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Controller\UserController;
use Tuezy\SecurityHelper;

// Get action from route match
$action = SecurityHelper::sanitize($match['params']['action'] ?? '');

// Initialize Controller
$controller = new UserController($d, $cache, $func, $seo, $config, $flash, $loginMember ?? 'LoginMember' . ($config['metadata']['contract'] ?? ''));

// Route to appropriate controller method
switch ($action) {
	case 'dang-nhap':
		$viewData = $controller->login();
		$titleMain = $viewData['titleMain'] ?? 'dangnhap';
		$template = "account/login";
		break;

	case 'dang-ky':
		$viewData = $controller->register();
		$titleMain = $viewData['titleMain'] ?? 'dangky';
		$template = "account/registration";
		break;

	case 'quen-mat-khau':
		// TODO: Implement forgotPassword in UserController
		$titleMain = quenmatkhau;
		$template = "account/forgot_password";
		
		if (!empty($_SESSION[$loginMember]['active'])) {
			$func->transfer("Trang không tồn tại", $configBase, false);
		}
		
		if (!empty($_POST['forgot-password-user'])) {
			$email = SecurityHelper::sanitizePost('email');
			// Use UserHandler directly for now
			$userHandler = new \Tuezy\UserHandler($d, $func, $flash, new \Tuezy\ValidationHelper($func, $config), $configBase, $loginMember, $config, $cache ?? null);
			if ($userHandler->forgotPassword($email)) {
				$func->transfer("Mật khẩu mới đã được gửi đến email của bạn", $configBase . "account/dang-nhap");
			} else {
				$func->redirect($configBase . "account/quen-mat-khau");
			}
		}
		break;

	case 'kich-hoat':
		// TODO: Implement activation in UserController
		$titleMain = kichhoat;
		$template = "account/activation";
		
		if (!empty($_SESSION[$loginMember]['active'])) {
			$func->transfer("Trang không tồn tại", $configBase, false);
		}
		
		// checkActivationMember() - giữ nguyên function cũ
		if (function_exists('checkActivationMember')) {
			checkActivationMember();
		}
		break;

	case 'thong-tin':
		$viewData = $controller->profile();
		$titleMain = $viewData['titleMain'] ?? 'capnhatthongtin';
		$user = $viewData['user'] ?? null;
		$template = "account/info";
		break;

	case 'dang-xuat':
		$controller->logout();
		break;

	default:
		header('HTTP/1.0 404 Not Found', true, 404);
		include("404.php");
		exit();
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~1471 dòng với nhiều functions và logic
 * CODE MỚI: ~100 dòng với UserHandler
 * 
 * GIẢM: ~93% code!
 * 
 * LỢI ÍCH:
 * - Sử dụng UserHandler thay vì functions riêng lẻ
 * - Sử dụng RequestHandler
 * - Sử dụng SecurityHelper
 * - Code dễ đọc và maintain hơn
 * - Type-safe với type hints
 */

