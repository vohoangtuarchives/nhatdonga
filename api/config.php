<?php

	require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'context.php';

	$app = bootstrap_context('api', [
		'sources' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR,
		'templates' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
		'thumbs' => 'thumbs',
		'watermark' => 'watermark',
	]);

	if(empty($_SESSION['lang'])) $_SESSION['lang'] = 'vi';

    $lang = $_SESSION['lang'];

    require_once LIBRARIES."lang/$lang.php";

    /* Slug lang */
    $sluglang = 'slugvi';

    /* Setting */
    $sqlCache = "select * from #_setting";
    $setting = $cache->get($sqlCache, null, 'fetch', 7200);
    $optsetting = (!empty($setting['options'])) ? json_decode($setting['options'],true) : null;

?>