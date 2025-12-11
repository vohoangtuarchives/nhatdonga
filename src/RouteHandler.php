<?php

namespace Tuezy;

/**
 * RouteHandler - Handles route configuration and matching
 * Refactored from large switch statement to configuration-based approach
 */
class RouteHandler
{
    private array $routeConfig;

    public function __construct()
    {
        $this->routeConfig = $this->buildRouteConfig();
    }

    /**
     * Build route configuration array
     * Replaces the large switch statement with a maintainable configuration
     * 
     * @return array
     */
    private function buildRouteConfig(): array
    {
        return [
            'lien-he' => [
                'controller' => \Tuezy\Controller\ContactController::class,
                'action' => 'index',
                'template' => 'contact/contact',
                'seoType' => 'object',
                'titleMain' => 'lienhe',
            ],
            'gioi-thieu' => [
                'controller' => \Tuezy\Controller\StaticController::class,
                'action' => 'index',
                'template' => 'static/static',
                'seoType' => 'article',
                'titleMain' => 'gioithieu',
                'type' => 'gioi-thieu',
            ],
            'bang-gia' => [
                'controller' => \Tuezy\Controller\StaticController::class,
                'action' => 'index',
                'template' => 'static/static',
                'seoType' => 'article',
                'titleMain' => 'Bảng Giá',
                'type' => 'bang-gia',
            ],
            'tin-tuc' => [
                'controller' => \Tuezy\Controller\NewsController::class,
                // Action will be determined dynamically (list, cat, item, sub, detail) or default to 'index'
                'template' => null, 
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'tintuc',
                'type' => 'tin-tuc',
            ],
            'su-kien' => [
                'controller' => \Tuezy\Controller\NewsController::class,
                'template' => null,
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'Sự kiện',
                'type' => 'su-kien',
            ],
            'tuyen-dung' => [
                'controller' => \Tuezy\Controller\NewsController::class,
                'template' => null,
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'tuyendung',
                'type' => 'tuyen-dung',
            ],
            'kien-thuc' => [
                'controller' => \Tuezy\Controller\NewsController::class,
                'template' => null,
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'Kiến Thức',
                'type' => 'kien-thuc',
            ],
            'tai-sao-chon' => [
                'controller' => \Tuezy\Controller\NewsController::class,
                'template' => null,
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'Tại sao chọn',
                'type' => 'tai-sao-chon',
            ],
            'dich-vu' => [
                'controller' => \Tuezy\Controller\NewsController::class,
                'template' => null,
                'templateDetail' => 'news/news_detail2',
                'templateList' => 'news/news',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'Dịch Vụ',
                'type' => 'dich-vu',
            ],
            'thu-vien' => [
                'controller' => \Tuezy\Controller\NewsController::class,
                'template' => null,
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news_dichvu',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'Thư Viện',
                'type' => 'thu-vien',
            ],
            'catalogue' => [
                'controller' => \Tuezy\Controller\NewsController::class,
                'template' => null,
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'Catalogue',
                'type' => 'catalogue',
            ],
            'chinh-sach' => [
                'controller' => \Tuezy\Controller\NewsController::class,
                'template' => null,
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news',
                'seoType' => 'article',
                'titleMain' => 'Chính Sách',
                'type' => 'chinh-sach',
            ],
            'yeu-thich' => [
                'controller' => \Tuezy\Controller\ProductController::class,
                // Need to specify an action for these special product lists if they exist, or map to index with filters
                'action' => 'index', 
                'template' => 'product/product',
                'seoType' => 'object',
                'type' => 'san-pham',
                'titleMain' => null,
            ],
            'noi-bat' => [
                'controller' => \Tuezy\Controller\ProductController::class,
                'action' => 'index',
                'template' => 'product/product',
                'seoType' => 'object',
                'type' => 'san-pham',
                'titleMain' => null,
            ],
            'khuyen-mai' => [
                'controller' => \Tuezy\Controller\ProductController::class,
                'action' => 'index',
                'template' => 'product/product',
                'seoType' => 'object',
                'type' => 'san-pham',
                'titleMain' => null,
            ],
            'san-pham' => [
                'controller' => \Tuezy\Controller\ProductController::class,
                // Action dynamic
                'template' => null,
                'templateDetail' => 'product/product_detail',
                'templateList' => 'product/product',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'sanpham',
                'type' => 'san-pham',
            ],
            'tim-kiem' => [
                'controller' => \Tuezy\Controller\ProductController::class,
                'action' => 'search',
                'template' => 'product/product',
                'seoType' => 'object',
                'titleMain' => 'timkiem',
            ],
            'tags-san-pham' => [
                'controller' => \Tuezy\Controller\ProductController::class, // Or TagsController if exists, but usually product list
                'action' => 'tags', // Need to check if this action exists or implement it
                'template' => 'product/product',
                'seoType' => 'object',
                'titleMain' => null,
                'dynamicType' => true, 
                'dynamicTable' => true, 
            ],
            'tags-tin-tuc' => [
                'controller' => \Tuezy\Controller\NewsController::class,
                'action' => 'tags', // Need to check
                'template' => 'news/news',
                'seoType' => 'object',
                'titleMain' => null,
                'dynamicType' => true,
                'dynamicTable' => true,
            ],
            'thu-vien-anh' => [
                'controller' => \Tuezy\Controller\ProductController::class, // Or AlbumController? 'thu-vien-anh' typically maps to Album logic?
                // The legacy config had 'source' => 'product', 'type' => 'thu-vien-anh'. 
                // Wait, if source was product, it used ProductController logic.
                'template' => null,
                'templateDetail' => 'album/album_detail',
                'templateList' => 'album/album',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'thuvienanh',
                'type' => 'thu-vien-anh',
            ],
            'video' => [
                'controller' => \Tuezy\Controller\VideoController::class,
                'action' => 'index',
                'template' => 'video/video',
                'seoType' => 'object',
                'titleMain' => 'Video',
                'type' => 'video',
            ],
            'gio-hang' => [
                'controller' => \Tuezy\Controller\OrderController::class,
                'action' => 'index',
                'template' => 'order/order',
                'seoType' => 'object',
                'titleMain' => 'giohang',
            ],
            'account' => [
                'controller' => \Tuezy\Controller\UserController::class,
                // Action determined by sub-route or default
                'template' => null,
            ],
            'index' => [
                'controller' => \Tuezy\Controller\HomeController::class,
                'action' => 'index',
                'template' => 'index/index',
                'seoType' => 'website',
            ],
            '' => [
                'controller' => \Tuezy\Controller\HomeController::class,
                'action' => 'index',
                'template' => 'index/index',
                'seoType' => 'website',
            ],
        ];
    }

    /**
     * Handle special routes that require custom logic
     * 
     * @param string $com Component name
     * @param object $seo SEO object
     * @param object $func Functions object
     * @param string|null $lang Language code
     * @return array|null Route result or null if not a special route
     */
    public function handleSpecialRoutes(string $com, $seo, $func, ?string $lang = null): ?array
    {
        switch ($com) {
            case 'ngon-ngu':
                if (isset($lang)) {
                    $allowedLangs = ['vi', 'en'];
                    $_SESSION['lang'] = in_array($lang, $allowedLangs) ? $lang : 'vi';
                }
                $func->redirect($_SERVER['HTTP_REFERER'] ?? '/');
                return ['exit' => true];

            case 'sitemap':
                include_once LIBRARIES . "sitemap.php";
                return ['exit' => true];

            default:
                return null;
        }
    }

    /**
     * Get route configuration for a given component
     * 
     * @param string $com Component name
     * @param array $context Additional context (e.g., hasId, urlType, urlTblTag)
     * @return array|null Route configuration or null if not found
     */
    public function getRouteConfig(string $com, array $context = []): ?array
    {
        if (!isset($this->routeConfig[$com])) {
            return null;
        }

        $config = $this->routeConfig[$com];

        // Handle dynamic template selection (detail vs list)
        if (isset($config['templateDetail']) && isset($config['templateList'])) {
            $hasId = !empty($context['hasId'] ?? $_GET['id'] ?? null);
            $config['template'] = $hasId ? $config['templateDetail'] : $config['templateList'];
            
            // DEBUG: Log template selection
            if(isset($_GET['debug_routing']) || isset($_GET['debug_template'])) {
                error_log("DEBUG [RouteHandler::getRouteConfig] Dynamic template selection:");
                error_log("  - Com: $com");
                error_log("  - hasId: " . ($hasId ? 'YES' : 'NO'));
                error_log("  - context['hasId']: " . ($context['hasId'] ?? 'NOT SET'));
                error_log("  - \$_GET['id']: " . ($_GET['id'] ?? 'NOT SET'));
                error_log("  - templateDetail: " . $config['templateDetail']);
                error_log("  - templateList: " . $config['templateList']);
                error_log("  - Selected template: " . $config['template']);
            }
            
            unset($config['templateDetail'], $config['templateList']);
        }

        // Handle dynamic SEO type selection
        if (isset($config['seoTypeDetail']) && isset($config['seoTypeList'])) {
            $hasId = !empty($context['hasId'] ?? $_GET['id'] ?? null);
            $config['seoType'] = $hasId ? $config['seoTypeDetail'] : $config['seoTypeList'];
            unset($config['seoTypeDetail'], $config['seoTypeList']);
        }

        // Handle dynamic type and table for tags
        if (!empty($config['dynamicType']) && isset($context['urlType'])) {
            $config['type'] = $context['urlType'];
            unset($config['dynamicType']);
        }

        if (!empty($config['dynamicTable']) && isset($context['urlTblTag'])) {
            $config['table'] = $context['urlTblTag'];
            unset($config['dynamicTable']);
        }

        // DEBUG: Log final config template
        if(isset($_GET['debug_routing']) || isset($_GET['debug_template'])) {
            error_log("DEBUG [RouteHandler::getRouteConfig] Final config template: " . ($config['template'] ?? 'NULL'));
            error_log("DEBUG [RouteHandler::getRouteConfig] Final config keys: " . implode(', ', array_keys($config)));
        }

        return $config;
    }

    /**
     * Check if route exists
     * 
     * @param string $com Component name
     * @return bool
     */
    public function hasRoute(string $com): bool
    {
        return isset($this->routeConfig[$com]);
    }
}

