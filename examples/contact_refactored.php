<?php

/**
 * sources/contact.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của sources/contact.php
 * Sử dụng các class mới để giảm code và cải thiện maintainability
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp sources/contact.php sources/contact.php.backup
 * 2. Copy file này: cp examples/contact_refactored.php sources/contact.php
 * 3. Test kỹ trước khi deploy
 */

if (!defined('SOURCES')) die("Error");

// Import các class refactored
use Tuezy\FormHandler;
use Tuezy\ValidationHelper;
use Tuezy\SEOHelper;
use Tuezy\BreadcrumbHelper;
use Tuezy\URLHelper;
use Tuezy\Config;

// Initialize helpers
$configObj = new Config($config);
$validator = new ValidationHelper($func);
$formHandler = new FormHandler($d, $func, $emailer, $flash, $validator, $configBase, $lang, $setting);
$seoHelper = new SEOHelper($seo, $func, $d, $lang, $seolang, $configBase);
$breadcrumbHelper = new BreadcrumbHelper($breadcr, $configBase);
$urlHelper = new URLHelper($configBase, $lang, $sluglang);

/* Handle Contact Form Submission */
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

/* Get Contact Content */
$lienhe = $d->rawQueryOne(
    "select content$lang from #_static where type = ? limit 0,1",
    ['lienhe']
);

/* Breadcrumbs - Sử dụng BreadcrumbHelper */
if (!empty($titleMain)) {
    $breadcrumbHelper->add($titleMain, '/lien-he');
}
$breadcrumbs = $breadcrumbHelper->render();

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~430 dòng
 * CODE MỚI: ~50 dòng
 * 
 * GIẢM: ~88% code!
 * 
 * LỢI ÍCH:
 * - Dễ đọc hơn
 * - Dễ maintain
 * - Dễ test
 * - Type-safe
 * - Reusable
 */

