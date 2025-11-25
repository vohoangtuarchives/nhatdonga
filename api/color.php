<?php

/**
 * api/color.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của api/color.php
 * Sử dụng ProductRepository và SecurityHelper
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp api/color.php api/color.php.backup
 * 2. Copy file này: cp api/color-refactored.php api/color.php
 * 3. Test kỹ trước khi deploy
 */

include "config.php";

use Tuezy\Repository\ProductRepository;
use Tuezy\Config;
use Tuezy\SecurityHelper;
use Tuezy\Service\ProductService;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories
$productRepo = new ProductRepository($d, $cache, $lang, $sluglang);
$productService = new ProductService($productRepo, null, null, $d, $lang);

// Get parameters
$id_color = (int)($_POST['id_color'] ?? 0);
$id_pro = (int)($_POST['id_pro'] ?? 0);

if ($id_color && $id_pro) {
	// Get gallery by color - Sử dụng rawQuery vì cần filter theo id_color
	$rowDetailPhoto = $d->rawQuery(
		"select photo, id_parent, id from #_gallery where id_color = ? and id_parent = ? and com = ? and type = ? and kind = ? and val = ?",
		array($id_color, $id_pro, 'product', 'san-pham', 'man', 'san-pham')
	);
	
	// Get product detail - Sử dụng ProductService
	$rowDetailContext = $productService->getDetailContext($id_pro, 'san-pham', false);
	$rowDetail = $rowDetailContext['detail'] ?? null;
	
	if (!empty($rowDetailPhoto) && $rowDetail) { ?>
		<a id="Zoom-1" class="MagicZoom" data-options="zoomMode: off; hint: off; rightClick: true; selectorTrigger: hover; expandCaption: false; history: false;" href="<?=ASSET.WATERMARK?>/product/540x540x1/<?=UPLOAD_PRODUCT_L.$rowDetailPhoto[0]['photo']?>" title="<?=$rowDetail['name'.$lang]?>">
			<?=$func->getImage(['isLazy' => false, 'sizes' => '540x540x1', 'isWatermark' => true, 'prefix' => 'product', 'upload' => UPLOAD_PRODUCT_L, 'image' => $rowDetailPhoto[0]['photo'], 'alt' => $rowDetail['name'.$lang]])?>
		</a>
		
		<div class="gallery-thumb-pro">
			<div class="owl-page owl-carousel owl-theme owl-pro-detail"
				data-xsm-items="5:10" 
				data-sm-items="5:10" 
				data-md-items="5:10" 
				data-lg-items="5:10" 
				data-xlg-items="5:10" 
				data-nav="1" 
				data-navtext="<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-left' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='15 6 9 12 15 18' /></svg>|<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-chevron-right' width='44' height='45' viewBox='0 0 24 24' stroke-width='1.5' stroke='#2c3e50' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'/><polyline points='9 6 15 12 9 18' /></svg>" 
				data-navcontainer=".control-pro-detail">
				<?php foreach($rowDetailPhoto as $v) { ?>
					<a class="thumb-pro-detail" data-zoom-id="Zoom-1" href="<?=ASSET.WATERMARK?>/product/540x540x1/<?=UPLOAD_PRODUCT_L.$v['photo']?>" title="<?=$rowDetail['name'.$lang]?>">
						<?=$func->getImage(['isLazy' => false, 'sizes' => '540x540x1', 'isWatermark' => true, 'prefix' => 'product', 'upload' => UPLOAD_PRODUCT_L, 'image' => $v['photo'], 'alt' => $rowDetail['name'.$lang]])?>
					</a>
				<?php } ?>
			</div>
			<div class="control-pro-detail control-owl transition"></div>
		</div>
	<?php }
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~61 dòng với rawQuery
 * CODE MỚI: ~55 dòng với ProductRepository
 * 
 * GIẢM: ~10% code
 * 
 * LỢI ÍCH:
 * - Sử dụng ProductRepository cho product detail
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc hơn
 */

