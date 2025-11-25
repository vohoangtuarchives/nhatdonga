<?php

/**
 * api/comment.php - REFACTORED VERSION
 * 
 * Sử dụng CommentAPIController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

include "config.php";

use Tuezy\API\Controller\CommentAPIController;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize Controller
$controller = new CommentAPIController($d, $cache, $func, $configObj, $lang, $sluglang);

// Get route
$route = SecurityHelper::sanitizeGet('get', '');

// Route to appropriate method
switch ($route) {
	case 'limitLists':
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$controller->limitLists();
		} else {
			$controller->error('Method not allowed', 405);
		}
		break;
		
	case 'limitReplies':
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$controller->limitReplies();
		} else {
			$controller->error('Method not allowed', 405);
		}
		break;
		
	case 'add':
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$controller->add();
		} else {
			$controller->error('Method not allowed', 405);
		}
		break;
		
	default:
		$controller->error('Invalid route', 404);
		break;
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~35 dòng với htmlspecialchars trực tiếp
 * CODE MỚI: ~35 dòng với SecurityHelper
 * 
 * LỢI ÍCH:
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 */

