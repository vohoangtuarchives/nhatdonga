<?php
$assets = include __DIR__ . '/assets-manifest.php';
$cssAssets = $assets['css'] ?? [];
?>
<!-- Css Fonts -->

<!-- Css Files -->
<?php foreach ($cssAssets as $path) { ?>
	<link href="<?=htmlspecialchars($path, ENT_QUOTES, 'UTF-8')?>" rel="stylesheet">
<?php } ?>