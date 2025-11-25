<?php

/**
 * admin/sources/product.php - REFACTORED VERSION (Partial)
 * 
 * File này là phiên bản refactored của admin/sources/product.php
 * Sử dụng AdminCRUDHelper và các helpers
 * 
 * CÁCH SỬ DỤNG:
 * Có thể copy từng phần vào admin/sources/product.php hoặc thay thế hoàn toàn
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\AdminCRUDHelper;
use Tuezy\Admin\AdminURLHelper;
use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\TagsRepository;
use Tuezy\Service\ProductService;
use Tuezy\SecurityHelper;

// Initialize language variables (default to Vietnamese for admin)
if (!isset($lang)) {
	$lang = $_SESSION['lang'] ?? 'vi';
}
if (!isset($sluglang)) {
	$sluglang = 'slugvi';
}

// Initialize repositories & service
$productRepo = new ProductRepository($d, $cache, $lang, $sluglang, $type);
$categoryRepo = new CategoryRepository($d, $cache, $lang, $sluglang, 'product');
$tagsRepo = new TagsRepository($d, $cache, $lang, $sluglang);
$productService = new ProductService($productRepo, $categoryRepo, $tagsRepo, $d, $lang);

/* Kiểm tra active product */
if (isset($config['product'])) {
	$arrCheck = array();
	foreach ($config['product'] as $k => $v) $arrCheck[] = $k;
	if (!count($arrCheck) || !in_array($type, $arrCheck)) {
		$func->transfer("Trang không tồn tại", "index.php", false);
	}
} else {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

// Initialize AdminURLHelper for URL building
$urlHelper = new AdminURLHelper('index.php');

/* Cấu hình đường dẫn trả về - Sử dụng AdminURLHelper */
if (isset($_POST['data'])) {
	$strUrl = $urlHelper->buildFromPost($_POST['data'] ?? []);
} else {
	$strUrl = $urlHelper->buildFromRequest(
		['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'],
		['comment_status', 'keyword']
	);
}

// Initialize AdminCRUDHelper for products
$adminCRUD = new AdminCRUDHelper(
	$d, 
	$func, 
	'product', 
	$type, 
	$config['product'][$type] ?? []
);

// Initialize AdminCRUDHelper for product_list
$listCRUD = new AdminCRUDHelper(
	$d,
	$func,
	'product_list',
	$type,
	$config['product'][$type] ?? []
);

switch ($act) {
	/* Man - Sử dụng AdminCRUDHelper */
	case "man":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['id_list'])) $filters['id_list'] = (int)$_REQUEST['id_list'];
		if (!empty($_REQUEST['id_cat'])) $filters['id_cat'] = (int)$_REQUEST['id_cat'];
		if (!empty($_REQUEST['id_item'])) $filters['id_item'] = (int)$_REQUEST['id_item'];
		if (!empty($_REQUEST['id_sub'])) $filters['id_sub'] = (int)$_REQUEST['id_sub'];
		if (!empty($_REQUEST['id_brand'])) $filters['id_brand'] = (int)$_REQUEST['id_brand'];
		if (!empty($_REQUEST['keyword'])) $filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		if (!empty($_REQUEST['comment_status'])) $filters['status'] = SecurityHelper::sanitize($_REQUEST['comment_status']);

		$listing = $productService->getListing($type, $filters, $curPage, 10);
		$items = $listing['items'];
		$totalItems = $listing['total'];
		
		// Build URL using AdminURLHelper
		$urlHelper->reset();
		$urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
		$url = $urlHelper->getUrl('product', 'man', $type);
		$paging = $func->pagination($totalItems, 10, $curPage, $url);
		
		/* Comment */
		$comment = new Comments($d, $func);
		$template = "product/man/mans";
		break;

	case "add":
		$template = "product/man/man_add";
		break;

	case "edit":
	case "copy":
		if ((!isset($config['product'][$type]['copy']) || $config['product'][$type]['copy'] == false) && $act == 'copy') {
			$template = "404";
			return false;
		}
		
		$id = (int)($_GET['id'] ?? $_GET['id_copy'] ?? 0);
		if ($id) {
			$detailContext = $productService->getDetailContext($id, $type, false);
			if ($detailContext) {
				$item = $detailContext['detail'];
				$gallery = $detailContext['photos'];
			} else {
				$item = $adminCRUD->getItem($id);
				$gallery = [];
			}
		}
		$template = "product/man/man_add";
		break;

	case "save":
	case "save_copy":
		// Save product với đầy đủ dữ liệu liên quan - Sử dụng ProductService
		if (empty($_POST)) {
			// Build return URL using AdminURLHelper
			$urlHelper->reset();
			$urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
			$urlHelper->addParam('p', $curPage);
			$returnUrl = $urlHelper->getUrl('product', 'man', $type);
			$func->transfer("Không nhận được dữ liệu", $returnUrl, false);
		}

		$id = !empty($_POST['data']['id']) ? (int)$_POST['data']['id'] : null;
		$data = $_POST['data'] ?? [];
		
		// Sanitize data
		foreach ($data as $key => $value) {
			if (is_string($value)) {
				$data[$key] = SecurityHelper::sanitize($value);
			} elseif (is_array($value)) {
				$data[$key] = SecurityHelper::sanitizeArray($value);
			}
		}

		// Get related data
		$dataSC = $_POST['dataSC'] ?? [];
		$dataTags = $_POST['dataTags'] ?? [];
		
		// Sanitize dataSC
		foreach ($dataSC as $key => $item) {
			if (is_array($item)) {
				$dataSC[$key] = array_map(function($v) {
					return is_string($v) ? SecurityHelper::sanitize($v) : $v;
				}, $item);
			}
		}

		// Sanitize dataTags
		$dataTags = array_map('intval', $dataTags);
		$dataTags = array_filter($dataTags, function($v) { return $v > 0; });

		// Save product using ProductService
		try {
			$productId = $productService->saveProduct($data, $id, $dataSC, $dataTags, $type, $func);

			if ($productId) {
				$message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
				// Build return URL using AdminURLHelper
				$urlHelper->reset();
				$urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
				$urlHelper->addParam('p', $curPage);
				$returnUrl = $urlHelper->getUrl('product', 'man', $type);
				$func->transfer($message, $returnUrl);
			} else {
				$urlHelper->reset();
				$urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
				$urlHelper->addParam('p', $curPage);
				$returnUrl = $urlHelper->getUrl('product', 'man', $type);
				$func->transfer("Có lỗi xảy ra khi lưu dữ liệu", $returnUrl, false);
			}
		} catch (\Exception $e) {
			// Handle slug validation error
			$urlHelper->reset();
			$urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
			$urlHelper->addParam('p', $curPage);
			$returnUrl = $urlHelper->getUrl('product', 'man', $type);
			$func->transfer($e->getMessage(), $returnUrl, false);
		}
		break;

	case "delete":
		$id = (int)($_GET['id'] ?? 0);
		// Build return URL using AdminURLHelper
		$urlHelper->reset();
		$urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
		$urlHelper->addParam('p', $curPage);
		$returnUrl = $urlHelper->getUrl('product', 'man', $type);
		
		if ($id && $adminCRUD->delete($id)) {
			$func->transfer("Xóa dữ liệu thành công", $returnUrl);
		} else {
			$func->transfer("Xóa dữ liệu thất bại", $returnUrl, false);
		}
		break;

	/* List management (Danh mục cấp 1) - Sử dụng AdminCRUDHelper */
	case "man_list":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}
		
		// Build WHERE conditions for AdminCRUDHelper
		$where = [];
		if (!empty($filters['keyword'])) {
			$where[] = [
				'clause' => '(tenvi LIKE ? OR tenen LIKE ?)',
				'params' => ["%{$filters['keyword']}%", "%{$filters['keyword']}%"]
			];
		}
		
		// Get items using AdminCRUDHelper
		$perPage = 10;
		$result = $listCRUD->getList($curPage, $perPage, $where);
		$items = $result['items'];
		$totalItems = $result['total'];
		
		// Build URL for pagination
		$urlHelper->reset();
		if (!empty($filters['keyword'])) {
			$urlHelper->addParam('keyword', $filters['keyword']);
		}
		$url = $urlHelper->getUrl('product', 'man_list', $type);
		$paging = $func->pagination($totalItems, $perPage, $curPage, $url);
		$template = "product/list/lists";
		break;

	case "add_list":
		$template = "product/list/list_add";
		break;

	case "edit_list":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			$item = $listCRUD->getItem($id);
			if (!$item) {
				$func->transfer("Dữ liệu không có thực", "index.php?com=product&act=man_list&type=" . $type, false);
			}
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=product&act=man_list&type=" . $type, false);
		}
		$template = "product/list/list_add";
		break;

	case "save_list":
		if (empty($_POST)) {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=product&act=man_list&type=" . $type, false);
		}
		
		$id = !empty($_POST['data']['id']) ? (int)$_POST['data']['id'] : null;
		$data = $_POST['data'] ?? [];
		
		// Save using AdminCRUDHelper
		try {
			if ($listCRUD->save($data, $id)) {
				$message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
				$func->transfer($message, "index.php?com=product&act=man_list&type=" . $type);
			} else {
				$func->transfer("Có lỗi xảy ra khi lưu dữ liệu", "index.php?com=product&act=man_list&type=" . $type, false);
			}
		} catch (\Exception $e) {
			// Handle slug validation error
			$func->transfer($e->getMessage(), "index.php?com=product&act=man_list&type=" . $type, false);
		}
		break;

	case "delete_list":
		$id = (int)($_GET['id'] ?? 0);
		if ($id && $listCRUD->delete($id)) {
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=product&act=man_list&type=" . $type);
		} else {
			$func->transfer("Xóa dữ liệu thất bại", "index.php?com=product&act=man_list&type=" . $type, false);
		}
		break;

	// Các case khác (size, color, brand, cat, item, sub, gallery) giữ nguyên
	// vì có logic riêng phức tạp
	
	default:
		$template = "404";
}


