<?php
namespace Tuezy\Libraries;

if (!\class_exists('\\MobileDetect')) {
    require \LIBRARIES . 'class/class.MobileDetect.php';
}

\class_alias('\\MobileDetect', __NAMESPACE__.'\\MobileDetect');
