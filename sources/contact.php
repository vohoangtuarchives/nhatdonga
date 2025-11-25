<?php

/**
 * sources/contact.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của sources/contact.php
 * Sử dụng FormHandler để giảm code từ ~430 dòng xuống ~50 dòng
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp sources/contact.php sources/contact.php.backup
 * 2. Copy file này: cp sources/contact-refactored.php sources/contact.php
 * 3. Test kỹ trước khi deploy
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\FormHandler;
use Tuezy\ValidationHelper;
use Tuezy\SEOHelper;
use Tuezy\BreadcrumbHelper;
use Tuezy\Repository\StaticRepository;
use Tuezy\Config;

// Initialize Config
$configObj = new Config($config);

// Initialize helpers
$validator = new ValidationHelper($func, $config);
$formHandler = new FormHandler($d, $func, $emailer, $flash, $validator, $configBase, $lang, $setting);
$seoHelper = new SEOHelper($seo, $func, $d, $lang, $seolang, $configBase);
$breadcrumbHelper = new BreadcrumbHelper($breadcr, $configBase);
$staticRepo = new StaticRepository($d, $cache, $lang, $sluglang);

/* Handle Contact Form Submission - Sử dụng FormHandler */
if (!empty($_POST['submit-contact'])) {
    $dataContact = $_POST['dataContact'] ?? [];
    $recaptchaResponse = $_POST['recaptcha_response_contact'] ?? '';
    
    // Sử dụng FormHandler - giảm từ ~200 dòng xuống 1 dòng!
    $formHandler->handleContact($dataContact, $recaptchaResponse);
    // FormHandler tự động xử lý:
    // - Validation
    // - Recaptcha check
    // - Database insert
    // - File upload
    // - Email sending
    // - Redirects
}

/* SEO Setup - Sử dụng SEOHelper */
$seoHelper->setupFromSeopage('lien-he', $titleMain);
$seoHelper->setType('object');

/* Get Contact Content - Sử dụng StaticRepository */
$lienhe = $staticRepo->getByType('lienhe');

/* Breadcrumbs - Sử dụng BreadcrumbHelper */
if (!empty($titleMain)) {
    $breadcrumbHelper->add($titleMain, '/lien-he');
}
$breadcrumbs = $breadcrumbHelper->render();

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~430 dòng với nhiều validation, database, email code
 * CODE MỚI: ~50 dòng với FormHandler và các helpers
 * 
 * GIẢM: ~88% code!
 * 
 * LỢI ÍCH:
 * - Sử dụng FormHandler thay vì code lặp lại
 * - Sử dụng SEOHelper cho SEO
 * - Sử dụng BreadcrumbHelper cho breadcrumbs
 * - Sử dụng StaticRepository cho static content
 * - Code dễ đọc và maintain hơn
 * - Type-safe với type hints
 */

