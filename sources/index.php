<?php

if (!defined('SOURCES')) die("Error");

$indexProductsNoiBat = $iCache->remember('indexProductsNoiBat', 3600, function () use ($lang, $d) {
    $productNB = new \Illuminate\Support\Collection($d->rawQuery("select * from #_product where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status)", ['san-pham']));

    return $productNB;
});

$indexProductListsNoiBat = $iCache->remember('indexProductListsNoiBat', 3600, function () use ($lang, $d) {
    $productNB = new \Illuminate\Support\Collection($d->rawQuery("select * from #_product_list where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status)", ['san-pham']));
    return $productNB;
});


$popup = $cache->get("select name$lang, photo, link from #_photo where type = ? and act = ? and find_in_set('hienthi',status) limit 0,1", array('popup', 'photo_static'), 'fetch', 7200);

$slider = $cache->get("select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) order by numb desc", array('slide'), 'result', 7200);
$sliderGioithieu = $cache->get("select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('gioi-thieu'), 'result', 7200);
$sliderFooter = $cache->get("select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) order by numb desc", array('slide-footer'), 'result', 7200);


$brand = $cache->get("select name$lang, slugvi, slugen, id, photo from #_product_brand where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('san-pham'), 'result', 7200);

$pronb = $cache->get("select id from #_product where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status)", array('san-pham'), 'result', 7200);

$splistnb = $cache->get("select name$lang, slugvi, slugen, id, photo from #_product_list where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) order by numb,id desc", array('san-pham'), 'result', 7200);

$newsnb = $cache->get("select name$lang, slugvi, slugen, desc$lang, date_created, id, photo from #_news where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) order by numb,id desc limit 3", array('tin-tuc'), 'result', 7200);

$videonb = $cache->get("select id from #_photo where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status)", array('video'), 'result', 7200);

$productNews = $cache->get("select * from #_product where type = ? and find_in_set('moi',status) and find_in_set('hienthi',status)", array('san-pham'), 'result', 7200);

$partner = $cache->get("select name$lang, link, photo from #_photo where type = ? and find_in_set('hienthi',status) order by numb, id desc", array('doitac'), 'result', 7200);

$sliderSocial = $cache->get("select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('slide-social'), 'result', 7200);

$sliderQC = $cache->get("select name$lang, photo, link from #_photo where type = ? and find_in_set('hienthi',status) order by numb,id desc", array('slide-qc'), 'result', 7200);

$khachhangNews = $cache->get("select name$lang, slugvi, slugen, desc$lang, date_created, id, photo from #_news where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) order by numb,id desc", array('khach-hang'), 'result', 7200);

$gallery = $cache->get("select * from #_product where type=? and find_in_set('hienthi',status) order by numb,id desc limit 8", array('thu-vien-anh'), 'result', 7200);
$indexServices = $cache->get("select * from #_news where type = ? and find_in_set('noibat',status) and find_in_set('hienthi',status) order by numb,id desc", array('dich-vu'), 'result', 7200);

$gioithieuIndex = $cache->get("SELECT * from table_static where type=? and find_in_set('hienthi',status) limit 0,1", array('gioi-thieu'), 'fetch', 7200);

/* SEO */

$seoDB = $seo->getOnDB(0, 'setting', 'update', 'setting');
if (!empty($seoDB['title' . $seolang])) $seo->set('h1', $seoDB['title' . $seolang]);

if (!empty($seoDB['title' . $seolang])) $seo->set('title', $seoDB['title' . $seolang]);

if (!empty($seoDB['keywords' . $seolang])) $seo->set('keywords', $seoDB['keywords' . $seolang]);

if (!empty($seoDB['description' . $seolang])) $seo->set('description', $seoDB['description' . $seolang]);

$seo->set('url', $func->getPageURL());

$imgJson = (!empty($logo['options'])) ? json_decode($logo['options'], true) : null;

if (empty($imgJson) || ($imgJson['p'] != $logo['photo'])) {

    $imgJson = $func->getImgSize($logo['photo'], UPLOAD_PHOTO_L . $logo['photo']);

    $seo->updateSeoDB(json_encode($imgJson), 'photo', $logo['id']);

}

if (!empty($imgJson)) {

    $seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_PHOTO_L . $logo['photo']);

    $seo->set('photo:width', $imgJson['w']);

    $seo->set('photo:height', $imgJson['h']);

    $seo->set('photo:type', $imgJson['m']);

}

