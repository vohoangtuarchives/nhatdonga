<?php

namespace Tuezy;

/**
 * BreadcrumbHelper - Enhanced breadcrumb management
 * Wrapper for BreadCrumbs class with additional features
 */
class BreadcrumbHelper
{
    private $breadcr;
    private string $configBase;
    private array $items = [];

    public function __construct($breadcr, string $configBase)
    {
        $this->breadcr = $breadcr;
        $this->configBase = rtrim($configBase, '/');
    }

    /**
     * Add breadcrumb item
     * 
     * @param string $name Item name
     * @param string|null $slug Item slug/URL
     */
    public function add(string $name, ?string $slug = null): void
    {
        if (!empty($name)) {
            $this->breadcr->set($slug ?? '', $name);
            $this->items[] = [
                'name' => $name,
                'slug' => $slug,
                'url' => $slug ? $this->configBase . $slug : null,
            ];
        }
    }

    /**
     * Get breadcrumb HTML
     * 
     * @return string Breadcrumb HTML
     */
    public function render(): string
    {
        return $this->breadcr->get();
    }

    /**
     * Get breadcrumb items as array
     * 
     * @return array Breadcrumb items
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get breadcrumb JSON-LD structured data
     * 
     * @return string JSON-LD script tag
     */
    public function getJsonLd(): string
    {
        if (empty($this->items)) {
            return '';
        }

        $json = [];
        $position = 1;

        // Add home
        $json[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Trang chủ',
            'item' => $this->configBase,
        ];

        // Add other items
        foreach ($this->items as $item) {
            if (!empty($item['url'])) {
                $json[] = [
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => $item['name'],
                    'item' => $item['url'],
                ];
            }
        }

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $json,
        ];

        return '<script type="application/ld+json">' . json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    }

    /**
     * Clear all breadcrumbs
     */
    public function clear(): void
    {
        $this->items = [];
    }
}

