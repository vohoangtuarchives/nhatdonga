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
    protected $db;
    private string $lang;

    public function __construct(
        $db,
        $cache,
        $func,
        $seo,
        array $config,
        string $type = 'san-pham'
    ) {
        parent::__construct($db, $cache, $func, $seo, $config);

        $this->db = $db;
        $this->lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->productRepo = new ProductRepository($db, $cache, $this->lang, $sluglang, $type);
        $this->categoryRepo = new CategoryRepository($db, $cache, $this->lang, $sluglang, 'product');
        $this->tagsRepo = new TagsRepository($db, $cache, $this->lang, $sluglang);
        $this->productService = new ProductService($this->productRepo, $this->categoryRepo, $this->tagsRepo, $db, $this->lang);
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
        $detailContext = (new \Tuezy\Application\Catalog\GetProductDetail($this->productRepo, $this->categoryRepo, $this->tagsRepo, $this->db, $this->lang))->execute($id, $type, true, true);

        if (!$detailContext) {
            // Return empty data structure instead of exit to allow template to handle
            return [
                'detail' => null,
                'rowDetail' => null, // Template expects $rowDetail variable
                'tags' => [],
                'rowTags' => [], // Template expects $rowTags variable
                'colors' => [],
                'sizes' => [],
                'list' => null,
                'cat' => null,
                'item' => null,
                'sub' => null,
                'brand' => null,
                'photos' => [],
                'rowDetailPhoto' => [], // Template expects $rowDetailPhoto
                'related' => [],
                'breadcrumbs' => ''
            ];
        }

        $rowDetail = $detailContext['detail'];
        $lang = $this->lang;
        $seolang = 'vi';
        $productEntity = (new \Tuezy\Application\Catalog\GetProductDetailEntity($this->productRepo))->execute($id, $type, true);

        // SEO
        $seoMeta = (new \Tuezy\Application\SEO\GetSeoMetaByParentVo(new \Tuezy\Repository\SeoRepository($this->db)))->execute($rowDetail['id'], 'product', 'man', $rowDetail['type'], $seolang);
        $seoDB = $seoMeta ? [
            'title' . $seolang => $seoMeta->title,
            'keywords' . $seolang => $seoMeta->keywords,
            'description' . $seolang => $seoMeta->description,
        ] : [];
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
        $this->seo->set('h1', $productEntity ? $productEntity->name : $rowDetail['name' . $lang]);
        $this->seo->set('url', $this->func->getPageURL());
        $this->seo->set('canonical', $this->func->getPageURL());
        $this->seo->set('robots', 'index,follow');

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
            'tags' => $detailContext['tags'] ?? [],
            'rowTags' => $detailContext['tags'] ?? [], // Template expects $rowTags variable
            'colors' => $detailContext['colors'] ?? [],
            'sizes' => $detailContext['sizes'] ?? [],
            'list' => $detailContext['list'] ?? null,
            'cat' => $detailContext['cat'] ?? null,
            'item' => $detailContext['item'] ?? null,
            'sub' => $detailContext['sub'] ?? null,
            'brand' => $detailContext['brand'] ?? null,
            'photos' => $detailContext['photos'] ?? [],
            'rowDetailPhoto' => $detailContext['photos'] ?? [], // Template expects $rowDetailPhoto
            'related' => $detailContext['related'] ?? [],
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'dto' => $detailContext['dto'] ?? null,
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
    public function index(string $type = 'san-pham', array $filters = [], int $page = 1, int $perPage = 12, string $sortBy = 'default', string $sortOrder = 'desc'): array
    {
        $listResult = (new \Tuezy\Application\Catalog\ListProducts($this->productRepo))->execute($type, $filters, $page, $perPage, $sortBy, $sortOrder, true);

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);

        // Get categories for sidebar
        $productLists = $this->categoryRepo->getLists($type, true, false);
        $categoriesTree = [];
        foreach ($productLists as $list) {
            $cats = $this->categoryRepo->getCats($type, $list['id'], true);
            $categoriesTree[] = [
                'list' => $list,
                'cats' => $cats
            ];
        }

        // Get brands for filter
        $brands = $this->productRepo->getBrands($type);

        // Get titleMain from global or use default constant
        $titleMain = $GLOBALS['titleMain'] ?? null;
        // If titleMain is 'sanpham' or empty, use constant
        if (empty($titleMain) || $titleMain === 'sanpham') {
            $titleMain = null; // Will use constant in template
        }

        // Breadcrumbs
        if (!empty($titleMain)) {
            $moduleSlug = '/' . $type;
            $this->breadcrumbHelper->add($titleMain, $moduleSlug);
        }

        $productsVo = array_map(fn($r) => $this->mapProductListItem($r), $listResult['items'] ?? []);

        return [
            'products' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'categoriesTree' => $categoriesTree,
            'brands' => $brands,
            'titleMain' => $titleMain,
            'dto' => $listResult['dto'],
            'productsVo' => $productsVo,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
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
        $seoMeta = (new \Tuezy\Application\SEO\GetSeoMetaByParentVo(new \Tuezy\Repository\SeoRepository($this->db)))->execute($category['id'], 'product', 'man_cat', $category['type'], $seolang);
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

        // Breadcrumbs
        $sluglang = 'slugvi';
        if (!empty($GLOBALS['titleMain'])) {
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], '/san-pham');
        }
        $this->breadcrumbHelper->add($category['name' . $lang], $category[$sluglang]);

        // Get products in category
        $listResult = (new \Tuezy\Application\Catalog\ListProductsByHierarchy($this->productRepo))->execute($type, 'cat', $id, $page, $perPage);

        // Pagination
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);

        // Get categories for sidebar
        $productLists = $this->categoryRepo->getLists($type, true, false);
        $categoriesTree = [];
        foreach ($productLists as $list) {
            $cats = $this->categoryRepo->getCats($type, $list['id'], true);
            $categoriesTree[] = [
                'list' => $list,
                'cats' => $cats
            ];
        }

        // Get brands for filter
        $brands = $this->productRepo->getBrands($type);

        $productsVo = array_map(fn($r) => $this->mapProductListItem($r), $listResult['items'] ?? []);

        return [
            'category' => $category,
            'products' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'categoriesTree' => $categoriesTree,
            'brands' => $brands,
            'dto' => $listResult['dto'],
            'productsVo' => $productsVo,
        ];
    }

    public function list(int $id, string $type = 'san-pham', int $page = 1, int $perPage = 12): array
    {
        $category = $this->categoryRepo->getListById($id, $type);
        if (!$category) {
            header('HTTP/1.0 404 Not Found', true, 404);
            include("404.php");
            exit;
        }

        $lang = $this->lang;
        $seolang = 'vi';

        $seoMeta = (new \Tuezy\Application\SEO\GetSeoMetaByParentVo(new \Tuezy\Repository\SeoRepository($this->db)))->execute($category['id'], 'product', 'man_list', $category['type'], $seolang);
        $this->seo->set('h1', $category['name' . $lang]);
        if ($seoMeta && $seoMeta->title) { $this->seo->set('title', $seoMeta->title); } else { $this->seo->set('title', $category['name' . $lang]); }
        if ($seoMeta && $seoMeta->keywords) { $this->seo->set('keywords', $seoMeta->keywords); }
        if ($seoMeta && $seoMeta->description) { $this->seo->set('description', $seoMeta->description); }
        $this->seo->set('url', $this->func->getPageURL());

        $sluglang = 'slugvi';
        if (!empty($GLOBALS['titleMain'])) { 
            $moduleSlug = '/' . $type;
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], $moduleSlug); 
        }
        $this->breadcrumbHelper->add($category['name' . $lang], $category[$sluglang]);

        $listResult = (new \Tuezy\Application\Catalog\ListProductsByHierarchy($this->productRepo))->execute($type, 'list', $id, $page, $perPage);

        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);

        $productsVo = array_map(fn($r) => $this->mapProductListItem($r), $listResult['items'] ?? []);

        return [
            'category' => $category,
            'products' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'dto' => $listResult['dto'],
            'productsVo' => $productsVo,
        ];
    }

    public function item(int $id, string $type = 'san-pham', int $page = 1, int $perPage = 12): array
    {
        $category = $this->categoryRepo->getItemById($id, $type);
        if (!$category) {
            header('HTTP/1.0 404 Not Found', true, 404);
            include("404.php");
            exit;
        }
        $lang = $this->lang; $sluglang = 'slugvi';
        if (!empty($GLOBALS['titleMain'])) { 
            $moduleSlug = '/' . $type;
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], $moduleSlug); 
        }
        $this->breadcrumbHelper->add($category['name' . $lang], $category[$sluglang]);
        $listResult = (new \Tuezy\Application\Catalog\ListProductsByHierarchy($this->productRepo))->execute($type, 'item', $id, $page, $perPage);
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);
        $productsVo = array_map(fn($r) => $this->mapProductListItem($r), $listResult['items'] ?? []);

        return [
            'category' => $category,
            'products' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'dto' => $listResult['dto'],
            'productsVo' => $productsVo,
        ];
    }

    public function sub(int $id, string $type = 'san-pham', int $page = 1, int $perPage = 12): array
    {
        $category = $this->categoryRepo->getSubById($id, $type);
        if (!$category) {
            header('HTTP/1.0 404 Not Found', true, 404);
            include("404.php");
            exit;
        }
        $lang = $this->lang; $sluglang = 'slugvi';
        if (!empty($GLOBALS['titleMain'])) { 
            $moduleSlug = '/' . $type;
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], $moduleSlug); 
        }
        $this->breadcrumbHelper->add($category['name' . $lang], $category[$sluglang]);
        $listResult = (new \Tuezy\Application\Catalog\ListProductsByHierarchy($this->productRepo))->execute($type, 'sub', $id, $page, $perPage);
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);
        $productsVo = array_map(fn($r) => $this->mapProductListItem($r), $listResult['items'] ?? []);

        return [
            'category' => $category,
            'products' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'dto' => $listResult['dto'],
            'productsVo' => $productsVo,
        ];
    }

    public function brand(int $id, string $type = 'san-pham', int $page = 1, int $perPage = 12): array
    {
        $brand = $this->productRepo->getBrandById($id, $type);
        if (!$brand) { header('HTTP/1.0 404 Not Found', true, 404); include("404.php"); exit; }
        $lang = $this->lang; $sluglang = 'slugvi';
        $seolang = 'vi';
        $seoMeta = (new \Tuezy\Application\SEO\GetSeoMetaByParentVo(new \Tuezy\Repository\SeoRepository($this->db)))->execute($brand['id'], 'product', 'man_brand', $type, $seolang);
        $this->seo->set('h1', $brand['name' . $lang]);
        if ($seoMeta && $seoMeta->title) { $this->seo->set('title', $seoMeta->title); } else { $this->seo->set('title', $brand['name' . $lang]); }
        if ($seoMeta && $seoMeta->keywords) { $this->seo->set('keywords', $seoMeta->keywords); }
        if ($seoMeta && $seoMeta->description) { $this->seo->set('description', $seoMeta->description); }
        if (!empty($GLOBALS['titleMain'])) { 
            $moduleSlug = '/' . $type;
            $this->breadcrumbHelper->add($GLOBALS['titleMain'], $moduleSlug); 
        }
        $this->breadcrumbHelper->add($brand['name' . $lang], $brand[$sluglang]);
        $listResult = (new \Tuezy\Application\Catalog\ListProductsByHierarchy($this->productRepo))->execute($type, 'brand', $id, $page, $perPage);
        $url = $this->func->getCurrentPageURL();
        $paging = $this->paginationHelper->getPagination($listResult['total'], $url, '', $perPage);
        return [
            'brand' => $brand,
            'products' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'breadcrumbs' => $this->breadcrumbHelper->render(),
            'dto' => $listResult['dto'],
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

        // Get categories for sidebar
        $productLists = $this->categoryRepo->getLists($type, true, false);
        $categoriesTree = [];
        foreach ($productLists as $list) {
            $cats = $this->categoryRepo->getCats($type, $list['id'], true);
            $categoriesTree[] = [
                'list' => $list,
                'cats' => $cats
            ];
        }

        // Get brands for filter
        $brands = $this->productRepo->getBrands($type);

        $productsVo = array_map(fn($r) => $this->mapProductListItem($r), $listResult['items'] ?? []);

        return [
            'keyword' => $keyword,
            'products' => $listResult['items'],
            'total' => $listResult['total'],
            'paging' => $paging,
            'categoriesTree' => $categoriesTree,
            'brands' => $brands,
            'productsVo' => $productsVo,
        ];
    }

    /**
     * Display product tag page
     * 
     * @return array View data
     */
    public function tags(): array
    {
        $id = (int)($_GET['id'] ?? 0);
        $type = 'san-pham'; // Default for product controller tags
        
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
        $idTags = $this->db->rawQuery("select id_parent from #_product_tags where id_tags = ?", array($id));
        $idTags = (!empty($idTags)) ? $this->func->joinCols($idTags, 'id_parent') : '';

        $curPage = $this->paginationHelper->getCurrentPage();
        $perPage = 12;
        $start = $this->paginationHelper->getStartPoint($curPage, $perPage);

        // Get products by IDs
        $items = [];
        $totalItems = 0;
        
        if (!empty($idTags)) {
            $ids = explode(',', $idTags);
            foreach ($ids as $productId) {
                $product = $this->productRepo->getProductDetail((int)$productId, $type);
                if ($product) {
                    $items[] = $product;
                }
            }
            $totalItems = count($items);
            // Paginate manually since we fetched all compatible items (could be optimized with WHERE IN query in Repo but this follows port logic)
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

        $productsVo = array_map(fn($r) => $this->mapProductListItem($r), $items ?? []);
        
        // Return standard view data for product template
        return [
            'product' => $items, 
            'paging' => $paging,
            'titleMain' => $titleMain,
            'breadcrumbs' => $breadcrumbs,
            'productsVo' => $productsVo,
        ];
    }

    private function mapProductListItem(array $r): \Tuezy\Domain\Catalog\ProductListItem
    {
        $name = (string)($r['name' . $this->lang] ?? '');
        $slug = (string)($r['slugvi'] ?? $r['slug' . $this->lang] ?? '');
        $photo = (string)($r['photo'] ?? '');
        $sale = isset($r['sale_price']) ? (float)$r['sale_price'] : null;
        $regular = isset($r['regular_price']) ? (float)$r['regular_price'] : null;
        $discount = isset($r['discount']) ? (int)$r['discount'] : null;
        return new \Tuezy\Domain\Catalog\ProductListItem((int)$r['id'], $name, $slug, $photo, $sale, $regular, $discount);
    }
}

