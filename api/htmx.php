<?php

include "config.php";

if (isset($_SERVER['X-Requested-With']) && $_SERVER['X-Requested-With'] == 'XMLHttpRequest') {

} else {
    throw new Exception("HTMX not support this protocol");
}
