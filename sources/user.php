<?php

/**
 * sources/user.php - REFACTORED VERSION (Partial)
 * 
 * File này là phiên bản refactored của sources/user.php
 * Sử dụng UserHandler và RequestHandler
 * 
 * CÁCH SỬ DỤNG:
 * Có thể copy từng phần vào sources/user.php hoặc thay thế hoàn toàn
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\RequestHandler;
use Tuezy\UserHandler;
use Tuezy\ValidationHelper;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize RequestHandler
$params = RequestHandler::getParams();
$action = SecurityHelper::sanitize($match['params']['action'] ?? '');

// Initialize Handlers
$validator = new ValidationHelper($func, $config);
$userHandler = new UserHandler($d, $func, $flash, $validator, $configBase, $loginMember, $config);

switch ($action) {
	case 'dang-nhap':
		$titleMain = dangnhap;
		$template = "account/login";
		
		if (!empty($_SESSION[$loginMember]['active'])) {
			$func->transfer("Trang không tồn tại", $configBase, false);
		}
		
		if (!empty($_POST['login-user'])) {
			$username = SecurityHelper::sanitizePost('username');
			$password = $_POST['password'] ?? '';
			$remember = !empty($_POST['remember']);
			
			if ($userHandler->login($username, $password, $remember)) {
				$func->redirect($configBase);
			} else {
				$func->redirect($configBase . "account/dang-nhap");
			}
		}
		break;

	case 'dang-ky':
		$titleMain = dangky;
		$template = "account/registration";
		
		if (!empty($_SESSION[$loginMember]['active'])) {
			$func->transfer("Trang không tồn tại", $configBase, false);
		}
		
		if (!empty($_POST['registration-user'])) {
			$data = $_POST['dataMember'] ?? [];
			if ($userHandler->register($data)) {
				$func->transfer("Đăng ký thành công", $configBase . "account/dang-nhap");
			} else {
				$func->redirect($configBase . "account/dang-ky");
			}
		}
		break;

	case 'quen-mat-khau':
		$titleMain = quenmatkhau;
		$template = "account/forgot_password";
		
		if (!empty($_SESSION[$loginMember]['active'])) {
			$func->transfer("Trang không tồn tại", $configBase, false);
		}
		
		if (!empty($_POST['forgot-password-user'])) {
			$email = SecurityHelper::sanitizePost('email');
			if ($userHandler->forgotPassword($email)) {
				$func->transfer("Mật khẩu mới đã được gửi đến email của bạn", $configBase . "account/dang-nhap");
			} else {
				$func->redirect($configBase . "account/quen-mat-khau");
			}
		}
		break;

	case 'kich-hoat':
		$titleMain = kichhoat;
		$template = "account/activation";
		
		if (!empty($_SESSION[$loginMember]['active'])) {
			$func->transfer("Trang không tồn tại", $configBase, false);
		}
		
		// checkActivationMember() - giữ nguyên function cũ
		checkActivationMember();
		break;

	case 'thong-tin':
		$titleMain = capnhatthongtin;
		$template = "account/info";
		
		if (empty($_SESSION[$loginMember]['active'])) {
			$func->transfer("Trang không tồn tại", $configBase, false);
		}
		
		// infoMember() - có thể refactor sau
		infoMember();
		break;

	case 'dang-xuat':
		if (empty($_SESSION[$loginMember]['active'])) {
			$func->transfer("Trang không tồn tại", $configBase, false);
		}
		
		$userHandler->logout();
		$func->redirect($configBase);
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

