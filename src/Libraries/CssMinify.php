<?php
namespace Tuezy\Libraries;

if (!\class_exists('\\CssMinify')) {
    require \LIBRARIES . 'class/class.CssMinify.php';
}

\class_alias('\\CssMinify', __NAMESPACE__.'\\CssMinify');
