<?php

namespace Tuezy\Repository;

/**
 * CategoryRepository - Data access layer for categories (list, cat, item, sub)
 * Works with product_list, product_cat, product_item, product_sub
 * and news_list, news_cat, news_item, news_sub
 */
class CategoryRepository
{
    private $d;
    private $cache;
    private string $lang;
    private string $sluglang;
    private string $tablePrefix;

    public function __construct($d, $cache, string $lang, string $sluglang, string $tablePrefix = 'product')
    {
        $this->d = $d;
        $this->cache = $cache;
        $this->lang = $lang;
        $this->sluglang = $sluglang;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * Get list categories
     * 
     * @param string $type Category type
     * @param bool $active Only active
     * @param bool $featured Only featured
     * @return array
     */
    public function getLists(string $type, bool $active = true, bool $featured = false): array
    {
        $where = "type = ?";
        $params = [$type];
        
        if ($active) {
            $where .= " AND find_in_set('hienthi',status)";
        }
        
        if ($featured) {
            $where .= " AND find_in_set('noibat',status)";
        }

        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, slug{$this->lang}, type, photo 
             FROM #_{$this->tablePrefix}_list 
             WHERE {$where} 
             ORDER BY numb, id DESC",
            $params
        ) ?: [];
    }

    /**
     * Get list by ID
     * 
     * @param int $id List ID
     * @param string $type Category type
     * @return array|null
     */
    public function getListById(int $id, string $type): ?array
    {
        $result = $this->d->rawQueryOne(
            "SELECT id, name{$this->lang}, slug{$this->lang}, type, photo, options 
             FROM #_{$this->tablePrefix}_list 
             WHERE id = ? AND type = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$id, $type]
        );
        return $result ?: null;
    }

    /**
     * Get list by slug
     * 
     * @param string $slug List slug
     * @param string $type Category type
     * @return array|null
     */
    public function getListBySlug(string $slug, string $type): ?array
    {
        $slugField = $this->sluglang;
        $result = $this->d->rawQueryOne(
            "SELECT id, name{$this->lang}, slug{$this->lang}, type, photo, options 
             FROM #_{$this->tablePrefix}_list 
             WHERE {$slugField} = ? AND type = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$slug, $type]
        );
        return $result ?: null;
    }

    private function toLink(array $row): \Tuezy\Domain\Catalog\CategoryLink
    {
        $name = (string)($row['name' . $this->lang] ?? '');
        $slugField = $this->sluglang;
        $slug = (string)($row[$slugField] ?? '');
        return new \Tuezy\Domain\Catalog\CategoryLink($name, $slug);
    }

    public function getListLinkById(int $id, string $type): ?\Tuezy\Domain\Catalog\CategoryLink
    {
        $row = $this->getListById($id, $type);
        return $row ? $this->toLink($row) : null;
    }

    /**
     * Get categories (cat)
     * 
     * @param string $type Category type
     * @param int|null $listId Parent list ID
     * @param bool $active Only active
     * @return array
     */
    public function getCats(string $type, ?int $listId = null, bool $active = true): array
    {
        $where = "type = ?";
        $params = [$type];
        
        if ($listId) {
            $where .= " AND id_list = ?";
            $params[] = $listId;
        }
        
        if ($active) {
            $where .= " AND find_in_set('hienthi',status)";
        }

        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, slug{$this->lang}, type, photo, id_list 
             FROM #_{$this->tablePrefix}_cat 
             WHERE {$where} 
             ORDER BY numb, id DESC",
            $params
        ) ?: [];
    }

    /**
     * Get cat by ID
     * 
     * @param int $id Cat ID
     * @param string $type Category type
     * @return array|null
     */
    public function getCatById(int $id, string $type): ?array
    {
        $result = $this->d->rawQueryOne(
            "SELECT id, name{$this->lang}, slug{$this->lang}, type, photo, id_list, options 
             FROM #_{$this->tablePrefix}_cat 
             WHERE id = ? AND type = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$id, $type]
        );
        return $result ?: null;
    }

    public function getCatLinkById(int $id, string $type): ?\Tuezy\Domain\Catalog\CategoryLink
    {
        $row = $this->getCatById($id, $type);
        return $row ? $this->toLink($row) : null;
    }

    /**
     * Get items
     * 
     * @param string $type Category type
     * @param int|null $catId Parent cat ID
     * @param bool $active Only active
     * @return array
     */
    public function getItems(string $type, ?int $catId = null, bool $active = true): array
    {
        $where = "type = ?";
        $params = [$type];
        
        if ($catId) {
            $where .= " AND id_cat = ?";
            $params[] = $catId;
        }
        
        if ($active) {
            $where .= " AND find_in_set('hienthi',status)";
        }

        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, slug{$this->lang}, type, photo, id_cat 
             FROM #_{$this->tablePrefix}_item 
             WHERE {$where} 
             ORDER BY numb, id DESC",
            $params
        ) ?: [];
    }

    /**
     * Get item by ID
     * 
     * @param int $id Item ID
     * @param string $type Category type
     * @return array|null
     */
    public function getItemById(int $id, string $type): ?array
    {
        $result = $this->d->rawQueryOne(
            "SELECT id, name{$this->lang}, slug{$this->lang}, type, photo, id_cat, options 
             FROM #_{$this->tablePrefix}_item 
             WHERE id = ? AND type = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$id, $type]
        );
        return $result ?: null;
    }

    public function getItemLinkById(int $id, string $type): ?\Tuezy\Domain\Catalog\CategoryLink
    {
        $row = $this->getItemById($id, $type);
        return $row ? $this->toLink($row) : null;
    }

    /**
     * Get subs
     * 
     * @param string $type Category type
     * @param int|null $itemId Parent item ID
     * @param bool $active Only active
     * @return array
     */
    public function getSubs(string $type, ?int $itemId = null, bool $active = true): array
    {
        $where = "type = ?";
        $params = [$type];
        
        if ($itemId) {
            $where .= " AND id_item = ?";
            $params[] = $itemId;
        }
        
        if ($active) {
            $where .= " AND find_in_set('hienthi',status)";
        }

        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, slug{$this->lang}, type, photo, id_item 
             FROM #_{$this->tablePrefix}_sub 
             WHERE {$where} 
             ORDER BY numb, id DESC",
            $params
        ) ?: [];
    }

    /**
     * Get sub by ID
     * 
     * @param int $id Sub ID
     * @param string $type Category type
     * @return array|null
     */
    public function getSubById(int $id, string $type): ?array
    {
        $result = $this->d->rawQueryOne(
            "SELECT id, name{$this->lang}, slug{$this->lang}, type, photo, id_item, options 
             FROM #_{$this->tablePrefix}_sub 
             WHERE id = ? AND type = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$id, $type]
        );
        return $result ?: null;
    }

    public function getSubLinkById(int $id, string $type): ?\Tuezy\Domain\Catalog\CategoryLink
    {
        $row = $this->getSubById($id, $type);
        return $row ? $this->toLink($row) : null;
    }

    /**
     * Get category hierarchy (list -> cat -> item -> sub)
     * 
     * @param int $listId List ID
     * @param int|null $catId Cat ID
     * @param int|null $itemId Item ID
     * @param string $type Category type
     * @return array
     */
    public function getHierarchy(int $listId, ?int $catId = null, ?int $itemId = null, string $type = ''): array
    {
        $hierarchy = [];

        if ($listId) {
            $hierarchy['list'] = $this->getListById($listId, $type);
        }

        if ($catId) {
            $hierarchy['cat'] = $this->getCatById($catId, $type);
        }

        if ($itemId) {
            $hierarchy['item'] = $this->getItemById($itemId, $type);
        }

        return $hierarchy;
    }
}

