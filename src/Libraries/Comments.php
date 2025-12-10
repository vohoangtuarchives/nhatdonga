<?php
namespace Tuezy\Libraries;

if (!\class_exists('\\Comments')) {
    require \LIBRARIES . 'class/class.Comments.php';
}

\class_alias('\\Comments', __NAMESPACE__.'\\Comments');
