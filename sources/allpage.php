<?php


if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\PhotoRepository;
use Tuezy\Repository\StaticRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\NewsRepository;
use Tuezy\FormHandler;
use Tuezy\ValidationHelper;
use Tuezy\Config;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories
$photoRepo = new PhotoRepository($d, $cache, $lang, $sluglang);
$staticRepo = new StaticRepository($d, $cache, $lang, $sluglang);
$categoryRepo = new CategoryRepository($d, $cache, $lang, $sluglang, 'product');
$newsRepo = new NewsRepository($d, $lang, 'tin-tuc');

/* Query allpage - Sử dụng Repositories */

// Product list (featured) - Sử dụng CategoryRepository
$splist = $categoryRepo->getLists('san-pham', true, true); // active, featured

// Photos - Sử dụng PhotoRepository
$favicon = $photoRepo->getFavicon();
$logo = $photoRepo->getLogo();
$banner = $photoRepo->getBanner();
$social = $photoRepo->getSocial();
$slider = $photoRepo->getSlider();
$doitac = $photoRepo->getPartners();
$screenshot = $photoRepo->getScreenshot();
$link_video = $photoRepo->getVideoLink();

// Static content - Sử dụng StaticRepository
$footer = $staticRepo->getByType('footer');
$txtDichVu = $staticRepo->getByType('txt-dich-vu');
$txtDKNT = $staticRepo->getByType('txt-dknt');

// News - Sử dụng NewsRepository
$chinhsachRepo = new NewsRepository($d, $lang, 'chinh-sach');
$dichvuRepo = new NewsRepository($d, $lang, 'dich-vu');
$chinhsach = $chinhsachRepo->getByType('chinh-sach', true, 100);
$dichvuMenu = $dichvuRepo->getByType('dich-vu', true, 100);

// News list - Sử dụng CategoryRepository cho news
$newsCategoryRepo = new CategoryRepository($d, $cache, $lang, $sluglang, 'news');
$dichvulist = $newsCategoryRepo->getLists('dich-vu', true, false);
$dichvuFooter = $newsCategoryRepo->getLists('dich-vu', true, false);

$slogan = $setting['sloganvi'] ?? '';

/* Get statistic */
$counter = $statistic->getCounter();
$online = $statistic->getOnline();

/* Newsletter - Sử dụng FormHandler */
if (isset($_POST['submit-newsletter'])) {
    $validator = new ValidationHelper($func, $config);
    $formHandler = new FormHandler($d, $func, $emailer, $flash, $validator, $configBase, $lang, $setting);
    
    $dataNewsletter = $_POST['dataNewsletter'] ?? [];
    $recaptchaResponse = $_POST['recaptcha_response_newsletter'] ?? '';
    
    // Sử dụng FormHandler - giảm từ ~250 dòng xuống 1 dòng!
    $formHandler->handleNewsletter($dataNewsletter, $recaptchaResponse);
}


