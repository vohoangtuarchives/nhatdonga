<?php


include "config.php";

use Tuezy\Repository\PhotoRepository;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories
$photoRepo = new PhotoRepository($d, $cache, $lang, $sluglang);

// Get video ID
$id = (int)($_POST['id'] ?? 0);

if ($id) {
	// Get video - Sử dụng PhotoRepository
	$video = $photoRepo->getById($id);
	
	if (!empty($video['link_video'])) {
		$youtubeId = $func->getYoutube($video['link_video']);
		?>
		<iframe width="100%" height="100%" src="//www.youtube.com/embed/<?=$youtubeId?>" frameborder="0" allowfullscreen></iframe>
		<?php
	}
}