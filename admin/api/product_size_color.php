<?php
use Tuezy\Repository\ProductRepository;
use Tuezy\Service\ProductService;

include "config.php";

$currentLang = $lang ?? ($_SESSION['lang'] ?? 'vi');
$currentSlug = $sluglang ?? 'slugvi';
$productRepo = new ProductRepository($d, $cache, $currentLang, $currentSlug, $type);
$productService = new ProductService($productRepo, null, null, $d, $currentLang);
$action = (!empty($_GET['action'])) ? $_GET['action'] : null;
$type = (!empty($_GET['type'])) ? $_GET['type'] : 'san-pham';
$target = (!empty($_GET['target'])) ? $_GET['target'] : array();
$arrTarget = explode(',', $target);
$id_product = (!empty($_GET['id_product'])) ? $_GET['id_product'] : 0;
$id_color = (!empty($_GET['id_color'])) ? $_GET['id_color'] : 0;
$id_size = (!empty($_GET['id_size'])) ? $_GET['id_size'] : 0;
$key = (!empty($_GET['key'])) ? $_GET['key'] : 0;
$price = (!empty($_GET['price'])) ? $_GET['price'] : 0;
$price = str_replace(",", "", $price);
if ($action == "add") {
    $template = '<div class="group-size-color mb-3">';
    $template .= '<div class="row row-size-color">';
    //size
    if (in_array("size", $arrTarget)) {
        $template .= '<div class="form-group col-md-4">';
        $template .= '<label class="form-label">Danh mục kích thước:</label>';
        $template .= '<div class="form-custom">';
        $template .= $func->getSizeSC(0, 'dataSC[' . ($key + 1) . '][size]',$type);
        $template .= '</div>';
        $template .= '</div>';
    }
    //color
    if (in_array("color", $arrTarget)) {
        $template .= '<div class="form-group col-md-4">';
        $template .= '<label class="form-label">Danh mục màu sắc:</label>';
        $template .= '<div class="form-custom">';
        $template .= $func->getColorSC(0, 'dataSC[' . ($key + 1) . '][color]',$type);
        $template .= '</div>';
        $template .= '</div>';
    }

    $template .= '<div class="form-group col-md-4">';
    $template .= '<label class="form-label">Giá:</label>';
    $template .= '<div class="input-group">';
    $template .= '<input type="text" class="form-control format-price price-size-color" placeholder="Giá" name="dataSC[' . ($key + 1) . '][price]">';
    $template .= '<div class="input-group-append">';
    $template .= '<div class="input-group-text"><strong>VNĐ</strong></div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    $template .= '</div>';
    // if($id_product){
    //     $template .='<button type="button" class="btn btn-success save-size-color mr-1" data-id="' . $id_product . '"><i class="fas fa-save mr-2"></i>Lưu</button>';
    // }
    $template .= '<button type="button" class="btn btn-danger cancel-size-color" data-id="' . $id_product . '"><i class="fas fa-minus-circle mr-2"></i>Xóa</button>';
    $template .= '   </div>';
    echo $template;
} elseif ($action == "delete") {
    if ($id_product) {
        $productService->removeSizeColorCombination((int)$id_product, (int)$id_color, (int)$id_size);
    }
} 

// else if ($action == "save") {
//     $data = array();
//     $data['id_product'] = $id_product;
//     $data['id_color'] = $id_color;
//     $data['id_size'] = $id_size;
//     $data['price'] = $price;
//     if ($price) {
//         $check = $d->rawQueryOne("SELECT * from table_product_size_color where id_product = ? and id_color=? and id_size=?", array($id_product, $id_color, $id_size));
//         if ($check) {
//             $d->where('id', $check['id']);
//             $d->update('product_size_color', $data);
//         } else {
//             $d->insert('product_size_color', $data);
//         }
//     }
// }
