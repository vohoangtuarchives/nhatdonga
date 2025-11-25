<?php

namespace Tuezy;

/**
 * URLHelper - URL generation and manipulation
 * Provides convenient methods for URL operations
 */
class URLHelper
{
    private string $configBase;
    private string $lang;
    private string $sluglang;

    public function __construct(string $configBase, string $lang = 'vi', string $sluglang = 'slugvi')
    {
        $this->configBase = rtrim($configBase, '/');
        $this->lang = $lang;
        $this->sluglang = $sluglang;
    }

    /**
     * Generate product URL
     * 
     * @param array|string $product Product data or slug
     * @return string Product URL
     */
    public function product($product): string
    {
        $slug = is_array($product) ? ($product[$this->sluglang] ?? $product['slugvi'] ?? '') : $product;
        return $this->configBase . '/san-pham/' . $slug . '.html';
    }

    /**
     * Generate news URL
     * 
     * @param array|string $news News data or slug
     * @param string $type News type (default: tin-tuc)
     * @return string News URL
     */
    public function news($news, string $type = 'tin-tuc'): string
    {
        $slug = is_array($news) ? ($news[$this->sluglang] ?? $news['slugvi'] ?? '') : $news;
        return $this->configBase . '/' . $type . '/' . $slug . '.html';
    }

    /**
     * Generate static page URL
     * 
     * @param string $type Static type
     * @return string Static URL
     */
    public function static(string $type): string
    {
        return $this->configBase . '/' . $type;
    }

    /**
     * Generate category URL
     * 
     * @param array $category Category data
     * @param string $type Category type (product, news)
     * @return string Category URL
     */
    public function category(array $category, string $type = 'product'): string
    {
        $slug = $category[$this->sluglang] ?? $category['slugvi'] ?? '';
        $baseType = ($type === 'product') ? 'san-pham' : 'tin-tuc';
        return $this->configBase . '/' . $baseType . '/' . $slug;
    }

    /**
     * Generate current page URL
     * 
     * @param array $params Additional parameters
     * @return string Current URL
     */
    public function current(array $params = []): string
    {
        $url = $this->configBase . $_SERVER['REQUEST_URI'];
        
        if (!empty($params)) {
            $separator = strpos($url, '?') !== false ? '&' : '?';
            $url .= $separator . http_build_query($params);
        }

        return $url;
    }

    /**
     * Generate URL with query parameters
     * 
     * @param string $path URL path
     * @param array $params Query parameters
     * @return string Full URL
     */
    public function to(string $path, array $params = []): string
    {
        $url = $this->configBase . '/' . ltrim($path, '/');
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Generate asset URL
     * 
     * @param string $path Asset path
     * @return string Asset URL
     */
    public function asset(string $path): string
    {
        return $this->configBase . '/' . ltrim($path, '/');
    }

    /**
     * Generate image URL
     * 
     * @param string $image Image filename
     * @param string $uploadPath Upload path
     * @return string Image URL
     */
    public function image(string $image, string $uploadPath = UPLOAD_PHOTO_L): string
    {
        return $this->configBase . '/' . $uploadPath . $image;
    }

    /**
     * Generate thumbnail URL
     * 
     * @param string $image Image filename
     * @param string $sizes Thumb sizes (e.g., '300x300x2')
     * @param string $uploadPath Upload path
     * @return string Thumbnail URL
     */
    public function thumb(string $image, string $sizes = '300x300x2', string $uploadPath = UPLOAD_PHOTO_L): string
    {
        return $this->configBase . '/thumbs/' . $sizes . '/' . $uploadPath . $image;
    }

    /**
     * Get base URL
     * 
     * @return string Base URL
     */
    public function base(): string
    {
        return $this->configBase;
    }

    /**
     * Get previous URL (referer)
     * 
     * @param string $default Default URL if no referer
     * @return string Previous URL
     */
    public function previous(string $default = '/'): string
    {
        return $_SERVER['HTTP_REFERER'] ?? $this->configBase . $default;
    }
}

