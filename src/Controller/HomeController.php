<?php

namespace Tuezy\Controller;

use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\PhotoRepository;

/**
 * HomeController - Handles homepage requests
 * Quản lý logic cho trang chủ, sử dụng Repository pattern
 */
class HomeController extends BaseController
{
    private ProductRepository $productRepo;
    private CategoryRepository $categoryRepo;
    private PhotoRepository $photoRepo;

    public function __construct($db, $cache, $func, $seo, array $config)
    {
        parent::__construct($db, $cache, $func, $seo, $config);
        
        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';
        
        // Khởi tạo repositories
        $this->productRepo = new ProductRepository($db, $cache, $lang, $sluglang, 'san-pham');
        $this->categoryRepo = new CategoryRepository($db, $cache, $lang, $sluglang, 'product');
        $this->photoRepo = new PhotoRepository($db, $lang);
    }

    /**
     * Display homepage
     * 
     * @return array View data
     */
    public function index(): array
    {
        // Lấy dữ liệu qua Repository
        $featuredProducts = $this->productRepo->getFeaturedProducts(12);
        $productCategories = $this->categoryRepo->getLists('san-pham', true, false);
        
        // Lấy tối đa 8 danh mục đầu tiên
        $productCategories = array_slice($productCategories, 0, 8);
        
        // Lấy chứng nhận
        $certificates = $this->photoRepo->getByType('chung-nhan', 6);
        
        // Lấy sản phẩm theo danh mục (lấy 2 danh mục đầu tiên)
        $categoryProducts = [];
        foreach (array_slice($productCategories, 0, 2) as $category) {
            $categoryProducts[$category['id']] = [
                'info' => $category,
                'products' => $this->productRepo->getProductsByCategory($category['id'], 8)
            ];
        }

        // Setup SEO
        $seolang = 'vi';
        $seoDB = $this->seo->getOnDB(0, 'setting', 'update', 'setting');
        
        if (!empty($seoDB['title' . $seolang])) {
            $this->seo->set('title', $seoDB['title' . $seolang]);
            $this->seo->set('h1', $seoDB['title' . $seolang]);
        }
        
        if (!empty($seoDB['keywords' . $seolang])) {
            $this->seo->set('keywords', $seoDB['keywords' . $seolang]);
        }
        
        if (!empty($seoDB['description' . $seolang])) {
            $this->seo->set('description', $seoDB['description' . $seolang]);
        }
        
        $this->seo->set('url', $this->func->getPageURL());

        // Return data cho view
        return [
            'featuredProducts' => $featuredProducts ?? [],
            'productCategories' => $productCategories ?? [],
            'categoryProducts' => $categoryProducts ?? [],
            'certificates' => $certificates ?? [],
            'slider' => $this->photoRepo->getSlider(),
        ];
    }
}

