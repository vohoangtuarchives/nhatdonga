<?php
namespace Tuezy\Libraries;

if (!\class_exists('\\JsMinify')) {
    require \LIBRARIES . 'class/class.JsMinify.php';
}

\class_alias('\\JsMinify', __NAMESPACE__.'\\JsMinify');
