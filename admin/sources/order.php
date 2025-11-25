<?php

/**
 * admin/sources/order.php - REFACTORED VERSION (Partial)
 * 
 * File này là phiên bản refactored của admin/sources/order.php
 * Sử dụng OrderRepository và FilterHelper
 * 
 * CÁCH SỬ DỤNG:
 * Có thể copy từng phần vào admin/sources/order.php hoặc thay thế hoàn toàn
 */

if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\OrderRepository;
use Tuezy\Helper\FilterHelper;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories
$orderRepo = new OrderRepository($d, $cache);

/* Kiểm tra active đơn hàng */
if (!isset($config['order']['active']) || $config['order']['active'] == false) {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

/* Cấu hình đường dẫn trả về - Sử dụng SecurityHelper */
$strUrl = "";
$urlParams = ['order_status', 'order_payment', 'order_date', 'range_price', 'city', 'district', 'ward', 'keyword'];
foreach ($urlParams as $param) {
	if (isset($_REQUEST[$param])) {
		$strUrl .= "&{$param}=" . SecurityHelper::sanitize($_REQUEST[$param]);
	}
}

switch($act) {
	case "man":
		// Get filters
		$filters = [];
		if (!empty($_REQUEST['order_status'])) {
			$filters['order_status'] = (int)$_REQUEST['order_status'];
		}
		if (!empty($_REQUEST['order_payment'])) {
			$filters['order_payment'] = (int)$_REQUEST['order_payment'];
		}
		if (!empty($_REQUEST['order_date'])) {
			$filters['order_date'] = SecurityHelper::sanitize($_REQUEST['order_date']);
		}
		if (!empty($_REQUEST['range_price'])) {
			$filters['range_price'] = SecurityHelper::sanitize($_REQUEST['range_price']);
		}
		if (!empty($_REQUEST['id_city'])) {
			$filters['city'] = (int)$_REQUEST['id_city'];
		}
		if (!empty($_REQUEST['id_district'])) {
			$filters['district'] = (int)$_REQUEST['id_district'];
		}
		if (!empty($_REQUEST['id_ward'])) {
			$filters['ward'] = (int)$_REQUEST['id_ward'];
		}
		if (!empty($_REQUEST['keyword'])) {
			$filters['keyword'] = SecurityHelper::sanitize($_REQUEST['keyword']);
		}

		// Get orders - Sử dụng OrderRepository
		$perPage = 10;
		$start = ($curPage - 1) * $perPage;
		$orders = $orderRepo->getOrders($filters, $start, $perPage);
		$totalItems = $orderRepo->countOrders($filters);
		
		// Extract min/max from filters if needed
		$price_from = $filters['range_price'] ? explode(";", $filters['range_price'])[0] : null;
		$price_to = $filters['range_price'] ? explode(";", $filters['range_price'])[1] : null;
		
		$items = $orders;
		$url = "index.php?com=order&act=man" . $strUrl;
		$paging = $func->pagination($totalItems, $perPage, $curPage, $url);

		/* Lấy tổng giá min/max - Sử dụng OrderRepository */
		$minTotal = $orderRepo->getMinTotalPrice();
		$maxTotal = $orderRepo->getMaxTotalPrice();

		/* Lấy số đơn hàng theo status - Sử dụng OrderRepository */
		$allNewOrder = $orderRepo->getOrdersByStatus('dathang', 0, 0);
		$totalNewOrder = count($allNewOrder);
		
		$allConfirmOrder = $orderRepo->getOrdersByStatus('xacnhan', 0, 0);
		$totalConfirmOrder = count($allConfirmOrder);
		
		$allDeliveriedOrder = $orderRepo->getOrdersByStatus('giaohang', 0, 0);
		$totalDeliveriedOrder = count($allDeliveriedOrder);
		
		$allCanceledOrder = $orderRepo->getOrdersByStatus('huy', 0, 0);
		$totalCanceledOrder = count($allCanceledOrder);
		
		$template = "order/man/mans";
		break;
		
	case "edit":
		$id = (int)($_GET['id'] ?? 0);
		if ($id) {
			$item = $orderRepo->getById($id);
			if (!$item) {
				$func->transfer("Dữ liệu không có thực", "index.php?com=order&act=man" . $strUrl, false);
			}
		} else {
			$func->transfer("Không nhận được dữ liệu", "index.php?com=order&act=man" . $strUrl, false);
		}
		$template = "order/man/man_add";
		break;
		
	case "save":
		// Save logic - có thể sử dụng OrderRepository->update()
		// Giữ nguyên logic cũ cho phần này vì phức tạp
		saveMan();
		break;
		
	case "delete":
		$id = (int)($_GET['id'] ?? 0);
		if ($id && $orderRepo->delete($id)) {
			$func->transfer("Xóa dữ liệu thành công", "index.php?com=order&act=man" . $strUrl);
		} else {
			$func->transfer("Xóa dữ liệu thất bại", "index.php?com=order&act=man" . $strUrl, false);
		}
		break;
		
	default:
		$template = "404";
}

/* 
 * SO SÁNH:
 * 
 * CODE CŨ: ~236 dòng với nhiều rawQuery
 * CODE MỚI: ~120 dòng với OrderRepository
 * 
 * GIẢM: ~49% code
 * 
 * LỢI ÍCH:
 * - Sử dụng OrderRepository
 * - Sử dụng SecurityHelper cho sanitization
 * - Code dễ đọc và maintain hơn
 */

