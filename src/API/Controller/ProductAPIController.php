<?php

namespace Tuezy\API\Controller;

use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Service\ProductService;
use Tuezy\Helper\PaginationAjaxHelper;

/**
 * ProductAPIController - Handles product API requests
 */
class ProductAPIController extends BaseAPIController
{
    private ProductService $productService;
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;
    private PaginationAjaxHelper $paginationAjax;

    public function __construct($db, $cache, $func, $config, string $lang = 'vi', string $sluglang = 'slugvi')
    {
        parent::__construct($db, $cache, $func, $config, $lang, $sluglang);

        $this->productRepo = new ProductRepository($db, $cache, $lang, $sluglang, 'san-pham');
        $this->categoryRepo = new CategoryRepository($db, $cache, $lang, $sluglang, 'product');
        $this->productService = new ProductService($this->productRepo, $this->categoryRepo, new \Tuezy\Repository\TagsRepository($db, $cache, $lang, $sluglang), $db, $lang);
        
        // Initialize pagination helper
        $pagingAjax = new \PaginationsAjax();
        $this->paginationAjax = new PaginationAjaxHelper($pagingAjax, $func);
    }

    /**
     * Get product list
     * 
     * @return void Outputs JSON
     */
    public function getList(): void
    {
        $perPage = (int)$this->get('perpage', 12);
        $page = (int)$this->get('p', 1);
        $idList = (int)$this->get('idList', 0);
        $noibat = $this->get('noibat', 'all');
        $eShow = $this->get('eShow', '');

        // Build filters
        $filters = [];
        if ($idList > 0) {
            $filters['id_list'] = $idList;
        }
        if ($noibat !== 'all') {
            $filters['noibat'] = $noibat === 'true' ? 1 : 0;
        }

        // Get products
        $listing = $this->productService->getListing('san-pham', $filters, $page, $perPage);

        // Generate pagination HTML
        $paginationHtml = $this->paginationAjax->generate(
            $listing['total'],
            $perPage,
            $page,
            $eShow
        );

        $this->success([
            'products' => $listing['items'],
            'total' => $listing['total'],
            'pagination' => $paginationHtml,
        ]);
    }

    /**
     * Get product detail
     * 
     * @param int $id Product ID
     * @return void Outputs JSON
     */
    public function getDetail(int $id): void
    {
        if ($id <= 0) {
            $this->error('Invalid product ID');
            return;
        }

        $detailContext = $this->productService->getDetailContext($id, 'san-pham');

        if (!$detailContext) {
            $this->error('Product not found', 404);
            return;
        }

        $this->success([
            'product' => $detailContext['detail'],
            'tags' => $detailContext['tags'] ?? [],
            'colors' => $detailContext['colors'] ?? [],
            'sizes' => $detailContext['sizes'] ?? [],
            'photos' => $detailContext['photos'] ?? [],
            'related' => $detailContext['related'] ?? [],
        ]);
    }

    /**
     * Get quick view
     * 
     * @param int $id Product ID
     * @return void Outputs HTML or JSON
     */
    public function quickView(int $id): void
    {
        if ($id <= 0) {
            $this->error('Invalid product ID');
            return;
        }

        $detailContext = $this->productService->getDetailContext($id, 'san-pham');

        if (!$detailContext) {
            $this->error('Product not found', 404);
            return;
        }

        // Return HTML for quick view modal
        // This can be refactored to use ViewRenderer
        $product = $detailContext['detail'];
        include TEMPLATE . 'components/quickview.php';
        exit;
    }
}

