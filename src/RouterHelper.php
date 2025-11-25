<?php

namespace Tuezy;

/**
 * RouterHelper - Helper class to refactor router logic
 * Can be used to replace the switch statement in router.php
 * while maintaining exact functionality
 */
class RouterHelper
{
    private RouteHandler $routeHandler;
    private $seo;
    private $func;

    public function __construct(RouteHandler $routeHandler, $seo, $func)
    {
        $this->routeHandler = $routeHandler;
        $this->seo = $seo;
        $this->func = $func;
    }

    /**
     * Process route and set variables (maintains exact functionality)
     * 
     * @param string $com Component name
     * @param string|null $lang Language code
     * @param string|null $urlType URL type (for tags)
     * @param string|null $urlTblTag URL table tag (for tags)
     * @return array|null Returns array with 'source', 'template', 'type', 'table', 'titleMain' or null if route not found
     */
    public function processRoute(string $com, ?string $lang = null, ?string $urlType = null, ?string $urlTblTag = null): ?array
    {
        // Handle special routes
        $specialResult = $this->routeHandler->handleSpecialRoutes($com, $this->seo, $this->func, $lang);
        if ($specialResult && !empty($specialResult['exit'])) {
            return ['exit' => true];
        }

        // Get route configuration
        $routeConfig = $this->routeHandler->getRouteConfig($com, [
            'hasId' => !empty($_GET['id']),
            'urlType' => $urlType,
            'urlTblTag' => $urlTblTag,
        ]);

        if (!$routeConfig) {
            return null;
        }

        $result = [
            'source' => $routeConfig['source'] ?? null,
            'template' => $routeConfig['template'] ?? null,
            'type' => $routeConfig['type'] ?? null,
            'table' => $routeConfig['table'] ?? null,
            'titleMain' => $this->resolveTitleMain($routeConfig['titleMain'] ?? null),
        ];

        // Set SEO type
        if (isset($routeConfig['seoType'])) {
            $this->seo->set('type', $routeConfig['seoType']);
        }

        return $result;
    }

    /**
     * Resolve titleMain constant to actual value
     * Maintains compatibility with language constants
     * 
     * @param string|null $titleMainKey
     * @return mixed
     */
    private function resolveTitleMain(?string $titleMainKey)
    {
        if ($titleMainKey === null) {
            return null;
        }

        // Map constant names to actual constants (maintains exact functionality)
        $constantMap = [
            'lienhe' => 'lienhe',
            'gioithieu' => 'gioithieu',
            'tintuc' => 'tintuc',
            'tuyendung' => 'tuyendung',
            'sanpham' => 'sanpham',
            'timkiem' => 'timkiem',
            'thuvienanh' => 'thuvienanh',
            'giohang' => 'giohang',
        ];

        // If it's a mapped constant, return the constant name (will be evaluated in global scope)
        if (isset($constantMap[$titleMainKey])) {
            return $titleMainKey; // Return as-is, will be used as constant in calling code
        }

        // Otherwise return the string value
        return $titleMainKey;
    }
}

