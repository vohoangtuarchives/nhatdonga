<?php

/**
 * api/ward.php - REFACTORED VERSION
 * 
 * Sử dụng LocationAPIController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

include "config.php";

use Tuezy\API\Controller\LocationAPIController;
use Tuezy\Config;

// Initialize Config
$configObj = new Config($config);

// Initialize Controller
$controller = new LocationAPIController($d, $cache, $func, $configObj, $lang, $sluglang);

// Handle request
$controller->getWards();

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~36 dòng
 * CODE MỚI: ~30 dòng với SecurityHelper
 * 
 * GIẢM: ~17% code
 * 
 * LỢI ÍCH:
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc hơn
 */

