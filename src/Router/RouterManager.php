<?php

namespace Tuezy\Router;

use Tuezy\RouteHandler;
use Tuezy\RequestHandler;
use Tuezy\RouterHelper;
use Tuezy\Repository\PhotoRepository;
use Tuezy\SecurityHelper;
use Tuezy\Router\AssetRouteHandler;

/**
 * RouterManager - Manages routing configuration and execution
 * Centralizes routing logic from libraries/router.php
 */
class RouterManager
{
    private $router;
    private array $config;
    private $func;
    private $cache;
    private $d;
    private RouteHandler $routeHandler;
    private AssetRouteHandler $assetRouteHandler;
    private array $routes = [];
    private array $middlewares = [];

    public function __construct($router, array $config, $func, $cache, $d)
    {
        $this->router = $router;
        $this->config = $config;
        $this->func = $func;
        $this->cache = $cache;
        $this->d = $d;
        $this->routeHandler = new RouteHandler();
        $this->assetRouteHandler = new AssetRouteHandler($func, $cache, $d, $config);
    }

    /**
     * Register a route
     * 
     * @param string|array $method HTTP method(s)
     * @param string|array $route Route pattern
     * @param callable|string $target Route target
     * @param string|null $name Route name
     */
    public function register($method, $route, $target, ?string $name = null): void
    {
        $this->router->map($method, $route, $target, $name);
    }

    /**
     * Register default routes
     */
    public function registerDefaultRoutes(): void
    {
        $config = $this->config;
        $func = $this->func;

        // Admin redirects
        $this->register('GET', [ADMIN . '/', 'admin'], function () use ($func, $config) {
            $func->redirect($config['database']['url'] . ADMIN . "/index.php");
            exit;
        });

        $this->register('GET', [ADMIN, 'admin'], function () use ($func, $config) {
            $func->redirect($config['database']['url'] . ADMIN . "/index.php");
            exit;
        });

        // Main routes
        $this->register('GET|POST', '', 'index', 'home');
        $this->register('GET|POST', 'index.php', 'index', 'index');
        $this->register('GET|POST', 'sitemap.xml', 'sitemap', 'sitemap');
        $this->register('GET|POST', '[a:com]', 'allpage', 'show');
        $this->register('GET|POST', '[a:com]/[a:lang]/', 'allpagelang', 'lang');
        $this->register('GET|POST', '[a:com]/[a:action]', 'account', 'account');

        // Asset routes (thumbnails and watermarks)
        $this->registerAssetRoutes();
    }

    /**
     * Register asset routes (thumbnails, watermarks)
     */
    private function registerAssetRoutes(): void
    {
        $func = $this->func;
        $config = $this->config;
        $cache = $this->cache;
        $d = $this->d;

        // Thumbnail route
        $assetHandler = $this->assetRouteHandler;
        $this->register('GET', THUMBS . '/[i:w]x[i:h]x[i:z]/[**:src]', function ($w, $h, $z, $src) use ($assetHandler) {
            $assetHandler->handleThumbnail($w, $h, $z, $src);
        }, 'thumb');

        // Product watermark route
        $this->register('GET', WATERMARK . '/product/[i:w]x[i:h]x[i:z]/[**:src]', function ($w, $h, $z, $src) use ($assetHandler) {
            global $lang, $sluglang;
            $assetHandler->handleProductWatermark($w, $h, $z, $src, $lang ?? null, $sluglang ?? null);
        }, 'watermark');

        // News watermark route
        $this->register('GET', WATERMARK . '/news/[i:w]x[i:h]x[i:z]/[**:src]', function ($w, $h, $z, $src) use ($assetHandler) {
            global $lang, $sluglang;
            $assetHandler->handleNewsWatermark($w, $h, $z, $src, $lang ?? null, $sluglang ?? null);
        }, 'watermarkNews');
    }

    /**
     * Match current request against registered routes
     * 
     * @return array|null Match result or null
     */
    public function match(): ?array
    {
        return $this->router->match();
    }

    /**
     * Get route handler
     * 
     * @return RouteHandler
     */
    public function getRouteHandler(): RouteHandler
    {
        return $this->routeHandler;
    }

    /**
     * Get router instance
     * 
     * @return mixed
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Set base path for router
     * 
     * @param string $basePath Base path
     */
    public function setBasePath(string $basePath): void
    {
        $this->router->setBasePath($basePath);
    }
}

