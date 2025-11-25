<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\AdminCRUDHelper;
use Tuezy\SecurityHelper;

if (!isset($config['onesignal']) || $config['onesignal'] == false) {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

$adminCRUD = new AdminCRUDHelper(
	$d, 
	$func, 
	$flash, 
	'pushonesignal', 
	'', 
	'pushonesignal', 
	UPLOAD_FILE_L, 
	$lang, 
	$sluglang
);

switch($act) {
	case "man":
		$filters = [];
		if (isset($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}
		
		$where = "id<>0";
		$params = [];
		if (!empty($filters['keyword'])) {
			$where .= " AND name LIKE ?";
			$params[] = "%{$filters['keyword']}%";
		}
		
		$perPage = 10;
		$start = ($curPage - 1) * $perPage;
		$sql = "SELECT * FROM #_pushonesignal WHERE {$where} ORDER BY numb,id DESC LIMIT {$start},{$perPage}";
		$items = $d->rawQuery($sql, $params);
		
		$countSql = "SELECT COUNT(*) as total FROM #_pushonesignal WHERE {$where}";
		$total = $d->rawQueryOne($countSql, $params);
		$totalItems = (int)($total['total'] ?? 0);
		
		$url = "index.php?com=pushOnesignal&act=man";
		$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
		$template = "pushOnesignal/man/mans";
		break;
		
	case "add":
		$template = "pushOnesignal/man/man_add";
		break;
		
	case "edit":
		$id = (int)SecurityHelper::sanitizeGet('id', 0);
		if (!$id) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=pushOnesignal&act=man&p={$curPage}", false);
		}
		
		$item = $d->rawQueryOne("SELECT * FROM #_pushonesignal WHERE id = ? LIMIT 0,1", [$id]);
		if (empty($item)) {
			$func->transfer("Dữ liệu không có thực", "index.php?com=pushOnesignal&act=man&p={$curPage}", false);
		}
		$template = "pushOnesignal/man/man_add";
		break;
		
	case "save":
		saveMan();
		break;
		
	case "sync":
		sendSync();
		break;
		
	case "delete":
		deleteMan();
		break;
		
	default:
		$template = "404";
}

function saveMan()
{
	global $d, $func, $flash, $curPage;
	
	if (empty($_POST)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=pushOnesignal&act=man&p={$curPage}", false);
	}
	
	$id = (int)SecurityHelper::sanitizePost('id', 0);
	$data = $_POST['data'] ?? null;
	
	if ($data) {
		foreach($data as $column => $value) {
			$data[$column] = SecurityHelper::sanitize($value);
		}
	}
	
	$response = [];
	
	// Validation
	if (empty($data['name'])) {
		$response['messages'][] = 'Tiêu đề không được trống';
	}
	
	if (empty($data['link'])) {
		$response['messages'][] = 'Liên kết không được trống';
	} elseif (!$func->isUrl($data['link'])) {
		$response['messages'][] = 'Liên kết không hợp lệ';
	}
	
	if (empty($data['description'])) {
		$response['messages'][] = 'Mô tả không được trống';
	}
	
	if (!empty($response)) {
		// Flash data
		if (!empty($data)) {
			foreach($data as $k => $v) {
				if (!empty($v)) {
					$flash->set($k, $v);
				}
			}
		}
		
		// Errors
		$response['status'] = 'danger';
		$message = base64_encode(json_encode($response));
		$flash->set('message', $message);
		
		if ($id) {
			$func->redirect("index.php?com=pushOnesignal&act=edit&p={$curPage}&id={$id}");
		} else {
			$func->redirect("index.php?com=pushOnesignal&act=add&p={$curPage}");
		}
	}
	
	$data['date_created'] = time();
	
	if ($id) {
		$d->where('id', $id);
		if ($d->update('pushonesignal', $data)) {
			// Photo upload
			if ($func->hasFile("file")) {
				$file_name = $func->uploadName($_FILES["file"]["name"]);
				if ($photo = $func->uploadImage("file", '.jpg|.gif|.png|.jpeg|.gif', UPLOAD_SYNC, $file_name)) {
					$row = $d->rawQueryOne("SELECT photo FROM #_pushonesignal WHERE id = ? LIMIT 0,1", [$id]);
					if ($row && $row['photo']) {
						$func->deleteFile(UPLOAD_SYNC . $row['photo']);
					}
					
					$d->where('id', $id);
					$d->update('pushonesignal', ['photo' => $photo]);
				}
			}
			
			$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=pushOnesignal&act=man&p={$curPage}");
		} else {
			$func->transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=pushOnesignal&act=man&p={$curPage}", false);
		}
	} else {
		if ($d->insert('pushonesignal', $data)) {
			$id_insert = $d->getLastInsertId();
			
			// Photo upload
			if ($func->hasFile("file")) {
				$file_name = $func->uploadName($_FILES['file']["name"]);
				if ($photo = $func->uploadImage("file", '.jpg|.gif|.png|.jpeg|.gif', UPLOAD_SYNC, $file_name)) {
					$d->where('id', $id_insert);
					$d->update('pushonesignal', ['photo' => $photo]);
				}
			}
			
			$func->transfer("Lưu dữ liệu thành công", "index.php?com=pushOnesignal&act=man&p={$curPage}");
		} else {
			$func->transfer("Lưu dữ liệu bị lỗi", "index.php?com=pushOnesignal&act=man&p={$curPage}", false);
		}
	}
}

function deleteMan()
{
	global $d, $func, $curPage;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	
	if ($id) {
		$row = $d->rawQueryOne("SELECT id, photo FROM #_pushonesignal WHERE id = ? LIMIT 0,1", [$id]);
		if ($row) {
			if ($row['photo']) {
				$func->deleteFile(UPLOAD_SYNC . $row['photo']);
			}
			$d->rawQuery("DELETE FROM #_pushonesignal WHERE id = ?", [$id]);
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=pushOnesignal&act=man&p={$curPage}");
		} else {
			$func->transfer("Xóa dữ liệu bị lỗi", "index.php?com=pushOnesignal&act=man&p={$curPage}", false);
		}
	} elseif (isset($_GET['listid'])) {
		$listid = explode(",", SecurityHelper::sanitizeGet('listid', ''));
		foreach($listid as $id) {
			$id = (int)$id;
			$row = $d->rawQueryOne("SELECT id, photo FROM #_pushonesignal WHERE id = ? LIMIT 0,1", [$id]);
			if ($row && $row['photo']) {
				$func->deleteFile(UPLOAD_SYNC . $row['photo']);
			}
			$d->rawQuery("DELETE FROM #_pushonesignal WHERE id = ?", [$id]);
		}
		$func->transfer("Xóa dữ liệu thành công", "index.php?com=pushOnesignal&act=man&p={$curPage}");
	} else {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=pushOnesignal&act=man&p={$curPage}", false);
	}
}

function sendMessageOnesignal($heading, $content, $url = 'https://www.google.com/', $photo = '')
{
	global $d, $configBase, $config;
	
	$contents = ["en" => $content];
	$headings = ["en" => $heading];
	$uphoto = (!empty($photo)) ? $configBase . UPLOAD_SYNC_L . $photo : '';
	
	$fields = [
		'app_id' => $config['oneSignal']['id'],
		'included_segments' => ['All'],
		'contents' => $contents,
		'headings' => $headings,
		'url' => $url,
		'chrome_web_image' => $uphoto
	];
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json; charset=utf-8',
		'Authorization: Basic ' . $config['oneSignal']['restId']
	]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	
	$response = curl_exec($ch);
	curl_close($ch);
	
	return $response;
}

function sendSync()
{
	global $d, $func, $curPage;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	
	if ($id) {
		$row = $d->rawQueryOne("SELECT id, photo, name, description, link FROM #_pushonesignal WHERE id = ? LIMIT 0,1", [$id]);
		if ($row) {
			sendMessageOnesignal($row['name'], $row['description'], $row['link'], $row['photo']);
			$func->transfer("Gửi thông báo thành công", "index.php?com=pushOnesignal&act=man&p={$curPage}");
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=pushOnesignal&act=man&p={$curPage}", false);
		}
	} else {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=pushOnesignal&act=man&p={$curPage}", false);
	}
}

