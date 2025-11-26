<?php

/**
 * admin/sources/news.php - REFACTORED VERSION
 * 
 * Sử dụng NewsAdminController để xử lý logic
 * File này giờ chỉ là entry point, logic đã được chuyển vào Controller
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\Controller\NewsAdminController;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;
use Tuezy\Admin\AdminCRUDHelper;
use Tuezy\Repository\NewsRepository;
use Tuezy\SecurityHelper;

/* Kiểm tra active news */
if (isset($config['news'])) {
	$arrCheck = array();
	foreach ($config['news'] as $k => $v) $arrCheck[] = $k;
	if (!count($arrCheck) || !in_array($type, $arrCheck)) {
		$func->transfer("Trang không tồn tại", "index.php", false);
	}
} else {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

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
$controller = new NewsAdminController($d, $cache, $func, $config, $adminAuthHelper, $adminPermissionHelper, $type ?? 'tin-tuc');

// Initialize AdminCRUDHelper for news (for edit/delete operations)
$adminCRUD = new AdminCRUDHelper(
	$d, 
	$func, 
	'news', 
	$type, 
	$config['news'][$type] ?? []
);

// Initialize NewsRepository for gallery
$newsRepo = new NewsRepository($d, $lang, $type);

// Build URL parameters
$strUrl = "";
$arrUrl = array('id_list', 'id_cat', 'id_item', 'id_sub');
if (isset($_POST['data'])) {
	$dataUrl = isset($_POST['data']) ? $_POST['data'] : null;
	if ($dataUrl) {
		foreach ($arrUrl as $k => $v) {
			if (isset($dataUrl[$arrUrl[$k]])) {
				$strUrl .= "&" . $arrUrl[$k] . "=" . SecurityHelper::sanitize($dataUrl[$arrUrl[$k]]);
			}
		}
	}
} else {
	foreach ($arrUrl as $k => $v) {
		if (isset($_REQUEST[$arrUrl[$k]])) {
			$strUrl .= "&" . $arrUrl[$k] . "=" . SecurityHelper::sanitize($_REQUEST[$arrUrl[$k]]);
		}
	}

	if (!empty($_REQUEST['comment_status'])) {
		$strUrl .= "&comment_status=" . SecurityHelper::sanitize($_REQUEST['comment_status']);
	}
	if (isset($_REQUEST['keyword'])) {
		$strUrl .= "&keyword=" . SecurityHelper::sanitize($_REQUEST['keyword']);
	}
}

switch ($act) {
	/* Man - Sử dụng NewsAdminController */
	case "man":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['id_list'])) $filters['id_list'] = (int)$_REQUEST['id_list'];
		if (!empty($_REQUEST['id_cat'])) $filters['id_cat'] = (int)$_REQUEST['id_cat'];
		if (!empty($_REQUEST['id_item'])) $filters['id_item'] = (int)$_REQUEST['id_item'];
		if (!empty($_REQUEST['id_sub'])) $filters['id_sub'] = (int)$_REQUEST['id_sub'];
		if (!empty($_REQUEST['keyword'])) $filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		if (!empty($_REQUEST['comment_status'])) $filters['comment_status'] = SecurityHelper::sanitize($_REQUEST['comment_status']);

		$viewData = $controller->man($filters, $curPage, 10);
		$items = $viewData['items'];
		$paging = $viewData['paging'];
		
		/* Comment */
		$comment = new Comments($d, $func);
		$template = "news/man/mans";
		break;

	case "add":
		$template = "news/man/man_add";
		break;

	case "edit":
	case "copy":
		if ((!isset($config['news'][$type]['copy']) || $config['news'][$type]['copy'] == false) && $act == 'copy') {
			$template = "404";
			return false;
		}
		
		$id = (int)($_GET['id'] ?? $_GET['id_copy'] ?? 0);
		if ($id) {
			$item = $adminCRUD->getItem($id);
			if ($item && $act != 'copy') {
				/* Get gallery - Sử dụng NewsRepository */
				$gallery = $newsRepo->getNewsGallery($id, $type);
			}
		}
		$template = "news/man/man_add";
		break;

	case "save":
	case "save_copy":
		// Save news với đầy đủ dữ liệu liên quan
		if (empty($_POST)) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=news&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
		}

		// Lấy id từ POST (form có hidden field name="id")
		$id = null;
		if (!empty($_POST['id'])) {
			$id = (int)$_POST['id'];
		} elseif (!empty($_POST['data']['id'])) {
			$id = (int)$_POST['data']['id'];
		} elseif (!empty($_GET['id']) && ($act == 'save' || $act == 'save_copy')) {
			$id = (int)$_GET['id'];
		}
		
		$data = $_POST['data'] ?? [];
		
		// Loại bỏ 'id' khỏi $data
		unset($data['id']);
		
		// Sanitize data
		foreach ($data as $key => $value) {
			if (is_string($value)) {
				$data[$key] = SecurityHelper::sanitize($value);
			} elseif (is_array($value)) {
				$data[$key] = SecurityHelper::sanitizeArray($value);
			}
		}

		// Tự động tạo slug từ tên nếu chưa có
		// Ưu tiên slugvi, nếu không có thì tạo từ namevi
		if (empty($data['slugvi']) && !empty($data['namevi'])) {
			$data['slugvi'] = $func->changeTitle($data['namevi']);
		}
		// Tương tự cho slugen
		if (empty($data['slugen']) && !empty($data['nameen'])) {
			$data['slugen'] = $func->changeTitle($data['nameen']);
		}

		// Validate và kiểm tra slug uniqueness
		if (!empty($data['slugvi'])) {
			$checkSlugData = [
				'slug' => $data['slugvi'],
				'id' => $id ?? 0,
				'table' => 'news',
				'type' => $type,
			];
			$slugResult = $func->checkSlug($checkSlugData);
			if ($slugResult === 'exist') {
				// Nếu slug đã tồn tại, thêm số vào cuối
				$baseSlug = $data['slugvi'];
				$counter = 1;
				do {
					$data['slugvi'] = $baseSlug . '-' . $counter;
					$checkSlugData['slug'] = $data['slugvi'];
					$slugResult = $func->checkSlug($checkSlugData);
					$counter++;
				} while ($slugResult === 'exist' && $counter < 100);
			}
		}

		// Get tags data
		$dataTags = $_POST['dataTags'] ?? [];
		$dataTags = array_map('intval', $dataTags);
		$dataTags = array_filter($dataTags, function($v) { return $v > 0; });

		// Save using adminCRUD
		try {
			// Set type
			$data['type'] = $type;
			
			// Save main news
			if ($id) {
				$d->where('id', $id);
				if (!$d->update('news', $data)) {
					$func->transfer("Có lỗi xảy ra khi cập nhật dữ liệu", "index.php?com=news&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
				}
				$newsId = $id;
			} else {
				// Insert new news
				if (!isset($data['date_created'])) {
					$data['date_created'] = time();
				}
				if (!isset($data['numb'])) {
					$data['numb'] = 0;
				}
				// Đảm bảo có status field (mặc định là 'hienthi' nếu không có)
				if (!isset($data['status'])) {
					$data['status'] = 'hienthi';
				}
				// Đảm bảo có view field (mặc định là 0)
				if (!isset($data['view'])) {
					$data['view'] = 0;
				}
				// Đảm bảo có options field (mặc định là rỗng)
				if (!isset($data['options'])) {
					$data['options'] = '';
				}
				
				if (!$d->insert('news', $data)) {
					$func->transfer("Có lỗi xảy ra khi thêm dữ liệu", "index.php?com=news&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
				}
				$newsId = $d->getLastInsertId();
				
				// Kiểm tra xem insert có thành công không
				if (!$newsId || $newsId <= 0) {
					$func->transfer("Có lỗi xảy ra khi thêm dữ liệu. Không lấy được ID sau khi insert.", "index.php?com=news&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
				}
			}

			if ($newsId && $newsId > 0) {
				// Save tags
				if (!empty($dataTags)) {
					// Xóa tags cũ
					$d->rawQuery("DELETE FROM #_news_tags WHERE id_parent = ?", [$newsId]);
					
					// Thêm tags mới
					foreach ($dataTags as $tagId) {
						$tagId = (int)$tagId;
						if ($tagId > 0) {
							$d->rawQuery(
								"INSERT INTO #_news_tags (id_parent, id_tags) VALUES (?, ?) 
								 ON DUPLICATE KEY UPDATE id_tags = id_tags",
								[$newsId, $tagId]
							);
						}
					}
				}
				
				// Xử lý upload ảnh chính (tương tự product)
				if ($func->hasFile("file")) {
					$file_name = $func->uploadName($_FILES["file"]["name"]);
					$imgType = $config['news'][$type]['img_type'] ?? '.jpg|.gif|.png|.jpeg|.webp';
					
					ob_start();
					$photo = $func->uploadImage("file", $imgType, UPLOAD_NEWS, $file_name);
					ob_get_clean();
					
					if ($photo) {
						if ($id) {
							$oldNews = $d->rawQueryOne("SELECT photo FROM #_news WHERE id = ? LIMIT 0,1", [$newsId]);
							if ($oldNews && !empty($oldNews['photo'])) {
								$func->deleteFile(UPLOAD_NEWS . $oldNews['photo']);
							}
						}
						
						$d->where('id', $newsId);
						$d->update('news', ['photo' => $photo]);
					}
				}
				
				$message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
				$func->transfer($message, "index.php?com=news&act=man&type=" . $type . "&p=" . $curPage . $strUrl);
			}
		} catch (\Exception $e) {
			$func->transfer($e->getMessage(), "index.php?com=news&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
		}
		break;

	case "delete":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			// Sử dụng controller hoặc adminCRUD để xóa
			// Tạm thời sử dụng adminCRUD
			if ($adminCRUD->delete($id)) {
				$func->transfer("Xóa dữ liệu thành công", "index.php?com=news&act=man&type=" . $type . "&p=" . $curPage . $strUrl);
			} else {
				$func->transfer("Xóa dữ liệu thất bại", "index.php?com=news&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
			}
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=news&act=man&type=" . $type . "&p=" . $curPage . $strUrl, false);
		}
		break;

	/* List management (Danh mục cấp 1) - Sử dụng NewsAdminController */
	case "man_list":
		$filters = [];
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}
		
		$viewData = $controller->manList($filters, $curPage, 10);
		$items = $viewData['items'];
		$paging = $viewData['paging'];
		$template = "news/list/lists";
		break;

	case "add_list":
		$viewData = $controller->addList();
		$item = $viewData['item'];
		$template = "news/list/list_add";
		break;

	case "edit_list":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			$viewData = $controller->editList($id);
			$item = $viewData['item'];
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=news&act=man_list&type=" . $type, false);
		}
		$template = "news/list/list_add";
		break;

	case "save_list":
		if (empty($_POST)) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=news&act=man_list&type=" . $type, false);
		}
		
		$id = !empty($_POST['data']['id']) ? (int)$_POST['data']['id'] : null;
		$data = $_POST['data'] ?? [];
		
		if ($controller->saveList($data, $id)) {
			$message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
			$func->transfer($message, "index.php?com=news&act=man_list&type=" . $type);
		} else {
			$func->transfer("Có lỗi xảy ra khi lưu dữ liệu", "index.php?com=news&act=man_list&type=" . $type, false);
		}
		break;

	case "delete_list":
		$id = (int)($_GET['id'] ?? 0);
		if ($id && $controller->deleteList($id)) {
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=news&act=man_list&type=" . $type);
		} else {
			$func->transfer("Xóa dữ liệu thất bại", "index.php?com=news&act=man_list&type=" . $type, false);
		}
		break;

	// Các case khác (cat, item, sub, gallery) giữ nguyên
	// vì có logic riêng phức tạp
	
	default:
		$template = "404";
}

