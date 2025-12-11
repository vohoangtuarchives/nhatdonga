<?php

namespace Tuezy\Controller;

use Tuezy\Service\VideoService;
use Tuezy\Repository\PhotoRepository;
use Tuezy\Repository\SeoRepository;

class VideoController extends BaseController
{
    private VideoService $videoService;
    private PhotoRepository $photoRepo;

    public function __construct($db, $cache, $func, $seo, array $config)
    {
        parent::__construct($db, $cache, $func, $seo, $config);

        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->photoRepo = new PhotoRepository($db, $lang, $sluglang);
        $this->videoService = new VideoService($this->photoRepo, $db);
    }

    public function index(): array
    {
        $type = 'video';
        $filters = [];
        $curPage = $this->paginationHelper->getCurrentPage();
        $perPage = 10;
        $start = $this->paginationHelper->getStartPoint($curPage, $perPage);

        $videoList = $this->videoService->getVideoList($type, $filters, $start, $perPage);
        $totalItems = $this->videoService->countVideos($type, $filters);

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($totalItems, $url, '');

        // SEO
        $titleMain = 'Video'; // Default or from config
        // Use SEOHelper setup logic internally or replicated
        $this->seoHelper->setupFromSeopage($type, $titleMain);

        // Breadcrumbs
        $this->breadcrumbHelper->add($titleMain, '/video');

        return [
            'video' => $videoList,
            'paging' => $paging,
            'titleMain' => $titleMain,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
        ];
    }
}
