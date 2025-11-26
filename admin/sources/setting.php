<?php

/**
 * admin/sources/setting.php - REFACTORED VERSION
 * 
 * Sử dụng SettingAdminController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\Controller\SettingAdminController;
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
$controller = new SettingAdminController($d, $cache, $func, $config, $adminAuthHelper, $adminPermissionHelper);

switch($act) {
	case "update":
		$item = $controller->getFirst();
		$template = "setting/man/man_add";
		break;
		
	case "save":
		if (empty($_POST)) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=setting&act=update", false);
		}
		
		$id = (int)SecurityHelper::sanitizePost('id', 0);
		$data = $_POST['data'] ?? [];
		$dataSeo = $_POST['dataSeo'] ?? null;
		
		// Sanitize SEO data nếu có
		if ($dataSeo && is_array($dataSeo)) {
			$dataSeo = SecurityHelper::sanitizeArray($dataSeo);
		}
		
		$result = $controller->save($data, $dataSeo, $id);
		
		if ($result['success']) {
			$func->transfer($result['messages'][0], "index.php?com=setting&act=update");
		} else {
			$func->transfer(implode('<br>', $result['messages']), "index.php?com=setting&act=update", false);
		}
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

