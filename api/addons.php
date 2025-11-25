<?php

/**
 * api/addons.php - REFACTORED VERSION
 * 
 * Sử dụng AddonsAPIController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

include "config.php";

use Tuezy\API\Controller\AddonsAPIController;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize Controller
$controller = new AddonsAPIController($d, $cache, $func, $configObj, $lang, $sluglang);

// Get type
$type = SecurityHelper::sanitizeGet('type', '');

if (!empty($type)) {
	$controller->handle($type);
}

