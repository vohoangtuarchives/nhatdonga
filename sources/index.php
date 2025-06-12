<?php  
	if(!defined('SOURCES')) die("Error");

    $tintuc = $cache->get("SELECT name$lang, slug$lang,photo,desc$lang from table_news where type=? and find_in_set('hienthi',status) and find_in_set('noibat',status) order by numb",array('tin-tuc'),'result',7200);

    $pro_spec = $cache->get("SELECT id,name$lang, slug$lang,photo,regular_price,sale_price from table_product where type=? and find_in_set('hienthi',status) and find_in_set('noibat',status) order by numb limit 0,8",array('san-pham'),'result',7200);

    $gioithieu = $cache->get("SELECT name$lang, desc$lang from table_static where type=? and find_in_set('hienthi',status) limit 0,1",array('gioi-thieu'),'fetch',7200);

    /* SEO */
    $seoDB = $seo->getOnDB(0,'setting','update','setting');
    if(!empty($seoDB['title'.$seolang])) $seo->set('h1',$seoDB['title'.$seolang]);
    if(!empty($seoDB['title'.$seolang])) $seo->set('title',$seoDB['title'.$seolang]);
    if(!empty($seoDB['keywords'.$seolang])) $seo->set('keywords',$seoDB['keywords'.$seolang]);
    if(!empty($seoDB['description'.$seolang])) $seo->set('description',$seoDB['description'.$seolang]);
    $seo->set('url',$func->getPageURL());
    $imgJson = (!empty($screenshot['options'])) ? json_decode($screenshot['options'],true) : null;
    if(is_array($screenshot) && (empty($imgJson) || ($imgJson['p'] != $screenshot['photo'])))
    {
        $imgJson = $func->getImgSize($screenshot['photo'],UPLOAD_PHOTO_L.$screenshot['photo']);
        $seo->updateSeoDB(json_encode($imgJson),'photo',$screenshot['id']);
    }
    if(!empty($imgJson))
    {
        $seo->set('photo',$configBase.THUMBS.'/'.$imgJson['w'].'x'.$imgJson['h'].'x2/'.UPLOAD_PHOTO_L.$screenshot['photo']);
        $seo->set('photo:width',$imgJson['w']);
        $seo->set('photo:height',$imgJson['h']);
        $seo->set('photo:type',$imgJson['m']);
    }
?>