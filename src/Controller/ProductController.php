<?php

namespace Tuezy\Controller;

use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\TagsRepository;
use Tuezy\Service\ProductService;

/**
 * ProductController - Handles product-related requests
 */
class ProductController extends BaseController
{
    private ProductService $productService;
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;
    private TagsRepository $tagsRepo;

    public function __construct(
        $db,
        $cache,
        $func,
        $seo,
        array $config,
        string $type = 'san-pham'
    ) {
        parent::__construct($db, $cache, $func, $seo, $config);

        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->productRepo = new ProductRepository($db, $cache, $lang, $sluglang, $type);
        $this->categoryRepo = new CategoryRepository($db, $cache, $lang, $sluglang, 'product');
        $this->tagsRepo = new TagsRepository($db, $cache, $lang, $sluglang);
        $this->productService = new ProductService($this->productRepo, $this->categoryRepo, $this->tagsRepo, $db, $lang);
    }

    /**
     * Display product detail page
     * 
     * @param int $id Product ID
     * @param string $type Product type
     * @return array View data
     */
    public function detail(int $id, string $type = 'san-pham'): array
    {
        $detailContext = $this->productService->getDetailContext($id, $type);

        if (!$detailContext) {
            // Return empty data structure instead of exit to allow template to handle
            return [
                'detail' => null,
                'tags' => [],
                'colors' => [],
                'sizes' => [],
                'list' => null,
                'cat' => null,
                'item' => null,
                'sub' => null,
                'brand' => null,
                'photos' => [],
                'related' => [],
                'breadcrumbs' => ''
            ];
        }

        $rowDetail = $detailContext['detail'];
        $lang = $_SESSION['lang'] ?? 'vi';
        $seolang = 'vi';

        // SEO
        $seoDB = $this->seo->getOnDB($rowDetail['id'], 'product', 'man', $rowDetail['type']);
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
        $this->seo->set('h1', $rowDetail['name' . $lang]);
        $this->seo->set('url', $this->func->getPageURL());

        // Handle SEO image
        $imgJson = (!empty($rowDetail['options'])) ? json_decode($rowDetail['options'], true) : null;
        if (empty($imgJson) || ($imgJson['p'] != $rowDetail['photo'])) {
            $imgJson = $this->func->getImgSize($rowDetail['photo'], UPLOAD_PRODUCT_L . $rowDetail['photo']);
            $this->seo->updateSeoDB(json_encode($imgJson), 'product', $rowDetail['id']);
        }
        if (!empty($imgJson)) {
            $configBase = $this->config['database']['url'] ?? '';
            $this->seo->set('photo', $configBase . THUMBS . '/' . $imgJson['w'] . 'x' . $imgJson['h'] . 'x2/' . UPLOAD_PRODUCT_L . $rowDetail['photo']);
            $this->seo->set('photo:width', $imgJson['w']);
            $this->seo->set('photo:height', $imgJson['h']);
            $this->seo->set('photo:type', $imgJson['m']);
        }

        // Breadcrumbs
        $sluglang = 'slugvi';
        if (!empty($GLOBALS['titleMain'])) {
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], '/san-pham');
        }
        if (!empty($detailContext['list'])) {
            $this->breadcrumbHelper->add($detailContext['list']['name' . $lang], $detailContext['list'][$sluglang]);
        }
        if (!empty($detailContext['cat'])) {
            $this->breadcrumbHelper->add($detailContext['cat']['name' . $lang], $detailContext['cat'][$sluglang]);
        }
        if (!empty($detailContext['item'])) {
            $this->breadcrumbHelper->add($detailContext['item']['name' . $lang], $detailContext['item'][$sluglang]);
        }
        if (!empty($detailContext['sub'])) {
            $this->breadcrumbHelper->add($detailContext['sub']['name' . $lang], $detailContext['sub'][$sluglang]);
        }
        $this->breadcrumbHelper->add($rowDetail['name' . $lang], $rowDetail[$sluglang]);

        return [
            'detail' => $rowDetail,
            'tags' => $detailContext['tags'] ?? [],
            'colors' => $detailContext['colors'] ?? [],
            'sizes' => $detailContext['sizes'] ?? [],
            'list' => $detailContext['list'] ?? null,
            'cat' => $detailContext['cat'] ?? null,
            'item' => $detailContext['item'] ?? null,
            'sub' => $detailContext['sub'] ?? null,
            'brand' => $detailContext['brand'] ?? null,
            'photos' => $detailContext['photos'] ?? [],
            'related' => $detailContext['related'] ?? [],
            'breadcrumbs' => $this->breadcrumbHelper->render(),
        ];
    }

    /**
     * Display product listing page
     * 
     * @param string $type Product type
     * @param array $filters Filters (id_list, id_cat, id_item, id_sub, id_brand, keyword, status)
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function index(string $type = 'san-pham', array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $listResult = $this->productService->getListing($type, $filters, $page, $perPage);

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);

        return [
            'products' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
        ];
    }

    /**
     * Display product category page
     * 
     * @param int $id Category ID
     * @param string $type Product type
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function category(int $id, string $type = 'san-pham', int $page = 1, int $perPage = 12): array
    {
        $category = $this->categoryRepo->getById($id, $type);
        
        if (!$category) {
            header('HTTP/1.0 404 Not Found', true, 404);
            include("404.php");
            exit;
        }

        $lang = $_SESSION['lang'] ?? 'vi';
        $seolang = 'vi';

        // SEO
        $seoDB = $this->seo->getOnDB($category['id'], 'product', 'man_cat', $category['type']);
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

        // Breadcrumbs
        $sluglang = 'slugvi';
        if (!empty($GLOBALS['titleMain'])) {
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], '/san-pham');
        }
        $this->breadcrumbHelper->add($category['name' . $lang], $category[$sluglang]);

        // Get products in category
        $filters = ['id_cat' => $id];
        $listResult = $this->productService->getListing($type, $filters, $page, $perPage);

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);

        return [
            'category' => $category,
            'products' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
        ];
    }

    /**
     * Search products
     * 
     * @param string $keyword Search keyword
     * @param string $type Product type
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function search(string $keyword, string $type = 'san-pham', int $page = 1, int $perPage = 12): array
    {
        $filters = ['keyword' => $keyword];
        $listResult = $this->productService->getListing($type, $filters, $page, $perPage);

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);

        return [
            'keyword' => $keyword,
            'products' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
        ];
    }
}

