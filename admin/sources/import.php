<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\SecurityHelper;
use Tuezy\UploadHandler;

if (isset($config['product'])) {
	$arrCheck = array();
	foreach($config['product'] as $k => $v) {
		if (isset($v['import']) && $v['import'] == true) $arrCheck[] = $k;
	}
	if (!count($arrCheck) || !in_array($type, $arrCheck)) {
		$func->transfer("Trang không tồn tại", "index.php", false);
	}
} else {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

switch($act) {
	case "man":
		getImages();
		$template = "import/man/mans";
		break;
	case "uploadImages":
		uploadImages();
		break;
	case "editImages":
		editImages();
		$template = "import/man/man_edit";
		break;
	case "saveImages":
		saveImages();
		break;
	case "deleteImages":
		deleteImages();
		break;
	case "uploadExcel":
		uploadExcel();
		break;
	default:
		$template = "404";
}

function getImages()
{
	global $d, $func, $type, $curPage, $items, $paging;
	
	$perPage = 10;
	$start = ($curPage - 1) * $perPage;
	$sql = "SELECT * FROM #_excel WHERE type = ? ORDER BY numb,id DESC LIMIT {$start},{$perPage}";
	$items = $d->rawQuery($sql, [$type]);
	
	$countSql = "SELECT COUNT(*) as total FROM #_excel WHERE type = ?";
	$total = $d->rawQueryOne($countSql, [$type]);
	$totalItems = (int)($total['total'] ?? 0);
	
	$url = "index.php?com=import&act=man&type=" . $type;
	$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
}

function editImages()
{
	global $d, $func, $item, $type, $curPage;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	if (!$id) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=import&act=man&type=" . $type . "&p=" . $curPage, false);
	}
	
	$item = $d->rawQueryOne("SELECT * FROM #_excel WHERE id = ? AND type = ? LIMIT 0,1", [$id, $type]);
	if (empty($item)) {
		$func->transfer("Dữ liệu không có thực", "index.php?com=import&act=man&type=" . $type . "&p=" . $curPage, false);
	}
}

function saveImages()
{
	global $d, $item, $func, $type, $curPage, $config;
	
	if (empty($_POST)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=import&act=man&type=" . $type . "&p=" . $curPage, false);
	}
	
	$id = (int)SecurityHelper::sanitizePost('id', 0);
	
	if ($id) {
		if ($func->hasFile("file")) {
			$file_name = $func->uploadName($_FILES['file']["name"]);
			if ($photo = $func->uploadImage("file", $config['import']['img_type'], UPLOAD_EXCEL, $file_name)) {
				$row = $d->rawQueryOne("SELECT photo FROM #_excel WHERE id = ? AND type = ? LIMIT 0,1", [$id, $type]);
				if ($row && $row['photo']) {
					$func->deleteFile(UPLOAD_EXCEL . $row['photo']);
				}
				
				$d->where('id', $id);
				$d->where('type', $type);
				$d->update('excel', ['photo' => $photo]);
				$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=import&act=man&type=" . $type . "&p=" . $curPage);
			}
		}
	}
	
	$func->transfer("Lưu dữ liệu thất bại", "index.php?com=import&act=man&type=" . $type . "&p=" . $curPage, false);
}

function deleteImages()
{
	global $d, $type, $func, $curPage;
	
	$id = (int)SecurityHelper::sanitizeGet('id', 0);
	
	if ($id) {
		$row = $d->rawQueryOne("SELECT id, photo FROM #_excel WHERE id = ? AND type = ? LIMIT 0,1", [$id, $type]);
		if ($row) {
			if ($row['photo']) {
				$func->deleteFile(UPLOAD_EXCEL . $row['photo']);
			}
			$d->rawQuery("DELETE FROM #_excel WHERE id = ? AND type = ?", [$id, $type]);
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=import&act=man&type=" . $type . "&p=" . $curPage);
		}
	} elseif (isset($_GET['listid'])) {
		$listid = explode(",", SecurityHelper::sanitizeGet('listid', ''));
		foreach($listid as $id) {
			$id = (int)$id;
			$row = $d->rawQueryOne("SELECT id, photo FROM #_excel WHERE id = ? AND type = ? LIMIT 0,1", [$id, $type]);
			if ($row && $row['photo']) {
				$func->deleteFile(UPLOAD_EXCEL . $row['photo']);
			}
			$d->rawQuery("DELETE FROM #_excel WHERE id = ? AND type = ?", [$id, $type]);
		}
		$func->transfer("Xóa dữ liệu thành công", "index.php?com=import&act=man&type=" . $type . "&p=" . $curPage);
	}
	
	$func->transfer("Không nhận được dữ liệu", "index.php?com=import&act=man&type=" . $type . "&p=" . $curPage, false);
}

function uploadImages()
{
	global $d, $func, $type, $config;
	
	if (isset($_POST['uploadImg']) && isset($_FILES['files'])) {
		$str_remove = '';
		$arr_file_delete = [];
		
		if (isset($_POST['jfiler-items-exclude-files-0'])) {
			$str_remove = str_replace(['"', '[', ']', '\\', '0://'], '', $_POST['jfiler-items-exclude-files-0']);
			$arr_file_delete = explode(',', $str_remove);
		}
		
		$flagCount = 0;
		$myFile = $_FILES['files'];
		$fileCount = count($myFile["name"]);
		
		for($i = 0; $i < $fileCount; $i++) {
			if (!in_array($myFile["name"][$i], $arr_file_delete, true)) {
				$data = [];
				$data['numb'] = (isset($_POST['numb-filer'][$flagCount]) && $_POST['numb-filer'][$flagCount] > 0) ? (int)$_POST['numb-filer'][$flagCount] : 0;
				$data['type'] = $type;
				
				if ($d->insert('excel', $data)) {
					$id_insert = $d->getLastInsertId();
					
					$_FILES['file'] = [
						'name' => $myFile['name'][$i],
						'type' => $myFile['type'][$i],
						'tmp_name' => $myFile['tmp_name'][$i],
						'error' => $myFile['error'][$i],
						'size' => $myFile['size'][$i]
					];
					
					if ($func->hasFile("file")) {
						$photoUpdate = [];
						$file_name = $func->uploadName($myFile["name"][$i]);
						
						if ($photo = $func->uploadImage("file", $config['import']['img_type'], UPLOAD_EXCEL, $file_name)) {
							$photoUpdate['photo'] = $photo;
							$d->where('id', $id_insert);
							$d->update('excel', $photoUpdate);
							unset($photoUpdate);
						}
					}
				} else {
					$func->transfer("Lưu hình ảnh bị lỗi", "index.php?com=import&act=man&type=" . $type, false);
				}
				
				$flagCount++;
			}
		}
		
		$func->transfer("Lưu hình ảnh thành công", "index.php?com=import&act=man&type=" . $type);
	} else {
		$func->transfer("Dữ liệu rỗng", "index.php?com=import&act=man&type=" . $type, false);
	}
}

function transferPhoto($photo)
{
	global $d;
	
	$oldpath = UPLOAD_EXCEL . $photo;
	$newpath = UPLOAD_PRODUCT . $photo;
	
	if (file_exists($oldpath)) {
		if (rename($oldpath, $newpath)) {
			$d->rawQuery("DELETE FROM #_excel WHERE photo = ?", [$photo]);
		}
	}
}

function uploadExcel()
{
	global $d, $type, $func, $config;
	
	if (isset($_POST['importExcel'])) {
		$file_type = $_FILES['file-excel']['type'] ?? '';
		
		if (in_array($file_type, ["application/vnd.ms-excel", "application/x-ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"])) {
			$mess = '';
			$filename = $func->changeTitle($_FILES["file-excel"]["name"]);
			move_uploaded_file($_FILES["file-excel"]["tmp_name"], $filename);
			
			require LIBRARIES . 'PHPExcel.php';
			require_once LIBRARIES . 'PHPExcel/IOFactory.php';
			
			$objPHPExcel = PHPExcel_IOFactory::load($filename);
			
			foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
				$highestRow = $worksheet->getHighestRow();
				
				for($row = 2; $row <= $highestRow; $row++) {
					$code = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
					
					if ($code != "") {
						$numb = (int)$worksheet->getCellByColumnAndRow(0, $row)->getValue();
						$level1 = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
						$level2 = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
						$namevi = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
						$regular_price = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
						$sale_price = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
						$discount = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
						$descvi = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
						$contentvi = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
						$photo = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
						
						$proimport = $d->rawQueryOne("SELECT id, photo FROM #_product WHERE code = ? LIMIT 0,1", [$code]);
						
						$slug_level1 = !empty($level1) ? $func->changeTitle($level1) : '';
						$idlist = $d->rawQueryOne("SELECT id FROM #_product_list WHERE slugvi = ? LIMIT 0,1", [$slug_level1]);
						
						$slug_level2 = !empty($level2) ? $func->changeTitle($level2) : '';
						$idcat = $d->rawQueryOne("SELECT id FROM #_product_cat WHERE slugvi = ? LIMIT 0,1", [$slug_level2]);
						
						$data = [
							'numb' => $numb,
							'id_list' => (int)($idlist['id'] ?? 0),
							'id_cat' => (int)($idcat['id'] ?? 0),
							'code' => SecurityHelper::sanitize($code),
							'namevi' => SecurityHelper::sanitize($namevi),
							'slugvi' => !empty($namevi) ? $func->changeTitle($namevi) : '',
							'regular_price' => $regular_price,
							'sale_price' => $sale_price,
							'discount' => $discount,
							'descvi' => SecurityHelper::sanitize($descvi),
							'contentvi' => SecurityHelper::sanitize($contentvi),
							'type' => $type,
							'status' => 'hienthi'
						];
						
						if (isset($config['import']['images']) && $config['import']['images'] == true && $photo != "") {
							if (filter_var($photo, FILTER_VALIDATE_URL)) {
								$random = $func->digitalRandom(0, 9, 12);
								$ext = pathinfo(parse_url($photo, PHP_URL_PATH), PATHINFO_EXTENSION);
								$name = $random . "_online_img." . $ext;
								$pathSaveImg = UPLOAD_EXCEL . $name;
								
								$ch = curl_init($photo);
								$fp = fopen($pathSaveImg, 'wb');
								curl_setopt($ch, CURLOPT_FILE, $fp);
								curl_setopt($ch, CURLOPT_HEADER, 0);
								curl_exec($ch);
								curl_close($ch);
								fclose($fp);
								
								$data['photo'] = $name;
								$photo = $name;
							} else {
								$data['photo'] = $photo;
							}
						} else {
							$data['photo'] = '';
						}
						
						if (isset($proimport['id']) && $proimport['id'] > 0) {
							$d->where('type', $type);
							$d->where('code', $code);
							if ($d->update('product', $data)) {
								if (isset($config['import']['images']) && $config['import']['images'] == true && $photo != "" && $photo != $proimport['photo']) {
									$oldpathphoto = UPLOAD_PRODUCT . $proimport['photo'];
									if (file_exists($oldpathphoto)) unlink($oldpathphoto);
									transferPhoto($photo);
								}
							} else {
								$mess .= 'Lỗi tại dòng: ' . $row . "<br>";
							}
						} else {
							if ($d->insert('product', $data)) {
								if (isset($config['import']['images']) && $config['import']['images'] == true && $photo != "") {
									transferPhoto($photo);
								}
							} else {
								$mess .= 'Lỗi tại dòng: ' . $row . "<br>";
							}
						}
					}
				}
			}
			
			unlink($filename);
			
			if ($mess == '') {
				$func->transfer("Import danh sách thành công", "index.php?com=import&act=man&type=" . $type);
			} else {
				$func->transfer($mess, "index.php?com=import&act=man&type=" . $type, false);
			}
		} else {
			$func->transfer("Không hỗ trợ kiểu tập tin này", "index.php?com=import&act=man&type=" . $type, false);
		}
	} else {
		$func->transfer("Dữ liệu rỗng", "index.php?com=import&act=man&type=" . $type, false);
	}
}

