<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\SecurityHelper;

if (!isset($config['website']['debug-developer']) || $config['website']['debug-developer'] == false) {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

$strUrl = "";
if (isset($_REQUEST['keyword'])) {
	$strUrl = "&keyword=" . SecurityHelper::sanitize($_REQUEST['keyword']);
}

switch($act) {
	case "create":
		createMan();
		break;
	case "man":
		viewMans();
		$template = "lang/man/mans";
		break;
	case "add":
		$template = "lang/man/man_add";
		break;
	case "edit":
		editMan();
		$template = "lang/man/man_add";
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

function createMan()
{
	global $d, $config, $func, $curPage;
	
	$flag = 0;
	foreach($config['website']['lang'] as $k => $v) {
		$lang = $d->rawQuery("SELECT lang_define, lang{$k} FROM #_lang");
		
		if (file_exists(LIBRARIES . "lang/{$k}.php")) {
			$langfile = fopen(LIBRARIES . "lang/{$k}.php", "w");
			if (!$langfile) {
				$func->transfer("Không thể tạo tập tin.", "index.php?com=lang&act=man&p={$curPage}", false);
			}
			
			$flag++;
			$str = '<?php' . PHP_EOL;
			foreach($lang as $item) {
				$str .= 'define("' . $item['lang_define'] . '","' . $item['lang' . $k] . '");' . PHP_EOL;
			}
			$str .= '?>';
			
			fwrite($langfile, $str);
			fclose($langfile);
		}
	}
	
	if (!$flag) {
		$func->transfer("Tạo tập tin ngôn ngữ thất bại", "index.php?com=lang&act=man&p={$curPage}", false);
	} else {
		$func->transfer("Tạo tập tin ngôn ngữ thành công", "index.php?com=lang&act=man&p={$curPage}");
	}
}

function viewMans()
{
	global $d, $func, $curPage, $items, $paging, $strUrl;
	
	$where = "";
	$params = [];
	
	if (isset($_REQUEST['keyword'])) {
		$keyword = SecurityHelper::sanitize($_REQUEST['keyword']);
		$where = "WHERE lang_define LIKE ?";
		$params[] = "%{$keyword}%";
	}
	
	$perPage = 10;
	$start = ($curPage - 1) * $perPage;
	$sql = "SELECT * FROM #_lang {$where} ORDER BY id DESC LIMIT {$start},{$perPage}";
	$items = $d->rawQuery($sql, $params);
	
	$countSql = "SELECT COUNT(*) as total FROM #_lang {$where}";
	$total = $d->rawQueryOne($countSql, $params);
	$totalItems = (int)($total['total'] ?? 0);
	
	$url = "index.php?com=lang&act=man" . $strUrl;
	$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
}

function editMan()
{
	global $d, $curPage, $func, $item;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	if (!$id) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=lang&act=man&p={$curPage}", false);
	}
	
	$item = $d->rawQueryOne("SELECT * FROM #_lang WHERE id = ? LIMIT 0,1", [$id]);
	if (empty($item)) {
		$func->transfer("Dữ liệu không có thực", "index.php?com=lang&act=man&p={$curPage}", false);
	}
}

function saveMan()
{
	global $d, $func, $curPage, $flash, $config;
	
	if (empty($_POST)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=lang&act=man&p={$curPage}", false);
	}
	
	$id = (int)SecurityHelper::sanitizePost('id', 0);
	$data = $_POST['data'] ?? null;
	$message = '';
	$response = [];
	
	if ($data) {
		foreach($data as $column => $value) {
			$data[$column] = SecurityHelper::sanitize($value);
		}
	}
	
	// Validation
	if (empty($data['lang_define'])) {
		$response['messages'][] = 'Tên biến không được trống';
	}
	
	foreach($config['website']['lang'] as $k => $v) {
		if (isset($data['lang' . $k])) {
			$lang = trim($data['lang' . $k]);
			if (empty($lang)) {
				$response['messages'][] = 'Phần dịch nghĩa (' . $v . ') không được trống';
			}
		}
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
			$func->redirect("index.php?com=lang&act=edit&p={$curPage}&id={$id}");
		} else {
			$func->redirect("index.php?com=lang&act=add&p={$curPage}");
		}
		return;
	}
	
	// Save data
	if ($id) {
		$d->where('id', $id);
		if ($d->update('lang', $data)) {
			$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=lang&act=man&p={$curPage}");
		} else {
			$func->transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=lang&act=man&p={$curPage}", false);
		}
	} else {
		if ($d->insert('lang', $data)) {
			$func->transfer("Lưu dữ liệu thành công", "index.php?com=lang&act=man&p={$curPage}");
		} else {
			$func->transfer("Lưu dữ liệu bị lỗi", "index.php?com=lang&act=man&p={$curPage}", false);
		}
	}
}

function deleteMan()
{
	global $d, $func, $curPage;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	
	if ($id) {
		$row = $d->rawQueryOne("SELECT id FROM #_lang WHERE id = ? LIMIT 0,1", [$id]);
		if (!empty($row)) {
			$d->rawQuery("DELETE FROM #_lang WHERE id = ?", [$id]);
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=lang&act=man&p={$curPage}");
		} else {
			$func->transfer("Xóa dữ liệu bị lỗi", "index.php?com=lang&act=man&p={$curPage}", false);
		}
	} elseif (isset($_GET['listid'])) {
		$listid = explode(",", $_GET['listid']);
		foreach ($listid as $idItem) {
			$idItem = (int)SecurityHelper::sanitize($idItem);
			$row = $d->rawQueryOne("SELECT id FROM #_lang WHERE id = ? LIMIT 0,1", [$idItem]);
			if (!empty($row)) {
				$d->rawQuery("DELETE FROM #_lang WHERE id = ?", [$idItem]);
			}
		}
		$func->transfer("Xóa dữ liệu thành công", "index.php?com=lang&act=man&p={$curPage}");
	} else {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=lang&act=man&p={$curPage}", false);
	}
}

