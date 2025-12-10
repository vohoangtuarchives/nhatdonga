<?php
namespace Tuezy\Libraries;

if (!\class_exists('\\PDODb')) {
    require \LIBRARIES . 'class/class.PDODb.php';
}

\class_alias('\\PDODb', __NAMESPACE__.'\\PDODb');
