<?php
	require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'context.php';

	$app = bootstrap_context('admin', [
		'sources' => __DIR__ . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR,
		'templates' => __DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR,
		'watermark' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'watermark',
	]);

	/* Ensure variables are available */
	if (!isset($http)) {
		$http = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ||
			(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
			? 'https://' : 'http://';
	}

	if (!isset($configUrl)) {
		$configUrl = $config['database']['server-name'] . $config['database']['url'];
	}

	if (!isset($configBase)) {
		$configBase = $http . $configUrl;
	}

	if (!isset($loginAdmin)) {
		$loginAdmin = $config['login']['admin'] ?? 'LoginAdmin' . ($config['metadata']['contract'] ?? '788922w');
	}

	/* Check HTTP */
	$func->checkHTTP($http, $config['arrayDomainSSL'], $configBase, $configUrl);

	/* Config type */
	require_once LIBRARIES."config-type.php";

	/* Lang Init */
	// require_once LIBRARIES."lang/langinit.php";

	/* Setting */
	$setting = $d->rawQueryOne("select * from #_setting limit 0,1");
	$optsetting = (isset($setting['options']) && $setting['options'] != '') ? json_decode($setting['options'],true) : null;

	/* Requick */
	require_once LIBRARIES."requick.php";

	dump($_SESSION[$loginAdmin]);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="assets/images/admin_logo.jpg" rel="shortcut icon" type="image/x-icon" />
	<title>Administrator - <?=$setting['namevi']?></title>

	<!-- Css all -->
	<?php include TEMPLATE.LAYOUT."css.php"; ?>
</head>
<body class="sidebar-mini hold-transition text-sm <?=(!isset($_SESSION[$loginAdmin]['active']) || $_SESSION[$loginAdmin]['active']==false)?'login-page':''?>">
    <!-- Loader -->
	<?php if($template == 'index' || $template == 'user/login') include TEMPLATE.LAYOUT."loader.php"; ?>

    <!-- Wrapper -->
	<?php if(isset($_SESSION[$loginAdmin]['active']) && ($_SESSION[$loginAdmin]['active'] == true)) { ?>
		<div class="wrapper">
			<?php
				include TEMPLATE.LAYOUT."header.php";
				include TEMPLATE.LAYOUT."menu.php";
			?>
			<div class="content-wrapper">
				<?php if($alertlogin) { ?>
					<section class="content">
						<div class="container-fluid">
							<div class="alert my-alert alert-warning alert-dismissible text-sm bg-gradient-warning mt-3 mb-0">
								<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
								<i class="icon fas fa-exclamation-triangle"></i> <?=$alertlogin?>
							</div>
						</div>
					</section>
				<?php } ?>
				<?php include TEMPLATE.$template."_tpl.php"; ?>
			</div>
			<?php include TEMPLATE.LAYOUT."footer.php"; ?>
		</div>
	<?php } else { include TEMPLATE."user/login_tpl.php" ; } ?>

	<!-- Js all -->
	<?php include TEMPLATE.LAYOUT."js.php"; ?>
</body>
</html>