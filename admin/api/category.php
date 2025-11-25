<?php
include "config.php";

use Tuezy\Repository\CategoryRepository;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize CategoryRepository
$categoryRepo = new CategoryRepository($d, $cache, $lang, $sluglang, 'product');

$str = '<option value="0">Chọn danh mục</option>';

if (!empty($_POST["id"])) {
	$level = (int)SecurityHelper::sanitizePost('level', 0);
	$table = SecurityHelper::sanitizePost('table', '');
	$id = (int)SecurityHelper::sanitizePost('id', 0);
	$type = SecurityHelper::sanitizePost('type', '');
	
	$idField = '';
	switch($level) {
		case 0:
			$idField = "id_list";
			break;
		case 1:
			$idField = "id_cat";
			break;
		case 2:
			$idField = "id_item";
			break;
		default:
			echo $str;
			exit();
	}

	if ($id && $table) {
		$row = $d->rawQuery(
			"SELECT namevi, id FROM #_{$table} WHERE {$idField} = ? AND type = ? ORDER BY numb, id DESC",
			[$id, $type]
		);

		if (!empty($row)) {
			foreach($row as $v) {
				$str .= '<option value="' . htmlspecialchars($v["id"]) . '">' . htmlspecialchars($v["namevi"]) . '</option>';
			}
		}
	}
}

echo $str;
?>