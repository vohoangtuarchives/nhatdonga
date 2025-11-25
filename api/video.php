<?php

/**
 * api/video.php - REFACTORED VERSION
 * 
 * Sử dụng VideoAPIController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

include "config.php";

use Tuezy\API\Controller\VideoAPIController;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize Controller
$controller = new VideoAPIController($d, $cache, $func, $configObj, $lang, $sluglang);

// Get video ID
$id = (int)SecurityHelper::sanitizePost('id', 0);

if ($id > 0) {
	$controller->getVideoEmbed($id);
}