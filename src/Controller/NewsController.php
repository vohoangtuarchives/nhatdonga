<?php

namespace Tuezy\Controller;

use Tuezy\Repository\NewsRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\TagsRepository;
use Tuezy\Service\NewsService;

/**
 * NewsController - Handles news-related requests
 */
class NewsController extends BaseController
{
    private NewsService $newsService;
    private NewsRepository $newsRepo;
    private CategoryRepository $categoryRepo;
    private TagsRepository $tagsRepo;
    protected $db;
    private string $lang;

    public function __construct(
        $db,
        $cache,
        $func,
        $seo,
        array $config,
        string $type = 'tin-tuc'
    ) {
        parent::__construct($db, $cache, $func, $seo, $config);

        $this->db = $db;
        $this->lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->newsRepo = new NewsRepository($db, $this->lang, $type);
        $this->categoryRepo = new CategoryRepository($db, $cache, $this->lang, $sluglang, 'news');
        $this->tagsRepo = new TagsRepository($db, $cache, $this->lang, $sluglang);
        $this->newsService = new NewsService($this->newsRepo, $this->categoryRepo, $db, $this->lang, $sluglang);
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
        $newsContext = (new \Tuezy\Application\Content\GetArticleDetail($this->newsRepo))->execute($id, $type, true);
        $articleEntity = (new \Tuezy\Application\Content\GetArticleDetailEntity($this->newsRepo))->execute($id);

        if (!$newsContext) {
            header('HTTP/1.0 404 Not Found', true, 404);
            include("404.php");
            exit;
        }

        $rowDetail = $newsContext['detail'];
        $lang = $this->lang;
        $seolang = 'vi';
        $sluglang = 'slugvi';

        // SEO
        $seoMeta = (new \Tuezy\Application\SEO\GetSeoMetaByParentVo(new \Tuezy\Repository\SeoRepository($this->db)))->execute($rowDetail['id'], 'news', 'man', $rowDetail['type'], $seolang);
        $seoDB = $seoMeta ? [
            'title' . $seolang => $seoMeta->title,
            'keywords' . $seolang => $seoMeta->keywords,
            'description' . $seolang => $seoMeta->description,
        ] : [];
        $this->seo->set('h1', $articleEntity ? $articleEntity->name : $rowDetail['name' . $lang]);
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
        if (empty($this->seo->get('description'))) {
            $src = $rowDetail['desc' . $lang] ?? $rowDetail['content' . $lang] ?? '';
            $src = strip_tags($src);
            $src = preg_replace('/\s+/', ' ', $src);
            $this->seo->set('description', mb_substr($src, 0, 160));
        }
        $this->seo->set('url', $this->func->getPageURL());
        $this->seo->set('canonical', $this->func->getPageURL());
        $this->seo->set('robots', 'index,follow');

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
            $moduleSlug = '/' . ($type ?? 'tin-tuc');
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], $moduleSlug);
        }
        $sluglang = 'slugvi';
        $listItem = null; $catItem = null; $itemItem = null; $subItem = null;
        if (!empty($rowDetail['id_list'])) {
            $link = $this->categoryRepo->getListLinkById((int)$rowDetail['id_list'], $type);
            if ($link) $this->breadcrumbHelper->add($link->name, $link->slug);
        }
        if (!empty($rowDetail['id_cat'])) {
            $link = $this->categoryRepo->getCatLinkById((int)$rowDetail['id_cat'], $type);
            if ($link) $this->breadcrumbHelper->add($link->name, $link->slug);
        }
        if (!empty($rowDetail['id_item'])) {
            $link = $this->categoryRepo->getItemLinkById((int)$rowDetail['id_item'], $type);
            if ($link) $this->breadcrumbHelper->add($link->name, $link->slug);
        }
        if (!empty($rowDetail['id_sub'])) {
            $link = $this->categoryRepo->getSubLinkById((int)$rowDetail['id_sub'], $type);
            if ($link) $this->breadcrumbHelper->add($link->name, $link->slug);
        }
        $this->breadcrumbHelper->add($rowDetail['name' . $lang], $rowDetail[$sluglang]);

        return [
            'detail' => $rowDetail,
            'rowDetail' => $rowDetail, // Template expects $rowDetail variable
            'list' => $listItem,
            'cat' => $catItem,
            'item' => $itemItem,
            'sub' => $subItem,
            'photos' => $newsContext['photos'] ?? [],
            'related' => $newsContext['related'] ?? [],
            'otherNewss' => $newsContext['related'] ?? [], // Template expects $otherNewss variable
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
        $listResult = (new \Tuezy\Application\Content\ListArticles($this->newsRepo))->execute($type, $filters, $page, $perPage);

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '');

        // Get titleMain from global or use default constant
        $titleMain = $GLOBALS['titleMain'] ?? null;
        // If titleMain is 'tintuc' or empty, use constant
        if (empty($titleMain) || $titleMain === 'tintuc') {
            $titleMain = null; // Will use constant in template
        }

        // Breadcrumbs
        if (!empty($titleMain)) {
            $moduleSlug = '/' . $type;
            $this->breadcrumbHelper->add($titleMain, $moduleSlug);
        }

        $articlesVo = array_map(function ($r) {
            $name = (string)($r['name' . $this->lang] ?? '');
            $slug = (string)($r['slugvi'] ?? $r['slug' . $this->lang] ?? '');
            $photo = (string)($r['photo'] ?? '');
            return new \Tuezy\Domain\Content\ArticleListItem((int)$r['id'], $name, $slug, $photo);
        }, $listResult['items'] ?? []);

        return [
            'news' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'titleMain' => $titleMain,
            'articlesVo' => $articlesVo,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
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
        $seoMeta = (new \Tuezy\Application\SEO\GetSeoMetaByParentVo(new \Tuezy\Repository\SeoRepository($this->db)))->execute($category['id'], 'news', 'man_list', $category['type'], $seolang);
        $this->seo->set('h1', $category['name' . $lang]);
        if ($seoMeta && $seoMeta->title) {
            $this->seo->set('title', $seoMeta->title);
        } else {
            $this->seo->set('title', $category['name' . $lang]);
        }
        if ($seoMeta && $seoMeta->keywords) {
            $this->seo->set('keywords', $seoMeta->keywords);
        }
        if ($seoMeta && $seoMeta->description) {
            $this->seo->set('description', $seoMeta->description);
        }
        $this->seo->set('url', $this->func->getPageURL());
        $this->seo->set('canonical', $this->func->getPageURL());
        $this->seo->set('robots', 'index,follow');

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
            $moduleSlug = '/' . $type;
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], $moduleSlug);
        }
        $this->breadcrumbHelper->add($category['name' . $lang], $category[$sluglang]);

        // Get news in category
        $filters = ['id_list' => $id];
        $listResult = $this->newsService->getListing($type, $filters, $page, $perPage);

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '');

        $articlesVo = array_map(function ($r) {
            $name = (string)($r['name' . $this->lang] ?? '');
            $slug = (string)($r['slugvi'] ?? $r['slug' . $this->lang] ?? '');
            $photo = (string)($r['photo'] ?? '');
            return new \Tuezy\Domain\Content\ArticleListItem((int)$r['id'], $name, $slug, $photo);
        }, $listResult['items'] ?? []);

        return [
            'category' => $category,
            'news' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'dto' => $listResult['dto'],
            'articlesVo' => $articlesVo,
        ];
    }

    public function cat(int $id, string $type = 'tin-tuc', int $page = 1, int $perPage = 12): array
    {
        $category = $this->categoryRepo->getCatById($id, $type);
        if (!$category) { header('HTTP/1.0 404 Not Found', true, 404); include("404.php"); exit; }
        $lang = $this->lang; $sluglang = 'slugvi';
        $seolang = 'vi';
        if (!empty($GLOBALS['titleMain'])) { 
            $moduleSlug = '/' . $type;
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], $moduleSlug); 
        }
        $this->breadcrumbHelper->add($category['name' . $lang], $category[$sluglang]);
        $seoMeta = (new \Tuezy\Application\SEO\GetSeoMetaByParentVo(new \Tuezy\Repository\SeoRepository($this->db)))->execute($category['id'], 'news', 'man_cat', $category['type'], $seolang);
        $this->seo->set('h1', $category['name' . $lang]);
        if ($seoMeta && $seoMeta->title) { $this->seo->set('title', $seoMeta->title); } else { $this->seo->set('title', $category['name' . $lang]); }
        if ($seoMeta && $seoMeta->keywords) { $this->seo->set('keywords', $seoMeta->keywords); }
        if ($seoMeta && $seoMeta->description) { $this->seo->set('description', $seoMeta->description); }
        $listResult = (new \Tuezy\Application\Content\ListArticlesByHierarchy($this->newsRepo))->execute($type, 'cat', $id, $page, $perPage);
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);
        $articlesVo = array_map(function ($r) {
            $name = (string)($r['name' . $this->lang] ?? '');
            $slug = (string)($r['slugvi'] ?? $r['slug' . $this->lang] ?? '');
            $photo = (string)($r['photo'] ?? '');
            return new \Tuezy\Domain\Content\ArticleListItem((int)$r['id'], $name, $slug, $photo);
        }, $listResult['items'] ?? []);

        return [
            'category' => $category,
            'news' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'dto' => $listResult['dto'],
            'articlesVo' => $articlesVo,
        ];
    }

    public function item(int $id, string $type = 'tin-tuc', int $page = 1, int $perPage = 12): array
    {
        $category = $this->categoryRepo->getItemById($id, $type);
        if (!$category) { header('HTTP/1.0 404 Not Found', true, 404); include("404.php"); exit; }
        $lang = $this->lang; $sluglang = 'slugvi';
        $seolang = 'vi';
        if (!empty($GLOBALS['titleMain'])) { 
            $moduleSlug = '/' . $type;
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], $moduleSlug); 
        }
        $this->breadcrumbHelper->add($category['name' . $lang], $category[$sluglang]);
        $seoMeta = (new \Tuezy\Application\SEO\GetSeoMetaByParentVo(new \Tuezy\Repository\SeoRepository($this->db)))->execute($category['id'], 'news', 'man_item', $category['type'], $seolang);
        $this->seo->set('h1', $category['name' . $lang]);
        if ($seoMeta && $seoMeta->title) { $this->seo->set('title', $seoMeta->title); } else { $this->seo->set('title', $category['name' . $lang]); }
        if ($seoMeta && $seoMeta->keywords) { $this->seo->set('keywords', $seoMeta->keywords); }
        if ($seoMeta && $seoMeta->description) { $this->seo->set('description', $seoMeta->description); }
        $listResult = (new \Tuezy\Application\Content\ListArticlesByHierarchy($this->newsRepo))->execute($type, 'item', $id, $page, $perPage);
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);
        $articlesVo = array_map(function ($r) {
            $name = (string)($r['name' . $this->lang] ?? '');
            $slug = (string)($r['slugvi'] ?? $r['slug' . $this->lang] ?? '');
            $photo = (string)($r['photo'] ?? '');
            return new \Tuezy\Domain\Content\ArticleListItem((int)$r['id'], $name, $slug, $photo);
        }, $listResult['items'] ?? []);

        return [
            'category' => $category,
            'news' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'dto' => $listResult['dto'],
            'articlesVo' => $articlesVo,
        ];
    }

    public function sub(int $id, string $type = 'tin-tuc', int $page = 1, int $perPage = 12): array
    {
        $category = $this->categoryRepo->getSubById($id, $type);
        if (!$category) { header('HTTP/1.0 404 Not Found', true, 404); include("404.php"); exit; }
        $lang = $this->lang; $sluglang = 'slugvi';
        $seolang = 'vi';
        if (!empty($GLOBALS['titleMain'])) { 
            $moduleSlug = '/' . $type;
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], $moduleSlug); 
        }
        $this->breadcrumbHelper->add($category['name' . $lang], $category[$sluglang]);
        $seoMeta = (new \Tuezy\Application\SEO\GetSeoMetaByParentVo(new \Tuezy\Repository\SeoRepository($this->db)))->execute($category['id'], 'news', 'man_sub', $category['type'], $seolang);
        $this->seo->set('h1', $category['name' . $lang]);
        if ($seoMeta && $seoMeta->title) { $this->seo->set('title', $seoMeta->title); } else { $this->seo->set('title', $category['name' . $lang]); }
        if ($seoMeta && $seoMeta->keywords) { $this->seo->set('keywords', $seoMeta->keywords); }
        if ($seoMeta && $seoMeta->description) { $this->seo->set('description', $seoMeta->description); }
        $listResult = (new \Tuezy\Application\Content\ListArticlesByHierarchy($this->newsRepo))->execute($type, 'sub', $id, $page, $perPage);
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);
        $articlesVo = array_map(function ($r) {
            $name = (string)($r['name' . $this->lang] ?? '');
            $slug = (string)($r['slugvi'] ?? $r['slug' . $this->lang] ?? '');
            $photo = (string)($r['photo'] ?? '');
            return new \Tuezy\Domain\Content\ArticleListItem((int)$r['id'], $name, $slug, $photo);
        }, $listResult['items'] ?? []);

        return [
            'category' => $category,
            'news' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'dto' => $listResult['dto'],
            'articlesVo' => $articlesVo,
        ];
    }

    /**
     * Display news tag page
     * 
     * @return array View data
     */
    public function tags(): array
    {
        $id = (int)($_GET['id'] ?? 0);
        $type = 'tin-tuc'; // Default for news controller tags
        
        if ($id <= 0) {
            header('HTTP/1.0 404 Not Found', true, 404);
            include("404.php");
            exit;
        }

        /* Lấy tag detail */
        $tags_detail = $this->tagsRepo->getById($id, $type);
        
        if (!$tags_detail) {
            header('HTTP/1.0 404 Not Found', true, 404);
            include("404.php");
            exit;
        }

        /* Lấy items by tag */
        $idTags = $this->db->rawQuery("select id_parent from #_news_tags where id_tags = ?", array($id));
        $idTags = (!empty($idTags)) ? $this->func->joinCols($idTags, 'id_parent') : '';

        $curPage = $this->paginationHelper->getCurrentPage();
        $perPage = 12;
        $start = $this->paginationHelper->getStartPoint($curPage, $perPage);

        // Get news by IDs
        $items = [];
        $totalItems = 0;
        
        if (!empty($idTags)) {
            $ids = explode(',', $idTags);
            foreach ($ids as $newsId) {
                // NewsRepository doesn't have getNewsDetail matching exact ProductRepository signature usually
                // But context-based detail fetch or getOne?
                // Checking NewsController::detail -> uses GetArticleDetail -> newsRepo
                // Let's use getNewsDetail if it exists in Repo, or raw query or existing method.
                // In legacy tags.php: $newsItem = $newsRepo->getNewsDetail((int)$newsId, $type);
                // I should assume getNewsDetail exists on Repo if tags.php used it (assuming tags.php was correct legacy reference?)
                // Actually tags.php I saw was "REFACTORED VERSION" created by me or previous session? 
                // Ah, I created sources/tags.php in previous session? No, I read it. The file header said "REFACTORED VERSION".
                // So NewsRepository should have getNewsDetail.
                
                // Let's verify NewsRepository methods? No time, let's trust tags.php usage.
                
                // Wait, in NewsController::detail I see:
                // $newsContext = (new \Tuezy\Application\Content\GetArticleDetail($this->newsRepo))->execute($id, $type, true);
                // $rowDetail = $newsContext['detail'];
                
                // I can use GetArticleDetail application service again easily.
                
                $newsContext = (new \Tuezy\Application\Content\GetArticleDetail($this->newsRepo))->execute((int)$newsId, $type, true);
                if ($newsContext && !empty($newsContext['detail'])) {
                    $items[] = $newsContext['detail'];
                }
            }
            $totalItems = count($items);
            $items = array_slice($items, $start, $perPage);
        }

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($totalItems, $url, '', $perPage);

        /* SEO */
        $lang = $this->lang;
        $seolang = 'vi';
        $sluglang = 'slugvi';
        
        $titleMain = $tags_detail['name' . $lang];
        $seoDB = $this->seo->getOnDB($tags_detail['id'], 'tags', 'man', $tags_detail['type']);
        
        $this->seo->set('h1', $tags_detail['name' . $lang]);
        
        if (!empty($seoDB['title' . $seolang])) {
            $this->seo->set('title', $seoDB['title' . $seolang]);
        } else {
            $this->seo->set('title', $tags_detail['name' . $lang]);
        }
        
        if (!empty($seoDB['keywords' . $seolang])) {
            $this->seo->set('keywords', $seoDB['keywords' . $seolang]);
        }
        
        if (!empty($seoDB['description' . $seolang])) {
            $this->seo->set('description', $seoDB['description' . $seolang]);
        }
        
        $this->seo->set('url', $this->func->getPageURL());
        
        // Handle SEO image
        $imgJson = (!empty($tags_detail['options'])) ? json_decode($tags_detail['options'], true) : null;
        
        if (empty($imgJson) || ($imgJson['p'] != $tags_detail['photo'])) {
            $imgJson = $this->func->getImgSize($tags_detail['photo'], UPLOAD_TAGS_L . $tags_detail['photo']);
            $this->seo->updateSeoDB(json_encode($imgJson), 'tags', $tags_detail['id']);
        }
        
        if (!empty($imgJson)) {
            $configBase = $this->config['database']['url'] ?? '';
            $this->seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_TAGS_L . $tags_detail['photo']);
            $this->seo->set('photo:width', $imgJson['w']);
            $this->seo->set('photo:height', $imgJson['h']);
            $this->seo->set('photo:type', $imgJson['m']);
        }

        /* Breadcrumbs */
        if (!empty($titleMain)) {
            $this->breadcrumbHelper->add($titleMain, $tags_detail[$sluglang]);
        }
        $breadcrumbs = $this->breadcrumbHelper->render();

        $articlesVo = array_map(function ($r) {
            $name = (string)($r['name' . $this->lang] ?? '');
            $slug = (string)($r['slugvi'] ?? $r['slug' . $this->lang] ?? '');
            $photo = (string)($r['photo'] ?? '');
            return new \Tuezy\Domain\Content\ArticleListItem((int)$r['id'], $name, $slug, $photo);
        }, $items ?? []);

        return [
            'news' => $items,
            'paging' => $paging,
            'titleMain' => $titleMain,
            'breadcrumbs' => $breadcrumbs,
            'articlesVo' => $articlesVo,
        ];
    }
}

