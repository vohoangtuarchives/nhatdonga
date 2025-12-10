<?php

if (!defined('SOURCES')) die("Error");

// Ensure ROOT is defined
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR);
}

use Tuezy\Admin\AdminCRUDHelper;
use Tuezy\SecurityHelper;

if (isset($config['filter'])) {
	$arrCheck = array();
	foreach ($config['filter'] as $k => $v) $arrCheck[] = $k;
	if (!count($arrCheck) || !in_array($type, $arrCheck)) {
		$func->transfer("Trang không tồn tại", "index.php", false);
	}
} else {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

$adminCRUD = new AdminCRUDHelper(
	$d, 
	$func, 
	$flash, 
	'filter', 
	$type, 
	'filter', 
	UPLOAD_FILE_L, 
	$lang, 
	$sluglang
);

switch ($act) {
	case "man":
		$filters = [];
		if (isset($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}
		
		$where = "type = ?";
		$params = [$type];
		if (!empty($filters['keyword'])) {
			$where .= " AND (tenvi LIKE ? OR tenen LIKE ?)";
			$params[] = "%{$filters['keyword']}%";
			$params[] = "%{$filters['keyword']}%";
		}
		
		$perPage = 10;
		$start = ($curPage - 1) * $perPage;
		$sql = "SELECT * FROM #_filter WHERE {$where} ORDER BY numb,id DESC LIMIT {$start},{$perPage}";
		$items = $d->rawQuery($sql, $params);
		
		$countSql = "SELECT COUNT(*) as total FROM #_filter WHERE {$where}";
		$total = $d->rawQueryOne($countSql, $params);
		$totalItems = (int)($total['total'] ?? 0);
		
		$url = "index.php?com=filter&act=man&type=" . $type;
		$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
		$template = "filter/man/mans";
		break;

	case "add":
		$template = "filter/man/man_add";
		break;

	case "edit":
		$id = (int)SecurityHelper::sanitizeGet('id', 0);
		if (!$id) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
		}
		
		$item = $d->rawQueryOne("SELECT * FROM #_filter WHERE id = ? AND type = ? LIMIT 0,1", [$id, $type]);
		if (empty($item['id'])) {
			$func->transfer("Dữ liệu không có thực", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
		}
		$template = "filter/man/man_add";
		break;

	case "save":
		saveMan();
		break;

	case "delete":
		deleteMan();
		break;

	default:
		$template = "404";
}

/* Save filter */
function saveMan()
{
	global $d, $curPage, $func, $config, $com, $type, $adminCRUD;
	
	if (empty($_POST)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
	}
	
	$id = (int)SecurityHelper::sanitizePost('id', 0);
	$data = $_POST['data'] ?? null;
	
	// Handle value range (min-max)
	if (isset($config['filter'][$type]['value']) && $config['filter'][$type]['value'] == true) {
		$min = SecurityHelper::sanitizePost('min-value', '');
		$max = SecurityHelper::sanitizePost('max-value', '');
		
		if ($min != "" && $max != "" && $min >= $max) {
			$func->transfer("Giá trị lớn nhất phải lớn hơn giá trị nhỏ nhất", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
		}
		
		if (isset($config['filter'][$type]['price']) && $config['filter'][$type]['price'] == true) {
			$min = str_replace(',', '', $min);
			$max = str_replace(',', '', $max);
		}
		
		if ($min != "" && $max != "") {
			$data['value'] = $min . ',' . $max;
		}
	}
	
	// Sanitize data
	if ($data) {
		foreach($data as $column => $value) {
			$data[$column] = SecurityHelper::sanitize($value);
		}
	}
	
	// Handle status
	if (isset($_POST['status'])) {
		$status = '';
		foreach($_POST['status'] as $attr_value) {
			if ($attr_value != "") $status .= $attr_value . ',';
		}
		$data['status'] = !empty($status) ? rtrim($status, ",") : "";
	} else {
		$data['status'] = "";
	}
	
	// Handle photo upload
	if ($id) {
		$data['date_updated'] = time();
		$d->where('id', $id);
		$d->where('type', $type);
		if ($d->update('filter', $data)) {
			// Handle photo upload
			if ($func->hasFile("file")) {
				$row = $d->rawQueryOne("SELECT id, photo FROM #_filter WHERE id = ? AND type = ? LIMIT 0,1", [$id, $type]);
				if (!empty($row) && !empty($row['photo'])) {
					$func->deleteFile(UPLOAD_FILTER . $row['photo']);
				}
				
				$file_name = $func->uploadName($_FILES["file"]["name"]);
				if ($photo = $func->uploadImage("file", $config['filter'][$type]['img_type'], UPLOAD_FILTER, $file_name)) {
					$d->where('id', $id);
					$d->update('filter', ['photo' => $photo]);
				}
			}
			$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage);
		} else {
			$func->transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
		}
	} else {
		$data['type'] = $type;
		$data['date_created'] = time();
		$data['numb'] = ($d->rawQueryOne("SELECT MAX(numb) as max_numb FROM #_filter WHERE type = ?", [$type])['max_numb'] ?? 0) + 1;
		
		if ($d->insert('filter', $data)) {
			$id_insert = $d->getLastInsertId();
			
			// Handle photo upload
			if ($func->hasFile("file")) {
				$file_name = $func->uploadName($_FILES['file']["name"]);
				if ($photo = $func->uploadImage("file", $config['filter'][$type]['img_type'], UPLOAD_FILTER, $file_name)) {
					$d->where('id', $id_insert);
					$d->update('filter', ['photo' => $photo]);
				}
			}
			
			$func->transfer("Lưu dữ liệu thành công", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage);
		} else {
			$func->transfer("Lưu dữ liệu bị lỗi", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
		}
	}
}

/* Delete filter */
function deleteMan()
{
	global $d, $curPage, $func, $com, $type;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	
	if ($id) {
		$row = $d->rawQueryOne("SELECT id, photo FROM #_filter WHERE id = ? AND type = ? LIMIT 0,1", [$id, $type]);
		if (!empty($row['id'])) {
			if (!empty($row['photo'])) {
				$func->deleteFile(UPLOAD_FILTER . $row['photo']);
			}
			$d->rawQuery("DELETE FROM #_filter WHERE id = ?", [$id]);
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage);
		} else {
			$func->transfer("Xóa dữ liệu bị lỗi", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
		}
	} elseif (isset($_GET['listid'])) {
		$listid = explode(",", $_GET['listid']);
		foreach ($listid as $idItem) {
			$idItem = (int)SecurityHelper::sanitize($idItem);
			$row = $d->rawQueryOne("SELECT id, photo FROM #_filter WHERE id = ? AND type = ? LIMIT 0,1", [$idItem, $type]);
			if (!empty($row['id'])) {
				if (!empty($row['photo'])) {
					$uploadPath = defined('UPLOAD_FILTER_L') ? UPLOAD_FILTER_L : 'upload/filter/';
					$filePath = ROOT . $uploadPath . $row['photo'];
					$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
					if (file_exists($filePath)) {
						$func->deleteFile($filePath);
					}
				}
				$d->rawQuery("DELETE FROM #_filter WHERE id = ?", [$idItem]);
			}
		}
		$func->transfer("Xóa dữ liệu thành công", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage);
	} else {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
	}
}

