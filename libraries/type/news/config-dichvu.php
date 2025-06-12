<?php
/* Tin tức */
$nametype = "dich-vu";
$config['news'][$nametype]['title_main'] = "Dịch Vụ";
$config['news'][$nametype]['dropdown'] = true;
$config['news'][$nametype]['list'] = false;
$config['news'][$nametype]['cat'] = false;
$config['news'][$nametype]['item'] = false;
$config['news'][$nametype]['sub'] = false;
$config['news'][$nametype]['tags'] = false;
$config['news'][$nametype]['view'] = true;
$config['news'][$nametype]['copy'] = true;
$config['news'][$nametype]['copy_image'] = false;
$config['news'][$nametype]['comment'] = false;
$config['news'][$nametype]['slug'] = true;
$config['news'][$nametype]['schema'] = true;
$config['news'][$nametype]['check'] = array("noibat" => "Nổi bật", "hienthi" => "Hiển thị");
$config['news'][$nametype]['images'] = true;
$config['news'][$nametype]['show_images'] = true;
//$config['news'][$nametype]['gallery'] = array
//(
//    $nametype => array
//    (
//        "title_main_photo" => "Hình ảnh Tin tức",
//        "title_sub_photo" => "Hình ảnh",
//        "check_photo" => array("hienthi" => "Hiển thị"),
//        "number_photo" => 3,
//        "images_photo" => true,
//        "avatar_photo" => true,
//        "name_photo" => true,
//        "width_photo" => 540,
//        "height_photo" => 540,
//        "thumb_photo" => '100x100x1',
//        "img_type_photo" => '.jpg|.gif|.png|.jpeg|.gif'
//    ),
//    "video" => array
//    (
//        "title_main_photo" => "Video Tin tức",
//        "title_sub_photo" => "Video",
//        "check_photo" => array("hienthi" => "Hiển thị"),
//        "number_photo" => 2,
//        "video_photo" => true,
//        "name_photo" => true
//    ),
//    "taptin" => array
//    (
//        "title_main_photo" => "Tập tin Tin tức",
//        "title_sub_photo" => "Tập tin",
//        "check_photo" => array("hienthi" => "Hiển thị"),
//        "number_photo" => 2,
//        "file_photo" => true,
//        "name_photo" => true,
//        "file_type_photo" => '.doc|.docx|.pdf|.rar|.zip|.ppt|.pptx|.xls|.xlsx|.jpg|.png|.gif'
//    )
//);
$config['news'][$nametype]['desc'] = true;
$config['news'][$nametype]['content'] = true;
$config['news'][$nametype]['content_cke'] = true;
$config['news'][$nametype]['seo'] = true;
$config['news'][$nametype]['width'] = 640;
$config['news'][$nametype]['height'] = 425;
$config['news'][$nametype]['thumb'] = '100x100x1';
$config['news'][$nametype]['img_type'] = '.jpg|.gif|.png|.jpeg|.gif';

/* Tin tức (List) */
$config['news'][$nametype]['title_main_list'] = "Tin tức cấp 1";
$config['news'][$nametype]['images_list'] = true;
$config['news'][$nametype]['show_images_list'] = true;
$config['news'][$nametype]['slug_list'] = true;
$config['news'][$nametype]['check_list'] = array("noibat" => "Nổi bật", "hienthi" => "Hiển thị");
$config['news'][$nametype]['gallery_list'] = array(
    $nametype => array(
        "title_main_photo" => "Hình ảnh Tin tức cấp 1",
        "title_sub_photo" => "Hình ảnh",
        "check_photo" => array("hienthi" => "Hiển thị"),
        "number_photo" => 2,
        "images_photo" => true,
        "avatar_photo" => true,
        "name_photo" => true,
        "width_photo" => 320,
        "height_photo" => 240,
        "thumb_photo" => '100x100x1',
        "img_type_photo" => '.jpg|.gif|.png|.jpeg|.gif',
    ),
    "video" => array(
        "title_main_photo" => "Video Tin tức cấp 1",
        "title_sub_photo" => "Video",
        "check_photo" => array("hienthi" => "Hiển thị"),
        "number_photo" => 2,
        "video_photo" => true,
        "name_photo" => true
    )
);
$config['news'][$nametype]['desc_list'] = true;
$config['news'][$nametype]['desc_cke_list'] = true;
$config['news'][$nametype]['content_list'] = true;
$config['news'][$nametype]['content_cke_list'] = true;
$config['news'][$nametype]['seo_list'] = true;
$config['news'][$nametype]['width_list'] = 320;
$config['news'][$nametype]['height_list'] = 240;
$config['news'][$nametype]['thumb_list'] = '100x100x1';
$config['news'][$nametype]['img_type_list'] = '.jpg|.gif|.png|.jpeg|.gif';

/* Tin tức (Cat) */
$config['news'][$nametype]['title_main_cat'] = "Tin tức cấp 2";
$config['news'][$nametype]['images_cat'] = true;
$config['news'][$nametype]['show_images_cat'] = true;
$config['news'][$nametype]['slug_cat'] = true;
$config['news'][$nametype]['check_cat'] = array("noibat" => "Nổi bật", "hienthi" => "Hiển thị");
$config['news'][$nametype]['desc_cat'] = true;
$config['news'][$nametype]['desc_cke_cat'] = true;
$config['news'][$nametype]['content_cat'] = true;
$config['news'][$nametype]['content_cke_cat'] = true;
$config['news'][$nametype]['seo_cat'] = true;
$config['news'][$nametype]['width_cat'] = 320;
$config['news'][$nametype]['height_cat'] = 240;
$config['news'][$nametype]['thumb_cat'] = '100x100x1';
$config['news'][$nametype]['img_type_cat'] = '.jpg|.gif|.png|.jpeg|.gif';

/* Tin tức (Item) */
$config['news'][$nametype]['title_main_item'] = "Tin tức cấp 3";
$config['news'][$nametype]['images_item'] = true;
$config['news'][$nametype]['show_images_item'] = true;
$config['news'][$nametype]['slug_item'] = true;
$config['news'][$nametype]['check_item'] = array("noibat" => "Nổi bật", "hienthi" => "Hiển thị");
$config['news'][$nametype]['desc_item'] = true;
$config['news'][$nametype]['desc_cke_item'] = true;
$config['news'][$nametype]['content_item'] = true;
$config['news'][$nametype]['content_cke_item'] = true;
$config['news'][$nametype]['seo_item'] = true;
$config['news'][$nametype]['width_item'] = 320;
$config['news'][$nametype]['height_item'] = 240;
$config['news'][$nametype]['thumb_item'] = '100x100x1';
$config['news'][$nametype]['img_type_item'] = '.jpg|.gif|.png|.jpeg|.gif';

/* Tin tức (Sub) */
$config['news'][$nametype]['title_main_sub'] = "Tin tức cấp 4";
$config['news'][$nametype]['images_sub'] = true;
$config['news'][$nametype]['show_images_sub'] = true;
$config['news'][$nametype]['slug_sub'] = true;
$config['news'][$nametype]['check_sub'] = array("noibat" => "Nổi bật", "hienthi" => "Hiển thị");
$config['news'][$nametype]['desc_sub'] = true;
$config['news'][$nametype]['desc_cke_sub'] = true;
$config['news'][$nametype]['content_sub'] = true;
$config['news'][$nametype]['content_cke_sub'] = true;
$config['news'][$nametype]['seo_sub'] = true;
$config['news'][$nametype]['width_sub'] = 320;
$config['news'][$nametype]['height_sub'] = 240;
$config['news'][$nametype]['thumb_sub'] = '100x100x1';
$config['news'][$nametype]['img_type_sub'] = '.jpg|.gif|.png|.jpeg|.gif';
