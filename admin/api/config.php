<?php
	require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'context.php';

	$app = bootstrap_context('admin-api', [
		'sources' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR,
		'templates' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
		'thumbs' => 'thumbs',
	]);

	// Đảm bảo TOKEN được define trong mỗi request
	if (!defined('TOKEN')) {
		global $config;
		if (empty($config)) {
			$config = $app->getConfig()->all();
		}
		if (!defined('NN_CONTRACT')) {
			define('NN_CONTRACT', $config['metadata']['contract'] ?? 'contract');
		}
		define('TOKEN', md5(NN_CONTRACT . $config['database']['url']));
	}

	// Đảm bảo $loginAdmin được define
	global $loginAdmin, $config;
	if (!isset($loginAdmin)) {
		if (empty($config)) {
			$config = $app->getConfig()->all();
		}
		$loginAdmin = $config['login']['admin'] ?? 'LoginAdmin' . ($config['metadata']['contract'] ?? '788922w');
	}

	// Đảm bảo $_SESSION[TOKEN] được set nếu user đã login
	if (!empty($_SESSION[$loginAdmin]['active']) && !isset($_SESSION[TOKEN])) {
		$_SESSION[TOKEN] = true;
	}

    if($func->checkLoginAdmin()==false) { 
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['success' => false, 'msg' => 'Unauthorized'], JSON_UNESCAPED_UNICODE);
		exit;
	}
?>