<?php


include "config.php";

use Tuezy\Repository\PhotoRepository;
use Tuezy\Service\VideoService;
use Tuezy\Config;
use Tuezy\SecurityHelper;

// Initialize Config
$configObj = new Config($config);

// Initialize Repositories & Service
$photoRepo = new PhotoRepository($d, $cache, $lang, $sluglang);
$videoService = new VideoService($photoRepo, $d);

// Get video ID
$id = (int)SecurityHelper::sanitizePost('id', 0);

if ($id) {
	// Get video - Sử dụng VideoService
	$video = $videoService->getVideoDetail($id);
	
	if (!empty($video['link_video'])) {
		$youtubeId = $func->getYoutube($video['link_video']);
		?>
		<iframe width="100%" height="100%" src="//www.youtube.com/embed/<?=$youtubeId?>" frameborder="0" allowfullscreen></iframe>
		<?php
	}
}