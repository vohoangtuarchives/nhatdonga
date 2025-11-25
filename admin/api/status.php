<?php
include "config.php";

use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

$result = 0;
$table = SecurityHelper::sanitizePost('table', '');
$id = (int)SecurityHelper::sanitizePost('id', 0);
$attr = SecurityHelper::sanitizePost('attr', '');

if ($id && $table && $attr) {
	$status_detail = $d->rawQueryOne(
		"SELECT status FROM #_{$table} WHERE id = ? LIMIT 0,1",
		[$id]
	);
	
	$status_array = (!empty($status_detail['status'])) ? explode(',', $status_detail['status']) : [];

	if (($key = array_search($attr, $status_array)) !== false) {
		unset($status_array[$key]);
	} else {
		$status_array[] = $attr;
	}

	$data = [
		'status' => (!empty($status_array)) ? implode(',', $status_array) : ""
	];
	
	$d->where('id', $id);
	if ($d->update($table, $data)) {
		$result = 1;
		$cache->delete();
	}
}

echo $result;
?>