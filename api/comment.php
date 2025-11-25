<?php

/**
 * api/comment.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của api/comment.php
 * Sử dụng SecurityHelper
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp api/comment.php api/comment.php.backup
 * 2. Copy file này: cp api/comment-refactored.php api/comment.php
 * 3. Test kỹ trước khi deploy
 */

include "config.php";

use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Define routes
$routes = [
	'limitLists' => ['limitLists', 'GET'],
	'limitReplies' => ['limitReplies', 'GET'],
	'add' => ['add', 'POST']
];

// Get route - Sử dụng SecurityHelper
$route = SecurityHelper::sanitizeGet('get', '');
$route = (!empty($route) && !empty($routes[$route])) ? $route : false;

if (!empty($route)) {
	$comment = new Comments($d, $func);
	$method = $routes[$route][0];
	$requestType = $routes[$route][1];
	
	if (method_exists($comment, $method) && $_SERVER['REQUEST_METHOD'] == $requestType) {
		print $comment->$method();
	} else {
		// Error handling
		header('Content-Type: application/json');
		echo json_encode(['error' => 'Method not allowed or not found']);
	}
} else {
	// Error handling
	header('Content-Type: application/json');
	echo json_encode(['error' => 'Invalid route']);
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

