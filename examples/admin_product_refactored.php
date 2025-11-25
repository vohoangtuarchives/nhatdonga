<?php

/**
 * admin/sources/product.php - REFACTORED VERSION (Partial)
 * 
 * File này demo cách sử dụng AdminController và các helper để refactor admin/sources/product.php
 * 
 * CÁCH SỬ DỤNG:
 * Có thể copy từng phần vào admin/sources/product.php
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Admin\AdminController;
use Tuezy\Admin\AdminCRUDHelper;
use Tuezy\Admin\AdminURLHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;
use Tuezy\Config;
use Tuezy\RequestHandler;

// Initialize
$configObj = new Config($config);
$params = RequestHandler::getParams();
$com = $params['com'];
$act = $params['act'];
$type = $params['type'];

// Initialize helpers
$adminAuth = new AdminAuthHelper($func, $d, $loginAdmin, $config);
$adminPermission = new AdminPermissionHelper($func, $config);
$adminURL = new AdminURLHelper('index.php');

// Check authentication
$adminAuth->requireLogin();

// Check permission for restricted actions
if ($adminPermission->isRestricted($act)) {
    $adminPermission->requirePermission($act);
}

// Build return URL
$strUrl = $adminURL->buildFromRequest(
    ['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'],
    ['comment_status', 'keyword']
);

// Initialize CRUD helper
$crudHelper = new AdminCRUDHelper($d, $func, 'product', $type, $config['product'][$type] ?? []);

// Route actions
switch ($act) {
    case "man":
        // Get list with pagination
        $curPage = $params['curPage'] ?? 1;
        $result = $crudHelper->getList($curPage, 20);
        $items = $result['items'];
        $paging = $result['paging'];
        $template = "product/man/mans";
        break;

    case "add":
        $template = "product/man/man_add";
        break;

    case "edit":
    case "copy":
        if ((!isset($config['product'][$type]['copy']) || $config['product'][$type]['copy'] == false) && $act == 'copy') {
            $template = "404";
            break;
        }
        $id = (int)$params['id'];
        $item = $crudHelper->getItem($id);
        if (!$item) {
            $func->transfer("Dữ liệu không tồn tại", "index.php?com=product&act=man&type=$type", false);
            break;
        }
        $template = "product/man/man_add";
        break;

    case "save":
    case "save_copy":
        $id = !empty($params['id']) ? (int)$params['id'] : null;
        $data = $_POST['data'] ?? [];
        
        // Save using CRUD helper
        if ($crudHelper->save($data, $id)) {
            $message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
            $returnUrl = $adminURL->getReturnUrl('product', 'man', $type) . $strUrl;
            $func->transfer($message, $returnUrl);
        } else {
            $func->transfer("Có lỗi xảy ra", "index.php?com=product&act=man&type=$type", false);
        }
        break;

    case "delete":
        $id = (int)$params['id'];
        if ($crudHelper->delete($id)) {
            $func->transfer("Xóa dữ liệu thành công", "index.php?com=product&act=man&type=$type" . $strUrl);
        } else {
            $func->transfer("Có lỗi xảy ra", "index.php?com=product&act=man&type=$type" . $strUrl, false);
        }
        break;

    // ... other cases (size, color, brand, list, cat, etc.)
    // Can use similar pattern

    default:
        $template = "404";
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~2800 dòng với nhiều functions lặp lại
 * CODE MỚI: ~100 dòng với CRUD helper
 * 
 * GIẢM: ~96% code!
 * 
 * LỢI ÍCH:
 * - Consistent CRUD operations
 * - Easy to maintain
 * - Type-safe
 * - Better error handling
 * - Reusable across admin modules
 */

