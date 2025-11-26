<?php
// Bắt output buffering ngay từ đầu để tránh mọi output không mong muốn
ob_start();

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

include "config.php";
require_once LIBRARIES."config-type.php";

use Tuezy\Config;
use Tuezy\SecurityHelper;

// Đảm bảo các biến global được khởi tạo trong mỗi request
// Lấy từ $app nếu có, nếu không thì từ $GLOBALS
if(isset($app) && method_exists($app, 'getGlobals')) {
	$globals = $app->getGlobals();
	foreach($globals as $name => $value) {
		$GLOBALS[$name] = $value;
	}
}

// Đảm bảo các biến local được set từ GLOBALS (sử dụng extract để tự động)
// Chỉ extract các biến cần thiết để tránh conflict
$requiredVars = ['d', 'func', 'cache', 'config'];
foreach($requiredVars as $varName) {
	if(isset($GLOBALS[$varName])) {
		$$varName = $GLOBALS[$varName];
	}
}

// Initialize Config object nếu cần
if(!isset($configObj)) {
	$configObj = new Config($config ?? []);
}

/* Xử lý params - Sử dụng SecurityHelper */
$flag = true;
$param = SecurityHelper::sanitizePost('params', '');
$params = null;
if ($param) {
	parse_str(base64_decode($param), $params);
	$params = SecurityHelper::sanitizeArray($params);
}

$id = (int)($params['id'] ?? 0);
$com = SecurityHelper::sanitize($params['com'] ?? '');
$type = SecurityHelper::sanitize($params['type'] ?? '');
$hash = SecurityHelper::sanitizePost('hash', '');
$numb = (int)SecurityHelper::sanitizePost('numb', 0);

// Xử lý kind - ưu tiên lấy từ params, nếu không có thì tính từ act
$kind = SecurityHelper::sanitize($params['kind'] ?? '');
if(empty($kind)) {
	$e = (!empty($params['act'])) ? explode("_", $params['act']) : null;
	$ex = ($e && count($e) > 1) ? end($e) : '';
	$kind = "man" . (($ex) ? ("_" . $ex) : '');
}
// Nếu vẫn rỗng, mặc định là "man"
if(empty($kind)) {
	$kind = "man";
}

$data = ['success' => false, 'msg' => 'Upload thất bại', 'data' => null];

// Kiểm tra file upload
if(empty($_FILES['files']) || empty($_FILES['files']['name'][0])) {
	$data['msg'] = 'Không có file được upload';
	ob_clean();
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}

/* Xử lý $_FILE - Path image */
$myFile = $_FILES['files'];
$_FILES['file'] = array(
	'name' => $myFile['name'][0],
	'type' => $myFile['type'][0],
	'tmp_name' => $myFile['tmp_name'][0],
	'error' => $myFile['error'][0],
	'size' => $myFile['size'][0]
);

// Kiểm tra lỗi upload
if($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
	$data['msg'] = 'Lỗi upload file: ' . $_FILES['file']['error'];
	ob_clean();
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}

$file_name = $func->uploadName($_FILES['file']['name']);
// Sử dụng path có ../ cho upload file
$upload_path = array("product" => UPLOAD_PRODUCT, "news" => UPLOAD_NEWS);
// Sử dụng path không có ../ cho URL
$upload_path_url = array("product" => UPLOAD_PRODUCT_L, "news" => UPLOAD_NEWS_L);

// Kiểm tra com và type hợp lệ
if(empty($com) || empty($type) || !isset($upload_path[$com])) {
	$data['msg'] = 'Tham số không hợp lệ';
	ob_clean();
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}

/* Xử lý lưu image */
// Kiểm tra config - có thể có img_type hoặc gallery
// Đảm bảo $config được load đầy đủ từ config-type.php
global $config;
if(empty($config)) {
	$config = $app->getConfig()->all();
}

$hasImgType = !empty($config[$com][$type]['img_type']);
$hasGallery = !empty($config[$com][$type]['gallery']) && is_array($config[$com][$type]['gallery']);

// Đối với gallery, cần lấy img_type từ gallery config
$imgType = null;
if($hasImgType) {
	$imgType = $config[$com][$type]['img_type'];
} elseif($hasGallery) {
	// Tìm img_type trong gallery config
	// Ưu tiên tìm theo $kind trước, sau đó mới tìm trong tất cả các gallery items
	if(!empty($kind) && isset($config[$com][$type]['gallery'][$kind])) {
		$galleryItem = $config[$com][$type]['gallery'][$kind];
		if(isset($galleryItem['img_type_photo'])) {
			$imgType = $galleryItem['img_type_photo'];
		} elseif(isset($galleryItem['img_type'])) {
			$imgType = $galleryItem['img_type'];
		}
	}
	
	// Nếu vẫn chưa tìm thấy, tìm trong tất cả các gallery items
	if(empty($imgType)) {
		foreach($config[$com][$type]['gallery'] as $galleryKey => $galleryValue) {
			if(isset($galleryValue['img_type_photo'])) {
				$imgType = $galleryValue['img_type_photo'];
				break;
			} elseif(isset($galleryValue['img_type'])) {
				$imgType = $galleryValue['img_type'];
				break;
			}
		}
	}
}

// Nếu vẫn không có img_type, thử fallback về giá trị mặc định
if(empty($imgType)) {
	// Fallback: sử dụng giá trị mặc định là định dạng file ảnh
	$imgType = '.jpg|.gif|.png|.jpeg|.webp'; // Giá trị mặc định cho tất cả các loại
}

// Chỉ cần kiểm tra imgType không rỗng
if(!empty($imgType))
{
		$data_file = array();
		
		if(empty($id))
		{
			$data_file['hash'] = $hash;
		}

		$data_file['numb'] = 0;		
		$data_file['namevi'] = "";
		$data_file['id_parent'] = $id;
		$data_file['com'] = $com;
		$data_file['type'] = $type;
		$data_file['kind'] = $kind;
		$data_file['val'] = $type;
		$data_file['status'] = 'hienthi';
		$data_file['date_created'] = time();
		$max_numb = $d->rawQueryOne("select max(numb) as max_numb from #_gallery where com = ? and  type = ? and kind = ? and val = ? and id_parent = ?",array($com, $type, $kind, $type, $id));
		$data_file['numb'] = (!empty($max_numb['max_numb'])) ? ($max_numb['max_numb'] + 1) : 1;

		if($d->insert('gallery',$data_file))
		{
			$id_insert = $d->getLastInsertId();

			if($func->hasFile("file"))
			{
				$photoUpdate = array();

				// Bắt output buffer để tránh alert() output HTML/JS làm hỏng JSON
				ob_start();
				$photo = $func->uploadImage("file", $imgType, '../'.$upload_path[$com], $file_name);
				$alertOutput = ob_get_clean();
				
				// Nếu có output từ alert(), coi như lỗi và xử lý
				if(!empty($alertOutput)) {
					// Xóa record nếu upload file thất bại
					$d->where('id', $id_insert);
					$d->delete('gallery');
					$flag = false;
					// Trích xuất thông báo lỗi từ alert
					if(preg_match('/alert\("([^"]+)"\)/', $alertOutput, $matches)) {
						$data['msg'] = $matches[1];
					} else {
						$data['msg'] = 'Lỗi khi upload file ảnh: ' . strip_tags($alertOutput);
					}
				} elseif($photo)
				{
					$photoUpdate['photo'] = $photo;
					$d->where('id', $id_insert);
					$d->update('gallery', $photoUpdate);
					
					// Trả về dữ liệu thành công
					// Lấy configBase từ global hoặc build từ config
					global $configBase, $http, $configUrl;
					if(empty($configBase)) {
						if(empty($http)) {
							$http = ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ||
								(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
								? 'https://' : 'http://';
						}
						if(empty($configUrl)) {
							$configUrl = $config['database']['server-name'] . $config['database']['url'];
						}
						$configBase = $http . $configUrl;
					}
					
					// Build photo_url - loại bỏ dấu / thừa
					$uploadUrl = $upload_path_url[$com];
					$uploadUrl = ltrim($uploadUrl, '/'); // Loại bỏ / ở đầu nếu có
					$photoUrl = rtrim($configBase, '/') . '/' . $uploadUrl . $photo;
					
					$data = [
						'success' => true, 
						'msg' => 'Upload thành công',
						'data' => [
							'id' => $id_insert,
							'photo' => $photo,
							'photo_url' => $photoUrl
						]
					];
					unset($photoUpdate);
				}
				else
				{
					// Xóa record nếu upload file thất bại
					$d->where('id', $id_insert);
					$d->delete('gallery');
					$flag = false;
					$data['msg'] = 'Lỗi khi upload file ảnh';
				}
			}
			else
			{
				// Xóa record nếu không có file
				$d->where('id', $id_insert);
				$d->delete('gallery');
				$flag = false;
				$data['msg'] = 'Không tìm thấy file';
			}
		}
		else
		{
			$flag = false;
			$data['msg'] = 'Lỗi khi lưu vào database';
		}
	}
	else
	{
		$flag = false;
		$data['msg'] = 'Config không hợp lệ hoặc thiếu img_type';
	}

	if(!$flag && $data['success'] !== true)
	{
		$data = array('success' => false, 'msg' => $data['msg'] ?? 'Upload thất bại');
	}

	// Xóa mọi output buffer trước khi trả về JSON
	ob_clean();
	
	// Đảm bảo chỉ có JSON được output
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
?>