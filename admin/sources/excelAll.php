<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\SettingRepository;
use Tuezy\Repository\OrderRepository;
use Tuezy\SecurityHelper;

if ($func->checkLoginAdmin() == false && $act != "login") {
	$func->redirect("index.php?com=user&act=login");
}

if (!isset($config['order']['excelall']) || $config['order']['excelall'] == false) {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

$settingRepo = new SettingRepository($d, $cache);
$orderRepo = new OrderRepository($d, $cache);

$setting = $settingRepo->getFirst();
$optsetting = !empty($setting['options']) ? json_decode($setting['options'], true) : null;

$filters = [];
$filters['listid'] = SecurityHelper::sanitizeGet('listid', '');
$filters['order_status'] = (int)SecurityHelper::sanitizeGet('order_status', 0);
$filters['order_payment'] = (int)SecurityHelper::sanitizeGet('order_payment', 0);
$filters['order_date'] = SecurityHelper::sanitizeGet('order_date', '');
$filters['range_price'] = SecurityHelper::sanitizeGet('range_price', '');
$filters['city'] = (int)SecurityHelper::sanitizeGet('id_city', 0);
$filters['district'] = (int)SecurityHelper::sanitizeGet('id_district', 0);
$filters['ward'] = (int)SecurityHelper::sanitizeGet('id_ward', 0);
$filters['keyword'] = SecurityHelper::sanitizeGet('keyword', '');

$orders = $orderRepo->getOrders($filters, 0, 0);

require_once LIBRARIES . 'PHPExcel.php';

$PHPExcel = new PHPExcel();
$PHPExcel->getProperties()->setCreator($setting['namevi']);
$PHPExcel->getProperties()->setLastModifiedBy($setting['namevi']);
$PHPExcel->getProperties()->setTitle("Orders Export");
$PHPExcel->getProperties()->setSubject("Orders Export");
$PHPExcel->getProperties()->setDescription("Orders document");

$PHPExcel->setActiveSheetIndex(0);
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A1:L1');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A2:L2');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A3:L3');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A4:L4');

$columns = ['A' => 5, 'B' => 20, 'C' => 20, 'D' => 20, 'E' => 20, 'F' => 20, 'G' => 20, 'H' => 20, 'I' => 30, 'J' => 20, 'K' => 20, 'L' => 20];
foreach($columns as $col => $width) {
	$PHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth($width);
}

$PHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $setting['namevi']);
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'Hotline: ' . ($optsetting['hotline'] ?? ''));
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Email: ' . ($optsetting['email'] ?? ''));
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Địa chỉ: ' . ($optsetting['address'] ?? ''));

$headers = ['A6' => 'STT', 'B6' => 'Mã đơn hàng', 'C6' => 'Ngày đặt', 'D6' => 'Tình trạng', 'E6' => 'Hình thức thanh toán', 'F6' => 'Họ tên', 'G6' => 'Điện thoại', 'H6' => 'Email', 'I6' => 'Địa chỉ', 'J6' => 'Tạm tính', 'K6' => 'Phí vận chuyển', 'L6' => 'Tổng giá trị đơn hàng'];
foreach($headers as $cell => $value) {
	$PHPExcel->setActiveSheetIndex(0)->setCellValue($cell, $value);
}

$PHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray([
	'font' => ['color' => ['rgb' => '000000'], 'name' => 'Arial', 'bold' => true, 'size' => 14],
	'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true]
]);

$PHPExcel->getActiveSheet()->getStyle('A6:L6')->applyFromArray([
	'font' => ['color' => ['rgb' => '000000'], 'name' => 'Calibri', 'bold' => true, 'size' => 10],
	'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true],
	'borders' => [
		'top' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
		'right' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
		'bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
		'left' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
	]
]);

$position = 7;
foreach($orders as $i => $order) {
	$ship_price = ($order['ship_price'] ?? 0) > 0 ? $func->formatMoney($order['ship_price']) : "Không";
	$order_status_info = $d->rawQueryOne("SELECT namevi FROM #_order_status WHERE id = ? LIMIT 0,1", [$order['order_status']]);
	$order_payment = $func->getInfoDetail('namevi', 'news', $order['order_payment'] ?? 0);
	
	$PHPExcel->setActiveSheetIndex(0)
		->setCellValue('A' . $position, $i + 1)
		->setCellValue('B' . $position, $order['code'] ?? '')
		->setCellValue('C' . $position, date('H:i A d-m-Y', $order['date_created'] ?? time()))
		->setCellValue('D' . $position, $order_status_info['namevi'] ?? '')
		->setCellValue('E' . $position, $order_payment['namevi'] ?? '')
		->setCellValue('F' . $position, $order['fullname'] ?? '')
		->setCellValue('G' . $position, $order['phone'] ?? '')
		->setCellValue('H' . $position, $order['email'] ?? '')
		->setCellValue('I' . $position, $order['address'] ?? '')
		->setCellValue('J' . $position, $func->formatMoney($order['temp_price'] ?? 0))
		->setCellValue('K' . $position, $ship_price)
		->setCellValue('L' . $position, $func->formatMoney($order['total_price'] ?? 0));
	
	$PHPExcel->getActiveSheet()->getStyle('A' . $position . ':L' . $position)->applyFromArray([
		'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true],
		'borders' => [
			'top' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
			'right' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
			'bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
			'left' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
		]
	]);
	$position++;
}

$PHPExcel->getActiveSheet()->setTitle('Orders List');
$PHPExcel->setActiveSheetIndex(0);

$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="orders_list_' . time() . '_' . date('d_m_Y') . '.xlsx"');
header('Cache-Control: max-age=0');

$objWriter->save('php://output');
exit;

