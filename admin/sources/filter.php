<?php
if (!defined('SOURCES')) die("Error");

/* Kiểm tra active filter */
if (isset($config['filter'])) {
	$arrCheck = array();
	foreach ($config['filter'] as $k => $v) $arrCheck[] = $k;
	if (!count($arrCheck) || !in_array($type, $arrCheck)) $func->transfer("Trang không tồn tại", "index.php", false);
} else {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

switch ($act) {
	case "man":
		getMans();
		$template = "filter/man/mans";
		break;

	case "add":
		$template = "filter/man/man_add";
		break;

	case "edit":
		getMan();
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

/* Get filter */
function getMans()
{
	global $d, $func, $curPage, $items, $paging, $type;

	$where = "";

	if (isset($_REQUEST['keyword'])) {
		$keyword = htmlspecialchars($_REQUEST['keyword']);
		$where .= " and (tenvi LIKE '%$keyword%' or tenen LIKE '%$keyword%')";
	}

	$per_page = 10;
	$startpoint = ($curPage * $per_page) - $per_page;
	$limit = " limit " . $startpoint . "," . $per_page;
	$sql = "select * from #_filter where type = ? $where order by numb,id desc $limit";
	$items = $d->rawQuery($sql, array($type));
	$sqlNum = "select count(*) as 'num' from #_filter where type = ? $where order by numb,id desc";
	$count = $d->rawQueryOne($sqlNum, array($type));
	$total = $count['num'];
	$url = "index.php?com=filter&act=man&type=" . $type;
	$paging = $func->pagination($total, $per_page, $curPage, $url);
}

/* Edit filter */
function getMan()
{
	global $d, $func, $curPage, $item, $type;

	$id = (isset($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

	if (!$id) $func->transfer("Không nhận được dữ liệu", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);

	$item = $d->rawQueryOne("select * from #_filter where id = ? and type = ? limit 0,1", array($id, $type));

	if (!$item['id']) $func->transfer("Dữ liệu không có thực", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
}

/* Save filter */
function saveMan()
{
	global $d, $curPage, $func, $config, $com, $type;

	if (empty($_POST)) $func->transfer("Không nhận được dữ liệu", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);

	/* Post dữ liệu */
	$data = (isset($_POST['data'])) ? $_POST['data'] : null;

	if (isset($config['filter'][$type]['value']) && $config['filter'][$type]['value'] == true){
		$min = (!empty($_POST['min-value'])) ?  $_POST['min-value']:"";
		$max = (!empty($_POST['max-value'])) ?  $_POST['max-value']:"";
		if($min <= $max){
			$func->transfer("Giá trị lớn nhất phải lớn hơn giá trị nhỏ nhất", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
		}
		if (isset($config['filter'][$type]['price']) && $config['filter'][$type]['price'] == true) {
			$min = str_replace(',', '', $_POST['min-value']);
			$max = str_replace(',', '', $_POST['max-value']);
		} 
		$tempValue = "";
		if($min!="" && $max!="" ){
			$tempValue = $min . ',' . $max;
		}
		if($tempValue){
			$data['value'] = $min . ',' . $max;
		}
	}

	if ($data) {
		foreach ($data as $column => $value) {
			$data[$column] = htmlspecialchars($value);
		}
		if (isset($_POST['status'])) {
			$status = '';
			foreach ($_POST['status'] as $attr_column => $attr_value) if ($attr_value != "") $status .= $attr_value . ',';
			$data['status'] = (!empty($status)) ? rtrim($status, ",") : "";
		} else {
			$data['status'] = "";
		}
		$data['type'] = $type;
	}
	$id = (isset($_POST['id'])) ? htmlspecialchars($_POST['id']) : 0;

	if ($id) {
		$data['date_updated'] = time();

		$d->where('id', $id);
		$d->where('type', $type);
		if ($d->update('filter', $data)) {
			/* Photo */
			if ($func->hasFile("file")) {
				$photoUpdate = array();
				$file_name = $func->uploadName($_FILES["file"]["name"]);

				if ($photo = $func->uploadImage("file", $config['filter'][$type]['img_type'], UPLOAD_FILTER, $file_name)) {
					$row = $d->rawQueryOne("select id, photo from #_filter where id = ? and type = ? limit 0,1", array($id, $type));

					if (!empty($row)) {
						$func->deleteFile(UPLOAD_FILTER . $row['photo']);
					}

					$photoUpdate['photo'] = $photo;
					$d->where('id', $id);
					$d->update('filter', $photoUpdate);
					unset($photoUpdate);
				}
			}
			$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage);
		} else $func->transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
	} else {
		$data['date_updated'] = $data['date_created'] = time();

		if ($d->insert('filter', $data)) {
			$id_insert = $d->getLastInsertId();

			/* Photo */
			if ($func->hasFile("file")) {
				$photoUpdate = array();
				$file_name = $func->uploadName($_FILES['file']["name"]);

				if ($photo = $func->uploadImage("file", $config['filter'][$type]['img_type'], UPLOAD_FILTER, $file_name)) {
					$photoUpdate['photo'] = $photo;
					$d->where('id', $id_insert);
					$d->update('filter', $photoUpdate);
					unset($photoUpdate);
				}
			}

			$func->transfer("Lưu dữ liệu thành công", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage);
		} else $func->transfer("Lưu dữ liệu bị lỗi", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
	}
}

/* Delete filter */
function deleteMan()
{
	global $d, $curPage, $func, $com, $type;

	$id = (isset($_GET['id'])) ? htmlspecialchars($_GET['id']) : 0;

	if ($id) {
		/* Lấy dữ liệu */
		$row = $d->rawQueryOne("select id,photo from #_filter where id = ? and type = ? limit 0,1", array($id, $type));
		if ($row['id']) {
			$func->deleteFile(UPLOAD_FILTER . $row['photo']);
			$d->rawQuery("delete from #_filter where id = ?", array($id));

			$func->transfer("Xóa dữ liệu thành công", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage);
		} else $func->transfer("Xóa dữ liệu bị lỗi", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
	} elseif (isset($_GET['listid'])) {
		$listid = explode(",", $_GET['listid']);

		for ($i = 0; $i < count($listid); $i++) {
			$id = htmlspecialchars($listid[$i]);

			/* Lấy dữ liệu */
			$row = $d->rawQueryOne("select id,photo from #_filter where id = ? and type = ? limit 0,1", array($id, $type));

			if ($row['id']) {
				$func->deleteFile(UPLOAD_FILTER . $row['photo']);
				$d->rawQuery("delete from #_filter where id = ?", array($id));
			}
		}

		$func->transfer("Xóa dữ liệu thành công", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage);
	} else $func->transfer("Không nhận được dữ liệu", "index.php?com=filter&act=man&type=" . $type . "&p=" . $curPage, false);
}
