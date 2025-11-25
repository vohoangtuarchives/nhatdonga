<?php

if (!defined('SOURCES')) die("Error");

use Tuezy\Helper\ExportHelper;
use Tuezy\Repository\SettingRepository;
use Tuezy\SecurityHelper;

if (isset($config['product'])) {
	$arrCheck = array();
	foreach($config['product'] as $k => $v) {
		if (isset($v['export']) && $v['export'] == true) $arrCheck[] = $k;
	}
	if (!count($arrCheck) || !in_array($type, $arrCheck)) {
		$func->transfer("Trang không tồn tại", "index.php", false);
	}
} else {
	$func->transfer("Trang không tồn tại", "index.php", false);
}

$settingRepo = new SettingRepository($d, $cache);
$exportHelper = new ExportHelper();

switch($act) {
	case "man":
		$template = "export/man/mans";
		break;

	case "exportExcel":
		exportExcel();
		break;

	default:
		$template = "404";
}

function exportExcel()
{
	global $d, $func, $type, $settingRepo, $exportHelper;
	
	$setting = $settingRepo->getFirst();
	$optsetting = !empty($setting['options']) ? json_decode($setting['options'], true) : null;
	
	if (isset($_POST['exportExcel'])) {
		require_once LIBRARIES . 'PHPExcel.php';
		
		$PHPExcel = new PHPExcel();
		$PHPExcel->getProperties()->setCreator($setting['namevi']);
		$PHPExcel->getProperties()->setLastModifiedBy($setting['namevi']);
		$PHPExcel->getProperties()->setTitle("Export Product");
		$PHPExcel->getProperties()->setSubject("Export Product");
		$PHPExcel->getProperties()->setDescription("Document for Office 2007 XLSX");
		
		$alphas = range('A', 'Z');
		$array_columns = [
			'numb' => 'STT',
			'id_list' => 'Danh Mục Cấp 1',
			'id_cat' => 'Danh mục 2',
			'code' => 'Mã sản phẩm',
			'namevi' => 'Tên Sản phẩm',
			'regular_price' => 'Giá bán',
			'sale_price' => 'Giá mới',
			'discount' => 'Chiết khấu',
			'descvi' => 'Mô tả',
			'contentvi' => 'Nội dung',
			'photo' => 'Hình đại diện'
		];
		
		$i = 0;
		foreach($array_columns as $k => $v) {
			$width = match($k) {
				'numb' => 5,
				'id_list', 'id_cat' => 20,
				'code' => 15,
				'namevi' => 40,
				'regular_price', 'sale_price', 'discount' => 10,
				'descvi', 'contentvi', 'photo' => 35,
				default => 20
			};
			
			$PHPExcel->getActiveSheet()->getColumnDimension($alphas[$i])->setWidth($width);
			$PHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$i] . '1', $v);
			$PHPExcel->getActiveSheet()->getStyle($alphas[$i] . '1')->applyFromArray([
				'font' => [
					'color' => ['rgb' => 'ffffff'],
					'name' => 'Calibri',
					'bold' => true,
					'size' => 10
				],
				'alignment' => [
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					'wrap' => true
				],
				'fill' => [
					'type' => PHPExcel_Style_Fill::FILL_SOLID,
					'color' => ['rgb' => '007BFF']
				]
			]);
			$i++;
		}
		
		// Build where clause for category filters
		$whereCategory = "";
		$params = [$type];
		$data = $_POST['data'] ?? null;
		
		if ($data) {
			foreach($data as $column => $value) {
				$value = (int)$value;
				if ($value > 0) {
					$whereCategory .= " AND {$column} = ?";
					$params[] = $value;
				}
			}
		}
		
		$sql = "SELECT * FROM #_product WHERE type = ? {$whereCategory} ORDER BY numb,id DESC";
		$products = $d->rawQuery($sql, $params);
		
		$position = 2;
		for($i = 0; $i < count($products); $i++) {
			$j = 0;
			foreach($array_columns as $k => $v) {
				$datacell = '';
				
				if ($k == 'id_list') {
					$namelist = $d->rawQueryOne("SELECT namevi FROM #_product_list WHERE id = ? LIMIT 0,1", [$products[$i][$k]]);
					$datacell = $namelist['namevi'] ?? '';
				} elseif ($k == 'id_cat') {
					$namecat = $d->rawQueryOne("SELECT namevi FROM #_product_cat WHERE id = ? LIMIT 0,1", [$products[$i][$k]]);
					$datacell = $namecat['namevi'] ?? '';
				} else {
					$datacell = $products[$i][$k] ?? '';
				}
				
				if ($k == 'code') {
					$PHPExcel->getActiveSheet()->setCellValueExplicit($alphas[$j] . $position, htmlspecialchars_decode($datacell), PHPExcel_Cell_DataType::TYPE_STRING);
				} else {
					$PHPExcel->setActiveSheetIndex(0)->setCellValue($alphas[$j] . $position, htmlspecialchars_decode($datacell));
				}
				$j++;
			}
			$position++;
		}
		
		// Style cho các row dữ liệu
		$position = 2;
		for($i = 0; $i < count($products); $i++) {
			$j = 0;
			foreach($array_columns as $k => $v) {
				$PHPExcel->getActiveSheet()->getStyle($alphas[$j] . $position)->applyFromArray([
					'font' => [
						'color' => ['rgb' => '000000'],
						'name' => 'Calibri',
						'bold' => false,
						'size' => 10
					],
					'alignment' => [
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						'wrap' => true
					]
				]);
				$j++;
			}
			$position++;
		}
		
		// Rename title
		$PHPExcel->getActiveSheet()->setTitle('Products List');
		
		// Khởi tạo chỉ mục ở đầu sheet
		$PHPExcel->setActiveSheetIndex(0);
		
		// Xuất file
		$time = time();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="products_' . $time . '_' . date('d_m_Y') . '.xlsx"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit();
	}
}

