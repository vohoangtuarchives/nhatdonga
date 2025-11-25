<?php 
include "config.php";

use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Define routes
$routes = [
	'limitLists' => ['limitLists', 'GET'], 
	'limitReplies' => ['limitReplies', 'GET'], 
	'addAdmin' => ['addAdmin', 'POST'], 
	'status' => ['status', 'POST'], 
	'delete' => ['delete', 'POST']
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
	}
}
?>