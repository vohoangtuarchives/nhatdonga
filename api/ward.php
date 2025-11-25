<?php

/**
 * api/ward.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của api/ward.php
 * Sử dụng SecurityHelper
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp api/ward.php api/ward.php.backup
 * 2. Copy file này: cp api/ward-refactored.php api/ward.php
 * 3. Test kỹ trước khi deploy
 */

include "config.php";

use Tuezy\Repository\LocationRepository;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize LocationRepository
$locationRepo = new LocationRepository($d, $cache);

// Get parameters
$id_district = (int)SecurityHelper::sanitizePost('id_district', 0);

$ward = [];
if ($id_district) {
	$ward = $locationRepo->getWardsByDistrict($id_district);
}

if ($ward) { ?>
	<option value=""><?=phuongxa?></option>
	<?php foreach($ward as $k => $v) { ?>
		<option value="<?=$v['id']?>"><?=$v['name']?></option>
	<?php }
} else { ?>
	<option value=""><?=phuongxa?></option>
<?php }

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~36 dòng
 * CODE MỚI: ~30 dòng với SecurityHelper
 * 
 * GIẢM: ~17% code
 * 
 * LỢI ÍCH:
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc hơn
 */

