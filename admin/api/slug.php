<?php
	include "config.php";

	$dataSlug = array();
	$dataSlug['slug'] = (!empty($_POST['slug'])) ? trim(htmlspecialchars($_POST['slug'])) : '';
	$dataSlug['id'] = (!empty($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;
	$dataSlug['copy'] = (!empty($_POST['copy'])) ? htmlspecialchars($_POST['copy']) : 0;
	$dataSlug['table'] = (!empty($_POST['table'])) ? htmlspecialchars($_POST['table']) : '';
	$dataSlug['type'] = (!empty($_POST['type'])) ? htmlspecialchars($_POST['type']) : '';
	
	echo ($func->checkSlug($dataSlug) == 'exist') ? 0 : 1;
?>