<?php  

	if(!defined('SOURCES')) die("Error");

use Tuezy\Helper\GlobalHelper;
use Tuezy\Controller\HomeController;

// Get dependencies using helper functions
$db = GlobalHelper::db();
$cache = GlobalHelper::cache();
$seo = GlobalHelper::seo();
$func = GlobalHelper::func();
$config = GlobalHelper::config();

// Get language variables
$lang = $_SESSION['lang'] ?? 'vi';
$seolang = "vi";

// Get configBase
$http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ? 'https://' : 'http://';
$configUrl = $config['database']['server-name'] . $config['database']['url'];
$configBase = $http . $configUrl;

// Get screenshot from allpage (should be set in allpage.php)
if (!isset($screenshot)) {
    $screenshot = [];
}

// Sử dụng HomeController để lấy data cho homepage
$homeController = new HomeController($db, $cache, $func, $seo, $config);
$homeData = $homeController->index();

// Extract data từ controller
extract($homeData);

// Giữ lại các biến cũ để tương thích với template cũ (nếu có)
$tintuc = $cache->get("SELECT name$lang, slug$lang,photo,desc$lang from table_news where type=? and find_in_set('hienthi',status) and find_in_set('noibat',status) order by numb",array('tin-tuc'),'result',7200);
$pro_spec = $featuredProducts ?? []; // Sử dụng data từ controller
$gioithieu = $cache->get("SELECT name$lang, desc$lang from table_static where type=? and find_in_set('hienthi',status) limit 0,1",array('gioi-thieu'),'fetch',7200);

    $imgJson = (!empty($screenshot['options'])) ? json_decode($screenshot['options'],true) : null;

    if(is_array($screenshot) && !empty($screenshot['photo']) && (empty($imgJson) || ($imgJson['p'] != $screenshot['photo'])))

    {

        $imgJson = $func->getImgSize($screenshot['photo'],UPLOAD_PHOTO_L.$screenshot['photo']);

        if (!empty($screenshot['id']) && !empty($imgJson)) {
            $seo->updateSeoDB(json_encode($imgJson),'photo',$screenshot['id']);
        }

    }

    if(!empty($imgJson) && !empty($screenshot['photo']))

    {

        $seo->set('photo',$configBase.THUMBS.'/'.$imgJson['w'].'x'.$imgJson['h'].'x2/'.UPLOAD_PHOTO_L.$screenshot['photo']);

        $seo->set('photo:width',$imgJson['w']);

        $seo->set('photo:height',$imgJson['h']);

        $seo->set('photo:type',$imgJson['m']);

    }

?>