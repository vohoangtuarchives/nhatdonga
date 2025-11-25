<?php

/**
 * api/district.php - REFACTORED VERSION
 * 
 * File này là phiên bản refactored của api/district.php
 * Sử dụng SecurityHelper
 * 
 * CÁCH SỬ DỤNG:
 * 1. Backup file gốc: cp api/district.php api/district.php.backup
 * 2. Copy file này: cp api/district-refactored.php api/district.php
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
$id_city = (int)SecurityHelper::sanitizePost('id_city', 0);

$district = [];
if ($id_city) {
	$district = $locationRepo->getDistrictsByCity($id_city);
}

if ($district) { ?>
	<option value=""><?=quanhuyen?></option>
	<?php foreach($district as $k => $v) { ?>
		<option value="<?=$v['id']?>"><?=$v['name']?></option>
	<?php }
} else { ?>
	<option value=""><?=quanhuyen?></option>
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

