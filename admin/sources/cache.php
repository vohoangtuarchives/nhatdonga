<?php

if (!defined('SOURCES')) die("Error");

switch($act) {
	case "delete":
		if ($cache->delete()) {
			$func->transfer("Xóa cache thành công", "index.php");
		} else {
			$func->transfer("Xóa cache thất bại", "index.php", false);
		}
		break;

	default:
		$template = "404";
}

