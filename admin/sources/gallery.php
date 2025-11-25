<?php

/**
 * admin/sources/gallery.php - REFACTORED VERSION
 * 
 * Sử dụng GalleryAdminController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\Controller\GalleryAdminController;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;
use Tuezy\SecurityHelper;

// Initialize language variables
if (!isset($lang)) {
	$lang = $_SESSION['lang'] ?? 'vi';
}
if (!isset($sluglang)) {
	$sluglang = 'slugvi';
}

// Initialize Controller
$adminAuthHelper = new AdminAuthHelper($func, $d, $loginAdmin, $config);
$adminPermissionHelper = new AdminPermissionHelper($func, $config);
$controller = new GalleryAdminController($d, $cache, $func, $config, $adminAuthHelper, $adminPermissionHelper);

switch($act) {
	case "man_photo":
		$viewData = $controller->manPhoto($id_parent, $com, $type, $kind, $val, $curPage, 10);
		$items = $viewData['items'];
		$paging = $viewData['paging'];
		$template = "gallery/man/photos";
		break;
		
	case "add_photo":
		$template = "gallery/man/photo_add";
		break;
		
	case "edit_photo":
		$id = (int)SecurityHelper::sanitizeGet('id', 0);
		if (!$id) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}", false);
		}
		
		$item = $controller->getPhoto($id, $id_parent, $com, $type, $kind, $val);
		
		if (empty($item)) {
			$func->transfer("Dữ liệu không có thực", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}", false);
		}
		$template = "gallery/man/photo_edit";
		break;
		
	case "save_photo":
		// Save logic - giữ nguyên logic cũ vì phức tạp (file upload, dataMulti, etc.)
		if (function_exists('savePhoto')) {
			savePhoto();
		}
		break;
		
	case "delete_photo":
		$id = (int)SecurityHelper::sanitizeGet('id', 0);
		if ($id && $controller->deletePhoto($id)) {
			$func->transfer("Xóa dữ liệu thành công", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}");
		} else {
			$func->transfer("Xóa dữ liệu thất bại", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}", false);
		}
		break;
		
	default:
		$template = "404";
}

function savePhoto()
{
	global $d, $curPage, $func, $config, $dfgallery, $type, $kind, $val, $id_parent, $com;
	
	if (empty($_POST)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}", false);
	}
	
	$id = (int)SecurityHelper::sanitizePost('id', 0);
	$data = $_POST['data'] ?? null;
	$dataMultiTemp = $_POST['dataMulti'] ?? null;
	
	if ($data) {
		foreach($data as $column => $value) {
			$data[$column] = SecurityHelper::sanitize($value);
		}
	}
	
	$data['id_parent'] = $id_parent;
	$data['com'] = $com;
	$data['type'] = $type;
	$data['kind'] = $kind;
	$data['val'] = $val;
	
	if ($id) {
		if (isset($_POST['status'])) {
			$status = '';
			foreach($_POST['status'] as $attr_value) {
				if ($attr_value != "") $status .= $attr_value . ',';
			}
			$data['status'] = !empty($status) ? rtrim($status, ",") : "";
		}
		
		$data['date_updated'] = time();
		$d->where('id', $id);
		if ($d->update('gallery', $data)) {
			if ($func->hasFile("file")) {
				$row = $d->rawQueryOne("SELECT photo FROM #_gallery WHERE id = ? LIMIT 0,1", [$id]);
				if ($row && $row['photo']) {
					$func->deleteFile(UPLOAD_GALLERY . $row['photo']);
				}
				
				$file_name = $func->uploadName($_FILES["file"]["name"]);
				if ($photo = $func->uploadImage("file", $config['gallery']['img_type'], UPLOAD_GALLERY, $file_name)) {
					$d->where('id', $id);
					$d->update('gallery', ['photo' => $photo]);
				}
			}
			$func->transfer("Cập nhật dữ liệu thành công", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}");
		}
	} else {
		$data['date_created'] = time();
		$data['numb'] = $d->rawQueryOne("SELECT MAX(numb) as max_numb FROM #_gallery WHERE id_parent = ? AND com = ? AND type = ? AND kind = ? AND val = ?", [$id_parent, $com, $type, $kind, $val])['max_numb'] + 1;
		
		if ($d->insert('gallery', $data)) {
			$id_insert = $d->getLastInsertId();
			
			if ($func->hasFile("file")) {
				$file_name = $func->uploadName($_FILES["file"]["name"]);
				if ($photo = $func->uploadImage("file", $config['gallery']['img_type'], UPLOAD_GALLERY, $file_name)) {
					$d->where('id', $id_insert);
					$d->update('gallery', ['photo' => $photo]);
				}
			}
			
			if ($dataMultiTemp) {
				foreach($dataMultiTemp as $k => $v) {
					$dataMulti = [];
					foreach($v as $k2 => $v2) {
						$dataMulti[$k2] = SecurityHelper::sanitize($v2);
					}
					$dataMulti['id_parent'] = $id_insert;
					$dataMulti['com'] = $com;
					$dataMulti['type'] = $type;
					$dataMulti['kind'] = 'man';
					$dataMulti['val'] = $type;
					$dataMulti['date_created'] = time();
					$d->insert('gallery', $dataMulti);
				}
			}
			
			$func->transfer("Thêm dữ liệu thành công", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}");
		}
	}
	
	$func->transfer("Lưu dữ liệu thất bại", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}", false);
}

