<?php

/**
 * api/cart.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của api/cart.php
 * Sử dụng CartAPIHandler để giảm code và cải thiện structure
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp api/cart.php api/cart.php.backup
 * 2. Copy file này: cp examples/api_cart_refactored.php api/cart.php
 * 3. Test kỹ trước khi deploy
 */

include "config.php";

// Import các class refactored
use Tuezy\API\CartAPIHandler;
use Tuezy\Config;

// Initialize
$configObj = new Config($config);

// Sử dụng CartAPIHandler - giảm từ ~400 dòng xuống ~10 dòng!
$handler = new CartAPIHandler(
    $d, 
    $cache, 
    $func, 
    $custom, 
    $configObj, 
    $lang, 
    $sluglang, 
    $setting, 
    $cart
);

// Handle request - tự động xử lý tất cả commands
$handler->handle();

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~400 dòng với nhiều if-else
 * CODE MỚI: ~20 dòng
 * 
 * GIẢM: ~95% code!
 * 
 * LỢI ÍCH:
 * - Clean structure
 * - Easy to extend
 * - Better error handling
 * - Type-safe
 * - Testable
 */

