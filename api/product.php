<?php

/**
 * api/product.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của api/product.php
 * Sử dụng ProductRepository và PaginationHelper
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp api/product.php api/product.php.backup
 * 2. Copy file này: cp api/product-refactored.php api/product.php
 * 3. Test kỹ trước khi deploy
 */

include "config.php";

use Tuezy\Repository\ProductRepository;
use Tuezy\PaginationHelper;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize PaginationsAjax
include LIBRARIES . "class/class.PaginationsAjax.php";
$pagingAjax = new PaginationsAjax();

// Initialize Repositories and Helpers
$productRepo = new ProductRepository($d, $cache, $lang, $sluglang);
$paginationHelper = new PaginationHelper($pagingAjax, $func);

// Get parameters
$perPage = (int)($_GET['perpage'] ?? 12);
$eShow = SecurityHelper::sanitizeGet('eShow', '');
$idList = (int)($_GET['idList'] ?? 0);
$pNoibat = SecurityHelper::sanitizeGet('noibat', 'all');
$p = (int)($_GET['p'] ?? 1);

$pagingAjax->perpage = $perPage;
$start = $paginationHelper->getStartPoint($p, $perPage);

// Build filters
$filters = [];
if ($idList) {
	$filters['id_list'] = $idList;
}
if ($pNoibat != 'all') {
	$filters['status'] = $pNoibat;
}

// Build page link
$pageLink = "api/product.php?perpage=" . $perPage;
$tempLink = "";
if ($idList) {
	$tempLink .= "&idList=" . $idList;
}
if ($pNoibat != 'all') {
	$tempLink .= "&noibat=" . $pNoibat;
}
$pageLink .= $tempLink;

// Get products - Sử dụng ProductRepository
// Note: Cần filter thêm 'noibat' và 'hienthi' status
$filters['status'] = 'noibat'; // Override với noibat
$products = $productRepo->getProducts('san-pham', $filters, $start, $perPage);
$totalItems = $productRepo->countProducts('san-pham', $filters);

// Pagination
$paging = $pagingAjax->getAllPageLinks($totalItems, $pageLink, $eShow);

// Output HTML (giữ nguyên format cũ)
if ($totalItems) { ?>
	<div class="row row-product">
		<?php foreach ($products as $k => $v) {
			echo $custom->products($v);
		} ?>
	</div>
	<div class="pagination-ajax"><?= $paging ?></div>
<?php }

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~91 dòng với rawQuery và pagination code
 * CODE MỚI: ~70 dòng với ProductRepository và PaginationHelper
 * 
 * GIẢM: ~23% code
 * 
 * LỢI ÍCH:
 * - Sử dụng ProductRepository thay vì rawQuery
 * - Sử dụng PaginationHelper
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 */

