<?php

/**
 * admin/sources/setting.php - REFACTORED VERSION (Partial)
 * 
 * File này là phiên bản refactored của admin/sources/setting.php
 * Sử dụng SettingRepository và SecurityHelper
 * 
 * CÁCH SỬ DỤNG:
 * Có thể copy từng phần vào admin/sources/setting.php hoặc thay thế hoàn toàn
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\SettingRepository;
use Tuezy\Config;
use Tuezy\SecurityHelper;
use Tuezy\ValidationHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories
$settingRepo = new SettingRepository($d, $cache);

switch($act) {
	case "update":
		// Get setting - Sử dụng SettingRepository
		$item = $settingRepo->getFirst();
		$template = "setting/man/man_add";
		break;
		
	case "save":
		// Save setting - Sử dụng SettingRepository
		saveSetting();
		break;
		
	default:
		$template = "404";
}

/* Save Setting - Refactored version */
function saveSetting()
{
	global $d, $func, $flash, $config, $com, $settingRepo;
	
	/* Check post */
	if (empty($_POST)) {
		$func->transfer("Không nhận được dữ liệu", "index.php?com=setting&act=update", false);
	}
	
	/* Post dữ liệu */
	$id = (int)SecurityHelper::sanitizePost('id', 0);
	$row = $settingRepo->get($id);
	$option = !empty($row['options']) ? json_decode($row['options'], true) : [];
	$data = (!empty($_POST['data'])) ? $_POST['data'] : null;
	
	if ($data) {
		foreach($data as $column => $value) {
			if (is_array($value)) {
				foreach($value as $k2 => $v2) {
					if ($k2 == 'coords_iframe') {
						$option[$k2] = SecurityHelper::sanitize($v2);
					} else {
						$option[$k2] = $v2;
					}
				}
				$data[$column] = json_encode($option);
			} else {
				if ($column == 'mastertool') {
					$data[$column] = SecurityHelper::sanitize($value);
				} else if (in_array($column, array('headjs', 'bodyjs', 'analytics'))) {
					$data[$column] = SecurityHelper::sanitize($value);
				} else {
					$data[$column] = SecurityHelper::sanitize($value);
				}
			}
		}
	}
	
	/* Post Seo - Sử dụng SecurityHelper */
	$dataSeo = (isset($_POST['dataSeo'])) ? $_POST['dataSeo'] : null;
	if ($dataSeo) {
		foreach($dataSeo as $column => $value) {
			$dataSeo[$column] = SecurityHelper::sanitize($value);
		}
	}

	/* Valid data - Sử dụng ValidationHelper */
	$response = [];
	if (empty($option['address'])) {
		$response['messages'][] = 'Địa chỉ không được trống';
	}
	if (empty($option['email'])) {
		$response['messages'][] = 'Email không được trống';
	}
	if (!empty($option['email']) && !ValidationHelper::isEmail($option['email'])) {
		$response['messages'][] = 'Email không hợp lệ';
	}

	if (!empty($response['messages'])) {
		$flash->set('error', implode('<br>', $response['messages']));
		$func->transfer("Vui lòng kiểm tra lại dữ liệu", "index.php?com=setting&act=update", false);
	}

	/* Save data - Sử dụng SettingRepository */
	if ($id) {
		// Update
		if ($settingRepo->update($id, $data)) {
			// Update SEO
			if ($dataSeo) {
				$seo = $d->rawQueryOne("select * from #_seo where id_parent = ? and com = ? and act = ? limit 0,1", array($id, 'setting', 'update'));
				if (!empty($seo)) {
					$d->where('id', $seo['id']);
					$d->update('seo', $dataSeo);
				} else {
					$dataSeo['id_parent'] = $id;
					$dataSeo['com'] = 'setting';
					$dataSeo['act'] = 'update';
					$d->insert('seo', $dataSeo);
				}
			}
			
			$func->transfer("Cập nhật dữ liệu thành công", "index.php?com=setting&act=update");
		} else {
			$func->transfer("Cập nhật dữ liệu bị lỗi", "index.php?com=setting&act=update", false);
		}
	} else {
		// Create
		if ($settingRepo->create($data)) {
			$id = $d->getLastInsertId();
			
			// Update SEO
			if ($dataSeo) {
				$dataSeo['id_parent'] = $id;
				$dataSeo['com'] = 'setting';
				$dataSeo['act'] = 'update';
				$d->insert('seo', $dataSeo);
			}
			
			$func->transfer("Tạo dữ liệu thành công", "index.php?com=setting&act=update");
		} else {
			$func->transfer("Tạo dữ liệu bị lỗi", "index.php?com=setting&act=update", false);
		}
	}
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~246 dòng với nhiều rawQuery
 * CODE MỚI: ~150 dòng với SettingRepository
 * 
 * GIẢM: ~39% code
 * 
 * LỢI ÍCH:
 * - Sử dụng SettingRepository
 * - Sử dụng SecurityHelper cho sanitization
 * - Sử dụng ValidationHelper cho validation
 * - Code dễ đọc và maintain hơn
 */

