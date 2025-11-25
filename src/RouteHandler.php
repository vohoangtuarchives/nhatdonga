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
                'source' => 'contact',
                'template' => 'contact/contact',
                'seoType' => 'object',
                'titleMain' => 'lienhe',
            ],
            'gioi-thieu' => [
                'source' => 'static',
                'template' => 'static/static',
                'seoType' => 'article',
                'titleMain' => 'gioithieu',
                'type' => 'gioi-thieu',
            ],
            'bang-gia' => [
                'source' => 'static',
                'template' => 'static/static',
                'seoType' => 'article',
                'titleMain' => 'Bảng Giá',
                'type' => 'bang-gia',
            ],
            'tin-tuc' => [
                'source' => 'news',
                'template' => null, // Will be determined dynamically
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news',
                'seoType' => null, // Will be determined dynamically
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'tintuc',
                'type' => 'tin-tuc',
            ],
            'su-kien' => [
                'source' => 'news',
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
                'source' => 'news',
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
                'source' => 'news',
                'template' => null,
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news',
                'seoType' => null,
                'seoTypeDetail' => 'article',
                'seoTypeList' => 'object',
                'titleMain' => 'Kiến Thức',
                'type' => 'kien-thuc',
            ],
            'dich-vu' => [
                'source' => 'news',
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
                'source' => 'news',
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
                'source' => 'news',
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
                'source' => 'news',
                'template' => null,
                'templateDetail' => 'news/news_detail',
                'templateList' => 'news/news',
                'seoType' => 'article',
                'titleMain' => 'Chính Sách',
                'type' => 'chinh-sach',
            ],
            'yeu-thich' => [
                'source' => 'product',
                'template' => 'product/product',
                'seoType' => 'object',
                'type' => 'san-pham',
                'titleMain' => null,
            ],
            'noi-bat' => [
                'source' => 'product',
                'template' => 'product/product',
                'seoType' => 'object',
                'type' => 'san-pham',
                'titleMain' => null,
            ],
            'khuyen-mai' => [
                'source' => 'product',
                'template' => 'product/product',
                'seoType' => 'object',
                'type' => 'san-pham',
                'titleMain' => null,
            ],
            'san-pham' => [
                'source' => 'product',
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
                'source' => 'search',
                'template' => 'product/product',
                'seoType' => 'object',
                'titleMain' => 'timkiem',
            ],
            'tags-san-pham' => [
                'source' => 'tags',
                'template' => 'product/product',
                'seoType' => 'object',
                'titleMain' => null,
                'dynamicType' => true, // Type will be set from $urlType
                'dynamicTable' => true, // Table will be set from $urlTblTag
            ],
            'tags-tin-tuc' => [
                'source' => 'tags',
                'template' => 'news/news',
                'seoType' => 'object',
                'titleMain' => null,
                'dynamicType' => true,
                'dynamicTable' => true,
            ],
            'thu-vien-anh' => [
                'source' => 'product',
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
                'source' => 'video',
                'template' => 'video/video',
                'seoType' => 'object',
                'titleMain' => 'Video',
                'type' => 'video',
            ],
            'gio-hang' => [
                'source' => 'order',
                'template' => 'order/order',
                'seoType' => 'object',
                'titleMain' => 'giohang',
            ],
            'account' => [
                'source' => 'user',
                'template' => null,
            ],
            'index' => [
                'source' => 'index',
                'template' => 'index/index',
                'seoType' => 'website',
            ],
            '' => [
                'source' => 'index',
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

