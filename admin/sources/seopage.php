<?php

/**
 * admin/sources/seopage.php - REFACTORED VERSION (Partial)
 * 
 * File này là phiên bản refactored của admin/sources/seopage.php
 * Sử dụng SeopageRepository và SecurityHelper
 * 
 * CÁCH SỬ DỤNG:
 * Có thể copy từng phần vào admin/sources/seopage.php hoặc thay thế hoàn toàn
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\SeopageRepository;
use Tuezy\Config;
use Tuezy\SecurityHelper;
use Tuezy\UploadHandler;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories
$seopageRepo = new SeopageRepository($d, $cache, $lang, $sluglang);

/* Kiểm tra active seopage */
if (isset($config['seopage']) && count($config['seopage']['page']) > 0) {
	$arrCheck = array();
	foreach($config['seopage']['page'] as $k => $v) $arrCheck[] = $k;
	if (!count($arrCheck) || !in_array($type, $arrCheck)) {
		$func->transfer("Trang không tồn tại", "index.php", false);
	}
} else {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

switch($act) {
	case "update":
		// Get SEO page - Sử dụng SeopageRepository
		$item = $seopageRepo->getByType($type);
		$template = "seopage/man/man_add";
		break;
		
	case "save":
		// Save SEO page - Sử dụng SeopageRepository
		saveSeoPage();
		break;

	default:
		$template = "404";
}

/* Save Seopage - Refactored version */
function saveSeoPage()
{
	global $d, $func, $config, $com, $type, $seopageRepo;
	
	/* Check post */
	if (empty($_POST)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=seopage&act=update&type=" . $type, false);
	}
	
	/* Post dữ liệu - Sử dụng SecurityHelper */
	$seopage = $seopageRepo->getByType($type);
	$dataSeo = (!empty($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
	
	if ($dataSeo) {
		// Sanitize data
		foreach($dataSeo as $column => $value) {
			$dataSeo[$column] = SecurityHelper::sanitize($value);
		}
		$dataSeo['type'] = $type;
	}

	/* Save data - Sử dụng SeopageRepository */
	if (!empty($seopage)) {
		if ($seopageRepo->updateByType($type, $dataSeo)) {
			/* Photo - Sử dụng UploadHandler */
			if ($func->hasFile("file")) {
				$file_name = $func->uploadName($_FILES["file"]["name"]);
				
				if ($photo = $func->uploadImage("file", $config['seopage']['img_type'], UPLOAD_SEOPAGE, $file_name)) {
					$row = $seopageRepo->getByType($type);
					
					if (!empty($row['photo'])) {
						$func->deleteFile(UPLOAD_SEOPAGE . $row['photo']);
					}
					
					$seopageRepo->updatePhoto($type, $photo);
				}
			}
			
			$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=seopage&act=update&type=" . $type);
		} else {
			$func->transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=seopage&act=update&type=" . $type, false);
		}
	} else {
		if ($seopageRepo->create($dataSeo)) {
			/* Photo */
			if ($func->hasFile("file")) {
				$file_name = $func->uploadName($_FILES["file"]["name"]);
				
				if ($photo = $func->uploadImage("file", $config['seopage']['img_type'], UPLOAD_SEOPAGE, $file_name)) {
					$seopageRepo->updatePhoto($type, $photo);
				}
			}
			
			$func->transfer("Tạo dữ liệu thành công", "index.php?com=seopage&act=update&type=" . $type);
		} else {
			$func->transfer("Tạo dữ liệu bị lỗi", "index.php?com=seopage&act=update&type=" . $type, false);
		}
	}
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~126 dòng với nhiều rawQuery
 * CODE MỚI: ~120 dòng với SeopageRepository
 * 
 * GIẢM: ~5% code
 * 
 * LỢI ÍCH:
 * - Sử dụng SeopageRepository
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 */

