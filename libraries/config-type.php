<?php

/* Config type - Group */

$config['group'] = array();



/* Config type - Product */

require_once LIBRARIES . 'type/config-type-product.php';



/* Config type - Tags */

// require_once LIBRARIES.'type/config-type-tags.php';



/* Config type - Filter */

// require_once LIBRARIES.'type/config-type-filter.php';





/* Config type - Newsletter */

require_once LIBRARIES . 'type/config-type-newsletter.php';



/* Config type - News */

require_once LIBRARIES . 'type/config-type-news.php';



/* Config type - Static */

require_once LIBRARIES . 'type/config-type-static.php';



/* Config type - Photo */

require_once LIBRARIES . 'type/config-type-photo.php';



/* Seo page */

$config['seopage']['page'] = array(

//    "san-pham" => "Sản phẩm",

//    "dich-vu-sua-chua" => "Dịch vụ sữa chữa",

//    "dich-vu-bao-duong" => "Dịch vụ bão dưỡng",

//    "tu-van" => "Tư vấn",

    "lien-he" => "Liên hệ"

);

$config['seopage']['width'] = 300;

$config['seopage']['height'] = 200;

$config['seopage']['thumb'] = '300x200x1';

$config['seopage']['img_type'] = '.jpg|.gif|.png|.jpeg|.gif';



/* Setting */

$config['setting']['address'] = true;

$config['setting']['phone'] = true;

$config['setting']['hotline'] = true;

$config['setting']['zalo'] = true;

$config['setting']['zalo2'] = true;

$config['setting']['oaidzalo'] = true;

$config['setting']['email'] = true;

$config['setting']['website'] = true;

$config['setting']['fanpage'] = true;

$config['setting']['coords'] = true;

$config['setting']['coords_iframe'] = true;

$config['setting']['worktime'] = false;



/* Quản lý import */

$config['import']['images'] = false;

$config['import']['thumb'] = '100x100x1';

$config['import']['img_type'] = ".jpg|.gif|.png|.jpeg|.gif";



/* Quản lý export */

$config['export']['category'] = false;



/* Quản lý tài khoản */

$config['user']['active'] = true;

$config['user']['admin'] = false;

$config['user']['check_admin'] = array("hienthi" => "Kích hoạt");

$config['user']['member'] = false;

$config['user']['check_member'] = array("hienthi" => "Kích hoạt");



/* Quản lý phân quyền */

$config['permission']['active'] = false;

$config['permission']['check'] = array("hienthi" => "Kích hoạt");



/* Quản lý liên lệ */

$config['contact']['check'] = array("hienthi" => "Xác nhận");



/* Quản lý địa điểm */

$config['places']['active'] = false;

$config['places']['check_city'] = array("hienthi" => "Hiển thị");

$config['places']['check_district'] = array("hienthi" => "Hiển thị");

$config['places']['check_ward'] = array("hienthi" => "Hiển thị");

$config['places']['placesship'] = false;

$config['places']['ship_price'] = array("city","district","ward");





/* Quản lý thông báo đẩy */

$config['onesignal'] = false;



/* Quản lý giỏ hàng */

$config['order']['active'] = false;



if($config['order']['active']){

    include "type/config-giohang.php";

}

