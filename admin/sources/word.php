<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\SettingRepository;
use Tuezy\Repository\OrderRepository;
use Tuezy\SecurityHelper;

if ($func->checkLoginAdmin() == false && $act != "login") {
	$func->redirect("index.php?com=user&act=login");
}

if (!isset($config['order']['word']) || $config['order']['word'] == false) {
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

$detail = $d->rawQuery("SELECT * FROM #_order_detail WHERE id_order = ?", [$id_order]);

$time = time();
$code = $order_detail['code'] ?? '';
$order_date = date('H:i A d-m-Y', $order_detail['date_created'] ?? time());
$order_status = $order_detail['order_status'] ?? 0;
$fullname_customer = $order_detail['fullname'] ?? '';
$phone_customer = $order_detail['phone'] ?? '';
$email_customer = $order_detail['email'] ?? '';
$address_customer = $order_detail['address'] ?? '';
$requirements_customer = $order_detail['requirements'] ?? '';
$temp_price = $func->formatMoney($order_detail['temp_price'] ?? 0);
$total_price = $func->formatMoney($order_detail['total_price'] ?? 0);
$ship_price = $order_detail['ship_price'] ?? 0;
$ship_price = $ship_price ? $func->formatMoney($ship_price) : "Không";

$order_status_info = $d->rawQueryOne("SELECT namevi FROM #_order_status WHERE id = ? LIMIT 0,1", [$order_status]);

require_once LIBRARIES . 'PHPWord.php';

$PHPWord = new PHPWord();
$file_sample = LIBRARIES . 'sample/order.docx';
$document = $PHPWord->loadTemplate($file_sample);

$document->setValue('{name_company}', $setting['namevi']);
$document->setValue('{hotline_company}', $optsetting['hotline'] ?? '');
$document->setValue('{email_company}', $optsetting['email'] ?? '');
$document->setValue('{address_company}', $optsetting['address'] ?? '');

$document->setValue('{code}', $code);
$document->setValue('{order_date}', $order_date);
$document->setValue('{order_status}', $order_status_info['namevi'] ?? '');

$document->setValue('{fullname_customer}', $fullname_customer);
$document->setValue('{phone_customer}', $phone_customer);
$document->setValue('{email_customer}', $email_customer);
$document->setValue('{address_customer}', $address_customer);
$document->setValue('{requirements_customer}', $requirements_customer);

$data = [];
foreach($detail as $i => $item) {
	$sum_price = 0;
	if (isset($item['sale_price']) && $item['sale_price'] > 0) {
		$sum_price = $item['sale_price'] * $item['quantity'];
	} else {
		$sum_price = $item['regular_price'] * $item['quantity'];
	}
	
	$data["numb"][$i] = $i + 1;
	$data["name"][$i] = $item['name'] ?? '';
	$data["color"][$i] = $item['color'] ?? '';
	$data["size"][$i] = $item['size'] ?? '';
	$data["regular_price"][$i] = $func->formatMoney($item['regular_price'] ?? 0);
	$data["sale_price"][$i] = $func->formatMoney($item['sale_price'] ?? 0);
	$data["quantity"][$i] = $item['quantity'] ?? 0;
	$data["sum_price"][$i] = $func->formatMoney($sum_price);
}

$document->cloneRow('TB', $data);

$document->setValue('{temp_price}', $temp_price);
$document->setValue('{ship_price}', $ship_price);
$document->setValue('{total_price}', $total_price);

$filename = "order_" . $code . "_" . $time . "_" . date('d_m_Y') . ".docx";
$document->save($filename);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
readfile($filename);
unlink($filename);
exit;

