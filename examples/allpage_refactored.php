<?php

/**
 * sources/allpage.php - REFACTORED VERSION (Partial)
 * 
 * File này demo cách sử dụng DataProvider để refactor sources/allpage.php
 * Chỉ refactor phần data fetching, giữ nguyên phần newsletter
 * 
 * CÁCH SỬ DỤNG:
 * Có thể copy từng phần vào sources/allpage.php
 */

if (!defined('SOURCES')) die("Error");

// Import DataProvider
use Tuezy\DataProvider;

// Initialize DataProvider
$dataProvider = new DataProvider($cache, $lang, $seolang);

/* Query allpage - Sử dụng DataProvider */

// Thay vì:
// $splist = $cache->get("SELECT id, name$lang, slug$lang...", array('san-pham'), 'result', 7200);
$splist = $dataProvider->getProductList('san-pham');

// Thay vì:
// $favicon = $cache->get("select photo from #_photo where type = ? and act = ?...", array('favicon', 'photo_static'), 'fetch', 7200);
$favicon = $dataProvider->getPhoto('favicon', 'photo_static');

// Thay vì:
// $logo = $cache->get("select id, photo, options from #_photo where type = ? and act = ?...", array('logo', 'photo_static'), 'fetch', 7200);
$logo = $dataProvider->getLogo();

// Thay vì:
// $banner = $cache->get("select photo from #_photo where type = ? and act = ?...", array('banner', 'photo_static'), 'fetch', 7200);
$banner = $dataProvider->getPhoto('banner', 'photo_static');

// Thay vì:
// $social = $cache->get("select name$lang, photo, link from #_photo where type = ?...", array('social'), 'result', 7200);
$social = $dataProvider->getPhotos('social');

// Thay vì:
// $footer = $cache->get("select name$lang, content$lang from #_static where type = ?...", array('footer'), 'fetch', 7200);
$footer = $dataProvider->getStatic('footer');

// Thay vì:
// $txtDichVu = $cache->get("select name$lang, content$lang from #_static where type = ?...", array('txt-dich-vu'), 'fetch', 7200);
$txtDichVu = $dataProvider->getStatic('txt-dich-vu');

// Thay vì:
// $txtDKNT = $cache->get("select name$lang, content$lang from #_static where type = ?...", array('txt-dknt'), 'fetch', 7200);
$txtDKNT = $dataProvider->getStatic('txt-dknt');

// Thay vì:
// $screenshot = $cache->get("SELECT id, photo,options from table_photo where type = ?", array('screenshot'), 'fetch', 7200);
$screenshot = $dataProvider->getScreenshot();

// Thay vì:
// $slider = $cache->get("select name$lang, photo, link from #_photo where type = ?...", array('slide'), 'result', 7200);
$slider = $dataProvider->getPhotos('slide');

// Thay vì:
// $chinhsach = $cache->get("select name$lang, slugvi, slugen, id, photo from #_news where type = ?...", array('chinh-sach'), 'result', 7200);
$chinhsach = $dataProvider->getNews('chinh-sach');

// Thay vì:
// $dichvulist = $cache->get("select name$lang, slugvi, slugen, id, photo from #_news_list where type = ?...", array('dich-vu'), 'result', 7200);
$dichvulist = $dataProvider->getNewsList('dich-vu');

// Thay vì:
// $dichvuMenu = $cache->get("select name$lang, slugvi, slugen, id, photo from #_news where type = ?...", array('dich-vu'), 'result', 7200);
$dichvuMenu = $dataProvider->getNews('dich-vu');

// Thay vì:
// $doitac = $cache->get("select name$lang, photo, link from #_photo where type = ?...", array('doitac'), 'result', 7200);
$doitac = $dataProvider->getPhotos('doitac');

// Thay vì:
// $dichvuFooter = $cache->get("select name$lang, slugvi, slugen, id, photo from #_news_list where type = ?...", array('dich-vu'), 'result', 7200);
$dichvuFooter = $dataProvider->getNewsList('dich-vu');

$slogan = $setting['sloganvi'] ?? '';

/* Get statistic */
$counter = $statistic->getCounter();
$online = $statistic->getOnline();

// Thay vì:
// $link_video = $cache->get("select id, photo, link_video from #_photo where type = ? and act = ?...", array('video', 'photo_static'), 'fetch', 7200);
$link_video = $dataProvider->getVideoLink();

/* Newsletter - Có thể sử dụng FormHandler */
if (isset($_POST['submit-newsletter'])) {
    // Có thể sử dụng FormHandler->handleNewsletter() ở đây
    // Hoặc giữ nguyên code cũ nếu muốn
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~16 dòng cache->get() lặp lại
 * CODE MỚI: ~16 dòng nhưng dễ đọc và maintain hơn
 * 
 * LỢI ÍCH:
 * - Consistent API
 * - Type-safe
 * - Easy to extend
 * - Better IDE support
 */

