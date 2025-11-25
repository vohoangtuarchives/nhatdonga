<?php

namespace Tuezy\Controller;

use Tuezy\Repository\NewsRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Service\NewsService;

/**
 * NewsController - Handles news-related requests
 */
class NewsController extends BaseController
{
    private NewsService $newsService;
    private NewsRepository $newsRepo;
    private CategoryRepository $categoryRepo;

    public function __construct(
        $db,
        $cache,
        $func,
        $seo,
        array $config,
        string $type = 'tin-tuc'
    ) {
        parent::__construct($db, $cache, $func, $seo, $config);

        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->newsRepo = new NewsRepository($db, $lang, $type);
        $this->categoryRepo = new CategoryRepository($db, $cache, $lang, $sluglang, 'news');
        $this->newsService = new NewsService($this->newsRepo, $this->categoryRepo, $db, $lang, $sluglang);
    }

    /**
     * Display news detail page
     * 
     * @param int $id News ID
     * @param string $type News type
     * @return array View data
     */
    public function detail(int $id, string $type = 'tin-tuc'): array
    {
        $newsContext = $this->newsService->getDetailContext($id, $type, true);

        if (!$newsContext) {
            header('HTTP/1.0 404 Not Found', true, 404);
            include("404.php");
            exit;
        }

        $rowDetail = $newsContext['detail'];
        $lang = $_SESSION['lang'] ?? 'vi';
        $seolang = 'vi';
        $sluglang = 'slugvi';

        // SEO
        $seoDB = $this->seo->getOnDB($rowDetail['id'], 'news', 'man', $rowDetail['type']);
        $this->seo->set('h1', $rowDetail['name' . $lang]);
        if (!empty($seoDB['title' . $seolang])) {
            $this->seo->set('title', $seoDB['title' . $seolang]);
        } else {
            $this->seo->set('title', $rowDetail['name' . $lang]);
        }
        if (!empty($seoDB['keywords' . $seolang])) {
            $this->seo->set('keywords', $seoDB['keywords' . $seolang]);
        }
        if (!empty($seoDB['description' . $seolang])) {
            $this->seo->set('description', $seoDB['description' . $seolang]);
        }
        $this->seo->set('url', $this->func->getPageURL());

        // Handle SEO image
        $imgJson = (!empty($rowDetail['options'])) ? json_decode($rowDetail['options'], true) : null;
        if (empty($imgJson) || ($imgJson['p'] != $rowDetail['photo'])) {
            $imgJson = $this->func->getImgSize($rowDetail['photo'], UPLOAD_NEWS_L . $rowDetail['photo']);
            $this->seo->updateSeoDB(json_encode($imgJson), 'news', $rowDetail['id']);
        }
        if (!empty($imgJson)) {
            $configBase = $this->config['database']['url'] ?? '';
            $this->seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_NEWS_L . $rowDetail['photo']);
            $this->seo->set('photo:width', $imgJson['w']);
            $this->seo->set('photo:height', $imgJson['h']);
            $this->seo->set('photo:type', $imgJson['m']);
        }

        // Breadcrumbs
        if (!empty($GLOBALS['titleMain'])) {
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], '/tin-tuc');
        }
        if (!empty($newsContext['list'])) {
            $this->breadcrumbHelper->add($newsContext['list']['name' . $lang], $newsContext['list'][$sluglang]);
        }
        if (!empty($newsContext['cat'])) {
            $this->breadcrumbHelper->add($newsContext['cat']['name' . $lang], $newsContext['cat'][$sluglang]);
        }
        if (!empty($newsContext['item'])) {
            $this->breadcrumbHelper->add($newsContext['item']['name' . $lang], $newsContext['item'][$sluglang]);
        }
        if (!empty($newsContext['sub'])) {
            $this->breadcrumbHelper->add($newsContext['sub']['name' . $lang], $newsContext['sub'][$sluglang]);
        }
        $this->breadcrumbHelper->add($rowDetail['name' . $lang], $rowDetail[$sluglang]);

        return [
            'detail' => $rowDetail,
            'list' => $newsContext['list'] ?? null,
            'cat' => $newsContext['cat'] ?? null,
            'item' => $newsContext['item'] ?? null,
            'sub' => $newsContext['sub'] ?? null,
            'photos' => $newsContext['photos'] ?? [],
            'related' => $newsContext['related'] ?? [],
            'breadcrumbs' => $this->breadcrumbHelper->render(),
        ];
    }

    /**
     * Display news listing page
     * 
     * @param string $type News type
     * @param array $filters Filters (id_list, keyword)
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function index(string $type = 'tin-tuc', array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $listResult = $this->newsService->getListing($type, $filters, $page, $perPage);

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '');

        return [
            'news' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
        ];
    }

    /**
     * Display news category page
     * 
     * @param int $id Category ID
     * @param string $type News type
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function category(int $id, string $type = 'tin-tuc', int $page = 1, int $perPage = 12): array
    {
        $category = $this->categoryRepo->getListById($id, $type);

        if (!$category) {
            header('HTTP/1.0 404 Not Found', true, 404);
            include("404.php");
            exit;
        }

        $lang = $_SESSION['lang'] ?? 'vi';
        $seolang = 'vi';
        $sluglang = 'slugvi';

        // SEO
        $seoDB = $this->seo->getOnDB($category['id'], 'news', 'man_list', $category['type']);
        $this->seo->set('h1', $category['name' . $lang]);
        if (!empty($seoDB['title' . $seolang])) {
            $this->seo->set('title', $seoDB['title' . $seolang]);
        } else {
            $this->seo->set('title', $category['name' . $lang]);
        }
        if (!empty($seoDB['keywords' . $seolang])) {
            $this->seo->set('keywords', $seoDB['keywords' . $seolang]);
        }
        if (!empty($seoDB['description' . $seolang])) {
            $this->seo->set('description', $seoDB['description' . $seolang]);
        }
        $this->seo->set('url', $this->func->getPageURL());

        // Handle SEO image if exists
        if (!empty($category['photo'])) {
            $imgJson = (!empty($category['options'])) ? json_decode($category['options'], true) : null;
            if (empty($imgJson) || ($imgJson['p'] != $category['photo'])) {
                $imgJson = $this->func->getImgSize($category['photo'], UPLOAD_NEWS_L . $category['photo']);
                $this->seo->updateSeoDB(json_encode($imgJson), 'news_list', $category['id']);
            }
            if (!empty($imgJson)) {
                $configBase = $this->config['database']['url'] ?? '';
                $this->seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_NEWS_L . $category['photo']);
                $this->seo->set('photo:width', $imgJson['w']);
                $this->seo->set('photo:height', $imgJson['h']);
                $this->seo->set('photo:type', $imgJson['m']);
            }
        }

        // Breadcrumbs
        if (!empty($GLOBALS['titleMain'])) {
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], '/tin-tuc');
        }
        $this->breadcrumbHelper->add($category['name' . $lang], $category[$sluglang]);

        // Get news in category
        $filters = ['id_list' => $id];
        $listResult = $this->newsService->getListing($type, $filters, $page, $perPage);

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '');

        return [
            'category' => $category,
            'news' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
        ];
    }
}

