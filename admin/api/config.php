<?php
	require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'context.php';

	$app = bootstrap_context('admin-api', [
		'sources' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR,
		'templates' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
		'thumbs' => 'thumbs',
	]);

    if($func->checkLoginAdmin()==false) { die(); }
?>