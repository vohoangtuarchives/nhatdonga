<?php

namespace Tuezy\Repository;

/**
 * ProductRepository - Data access layer for products
 * Refactors repetitive product queries in sources/product.php
 */
class ProductRepository
{
    private $d;
    private $func;
    private string $lang;
    private string $type;

    public function __construct($d, $func, string $lang, string $type = 'san-pham')
    {
        $this->d = $d;
        $this->func = $func;
        $this->lang = $lang;
        $this->type = $type;
    }

    /**
     * Get product detail by ID
     * 
     * @param int $id Product ID
     * @return array|null
     */
    public function getDetail(int $id): ?array
    {
        return $this->d->rawQueryOne(
            "select type, id, name{$this->lang}, slugvi, slugen, desc{$this->lang}, content{$this->lang}, 
             code, view, id_brand, id_list, id_cat, id_item, id_sub, photo, options, discount, 
             sale_price, regular_price 
             from #_product 
             where id = ? and type = ? and find_in_set('hienthi',status) 
             limit 0,1",
            [$id, $this->type]
        );
    }

    /**
     * Increment product view count
     * 
     * @param int $id Product ID
     * @param int $currentViews Current view count
     */
    public function incrementViews(int $id, int $currentViews): void
    {
        $this->d->where('id', $id);
        $this->d->update('product', ['view' => $currentViews + 1]);
    }

    /**
     * Get product tags
     * 
     * @param int $productId Product ID
     * @return array
     */
    public function getTags(int $productId): array
    {
        $productTags = $this->d->rawQuery(
            "select id_tags from #_product_tags where id_parent = ?",
            [$productId]
        );

        if (empty($productTags)) {
            return [];
        }

        $tagIds = $this->func->joinCols($productTags, 'id_tags');
        if (empty($tagIds)) {
            return [];
        }

        return $this->d->rawQuery(
            "select id, name{$this->lang}, slugvi, slugen 
             from #_tags 
             where type = ? and id in ($tagIds) and find_in_set('hienthi',status) 
             order by numb,id desc",
            [$this->type]
        );
    }

    /**
     * Get product colors
     * 
     * @param int $productId Product ID
     * @return array
     */
    public function getColors(int $productId): array
    {
        $productColor = $this->d->rawQuery(
            "select id_color from #_product_sale where id_parent = ?",
            [$productId]
        );

        if (empty($productColor)) {
            return [];
        }

        $colorIds = $this->func->joinCols($productColor, 'id_color');
        if (empty($colorIds)) {
            return [];
        }

        return $this->d->rawQuery(
            "select type_show, photo, color, id 
             from #_color 
             where type = ? and id in ($colorIds) and find_in_set('hienthi',status) 
             order by numb,id desc",
            [$this->type]
        );
    }

    /**
     * Get product sizes
     * 
     * @param int $productId Product ID
     * @return array
     */
    public function getSizes(int $productId): array
    {
        $productSize = $this->d->rawQuery(
            "select id_size from #_product_sale where id_parent = ?",
            [$productId]
        );

        if (empty($productSize)) {
            return [];
        }

        $sizeIds = $this->func->joinCols($productSize, 'id_size');
        if (empty($sizeIds)) {
            return [];
        }

        return $this->d->rawQuery(
            "select id, name{$this->lang} 
             from #_size 
             where type = ? and id in ($sizeIds) and find_in_set('hienthi',status) 
             order by numb,id desc",
            [$this->type]
        );
    }

    /**
     * Get product category hierarchy
     * 
     * @param array $productDetail Product detail array
     * @return array
     */
    public function getCategoryHierarchy(array $productDetail): array
    {
        return [
            'list' => $this->getCategoryItem('product_list', $productDetail['id_list'] ?? 0),
            'cat' => $this->getCategoryItem('product_cat', $productDetail['id_cat'] ?? 0),
            'item' => $this->getCategoryItem('product_item', $productDetail['id_item'] ?? 0),
            'sub' => $this->getCategoryItem('product_sub', $productDetail['id_sub'] ?? 0),
            'brand' => $this->getCategoryItem('product_brand', $productDetail['id_brand'] ?? 0),
        ];
    }

    /**
     * Get category item
     * 
     * @param string $table Table name
     * @param int $id Category ID
     * @return array|null
     */
    private function getCategoryItem(string $table, int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $fields = ($table === 'product_brand') 
            ? "name{$this->lang}, slugvi, slugen, id"
            : "id, name{$this->lang}, slugvi, slugen";

        return $this->d->rawQueryOne(
            "select $fields 
             from #_$table 
             where id = ? and type = ? and find_in_set('hienthi',status) 
             limit 0,1",
            [$id, $this->type]
        );
    }

    /**
     * Get product gallery images
     * 
     * @param int $productId Product ID
     * @return array
     */
    public function getGallery(int $productId): array
    {
        return $this->d->rawQuery(
            "select photo 
             from #_gallery 
             where id_parent = ? and com='product' and type = ? and kind='man' and val = ? 
             and find_in_set('hienthi',status) 
             order by numb,id desc",
            [$productId, $this->type, $this->type]
        );
    }
}

