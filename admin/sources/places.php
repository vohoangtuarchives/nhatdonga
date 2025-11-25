<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\SecurityHelper;

if (!isset($config['places']['active']) || $config['places']['active'] == false) {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

$strUrl = "";
$arrUrl = ['id_region', 'id_city', 'id_district', 'id_ward'];
if (!empty($_POST['data'])) {
	$dataUrl = $_POST['data'];
	foreach($arrUrl as $v) {
		if (!empty($dataUrl[$v])) {
			$strUrl .= "&{$v}=" . SecurityHelper::sanitize($dataUrl[$v]);
		}
	}
} else {
	foreach($arrUrl as $v) {
		if (!empty($_REQUEST[$v])) {
			$strUrl .= "&{$v}=" . SecurityHelper::sanitize($_REQUEST[$v]);
		}
	}
	if (!empty($_REQUEST['keyword'])) {
		$strUrl .= "&keyword=" . SecurityHelper::sanitize($_REQUEST['keyword']);
	}
}

switch($act) {
	case "man_city":
		viewCitys();
		$template = "places/city/mans";
		break;
	case "add_city":
		$template = "places/city/man_add";
		break;
	case "edit_city":
		editCity();
		$template = "places/city/man_add";
		break;
	case "save_city":
		saveCity();
		break;
	case "delete_city":
		deleteCity();
		break;
	case "man_district":
		viewDistricts();
		$template = "places/district/mans";
		break;
	case "add_district":
		$template = "places/district/man_add";
		break;
	case "edit_district":
		editDistrict();
		$template = "places/district/man_add";
		break;
	case "save_district":
		saveDistrict();
		break;
	case "delete_district":
		deleteDistrict();
		break;
	case "man_ward":
		viewWards();
		$template = "places/ward/mans";
		break;
	case "add_ward":
		$template = "places/ward/man_add";
		break;
	case "edit_ward":
		editWard();
		$template = "places/ward/man_add";
		break;
	case "save_ward":
		saveWard();
		break;
	case "delete_ward":
		deleteWard();
		break;
	default:
		$template = "404";
}

function viewCitys()
{
	global $d, $func, $strUrl, $curPage, $items, $paging;
	
	$where = "";
	$params = [];
	
	if (isset($_REQUEST['keyword'])) {
		$keyword = SecurityHelper::sanitize($_REQUEST['keyword']);
		$where .= " AND namevi LIKE ?";
		$params[] = "%{$keyword}%";
	}
	
	$perPage = 10;
	$start = ($curPage - 1) * $perPage;
	$sql = "SELECT * FROM #_city WHERE id<>0 {$where} ORDER BY numb,id DESC LIMIT {$start},{$perPage}";
	$items = $d->rawQuery($sql, $params);
	
	$countSql = "SELECT COUNT(*) as total FROM #_city WHERE id<>0 {$where}";
	$total = $d->rawQueryOne($countSql, $params);
	$totalItems = (int)($total['total'] ?? 0);
	
	$url = "index.php?com=places&act=man_city" . $strUrl;
	$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
}

function editCity()
{
	global $d, $func, $strUrl, $curPage, $item;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	if (empty($id)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}", false);
	}
	
	$item = $d->rawQueryOne("SELECT * FROM #_city WHERE id = ? LIMIT 0,1", [$id]);
	if (empty($item)) {
		$func->transfer("Dữ liệu không có thực", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}", false);
	}
}

function saveCity()
{
	global $d, $func, $flash, $strUrl, $curPage;
	
	if (empty($_POST)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}", false);
	}
	
	$id = (int)SecurityHelper::sanitizePost('id', 0);
	$data = $_POST['data'] ?? null;
	$message = '';
	$response = [];
	
	if ($data) {
		foreach($data as $column => $value) {
			$data[$column] = SecurityHelper::sanitize($value);
		}
		
		if (isset($_POST['status'])) {
			$status = '';
			foreach($_POST['status'] as $attr_value) {
				if ($attr_value != "") $status .= $attr_value . ',';
			}
			$data['status'] = !empty($status) ? rtrim($status, ",") : "";
		} else {
			$data['status'] = "";
		}
		
		$data['ship_price'] = (isset($data['ship_price']) && $data['ship_price'] != '') ? str_replace(",", "", $data['ship_price']) : 0;
		$data['slug'] = (!empty($data['name'])) ? $func->changeTitle($data['name']) : '';
	}
	
	// Validation
	if (empty($data['name'])) {
		$response['messages'][] = 'Tiêu đề không được trống';
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
			$func->redirect("index.php?com=places&act=edit_city&p={$curPage}{$strUrl}&id={$id}");
		} else {
			$func->redirect("index.php?com=places&act=add_city&p={$curPage}{$strUrl}");
		}
		return;
	}
	
	// Save data
	if ($id) {
		$data['date_updated'] = time();
		$d->where('id', $id);
		if ($d->update('city', $data)) {
			$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}");
		} else {
			$func->transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}", false);
		}
	} else {
		$data['date_created'] = time();
		if ($d->insert('city', $data)) {
			$func->transfer("Lưu dữ liệu thành công", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}");
		} else {
			$func->transfer("Lưu dữ liệu bị lỗi", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}", false);
		}
	}
}

function deleteCity()
{
	global $d, $func, $strUrl, $curPage;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	
	if ($id) {
		$row = $d->rawQueryOne("SELECT id FROM #_city WHERE id = ? LIMIT 0,1", [$id]);
		if (!empty($row)) {
			$d->rawQuery("DELETE FROM #_city WHERE id = ?", [$id]);
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}");
		} else {
			$func->transfer("Xóa dữ liệu bị lỗi", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}", false);
		}
	} elseif (isset($_GET['listid'])) {
		$listid = explode(",", $_GET['listid']);
		foreach ($listid as $idItem) {
			$idItem = (int)SecurityHelper::sanitize($idItem);
			$row = $d->rawQueryOne("SELECT id FROM #_city WHERE id = ? LIMIT 0,1", [$idItem]);
			if (!empty($row)) {
				$d->rawQuery("DELETE FROM #_city WHERE id = ?", [$idItem]);
			}
		}
		$func->transfer("Xóa dữ liệu thành công", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}");
	} else {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=places&act=man_city&p={$curPage}{$strUrl}", false);
	}
}

function viewDistricts()
{
	global $d, $func, $strUrl, $curPage, $items, $paging;
	
	$where = "";
	$params = [];
	
	$id_city = (int)($_REQUEST['id_city'] ?? 0);
	if ($id_city) {
		$where .= " AND id_city = ?";
		$params[] = $id_city;
	}
	
	if (isset($_REQUEST['keyword'])) {
		$keyword = SecurityHelper::sanitize($_REQUEST['keyword']);
		$where .= " AND namevi LIKE ?";
		$params[] = "%{$keyword}%";
	}
	
	$perPage = 10;
	$start = ($curPage - 1) * $perPage;
	$sql = "SELECT * FROM #_district WHERE id<>0 {$where} ORDER BY numb,id DESC LIMIT {$start},{$perPage}";
	$items = $d->rawQuery($sql, $params);
	
	$countSql = "SELECT COUNT(*) as total FROM #_district WHERE id<>0 {$where}";
	$total = $d->rawQueryOne($countSql, $params);
	$totalItems = (int)($total['total'] ?? 0);
	
	$url = "index.php?com=places&act=man_district" . $strUrl;
	$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
}

function editDistrict()
{
	global $d, $func, $strUrl, $curPage, $item;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	if (empty($id)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}", false);
	}
	
	$item = $d->rawQueryOne("SELECT * FROM #_district WHERE id = ? LIMIT 0,1", [$id]);
	if (empty($item)) {
		$func->transfer("Dữ liệu không có thực", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}", false);
	}
}

function saveDistrict()
{
	global $d, $func, $flash, $strUrl, $curPage;
	
	if (empty($_POST)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}", false);
	}
	
	$id = (int)SecurityHelper::sanitizePost('id', 0);
	$data = $_POST['data'] ?? null;
	$message = '';
	$response = [];
	
	if ($data) {
		foreach($data as $column => $value) {
			$data[$column] = SecurityHelper::sanitize($value);
		}
		
		if (isset($_POST['status'])) {
			$status = '';
			foreach($_POST['status'] as $attr_value) {
				if ($attr_value != "") $status .= $attr_value . ',';
			}
			$data['status'] = !empty($status) ? rtrim($status, ",") : "";
		} else {
			$data['status'] = "";
		}
		
		$data['ship_price'] = (isset($data['ship_price']) && $data['ship_price'] != '') ? str_replace(",", "", $data['ship_price']) : 0;
		$data['slug'] = (!empty($data['name'])) ? $func->changeTitle($data['name']) : '';
	}
	
	// Validation
	if (empty($data['id_city'])) {
		$response['messages'][] = 'Chưa chọn tỉnh/thành phố';
	}
	
	if (empty($data['name'])) {
		$response['messages'][] = 'Tiêu đề không được trống';
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
			$func->redirect("index.php?com=places&act=edit_district&p={$curPage}{$strUrl}&id={$id}");
		} else {
			$func->redirect("index.php?com=places&act=add_district&p={$curPage}{$strUrl}");
		}
		return;
	}
	
	// Save data
	if ($id) {
		$data['date_updated'] = time();
		$d->where('id', $id);
		if ($d->update('district', $data)) {
			$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}");
		} else {
			$func->transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}", false);
		}
	} else {
		$data['date_created'] = time();
		if ($d->insert('district', $data)) {
			$func->transfer("Lưu dữ liệu thành công", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}");
		} else {
			$func->transfer("Lưu dữ liệu bị lỗi", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}", false);
		}
	}
}

function deleteDistrict()
{
	global $d, $func, $strUrl, $curPage;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	
	if ($id) {
		$row = $d->rawQueryOne("SELECT id FROM #_district WHERE id = ? LIMIT 0,1", [$id]);
		if (!empty($row)) {
			$d->rawQuery("DELETE FROM #_district WHERE id = ?", [$id]);
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}");
		} else {
			$func->transfer("Xóa dữ liệu bị lỗi", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}", false);
		}
	} elseif (isset($_GET['listid'])) {
		$listid = explode(",", $_GET['listid']);
		foreach ($listid as $idItem) {
			$idItem = (int)SecurityHelper::sanitize($idItem);
			$row = $d->rawQueryOne("SELECT id FROM #_district WHERE id = ? LIMIT 0,1", [$idItem]);
			if (!empty($row)) {
				$d->rawQuery("DELETE FROM #_district WHERE id = ?", [$idItem]);
			}
		}
		$func->transfer("Xóa dữ liệu thành công", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}");
	} else {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=places&act=man_district&p={$curPage}{$strUrl}", false);
	}
}

function viewWards()
{
	global $d, $func, $strUrl, $curPage, $items, $paging;
	
	$where = "";
	$params = [];
	
	$id_district = (int)($_REQUEST['id_district'] ?? 0);
	if ($id_district) {
		$where .= " AND id_district = ?";
		$params[] = $id_district;
	}
	
	if (isset($_REQUEST['keyword'])) {
		$keyword = SecurityHelper::sanitize($_REQUEST['keyword']);
		$where .= " AND namevi LIKE ?";
		$params[] = "%{$keyword}%";
	}
	
	$perPage = 10;
	$start = ($curPage - 1) * $perPage;
	$sql = "SELECT * FROM #_ward WHERE id<>0 {$where} ORDER BY numb,id DESC LIMIT {$start},{$perPage}";
	$items = $d->rawQuery($sql, $params);
	
	$countSql = "SELECT COUNT(*) as total FROM #_ward WHERE id<>0 {$where}";
	$total = $d->rawQueryOne($countSql, $params);
	$totalItems = (int)($total['total'] ?? 0);
	
	$url = "index.php?com=places&act=man_ward" . $strUrl;
	$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
}

function editWard()
{
	global $d, $func, $strUrl, $curPage, $item;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	if (empty($id)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}", false);
	}
	
	$item = $d->rawQueryOne("SELECT * FROM #_ward WHERE id = ? LIMIT 0,1", [$id]);
	if (empty($item)) {
		$func->transfer("Dữ liệu không có thực", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}", false);
	}
}

function saveWard()
{
	global $d, $func, $flash, $strUrl, $curPage, $config;
	
	if (empty($_POST)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}", false);
	}
	
	$id = (int)SecurityHelper::sanitizePost('id', 0);
	$data = $_POST['data'] ?? null;
	$message = '';
	$response = [];
	
	if ($data) {
		foreach($data as $column => $value) {
			$data[$column] = SecurityHelper::sanitize($value);
		}
		
		if (isset($_POST['status'])) {
			$status = '';
			foreach($_POST['status'] as $attr_value) {
				if ($attr_value != "") $status .= $attr_value . ',';
			}
			$data['status'] = !empty($status) ? rtrim($status, ",") : "";
		} else {
			$data['status'] = "";
		}
		
		$data['slug'] = (!empty($data['name'])) ? $func->changeTitle($data['name']) : '';
		$data['ship_price'] = (!empty($data['ship_price'])) ? str_replace(",", "", $data['ship_price']) : 0;
	}
	
	// Validation
	if (empty($data['id_city'])) {
		$response['messages'][] = 'Chưa chọn tỉnh/thành phố';
	}
	
	if (empty($data['id_district'])) {
		$response['messages'][] = 'Chưa chọn quận/huyện';
	}
	
	if (empty($data['name'])) {
		$response['messages'][] = 'Tiêu đề không được trống';
	}
	
	if (!empty($data['ship_price']) && !$func->isNumber($data['ship_price'])) {
		$response['messages'][] = 'Phí vận chuyển không hợp lệ';
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
			$func->redirect("index.php?com=places&act=edit_ward&p={$curPage}{$strUrl}&id={$id}");
		} else {
			$func->redirect("index.php?com=places&act=add_ward&p={$curPage}{$strUrl}");
		}
		return;
	}
	
	// Save data
	if ($id) {
		$data['date_updated'] = time();
		$d->where('id', $id);
		if ($d->update('ward', $data)) {
			$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}");
		} else {
			$func->transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}", false);
		}
	} else {
		$data['date_created'] = time();
		if ($d->insert('ward', $data)) {
			$func->transfer("Lưu dữ liệu thành công", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}");
		} else {
			$func->transfer("Lưu dữ liệu bị lỗi", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}", false);
		}
	}
}

function deleteWard()
{
	global $d, $func, $strUrl, $curPage;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	
	if ($id) {
		$row = $d->rawQueryOne("SELECT id FROM #_ward WHERE id = ? LIMIT 0,1", [$id]);
		if (!empty($row)) {
			$d->rawQuery("DELETE FROM #_ward WHERE id = ?", [$id]);
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}");
		} else {
			$func->transfer("Xóa dữ liệu bị lỗi", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}", false);
		}
	} elseif (isset($_GET['listid'])) {
		$listid = explode(",", $_GET['listid']);
		foreach ($listid as $idItem) {
			$idItem = (int)SecurityHelper::sanitize($idItem);
			$row = $d->rawQueryOne("SELECT id FROM #_ward WHERE id = ? LIMIT 0,1", [$idItem]);
			if (!empty($row)) {
				$d->rawQuery("DELETE FROM #_ward WHERE id = ?", [$idItem]);
			}
		}
		$func->transfer("Xóa dữ liệu thành công", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}");
	} else {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=places&act=man_ward&p={$curPage}{$strUrl}", false);
	}
}

