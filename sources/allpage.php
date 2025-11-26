<?php
/**
 * allpage.php - Common data for all pages
 * 
 * File này được load cho tất cả các trang, cung cấp:
 * - Photos (favicon, logo, banner, slider, social, partners, etc.)
 * - Static content (footer, text blocks)
 * - Product categories
 * - News categories và content
 * - Statistics
 * - Newsletter form handling
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\PhotoRepository;
use Tuezy\Repository\StaticRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\NewsRepository;
use Tuezy\Repository\SettingRepository;
use Tuezy\FormHandler;
use Tuezy\ValidationHelper;
use Tuezy\Config;
use Tuezy\Helper\GlobalHelper;

// ============================================
// Initialize Dependencies
// ============================================
$d = GlobalHelper::db();
$cache = GlobalHelper::cache();
$func = GlobalHelper::func();
$emailer = GlobalHelper::emailer();
$flash = GlobalHelper::flash();
$statistic = GlobalHelper::statistic();
$config = GlobalHelper::config();
$breadcr = GlobalHelper::breadcr();

// Language variables
$lang = $_SESSION['lang'] ?? 'vi';
$sluglang = 'slugvi';

// Initialize Config object
$configObj = new Config($config);

// Build configBase URL
$http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ? 'https://' : 'http://';
$configUrl = $config['database']['server-name'] . $config['database']['url'];
$config_base = $configBase = $http . $configUrl;

// Get setting from cache
$setting = $cache->get("SELECT * FROM #_setting", null, 'fetch', 7200) ?: [];
$slogan = $setting['sloganvi'] ?? '';

// ============================================
// Initialize Repositories
// ============================================
$photoRepo = new PhotoRepository($d, $lang, $sluglang);
$staticRepo = new StaticRepository($d, $cache, $lang, $sluglang);
$productCategoryRepo = new CategoryRepository($d, $cache, $lang, $sluglang, 'product');
$newsCategoryRepo = new CategoryRepository($d, $cache, $lang, $sluglang, 'news');

// ============================================
// Photos - PhotoRepository
// ============================================
$favicon = $photoRepo->getFavicon();
$logo = $photoRepo->getLogo();
$banner = $photoRepo->getBanner();
$slider = $photoRepo->getSlider();
$social = $photoRepo->getSocial();
$doitac = $photoRepo->getPartners();
$screenshot = $photoRepo->getScreenshot();
$link_video = $photoRepo->getVideoLink();

// ============================================
// Product Categories - CategoryRepository
// ============================================
$splist = $productCategoryRepo->getLists('san-pham', true, true); // active, featured

// ============================================
// Static Content - StaticRepository
// ============================================
$footer = $staticRepo->getByType('footer');
$txtDichVu = $staticRepo->getByType('txt-dich-vu');
$txtDKNT = $staticRepo->getByType('txt-dknt');

// ============================================
// News Content - NewsRepository
// ============================================
$chinhsachRepo = new NewsRepository($d, $lang, 'chinh-sach');
$dichvuRepo = new NewsRepository($d, $lang, 'dich-vu');

$chinhsach = $chinhsachRepo->getByType('chinh-sach', true, 100);
$dichvuMenu = $dichvuRepo->getByType('dich-vu', true, 100);

// ============================================
// News Categories - CategoryRepository (news)
// ============================================
$dichvulist = $newsCategoryRepo->getLists('dich-vu', true, false);
$dichvuFooter = $newsCategoryRepo->getLists('dich-vu', true, false);
$ttlist = $newsCategoryRepo->getLists('tin-tuc', true, true); // active, featured

// ============================================
// Statistics
// ============================================
$counter = $statistic->getCounter();
$online = $statistic->getOnline();

// ============================================
// Newsletter Form Handling
// ============================================
if (isset($_POST['submit-newsletter'])) {
    $validator = new ValidationHelper($func, $config);
    $formHandler = new FormHandler($d, $func, $emailer, $flash, $validator, $configBase, $lang, $setting);
    
    $dataNewsletter = $_POST['dataNewsletter'] ?? [];
    $recaptchaResponse = $_POST['recaptcha_response_newsletter'] ?? '';
    
    $formHandler->handleNewsletter($dataNewsletter, $recaptchaResponse);
}

// ============================================
// Helper function: Render Facebook Messages Footer
// ============================================
function renderMessagesFacebookFooter() {
    global $optsetting;
    
    if (empty($optsetting['fanpage'])) {
        return '';
    }
    
    ob_start();
    ?>
    <div style="width: 100%;">
        <div style="width: 100%; height: 276px; overflow: hidden;">
            <div class="fb-page" 
                 data-href="<?= $optsetting['fanpage'] ?>" 
                 data-tabs="messages" 
                 data-small-header="true" 
                 data-height="276"
                 data-width="1000"
                 data-adapt-container-width="true" 
                 data-hide-cover="false" 
                 data-show-facepile="true">
                <blockquote cite="<?= $optsetting['fanpage'] ?>" class="fb-xfbml-parse-ignore">
                    <a href="<?= $optsetting['fanpage'] ?>">Facebook</a>
                </blockquote>
            </div>
        </div>
        <style>
            .fb-page {
                width: 100% !important;
            }
            .fb-page iframe {
                width: 100% !important;
                height: 276px !important;
            }
        </style>
    </div>
    <?php
    return ob_get_clean();
}


