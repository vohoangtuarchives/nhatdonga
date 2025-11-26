<?php

namespace Tuezy\API\Controller;

use Tuezy\Service\VideoService;
use Tuezy\Repository\PhotoRepository;

/**
 * VideoAPIController - Handles video API requests
 */
class VideoAPIController extends BaseAPIController
{
    private VideoService $videoService;
    private PhotoRepository $photoRepo;

    public function __construct($db, $cache, $func, $config, string $lang = 'vi', string $sluglang = 'slugvi')
    {
        parent::__construct($db, $cache, $func, $config, $lang, $sluglang);

        $this->photoRepo = new PhotoRepository($db, $lang, $sluglang);
        $this->videoService = new VideoService($this->photoRepo, $db);
    }

    /**
     * Get video embed
     * 
     * @param int $id Video ID
     * @return void Outputs HTML iframe
     */
    public function getVideoEmbed(int $id): void
    {
        if ($id <= 0) {
            return;
        }

        $video = $this->videoService->getVideoDetail($id);

        if (!empty($video['link_video'])) {
            $youtubeId = $this->func->getYoutube($video['link_video']);
            echo '<iframe width="100%" height="100%" src="//www.youtube.com/embed/' . htmlspecialchars($youtubeId) . '" frameborder="0" allowfullscreen></iframe>';
        }
        exit;
    }
}

