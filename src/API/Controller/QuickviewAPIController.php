<?php

namespace Tuezy\API\Controller;

use Tuezy\Service\ProductService;
use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\TagsRepository;

/**
 * QuickviewAPIController - Handles quickview API requests
 */
class QuickviewAPIController extends BaseAPIController
{
    private ProductService $productService;

    public function __construct($db, $cache, $func, $config, string $lang = 'vi', string $sluglang = 'slugvi')
    {
        parent::__construct($db, $cache, $func, $config, $lang, $sluglang);

        $productRepo = new ProductRepository($db, $cache, $lang, $sluglang, 'san-pham');
        $categoryRepo = new CategoryRepository($db, $cache, $lang, $sluglang, 'product');
        $tagsRepo = new TagsRepository($db, $cache, $lang, $sluglang);
        $this->productService = new ProductService($productRepo, $categoryRepo, $tagsRepo, $db, $lang);
    }

    /**
     * Get quickview data
     * 
     * @param int $id Product ID
     * @return void Outputs HTML
     */
    public function getQuickview(int $id): void
    {
        if ($id <= 0) {
            $this->error('Invalid product ID');
            return;
        }

        $detailContext = $this->productService->getDetailContext($id, 'san-pham', false);

        if (!$detailContext) {
            $this->error('Product not found', 404);
            return;
        }

        // Configuration
        $w = 307;
        $h = 265;
        $r = 1;
        $z = 2;
        $thumbnail = $w * $z . 'x' . $h * $z . 'x' . $r;
        $isWater = false;
        $assets = $isWater ? WATERMARK . '/product' : THUMBS;

        // Extract data for template
        $rowDetail = $detailContext['detail'];
        $rowDetailPhoto = $detailContext['photos'];
        $rowColor = $detailContext['colors'];
        $rowSize = $detailContext['sizes'];

        // Make variables available to template
        $lang = $this->lang;
        $configBase = $this->config->get('database.url', '');
        $config = $this->config->all();

        // Include quickview template
        include TEMPLATE . "product/quickview.php";
        exit;
    }
}

