<?php

namespace Tuezy\Controller;

use Tuezy\Repository\ProductRepository;
use Tuezy\Repository\CategoryRepository;
use Tuezy\Repository\NewsRepository;
use Tuezy\Repository\TagsRepository;
use Tuezy\Service\SeoService;
use Tuezy\SEOHelper;
use Tuezy\BreadcrumbHelper;
use Tuezy\PaginationHelper;
use Tuezy\SecurityHelper;

/**
 * BaseController - Base controller class for all frontend controllers
 * Provides common functionality and dependency injection
 */
abstract class BaseController
{
    protected $db;
    protected $cache;
    protected $func;
    protected $seo;
    protected array $config;
    protected SEOHelper $seoHelper;
    protected BreadcrumbHelper $breadcrumbHelper;
    protected PaginationHelper $paginationHelper;

    public function __construct(
        $db,
        $cache,
        $func,
        $seo,
        array $config
    ) {
        $this->db = $db;
        $this->cache = $cache;
        $this->func = $func;
        $this->seo = $seo;
        $this->config = $config;
        
        // Initialize SEOHelper with required parameters
        $lang = $_SESSION['lang'] ?? 'vi';
        $seolang = 'vi';
        $http = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) ? 'https://' : 'http://';
        $configUrl = $config['database']['server-name'] . $config['database']['url'];
        $configBase = $http . $configUrl;
        
        $this->seoHelper = new SEOHelper($seo, $func, $db, $lang, $seolang, $configBase);
        
        // Initialize BreadcrumbHelper with required parameters
        $breadcr = new \BreadCrumbs($db);
        $this->breadcrumbHelper = new BreadcrumbHelper($breadcr, $configBase);
        
        // Initialize PaginationHelper with required parameters
        $pagingAjax = new \PaginationsAjax();
        $this->paginationHelper = new PaginationHelper($pagingAjax);
    }

    /**
     * Get repository instance
     * 
     * @param string $name Repository name
     * @return mixed Repository instance
     */
    protected function getRepository(string $name)
    {
        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi'; // Can be extended to support multiple languages

        switch ($name) {
            case 'product':
                return new ProductRepository($this->db, $this->cache, $lang, $sluglang);
            case 'category':
                return new CategoryRepository($this->db, $this->cache, $lang, $sluglang);
            case 'news':
                return new NewsRepository($this->db, $this->cache, $lang, $sluglang);
            case 'tags':
                return new TagsRepository($this->db, $this->cache, $lang, $sluglang);
            default:
                throw new \InvalidArgumentException("Repository '$name' not found");
        }
    }

    /**
     * Sanitize input
     * 
     * @param string $value Input value
     * @return string Sanitized value
     */
    protected function sanitize(string $value): string
    {
        return SecurityHelper::sanitize($value);
    }

    /**
     * Get request parameter
     * 
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed
     */
    protected function getParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $_POST[$key] ?? $_REQUEST[$key] ?? $default;
    }

    /**
     * Render view
     * 
     * @param string $template Template path
     * @param array $data Data to pass to view
     */
    protected function render(string $template, array $data = []): void
    {
        // Extract data to variables
        extract($data);

        // Include template
        $templatePath = TEMPLATE . $template . '.php';
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            throw new \RuntimeException("Template not found: $templatePath");
        }
    }

    /**
     * Redirect to URL
     * 
     * @param string $url URL to redirect to
     * @param int $code HTTP status code
     */
    protected function redirect(string $url, int $code = 302): void
    {
        header("Location: $url", true, $code);
        exit;
    }

    /**
     * Return JSON response
     * 
     * @param array $data Data to return
     * @param int $code HTTP status code
     */
    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

