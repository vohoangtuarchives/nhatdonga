<?php
// Extract params
$url = !empty($params['url']) ? $params['url'] : (isset($_SERVER['HTTP_HOST']) ? 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : '');
$oaid = !empty($params['oaid']) ? $params['oaid'] : '579745863508352884';
$title = !empty($params['title']) ? htmlspecialchars($params['title']) : '';
$description = !empty($params['description']) ? htmlspecialchars($params['description']) : '';
$image = !empty($params['image']) ? $params['image'] : '';
?>
<div class="addthis_inline_share_toolbox_dc09" data-url="<?= htmlspecialchars($url) ?>" data-title="<?= $title ?>" data-description="<?= $description ?>" data-media="<?= htmlspecialchars($image) ?>"></div>
<div class="zalo-share-button" data-href="<?= htmlspecialchars($url) ?>" data-oaid="<?= htmlspecialchars($oaid) ?>" data-layout="3" data-color="blue" data-customize="false"></div>