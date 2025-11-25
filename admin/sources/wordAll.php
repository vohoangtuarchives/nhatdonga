<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\Repository\SettingRepository;
use Tuezy\Repository\OrderRepository;
use Tuezy\SecurityHelper;

if ($func->checkLoginAdmin() == false && $act != "login") {
	$func->redirect("index.php?com=user&act=login");
}

if (!isset($config['order']['wordall']) || $config['order']['wordall'] == false) {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

$settingRepo = new SettingRepository($d, $cache);
$orderRepo = new OrderRepository($d, $cache);

$setting = $settingRepo->getFirst();
$optsetting = !empty($setting['options']) ? json_decode($setting['options'], true) : null;

$time = time();

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

require_once LIBRARIES . 'PHPWord.php';

$PHPWord = new PHPWord();
$file_sample = LIBRARIES . 'sample/orderlist.docx';
$document = $PHPWord->loadTemplate($file_sample);

$document->setValue('{name_company}', $setting['namevi']);
$document->setValue('{hotline_company}', $optsetting['hotline'] ?? '');
$document->setValue('{email_company}', $optsetting['email'] ?? '');
$document->setValue('{address_company}', $optsetting['address'] ?? '');

$data = [];
foreach($orders as $i => $order) {
	$ship_price = ($order['ship_price'] ?? 0) > 0 ? $func->formatMoney($order['ship_price']) : "Không";
	$order_status_info = $d->rawQueryOne("SELECT namevi FROM #_order_status WHERE id = ? LIMIT 0,1", [$order['order_status']]);
	$order_payment = $func->getInfoDetail('namevi', 'news', $order['order_payment'] ?? 0);
	
	$data["numb"][$i] = $i + 1;
	$data["code"][$i] = $order['code'] ?? '';
	$data["order_date"][$i] = date('H:i A d-m-Y', $order['date_created'] ?? time());
	$data["order_status"][$i] = $order_status_info['namevi'] ?? '';
	$data["order_payment"][$i] = $order_payment['namevi'] ?? '';
	$data["fullname_customer"][$i] = $order['fullname'] ?? '';
	$data["phone_customer"][$i] = $order['phone'] ?? '';
	$data["email_customer"][$i] = $order['email'] ?? '';
	$data["address_customer"][$i] = $order['address'] ?? '';
	$data["temp_price"][$i] = $func->formatMoney($order['temp_price'] ?? 0);
	$data["ship_price"][$i] = $ship_price;
	$data["total_price"][$i] = $func->formatMoney($order['total_price'] ?? 0);
}

$document->cloneRow('TB', $data);

$filename = "orders_list" . $time . "_" . date('d_m_Y') . ".docx";
$document->save($filename);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . $filename);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
flush();
readfile($filename);
unlink($filename);
exit;

