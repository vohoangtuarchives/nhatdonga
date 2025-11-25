<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\SettingRepository;
use Tuezy\Repository\OrderRepository;
use Tuezy\SecurityHelper;

if ($func->checkLoginAdmin() == false && $act != "login") {
	$func->redirect("index.php?com=user&act=login");
}

if (!isset($config['order']['excel']) || $config['order']['excel'] == false) {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

$settingRepo = new SettingRepository($d, $cache);
$orderRepo = new OrderRepository($d, $cache);

$setting = $settingRepo->getFirst();
$optsetting = !empty($setting['options']) ? json_decode($setting['options'], true) : null;

$id_order = (int)SecurityHelper::sanitizeGet('id', 0);
if (!$id_order) {
	$func->transfer("Dữ liệu không có thực", "index.php?com=order&act=man", false);
}

$order_detail = $orderRepo->getById($id_order);
if (!$order_detail) {
	$func->transfer("Dữ liệu không có thực", "index.php?com=order&act=man", false);
}

$code = $order_detail['code'] ?? '';
$order_status = $order_detail['order_status'] ?? 0;
$temp_price = $func->formatMoney($order_detail['temp_price'] ?? 0);
$total_price = $func->formatMoney($order_detail['total_price'] ?? 0);
$ship_price = $order_detail['ship_price'] ?? 0;
$ship_price = $ship_price ? $func->formatMoney($ship_price) : "Không";

$order_status_info = $d->rawQueryOne("SELECT namevi FROM #_order_status WHERE id = ? LIMIT 0,1", [$order_status]);

$order_details = $d->rawQuery("SELECT * FROM #_order_detail WHERE id_order = ?", [$id_order]);

require_once LIBRARIES . 'PHPExcel.php';

$PHPExcel = new PHPExcel();
$PHPExcel->getProperties()->setCreator($setting['namevi']);
$PHPExcel->getProperties()->setLastModifiedBy($setting['namevi']);
$PHPExcel->getProperties()->setTitle("Order Export");
$PHPExcel->getProperties()->setSubject("Order Export");
$PHPExcel->getProperties()->setDescription("Order document");

$PHPExcel->setActiveSheetIndex(0);
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A1:F1');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A2:F2');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A3:F3');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A4:F4');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A6:C6');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A7:C7');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A8:C8');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('D6:F6');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('D7:F7');
$PHPExcel->setActiveSheetIndex(0)->mergeCells('D8:F8');

$PHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$PHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$PHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$PHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$PHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$PHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);

$PHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $setting['namevi']);
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A2', 'Hotline: ' . ($optsetting['hotline'] ?? ''));
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A3', 'Email: ' . ($optsetting['email'] ?? ''));
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A4', 'Địa chỉ: ' . ($optsetting['address'] ?? ''));
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A6', 'Họ tên: ' . ($order_detail['fullname'] ?? ''));
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'Điện thoại: ' . ($order_detail['phone'] ?? ''));
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A8', 'Địa chỉ: ' . ($order_detail['address'] ?? ''));
$PHPExcel->setActiveSheetIndex(0)->setCellValue('D6', 'Mã đơn hàng: ' . $code);
$PHPExcel->setActiveSheetIndex(0)->setCellValue('D7', 'Ngày đặt: ' . date('H:i A d-m-Y', $order_detail['date_created'] ?? time()));
$PHPExcel->setActiveSheetIndex(0)->setCellValue('D8', 'Tình trạng: ' . ($order_status_info['namevi'] ?? ''));
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A10', 'STT');
$PHPExcel->setActiveSheetIndex(0)->setCellValue('B10', 'Sản phẩm');
$PHPExcel->setActiveSheetIndex(0)->setCellValue('C10', 'Số lượng');
$PHPExcel->setActiveSheetIndex(0)->setCellValue('D10', 'Giá');
$PHPExcel->setActiveSheetIndex(0)->setCellValue('E10', 'Giá mới');
$PHPExcel->setActiveSheetIndex(0)->setCellValue('F10', 'Tạm tính');

$PHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray([
	'font' => ['color' => ['rgb' => '000000'], 'name' => 'Arial', 'bold' => true, 'size' => 14],
	'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true]
]);

$PHPExcelStyleTitle = new PHPExcel_Style();
$PHPExcelStyleTitle->applyFromArray([
	'font' => ['color' => ['rgb' => '000000'], 'name' => 'Calibri', 'bold' => true, 'size' => 10],
	'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true],
	'borders' => [
		'top' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
		'right' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
		'bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
		'left' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
	]
]);
$PHPExcel->getActiveSheet()->setSharedStyle($PHPExcelStyleTitle, 'A10:F10');

$PHPExcelStyleContent = new PHPExcel_Style();

$position = 11;
foreach($order_details as $i => $detail) {
	$sum_price = 0;
	if (isset($detail['sale_price']) && $detail['sale_price'] > 0) {
		$sum_price = $detail['sale_price'] * $detail['quantity'];
	} else {
		$sum_price = $detail['regular_price'] * $detail['quantity'];
	}
	
	$PHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $position, $i + 1);
	$PHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $position, $detail['name'] ?? '');
	$PHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $position, $detail['quantity'] ?? 0);
	$PHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $position, $func->formatMoney($detail['regular_price'] ?? 0));
	$PHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $position, $func->formatMoney($detail['sale_price'] ?? 0));
	$PHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $position, $func->formatMoney($sum_price));
	
	$PHPExcelStyleContent->applyFromArray([
		'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true],
		'borders' => [
			'top' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
			'right' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
			'bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
			'left' => ['style' => PHPExcel_Style_Border::BORDER_THIN],
		]
	]);
	$PHPExcel->getActiveSheet()->setSharedStyle($PHPExcelStyleContent, 'A' . $position . ':F' . $position);
	
	$position++;
}

// Tính thành tiền
$position++;
if ($config['order']['ship']) {
	// Tạm tính
	$PHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $position . ':E' . $position);
	$PHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $position, 'Tạm tính');
	$PHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $position, $temp_price);
	$PHPExcel->getActiveSheet()->getStyle('A' . $position . ':F' . $position)->applyFromArray([
		'font' => ['color' => ['rgb' => '000000'], 'name' => 'Calibri', 'bold' => true, 'size' => 10],
		'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true]
	]);
	$position++;
}

// Phí vận chuyển
if ($config['order']['ship']) {
	$PHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $position . ':E' . $position);
	$PHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $position, 'Phí vận chuyển');
	$PHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $position, $ship_price);
	$PHPExcel->getActiveSheet()->getStyle('A' . $position . ':F' . $position)->applyFromArray([
		'font' => ['color' => ['rgb' => '000000'], 'name' => 'Calibri', 'bold' => true, 'size' => 10],
		'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true]
	]);
	$position++;
}

// Tổng tiền
$PHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $position . ':E' . $position);
$PHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $position, 'Tổng giá trị đơn hàng');
$PHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $position, $total_price);
$PHPExcel->getActiveSheet()->getStyle('A' . $position . ':F' . $position)->applyFromArray([
	'font' => ['color' => ['rgb' => '000000'], 'name' => 'Calibri', 'bold' => true, 'size' => 10],
	'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER, 'wrap' => true]
]);
$position++;

// Rename title
$PHPExcel->getActiveSheet()->setTitle('Order');

// Khởi tạo chỉ mục ở đầu sheet
$PHPExcel->setActiveSheetIndex(0);

// Xuất file
$time = time();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="order_' . $code . '_' . $time . '_' . date('d_m_Y') . '.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit();

