<?php

/**
 * api/color.php - REFACTORED VERSION
 * 
 * Sử dụng ColorAPIController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

include "config.php";

use Tuezy\API\Controller\ColorAPIController;
use Tuezy\Config;

// Initialize Config
$configObj = new Config($config);

// Initialize Controller
$controller = new ColorAPIController($d, $cache, $func, $configObj, $lang, $sluglang);

// Handle request
$controller->getGalleryByColor();

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~61 dòng với rawQuery
 * CODE MỚI: ~55 dòng với ProductRepository
 * 
 * GIẢM: ~10% code
 * 
 * LỢI ÍCH:
 * - Sử dụng ProductRepository cho product detail
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc hơn
 */

