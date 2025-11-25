<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\AdminCRUDHelper;
use Tuezy\SecurityHelper;

$adminCRUD = new AdminCRUDHelper(
	$d, 
	$func, 
	$flash, 
	'gallery', 
	$type, 
	'gallery', 
	UPLOAD_GALLERY_L, 
	$lang, 
	$sluglang
);

switch($act) {
	case "man_photo":
		$where = "id_parent = ? and com = ? and type = ? and kind = ? and val = ?";
		$params = [$id_parent, $com, $type, $kind, $val];
		
		$perPage = 10;
		$start = ($curPage - 1) * $perPage;
		$sql = "SELECT * FROM #_gallery WHERE {$where} ORDER BY numb,id DESC LIMIT {$start},{$perPage}";
		$items = $d->rawQuery($sql, $params);
		
		$countSql = "SELECT COUNT(*) as total FROM #_gallery WHERE {$where}";
		$total = $d->rawQueryOne($countSql, $params);
		$totalItems = (int)($total['total'] ?? 0);
		
		$url = "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}";
		$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
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
		
		$item = $d->rawQueryOne(
			"SELECT * FROM #_gallery WHERE id_parent = ? AND com = ? AND type = ? AND kind = ? AND val = ? AND id = ? LIMIT 0,1",
			[$id_parent, $com, $type, $kind, $val, $id]
		);
		
		if (empty($item)) {
			$func->transfer("Dữ liệu không có thực", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}", false);
		}
		$template = "gallery/man/photo_edit";
		break;
		
	case "save_photo":
		savePhoto();
		break;
		
	case "delete_photo":
		$id = (int)SecurityHelper::sanitizeGet('id', 0);
		if ($id) {
			$item = $d->rawQueryOne("SELECT * FROM #_gallery WHERE id = ? LIMIT 0,1", [$id]);
			if ($item) {
				if ($d->rawQuery("DELETE FROM #_gallery WHERE id = ?", [$id])) {
					$func->deleteFile(UPLOAD_GALLERY . $item['photo']);
					$func->transfer("Xóa dữ liệu thành công", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}");
				}
			}
		}
		$func->transfer("Xóa dữ liệu thất bại", "index.php?com={$com}&act=man_photo&id_parent={$id_parent}&type={$type}&kind={$kind}&val={$val}&p={$curPage}", false);
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

