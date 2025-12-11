<!-- Css Files -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5">

<?php

$css->set("css/animate.min.css");

//$css->set("bootstrap/bootstrap.css");

$css->set("bootstrap-5.2.3/css/bootstrap.min.css");

$css->set("fontawesome512/all.css");

$css->set("confirm/confirm.css");

$css->set("holdon/HoldOn.css");

$css->set("holdon/HoldOn-style.css");

$css->set("mmenu/mmenu.css");

$css->set("fancybox3/jquery.fancybox.css");

$css->set("fancybox3/jquery.fancybox.style.css");

$css->set("photobox/photobox.css");

$css->set("slick/slick.css");

$css->set("slick/slick-theme.css");

$css->set("slick/slick-style.css");

$css->set("magiczoomplus/magiczoomplus.css");

$css->set("datetimepicker/jquery.datetimepicker.css");

$css->set("owlcarousel2/owl.carousel.css");

$css->set("owlcarousel2/owl.theme.default.css");

$css->set("simplenotify/simple-notify.css");

$css->set("fileuploader/font-fileuploader.css");

$css->set("fileuploader/jquery.fileuploader.min.css");

$css->set("fileuploader/jquery.fileuploader-theme-dragdrop.css");

$css->set("flipster/jquery.flipster.css");

$css->set("flipster/flipsternavtabs.css");

$css->set("fotorama/fotorama.css");

$css->set("fotorama/fotorama-style.css");

$css->set("css/main.css");

$css->set("css/custom.css");

echo $css->get();

?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<link rel="canonical" href="<?= $seo->get('canonical') ?: $seo->get('url') ?>" />
<?php if (!empty($config['website']['lang']) && is_array($config['website']['lang'])) { 
    foreach ($config['website']['lang'] as $code => $name) { 
        $altUrl = rtrim($config['database']['url'], '/') . '/' . ($com ?? '') . '/' . $code . '/'; ?>
<link rel="alternate" href="<?= $altUrl ?>" hreflang="<?= $code ?>" />
<?php } } ?>
<meta name="robots" content="<?= $seo->get('robots') ?: 'index,follow' ?>" />
<!-- Background -->

<?php

//$bgbody = $d->rawQueryOne("select status, options, photo from #_photo where act = ? and type = ? limit 0,1", array('photo_static', 'background'));

//if (!empty($bgbody['status']) && strstr($bgbody['status'], 'hienthi')) {

//    $bgbodyOptions = json_decode($bgbody['options'], true)['background'];

//    if ($bgbodyOptions['type_show']) {

//        echo '<style type="text/css">body{background: url(' . UPLOAD_PHOTO_L . $bgbody['photo'] . ') ' . $bgbodyOptions['repeat'] . ' ' . $bgbodyOptions['position'] . ' ' . $bgbodyOptions['attachment'] . ' ;background-size:' . $bgbodyOptions['size'] . '}</style>';

//    } else {

//        echo ' <style type="text/css">body{background-color:#' . $bgbodyOptions['color'] . '}</style>';

//    }

//}

?>



<?php if (isset($config['coppy']['lock']) && $config['coppy']['lock'] == true) { ?>

    <style type="text/css">

        body {

            -webkit-touch-callout: none;

            -webkit-user-select: none;

            -moz-user-select: none;

            -ms-user-select: none;

            -o-user-select: none;

            user-select: none;

        }

    </style>

<?php } ?>



<!-- Js Google Analytic -->

<?= !empty($setting['analytics']) ? htmlspecialchars_decode($setting['analytics']) : '' ?>
<?php if (empty($setting['analytics']) && !empty($config['googleAPI']['analytics']['id'])) { ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= $config['googleAPI']['analytics']['id'] ?>"></script>
<script>
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= $config['googleAPI']['analytics']['id'] ?>');
</script>
<?php } ?>



<!-- Js Head -->

<?= !empty($setting['headjs']) ? htmlspecialchars_decode($setting['headjs']) : '' ?>
