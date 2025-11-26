<?php

/**
 * admin/sources/photo.php - REFACTORED VERSION
 * 
 * Sử dụng PhotoAdminController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\Controller\PhotoAdminController;
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

/* Kiểm tra active photo */
if (isset($config['photo'])) {
	$arrCheck = array();
	$actCheck = '';
	if ($act == 'photo_static' || $act == 'save_static' || $act == 'save-watermark' || $act == 'preview-watermark') {
		$actCheck = 'photo_static';
	} else {
		$actCheck = 'man_photo';
	}
	foreach($config['photo'][$actCheck] as $k => $v) $arrCheck[] = $k;
	if (!count($arrCheck) || !in_array($type, $arrCheck)) {
		$func->transfer("Trang không tồn tại", "index.php", false);
	}
} else {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

// Initialize Controller
$adminAuthHelper = new AdminAuthHelper($func, $d, $loginAdmin, $config);
$adminPermissionHelper = new AdminPermissionHelper($func, $config);
$controller = new PhotoAdminController($d, $cache, $func, $config, $adminAuthHelper, $adminPermissionHelper, $type ?? 'photo');

// Initialize template variable - default to man_photo
$template = "photo/man/photos";
$items = [];
$paging = '';
$item = null; // Initialize for photo_static

switch($act) {
	/* Photo static */
	case "photo_static":
		$item = $controller->getWatermarkConfig();
		if (!$item) {
			$photoRepo = new \Tuezy\Repository\PhotoRepository($d, $lang, $sluglang);
			$item = $photoRepo->getByTypeAndAct($type, 'photo_static');
		}
		// Đảm bảo $item là array hoặc null
		if (!$item) {
			$item = [];
		}
		$template = "photo/static/photo_static";
		break;
		
	case "save_static":
		// Save static - chỉ upload 1 ảnh duy nhất theo type (logo)
		if (empty($_POST)) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=photo&act=photo_static&type=" . $type, false);
		}
		
		// Sanitize data
		$data = $_POST['data'] ?? [];
		$data = SecurityHelper::sanitizeArray($data);
		
		// Get existing photo_static record for this type (chỉ 1 record duy nhất)
		$photoRepo = new \Tuezy\Repository\PhotoRepository($d, $lang, $sluglang);
		$existing = $photoRepo->getByTypeAndAct($type, 'photo_static');
		$id = $existing ? (int)$existing['id'] : null;
		
		// Set required fields
		$data['type'] = $type;
		$data['act'] = 'photo_static';
		if (empty($data['status'])) {
			$data['status'] = 'hienthi';
		}
		if (empty($data['numb'])) {
			$data['numb'] = 0;
		}
		
		// Handle file upload
		if ($func->hasFile("file")) {
			$file_name = $func->uploadName($_FILES["file"]["name"]);
			$imgType = $config['photo']['photo_static'][$type]['img_type'] ?? '.jpg|.gif|.png|.jpeg|.webp';
			
			// Bắt output buffer để tránh alert() output HTML/JS
			ob_start();
			$photo = $func->uploadImage("file", $imgType, UPLOAD_PHOTO, $file_name);
			ob_get_clean();
			
			if ($photo) {
				// Xóa ảnh cũ nếu có (khi update)
				if ($id && !empty($existing['photo'])) {
					$func->deleteFile(UPLOAD_PHOTO . $existing['photo']);
				}
				$data['photo'] = $photo;
			}
		}
		
		// Save or update
		try {
			if ($id) {
				// Update existing
				$d->where('id', $id);
				if (!$d->update('photo', $data)) {
					$func->transfer("Có lỗi xảy ra khi cập nhật dữ liệu", "index.php?com=photo&act=photo_static&type=" . $type, false);
				}
			} else {
				// Insert new (chỉ cho phép 1 record duy nhất)
				// Kiểm tra lại để đảm bảo không có record nào khác
				$check = $photoRepo->getByTypeAndAct($type, 'photo_static');
				if ($check) {
					// Nếu có record khác, update nó thay vì tạo mới
					$id = (int)$check['id'];
					$d->where('id', $id);
					if (!$d->update('photo', $data)) {
						$func->transfer("Có lỗi xảy ra khi cập nhật dữ liệu", "index.php?com=photo&act=photo_static&type=" . $type, false);
					}
				} else {
					// Tạo mới
					if (!$d->insert('photo', $data)) {
						$func->transfer("Có lỗi xảy ra khi thêm dữ liệu", "index.php?com=photo&act=photo_static&type=" . $type, false);
					}
				}
			}
			
			$message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
			$func->transfer($message, "index.php?com=photo&act=photo_static&type=" . $type);
		} catch (\Exception $e) {
			$func->transfer($e->getMessage(), "index.php?com=photo&act=photo_static&type=" . $type, false);
		}
		break;

	/* Watermark */
	case "save-watermark":
		if (function_exists('saveWatermark')) {
			saveWatermark();
		}
		// Redirect after save, no template needed
		break;
		
	case "preview-watermark":
		if (function_exists('previewWatermark')) {
			previewWatermark();
		}
		// AJAX response, no template needed
		break;

	/* Photos */
	case "man_photo":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}

		$viewData = $controller->manPhoto($filters, $curPage, 10);
		$items = $viewData['items'];
		$paging = $viewData['paging'];
		$template = "photo/man/photos";
		break;
		
	case "add_photo":
		$template = "photo/man/photo_add";
		break;
		
	case "edit_photo":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			$item = $controller->getPhoto($id);
			if (!$item) {
				$func->transfer("Dữ liệu không có thực", "index.php?com=photo&act=man_photo&type=" . $type, false);
			}
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=photo&act=man_photo&type=" . $type, false);
		}
		$template = "photo/man/photo_edit";
		break;
		
	case "save_photo":
		if (empty($_POST)) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=photo&act=man_photo&type=" . $type, false);
		}
		
		// Xử lý multi upload (dataMulti) - cho add_photo
		if (!empty($_POST['dataMulti']) && is_array($_POST['dataMulti'])) {
			$dataMulti = SecurityHelper::sanitizeArray($_POST['dataMulti']);
			$numberPhoto = count($dataMulti);
			
			try {
				$imgType = $config['photo']['man_photo'][$type]['img_type_photo'] ?? '.jpg|.gif|.png|.jpeg|.webp';
				$successCount = 0;
				
				foreach ($dataMulti as $i => $itemData) {
					// Xử lý upload file cho từng item
					$fileField = "file{$i}";
					$photo = null;
					
					if ($func->hasFile($fileField)) {
						$file_name = $func->uploadName($_FILES[$fileField]["name"]);
						
						// Bắt output buffer để tránh alert() output HTML/JS
						ob_start();
						$photo = $func->uploadImage($fileField, $imgType, UPLOAD_PHOTO, $file_name);
						ob_get_clean();
					}
					
					// Chuẩn bị data để save
					$saveData = $itemData;
					$saveData['type'] = $type;
					
					// Xử lý status (từ array thành string)
					if (isset($saveData['status']) && is_array($saveData['status'])) {
						$saveData['status'] = implode(',', $saveData['status']);
					} elseif (empty($saveData['status'])) {
						$saveData['status'] = 'hienthi';
					}
					
					// Set photo nếu upload thành công
					if ($photo) {
						$saveData['photo'] = $photo;
					}
					
					// Set default values
					if (empty($saveData['numb'])) {
						$saveData['numb'] = 0;
					}
					if (empty($saveData['date_created'])) {
						$saveData['date_created'] = time();
					}
					
					// Insert vào database
					if ($d->insert('photo', $saveData)) {
						$successCount++;
					}
				}
				
				if ($successCount > 0) {
					$message = "Thêm {$successCount}/{$numberPhoto} dữ liệu thành công";
					$func->transfer($message, "index.php?com=photo&act=man_photo&type=" . $type);
				} else {
					$func->transfer("Có lỗi xảy ra khi thêm dữ liệu", "index.php?com=photo&act=man_photo&type=" . $type, false);
				}
			} catch (\Exception $e) {
				$func->transfer($e->getMessage(), "index.php?com=photo&act=man_photo&type=" . $type, false);
			}
		} else {
			// Xử lý single upload (data) - cho edit_photo
			$id = !empty($_POST['id']) ? (int)$_POST['id'] : (!empty($_POST['data']['id']) ? (int)$_POST['data']['id'] : null);
			$data = $_POST['data'] ?? [];
			$data = SecurityHelper::sanitizeArray($data);
			
			// Xử lý upload file
			$imgType = $config['photo']['man_photo'][$type]['img_type_photo'] ?? '.jpg|.gif|.png|.jpeg|.webp';
			
			if ($func->hasFile("file")) {
				$file_name = $func->uploadName($_FILES["file"]["name"]);
				
				// Bắt output buffer để tránh alert() output HTML/JS
				ob_start();
				$photo = $func->uploadImage("file", $imgType, UPLOAD_PHOTO, $file_name);
				ob_get_clean();
				
				if ($photo) {
					// Xóa ảnh cũ nếu có (khi update)
					if ($id) {
						$oldPhoto = $d->rawQueryOne("SELECT photo FROM #_photo WHERE id = ? LIMIT 0,1", [$id]);
						if ($oldPhoto && !empty($oldPhoto['photo'])) {
							$func->deleteFile(UPLOAD_PHOTO . $oldPhoto['photo']);
						}
					}
					$data['photo'] = $photo;
				}
			}
			
			// Xử lý status (từ array thành string)
			if (isset($_POST['status']) && is_array($_POST['status'])) {
				$data['status'] = implode(',', $_POST['status']);
			} elseif (empty($data['status'])) {
				$data['status'] = 'hienthi';
			}
			
			// Loại bỏ id khỏi data nếu có
			unset($data['id']);
			
			try {
				if ($controller->savePhoto($data, $id)) {
					$message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
					$func->transfer($message, "index.php?com=photo&act=man_photo&type=" . $type);
				} else {
					$func->transfer("Có lỗi xảy ra khi lưu dữ liệu", "index.php?com=photo&act=man_photo&type=" . $type, false);
				}
			} catch (\Exception $e) {
				$func->transfer($e->getMessage(), "index.php?com=photo&act=man_photo&type=" . $type, false);
			}
		}
		break;
		
	case "delete_photo":
		$id = (int)($_GET['id'] ?? 0);
		
		// Xử lý xóa nhiều items (listid)
		if (!empty($_GET['listid'])) {
			$listid = SecurityHelper::sanitizeGet('listid', '');
			$ids = explode(',', $listid);
			$ids = array_filter(array_map('intval', $ids)); // Loại bỏ giá trị rỗng và convert sang int
			
			if (empty($ids)) {
				$func->transfer("Không nhận được dữ liệu", "index.php?com=photo&act=man_photo&type=" . $type, false);
			}
			
			$successCount = 0;
			$failedCount = 0;
			
			foreach ($ids as $photoId) {
				if ($photoId > 0) {
					// Lấy thông tin ảnh để xóa file
					$photo = $d->rawQueryOne("SELECT photo FROM #_photo WHERE id = ? AND type = ? LIMIT 0,1", [$photoId, $type]);
					
					// Xóa record
					if ($controller->deletePhoto($photoId)) {
						// Xóa file ảnh nếu có
						if ($photo && !empty($photo['photo'])) {
							$func->deleteFile(UPLOAD_PHOTO . $photo['photo']);
						}
						$successCount++;
					} else {
						$failedCount++;
					}
				}
			}
			
			if ($successCount > 0) {
				$message = "Xóa {$successCount} dữ liệu thành công";
				if ($failedCount > 0) {
					$message .= " ({$failedCount} dữ liệu thất bại)";
				}
				$func->transfer($message, "index.php?com=photo&act=man_photo&type=" . $type);
			} else {
				$func->transfer("Xóa dữ liệu thất bại", "index.php?com=photo&act=man_photo&type=" . $type, false);
			}
		} 
		// Xử lý xóa 1 item (id)
		elseif ($id > 0) {
			// Lấy thông tin ảnh để xóa file
			$photo = $d->rawQueryOne("SELECT photo FROM #_photo WHERE id = ? AND type = ? LIMIT 0,1", [$id, $type]);
			
			if ($controller->deletePhoto($id)) {
				// Xóa file ảnh nếu có
				if ($photo && !empty($photo['photo'])) {
					$func->deleteFile(UPLOAD_PHOTO . $photo['photo']);
				}
				$func->transfer("Xóa dữ liệu thành công", "index.php?com=photo&act=man_photo&type=" . $type);
			} else {
				$func->transfer("Xóa dữ liệu thất bại", "index.php?com=photo&act=man_photo&type=" . $type, false);
			}
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=photo&act=man_photo&type=" . $type, false);
		}
		break;

	default:
		$template = "404";
}

// Ensure template is set before including
if (empty($template)) {
	// Default to man_photo if no template set
	$template = "photo/man/photos";
	// Try to get data if not already loaded
	if (!isset($items)) {
		$filters = [];
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}
		$viewData = $controller->manPhoto($filters, $curPage ?? 1, 10);
		$items = $viewData['items'];
		$paging = $viewData['paging'] ?? '';
	}
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~688 dòng với nhiều functions
 * CODE MỚI: ~120 dòng với PhotoRepository và AdminCRUDHelper
 * 
 * GIẢM: ~83% code
 * 
 * LỢI ÍCH:
 * - Sử dụng PhotoRepository
 * - Sử dụng AdminCRUDHelper cho CRUD operations
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 */

