<?php

namespace Tuezy;

/**
 * TemplateHelper - Helper functions for templates
 * Provides common template operations
 */
class TemplateHelper
{
    private $func;
    private string $lang;
    private string $sluglang;
    private string $configBase;

    public function __construct($func, string $lang, string $sluglang, string $configBase)
    {
        $this->func = $func;
        $this->lang = $lang;
        $this->sluglang = $sluglang;
        $this->configBase = $configBase;
    }

    /**
     * Get product URL
     * 
     * @param array $product Product data
     * @return string Product URL
     */
    public function getProductUrl(array $product): string
    {
        $slug = $product[$this->sluglang] ?? $product['slugvi'] ?? '';
        return $this->configBase . 'san-pham/' . $slug . '.html';
    }

    /**
     * Get news URL
     * 
     * @param array $news News data
     * @param string $type News type
     * @return string News URL
     */
    public function getNewsUrl(array $news, string $type = 'tin-tuc'): string
    {
        $slug = $news[$this->sluglang] ?? $news['slugvi'] ?? '';
        return $this->configBase . $type . '/' . $slug . '.html';
    }

    /**
     * Format price
     * 
     * @param float $price Price
     * @param bool $showOldPrice Show old price if sale price exists
     * @param float|null $oldPrice Old price
     * @return string Formatted price HTML
     */
    public function formatPrice(float $price, bool $showOldPrice = false, ?float $oldPrice = null): string
    {
        $formatted = $this->func->formatMoney($price);
        
        if ($showOldPrice && $oldPrice && $oldPrice > $price) {
            $oldFormatted = $this->func->formatMoney($oldPrice);
            return '<span class="price-old">' . $oldFormatted . '</span> <span class="price-new">' . $formatted . '</span>';
        }

        return '<span class="price">' . $formatted . '</span>';
    }

    /**
     * Truncate text
     * 
     * @param string $text Text to truncate
     * @param int $length Max length
     * @param string $suffix Suffix to append
     * @return string Truncated text
     */
    public function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . $suffix;
    }

    /**
     * Format date
     * 
     * @param int $timestamp Unix timestamp
     * @param string $format Date format
     * @return string Formatted date
     */
    public function formatDate(int $timestamp, string $format = 'd/m/Y'): string
    {
        return date($format, $timestamp);
    }

    /**
     * Get breadcrumb HTML
     * 
     * @param array $breadcrumbs Breadcrumb array
     * @return string Breadcrumb HTML
     */
    public function renderBreadcrumbs(array $breadcrumbs): string
    {
        if (empty($breadcrumbs)) {
            return '';
        }

        $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
        
        foreach ($breadcrumbs as $index => $crumb) {
            $isLast = ($index === count($breadcrumbs) - 1);
            $url = $crumb['url'] ?? '#';
            $title = $crumb['title'] ?? '';

            if ($isLast) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($title) . '</li>';
            } else {
                $html .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($url) . '">' . htmlspecialchars($title) . '</a></li>';
            }
        }

        $html .= '</ol></nav>';
        return $html;
    }

    /**
     * Get pagination HTML
     * 
     * @param string $pagination Pagination HTML from PaginationsAjax
     * @return string Pagination HTML
     */
    public function renderPagination(string $pagination): string
    {
        if (empty($pagination)) {
            return '';
        }

        return '<div class="pagination-ajax">' . $pagination . '</div>';
    }

    /**
     * Escape HTML
     * 
     * @param string $text Text to escape
     * @return string Escaped text
     */
    public function escape(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get language text (from constants)
     * 
     * @param string $key Language key
     * @return string Language text
     */
    public function lang(string $key): string
    {
        // This will use the language constants defined in lang files
        // Usage: $templateHelper->lang('sanpham') returns the constant value
        return defined($key) ? constant($key) : $key;
    }
}

