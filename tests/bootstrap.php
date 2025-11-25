<?php
/**
 * PHPUnit Bootstrap File
 * 
 * This file is executed before each test run
 */

// Define constants if not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

if (!defined('VENDOR')) {
    define('VENDOR', BASE_PATH . '/vendor');
}

if (!defined('LIBRARIES')) {
    define('LIBRARIES', BASE_PATH . '/libraries');
}

if (!defined('SOURCES')) {
    define('SOURCES', BASE_PATH . '/sources');
}

// Load Composer autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

