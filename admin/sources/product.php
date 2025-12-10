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

// Ensure ROOT is defined
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR);
}

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

        $listing = $productService->getListing($type, $filters, $curPage, 10, 'default', 'desc', false);
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
            $detailContext = $productService->getDetailContext($id, $type, false, false);
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

		// Lấy id từ POST (form có hidden field name="id", không phải name="data[id]")
		// Hoặc từ GET nếu không có trong POST
		$id = null;
		if (!empty($_POST['id'])) {
			$id = (int)$_POST['id'];
		} elseif (!empty($_POST['data']['id'])) {
			// Fallback: kiểm tra trong data nếu có
			$id = (int)$_POST['data']['id'];
		} elseif (!empty($_GET['id']) && ($act == 'save' || $act == 'save_copy')) {
			// Fallback: lấy từ GET nếu không có trong POST (khi edit)
			$id = (int)$_GET['id'];
		}
		
		$data = $_POST['data'] ?? [];
		
		// Loại bỏ 'id' khỏi $data vì id được truyền riêng vào saveProduct
		// Nếu không loại bỏ, có thể gây conflict và tạo row mới thay vì update
		unset($data['id']);
		
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = SecurityHelper::sanitize($value);
            } elseif (is_array($value)) {
                $data[$key] = SecurityHelper::sanitizeArray($value);
            }
        }

        if (!empty($config['website']['slug']) && is_array($config['website']['slug'])) {
            foreach ($config['website']['slug'] as $k => $_) {
                $slugKey = 'slug' . $k;
                if (isset($_POST[$slugKey])) {
                    $data[$slugKey] = SecurityHelper::sanitize($_POST[$slugKey]);
                }
            }
        } else {
            if (isset($_POST['slugvi'])) { $data['slugvi'] = SecurityHelper::sanitize($_POST['slugvi']); }
            if (isset($_POST['slugen'])) { $data['slugen'] = SecurityHelper::sanitize($_POST['slugen']); }
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
            if (method_exists($d, 'beginTransaction')) { $d->beginTransaction(); }
            $productId = $productService->saveProduct($data, $id, $dataSC, $dataTags, $type, $func);

			if ($productId) {
				// Xử lý upload ảnh chính và các ảnh phụ của sản phẩm
				// Field names: "file" (ảnh chính), "file2" (ảnh 2), "file3" (ảnh 3)
				$imgType = $config['product'][$type]['img_type'] ?? '.jpg|.gif|.png|.jpeg|.webp';
				
				// Upload ảnh chính (file -> photo)
				if ($func->hasFile("file")) {
					$file_name = $func->uploadName($_FILES["file"]["name"]);
					
					// Bắt output buffer để tránh alert() output HTML/JS
					ob_start();
					$photo = $func->uploadImage("file", $imgType, UPLOAD_PRODUCT, $file_name);
					$alertOutput = ob_get_clean();
					
					if ($photo) {
						// Xóa ảnh cũ nếu có (khi update)
						if ($id) {
							$oldProduct = $d->rawQueryOne("SELECT photo FROM #_product WHERE id = ? LIMIT 0,1", [$productId]);
							if ($oldProduct && !empty($oldProduct['photo'])) {
								$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
								$filePath = ROOT . $uploadPath . $oldProduct['photo'];
								$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
								if (file_exists($filePath)) {
									$func->deleteFile($filePath);
								}
							}
						}
						
						// Cập nhật ảnh mới vào database
						$d->where('id', $productId);
						$d->update('product', ['photo' => $photo]);
					}
				}
				
				// Upload ảnh 2 (file2 -> photo2)
				if (isset($config['product'][$type]['images2']) && $config['product'][$type]['images2'] == true && $func->hasFile("file2")) {
					$file_name = $func->uploadName($_FILES["file2"]["name"]);
					
					ob_start();
					$photo2 = $func->uploadImage("file2", $imgType, UPLOAD_PRODUCT, $file_name);
					ob_get_clean();
					
					if ($photo2) {
						if ($id) {
							$oldProduct = $d->rawQueryOne("SELECT photo2 FROM #_product WHERE id = ? LIMIT 0,1", [$productId]);
							if ($oldProduct && !empty($oldProduct['photo2'])) {
								$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
								$filePath = ROOT . $uploadPath . $oldProduct['photo2'];
								$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
								if (file_exists($filePath)) {
									$func->deleteFile($filePath);
								}
							}
						}
						
						$d->where('id', $productId);
						$d->update('product', ['photo2' => $photo2]);
					}
				}
				
				// Upload ảnh 3 (file3 -> photo3)
				if (isset($config['product'][$type]['images3']) && $config['product'][$type]['images3'] == true && $func->hasFile("file3")) {
					$file_name = $func->uploadName($_FILES["file3"]["name"]);
					
					ob_start();
					$photo3 = $func->uploadImage("file3", $imgType, UPLOAD_PRODUCT, $file_name);
					ob_get_clean();
					
					if ($photo3) {
						if ($id) {
							$oldProduct = $d->rawQueryOne("SELECT photo3 FROM #_product WHERE id = ? LIMIT 0,1", [$productId]);
							if ($oldProduct && !empty($oldProduct['photo3'])) {
								$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
								$filePath = ROOT . $uploadPath . $oldProduct['photo3'];
								$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
								if (file_exists($filePath)) {
									$func->deleteFile($filePath);
								}
							}
						}
						
						$d->where('id', $productId);
						$d->update('product', ['photo3' => $photo3]);
					}
				}
				
                // Save SEO and Schema
                $dataSeo = $_POST['dataSeo'] ?? [];
                $dataSchema = $_POST['dataSchema'] ?? [];
                if (is_array($dataSeo)) { $dataSeo = SecurityHelper::sanitizeArray($dataSeo); }
                if (is_array($dataSchema)) { $dataSchema = SecurityHelper::sanitizeArray($dataSchema); }
                $seoPayload = array_merge($dataSeo ?: [], $dataSchema ?: []);

                if (!empty($seoPayload)) {
                    $seoRepo = new \Tuezy\Repository\SeoRepository($d);
                    (new \Tuezy\Application\SEO\SaveSeoMeta($seoRepo))->execute($productId, 'product', 'man', $type, $seoPayload);
                }

                // Auto-generate minimal Product schema if empty
                if (empty($seoPayload) || (empty($seoPayload['schemavi']) && empty($seoPayload['schemaen']))) {
                    $row = $d->rawQueryOne("SELECT name{$lang}, desc{$lang}, content{$lang}, photo, code, slug{$lang}, date_created, type FROM #_product WHERE id = ? LIMIT 0,1", [$productId]);
                    if ($row) {
                        $imageUrl = (defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/') . ($row['photo'] ?? '');
                        $schema = json_encode([
                            '@context' => 'https://schema.org',
                            '@type' => 'Product',
                            'name' => $row['name' . $lang] ?? '',
                            'image' => $imageUrl,
                            'description' => strip_tags($row['desc' . $lang] ?? ''),
                            'sku' => $row['code'] ?? ''
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $seoRepo = new \Tuezy\Repository\SeoRepository($d);
                        (new \Tuezy\Application\SEO\SaveSeoMeta($seoRepo))->execute($productId, 'product', 'man', $type, ['schemavi' => $schema]);
                    }
                }

                $message = $id ? "Cập nhật dữ liệu thành công" : "Thêm dữ liệu thành công";
                // Build return URL using AdminURLHelper
                $urlHelper->reset();
                $urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
                $urlHelper->addParam('p', $curPage);
                $returnUrl = $urlHelper->getUrl('product', 'man', $type);
                if (method_exists($d, 'commit')) { $d->commit(); }
                $func->transfer($message, $returnUrl);
            } else {
                if (method_exists($d, 'rollBack')) { $d->rollBack(); }
                $urlHelper->reset();
				$urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
				$urlHelper->addParam('p', $curPage);
				$returnUrl = $urlHelper->getUrl('product', 'man', $type);
				$func->transfer("Có lỗi xảy ra khi lưu dữ liệu", $returnUrl, false);
			}
        } catch (\Exception $e) {
            if (method_exists($d, 'rollBack')) { $d->rollBack(); }
            // Handle slug validation error
            $urlHelper->reset();
			$urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
			$urlHelper->addParam('p', $curPage);
			$returnUrl = $urlHelper->getUrl('product', 'man', $type);
			$func->transfer($e->getMessage(), $returnUrl, false);
		}
		break;

	case "delete":
		// Xử lý xóa nhiều items (listid)
		if (!empty($_GET['listid'])) {
			$listid = SecurityHelper::sanitizeGet('listid', '');
			$ids = explode(',', $listid);
			$ids = array_filter(array_map('intval', $ids)); // Loại bỏ giá trị rỗng và convert sang int
			
			// Build return URL using AdminURLHelper
			$urlHelper->reset();
			$urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
			$urlHelper->addParam('p', $curPage);
			$returnUrl = $urlHelper->getUrl('product', 'man', $type);
			
			if (empty($ids)) {
				$func->transfer("Không nhận được dữ liệu", $returnUrl, false);
			}
			
			$successCount = 0;
			$failedCount = 0;
			
			foreach ($ids as $productId) {
				if ($productId > 0) {
					// Lấy thông tin sản phẩm để xóa ảnh nếu cần
					$product = $d->rawQueryOne("SELECT photo, photo2, photo3 FROM #_product WHERE id = ? AND type = ? LIMIT 0,1", [$productId, $type]);
					
					// Xóa record
					if ($adminCRUD->delete($productId)) {
						// Xóa file ảnh nếu có
						if ($product) {
							if (!empty($product['photo'])) {
								$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
								$filePath = ROOT . $uploadPath . $product['photo'];
								$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
								if (file_exists($filePath)) {
									$func->deleteFile($filePath);
								}
							}
							if (!empty($product['photo2'])) {
								$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
								$filePath = ROOT . $uploadPath . $product['photo2'];
								$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
								if (file_exists($filePath)) {
									$func->deleteFile($filePath);
								}
							}
							if (!empty($product['photo3'])) {
								$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
								$filePath = ROOT . $uploadPath . $product['photo3'];
								$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
								if (file_exists($filePath)) {
									$func->deleteFile($filePath);
								}
							}
						}
						$successCount++;
					} else {
						$failedCount++;
					}
				}
			}
			
			if ($successCount > 0) {
				$message = "Đã xóa thành công {$successCount} sản phẩm";
				if ($failedCount > 0) {
					$message .= " ({$failedCount} sản phẩm xóa thất bại)";
				}
				$func->transfer($message, $returnUrl);
			} else {
				$func->transfer("Xóa dữ liệu thất bại", $returnUrl, false);
			}
		} else {
			// Xóa một item (id)
			$id = (int)($_GET['id'] ?? 0);
			// Build return URL using AdminURLHelper
			$urlHelper->reset();
			$urlHelper->buildFromRequest(['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'], ['comment_status', 'keyword']);
			$urlHelper->addParam('p', $curPage);
			$returnUrl = $urlHelper->getUrl('product', 'man', $type);
			
			if ($id) {
				// Lấy thông tin sản phẩm để xóa ảnh nếu cần
				$product = $d->rawQueryOne("SELECT photo, photo2, photo3 FROM #_product WHERE id = ? AND type = ? LIMIT 0,1", [$id, $type]);
				
				if ($adminCRUD->delete($id)) {
					// Xóa file ảnh nếu có
					if ($product) {
						if (!empty($product['photo'])) {
							$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
							$filePath = ROOT . $uploadPath . $product['photo'];
							$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
							if (file_exists($filePath)) {
								$func->deleteFile($filePath);
							}
						}
						if (!empty($product['photo2'])) {
							$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
							$filePath = ROOT . $uploadPath . $product['photo2'];
							$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
							if (file_exists($filePath)) {
								$func->deleteFile($filePath);
							}
						}
						if (!empty($product['photo3'])) {
							$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
							$filePath = ROOT . $uploadPath . $product['photo3'];
							$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
							if (file_exists($filePath)) {
								$func->deleteFile($filePath);
							}
						}
					}
					$func->transfer("Xóa dữ liệu thành công", $returnUrl);
				} else {
					$func->transfer("Xóa dữ liệu thất bại", $returnUrl, false);
				}
			} else {
				$func->transfer("Không nhận được dữ liệu", $returnUrl, false);
			}
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
		
		// Lấy id từ POST (form có hidden field name="id")
		$id = !empty($_POST['id']) ? (int)$_POST['id'] : (!empty($_POST['data']['id']) ? (int)$_POST['data']['id'] : null);
		$data = $_POST['data'] ?? [];
		$data = SecurityHelper::sanitizeArray($data);
		
		// Loại bỏ id khỏi data vì id được truyền riêng
		unset($data['id']);

		// Gộp slug từ input bên ngoài vào data để lưu DB
		if (isset($_POST['slugvi'])) {
			$data['slugvi'] = SecurityHelper::sanitize($_POST['slugvi']);
		}
		if (isset($_POST['slugen'])) {
			$data['slugen'] = SecurityHelper::sanitize($_POST['slugen']);
		}
		// Tự động tạo slug nếu chưa nhập
		if (empty($data['slugvi']) && !empty($data['namevi'])) {
			$data['slugvi'] = $func->changeTitle($data['namevi']);
		}
		if (empty($data['slugen']) && !empty($data['nameen'])) {
			$data['slugen'] = $func->changeTitle($data['nameen']);
		}
		
		// Xử lý upload ảnh chính (file -> photo)
		$imgType = $config['product'][$type]['img_type_list'] ?? '.jpg|.gif|.png|.jpeg|.webp';
		
		if ($func->hasFile("file")) {
			$file_name = $func->uploadName($_FILES["file"]["name"]);
			
			// Bắt output buffer để tránh alert() output HTML/JS
			ob_start();
			$photo = $func->uploadImage("file", $imgType, UPLOAD_PRODUCT, $file_name);
			ob_get_clean();
			
			if ($photo) {
				// Xóa ảnh cũ nếu có (khi update)
				if ($id) {
					$oldList = $d->rawQueryOne("SELECT photo FROM #_product_list WHERE id = ? LIMIT 0,1", [$id]);
					if ($oldList && !empty($oldList['photo'])) {
						$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
						$filePath = ROOT . $uploadPath . $oldList['photo'];
						$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
						if (file_exists($filePath)) {
							$func->deleteFile($filePath);
						}
					}
				}
				$data['photo'] = $photo;
			}
		}
		
		// Xử lý upload ảnh 2 (file2 -> photo2)
		if (isset($config['product'][$type]['images_list2']) && $config['product'][$type]['images_list2'] == true && $func->hasFile("file2")) {
			$file_name = $func->uploadName($_FILES["file2"]["name"]);
			
			ob_start();
			$photo2 = $func->uploadImage("file2", $imgType, UPLOAD_PRODUCT, $file_name);
			ob_get_clean();
			
			if ($photo2) {
				if ($id) {
					$oldList = $d->rawQueryOne("SELECT photo2 FROM #_product_list WHERE id = ? LIMIT 0,1", [$id]);
					if ($oldList && !empty($oldList['photo2'])) {
						$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
						$filePath = ROOT . $uploadPath . $oldList['photo2'];
						$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
						if (file_exists($filePath)) {
							$func->deleteFile($filePath);
						}
					}
				}
				$data['photo2'] = $photo2;
			}
		}
		
		// Xử lý status (từ array thành string)
		if (isset($_POST['status']) && is_array($_POST['status'])) {
			$data['status'] = implode(',', $_POST['status']);
		} elseif (empty($data['status'])) {
			$data['status'] = 'hienthi';
		}
		
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
		// Xử lý xóa nhiều items (listid)
		if (!empty($_GET['listid'])) {
			$listid = SecurityHelper::sanitizeGet('listid', '');
			$ids = explode(',', $listid);
			$ids = array_filter(array_map('intval', $ids)); // Loại bỏ giá trị rỗng và convert sang int
			
			if (empty($ids)) {
				$func->transfer("Không nhận được dữ liệu", "index.php?com=product&act=man_list&type=" . $type, false);
			}
			
			$successCount = 0;
			$failedCount = 0;
			
			foreach ($ids as $productId) {
				if ($productId > 0) {
					// Lấy thông tin sản phẩm để xóa ảnh nếu cần
					$product = $d->rawQueryOne("SELECT photo, photo2, photo3 FROM #_product WHERE id = ? AND type = ? LIMIT 0,1", [$productId, $type]);
					
					// Xóa record
					if ($listCRUD->delete($productId)) {
						// Xóa file ảnh nếu có
						if ($product) {
						if (!empty($product['photo'])) {
							$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
							$filePath = ROOT . $uploadPath . $product['photo'];
							$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
							if (file_exists($filePath)) {
								$func->deleteFile($filePath);
							}
						}
						if (!empty($product['photo2'])) {
							$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
							$filePath = ROOT . $uploadPath . $product['photo2'];
							$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
							if (file_exists($filePath)) {
								$func->deleteFile($filePath);
							}
						}
						if (!empty($product['photo3'])) {
							$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
							$filePath = ROOT . $uploadPath . $product['photo3'];
							$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
							if (file_exists($filePath)) {
								$func->deleteFile($filePath);
							}
						}
						}
						$successCount++;
					} else {
						$failedCount++;
					}
				}
			}
			
			if ($successCount > 0) {
				$message = "Đã xóa thành công {$successCount} sản phẩm";
				if ($failedCount > 0) {
					$message .= " ({$failedCount} sản phẩm xóa thất bại)";
				}
				$func->transfer($message, "index.php?com=product&act=man_list&type=" . $type);
			} else {
				$func->transfer("Xóa dữ liệu thất bại", "index.php?com=product&act=man_list&type=" . $type, false);
			}
		} else {
			// Xóa một item (id)
			$id = (int)($_GET['id'] ?? 0);
			if ($id) {
				// Lấy thông tin sản phẩm để xóa ảnh nếu cần
				$product = $d->rawQueryOne("SELECT photo, photo2, photo3 FROM #_product WHERE id = ? AND type = ? LIMIT 0,1", [$id, $type]);
				
				if ($listCRUD->delete($id)) {
					// Xóa file ảnh nếu có
					if ($product) {
						if (!empty($product['photo'])) {
							$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
							$filePath = ROOT . $uploadPath . $product['photo'];
							$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
							if (file_exists($filePath)) {
								$func->deleteFile($filePath);
							}
						}
						if (!empty($product['photo2'])) {
							$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
							$filePath = ROOT . $uploadPath . $product['photo2'];
							$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
							if (file_exists($filePath)) {
								$func->deleteFile($filePath);
							}
						}
						if (!empty($product['photo3'])) {
							$uploadPath = defined('UPLOAD_PRODUCT_L') ? UPLOAD_PRODUCT_L : 'upload/product/';
							$filePath = ROOT . $uploadPath . $product['photo3'];
							$filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
							if (file_exists($filePath)) {
								$func->deleteFile($filePath);
							}
						}
					}
					$func->transfer("Xóa dữ liệu thành công", "index.php?com=product&act=man_list&type=" . $type);
				} else {
					$func->transfer("Xóa dữ liệu thất bại", "index.php?com=product&act=man_list&type=" . $type, false);
				}
			} else {
				$func->transfer("Không nhận được dữ liệu", "index.php?com=product&act=man_list&type=" . $type, false);
			}
		}
		break;

	// Các case khác (size, color, brand, cat, item, sub, gallery) giữ nguyên
	// vì có logic riêng phức tạp
	
	default:
		$template = "404";
}


