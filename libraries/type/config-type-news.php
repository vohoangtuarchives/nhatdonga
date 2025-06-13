<?php

include "news/config-tintuc.php";

include "news/config-dichvu.php";



$config['news']['tuyen-dung'] = array_merge(include "news/config-news-no-category.php", [

    'title_main' => 'Tuyển Dụng',

    'width' => 320,

    'height' => 240,

]);

$config['news']['dich-vu'] = array_merge(include "news/config-news-no-category.php", [

    'title_main' => 'Dịch Vụ',

    'check' => ["hienthi" => "Hiển thị", "noibat" => "Nổi Bật"],

    'width' => 320,

    'height' => 240,

]);

// $config['news']['kien-thuc'] = array_merge(include "news/config-news-no-category.php", [

//     'title_main' => 'Kiến Thức',

//     'check' => ["hienthi" => "Hiển thị", "noibat" => "Nổi Bật"],

//     'width' => 320,

//     'height' => 240,

// ]);

// $config['news']['bac-si'] = array_merge(include "news/config-news-no-category.php", [

//     'title_main' => 'Đội ngũ bác sĩ',

//     'check' => ["hienthi" => "Hiển thị", "noibat" => "Nổi Bật"],

//     'width' => 320,

//     'height' => 240,

// ]);

// $config['news']['tuyen-dung'] = array_merge(include "news/config-news-no-category.php", [

//     'title_main' => 'Tuyển Dụng',

//     'width' => 320,

//     'height' => 240,

// ]);

// $config['news']['tieu-chi'] = array_merge(include "news/config-news-no-category.php", [

//     'title_main' => 'Tiêu chí',

//     'check' => ["hienthi" => "Hiển thị"],

//     'width' => 50,

//     'height' => 60,

//     'slug' => false,

//     'copy' => false,

//     'content' => false,

//     'desc' => false,

//     'schema' => false,

//     'seo' => false,

// ]);



// $config['news']['kien-thuc'] = array_merge(include "news/config-news-no-category.php", [

//     'title_main' => 'Kiến Thức',

//     'check' => ["hienthi" => "Hiển thị", "noibat" => "Nổi Bật"],

//     'width' => 320,

//     'height' => 240,

// ]);



$config['news']['tai-sao-chon'] = array_merge(include "news/config-news-no-category.php", [

    'title_main' => 'Tại Sao Chọn',

    'width' => 65,

    'height' => 65,

    'slug' => false,

    'copy' => false,

    'images' => true,

    'check' => ["hienthi" => "Hiển thị", "noibat" => "Nổi Bật"],

]);



$config['news']['khach-hang'] = array_merge(include "news/config-news-no-category.php", [

    'title_main' => 'Khách Hàng',

    'width' => 105,

    'height' => 105,

    'slug' => false,

    'content' => false,

    'copy' => TRUE,

    'images' => true,

    'career' => true,

    'check' => ["hienthi" => "Hiển thị", "noibat" => "Nổi Bật"],

]);



$config['news']['catalogue'] = array_merge(include "news/config-news-no-category.php", [

    'title_main' => 'Catalogue',

    'check' => ["hienthi" => "Hiển thị", "noibat" => "Nổi Bật"],

    'width' => 260,

    'height' => 260,

    'slug' => true,

    'copy' => false,

    'images' => false,

]);

// $config['news']['ket-qua'] = array_merge(include "news/config-news-no-category.php", [

//     'title_main' => 'Kết quả',

//     'check' => ["hienthi" => "Hiển thị", "noibat" => "Nổi Bật"],

//     'width' => 880,

//     'height' => 470,

//     'slug' => true,

//     'copy' => false,

//     'slug' => false,

//     'copy' => false,

//     'content' => false,

//     'desc' => false,

//     'schema' => false,

//     'seo' => false,

// ]);

$config['news']['chinh-sach'] = array_merge(include "news/config-news-no-category.php", [

    'title_main' => 'Chính Sách',

    'width' => 320,

    'height' => 240,

    'slug' => true,

    'copy' => false,

    'images' => false,

]);



/* Quản lý mục (Không cấp) */

if (isset($config['news'])) {

    foreach ($config['news'] as $key => $value) {

        if (!isset($value['dropdown']) || (isset($value['dropdown']) && $value['dropdown'] == false)) {

            $config['shownews'] = 1;

            break;

        }

    }

}

