<?php

namespace Tuezy\Repository;

/**
 * TagsRepository - Data access layer for tags
 */
class TagsRepository
{
    private $d;
    private $cache;
    private string $lang;
    private string $sluglang;

    public function __construct($d, $cache, string $lang, string $sluglang)
    {
        $this->d = $d;
        $this->cache = $cache;
        $this->lang = $lang;
        $this->sluglang = $sluglang;
    }

    /**
     * Get tag by ID and type
     * 
     * @param int $id Tag ID
     * @param string $type Tag type
     * @return array|null
     */
    public function getById(int $id, string $type): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT id, name{$this->lang}, type, photo, slugvi, slugen, options 
             FROM #_tags 
             WHERE id = ? AND type = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$id, $type]
        );
    }

    /**
     * Get tags by type
     * 
     * @param string $type Tag type
     * @param bool $active Only active tags
     * @param int $limit Limit results
     * @return array
     */
    public function getByType(string $type, bool $active = true, int $limit = 0): array
    {
        $where = "type = ?";
        $params = [$type];
        
        if ($active) {
            $where .= " AND find_in_set('hienthi',status)";
        }

        $limitSql = $limit > 0 ? " LIMIT 0,{$limit}" : "";

        return $this->cache->get(
            "SELECT id, name{$this->lang}, slugvi, slugen, photo 
             FROM #_tags 
             WHERE {$where} 
             ORDER BY numb, id DESC {$limitSql}",
            $params,
            'result',
            7200
        );
    }

    /**
     * Get tags by slug
     * 
     * @param string $slug Tag slug
     * @param string $type Tag type
     * @return array|null
     */
    public function getBySlug(string $slug, string $type): ?array
    {
        $slugField = $this->sluglang;
        return $this->d->rawQueryOne(
            "SELECT id, name{$this->lang}, type, photo, slugvi, slugen, options 
             FROM #_tags 
             WHERE {$slugField} = ? AND type = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$slug, $type]
        );
    }

    /**
     * Get tags for product
     * 
     * @param int $productId Product ID
     * @param string $type Tag type
     * @return array
     */
    public function getByProduct(int $productId, string $type): array
    {
        $productTags = $this->d->rawQuery(
            "SELECT id_tags FROM #_product_tags WHERE id_parent = ?",
            [$productId]
        );

        if (empty($productTags)) {
            return [];
        }

        $tagIds = implode(',', array_column($productTags, 'id_tags'));

        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, slugvi, slugen 
             FROM #_tags 
             WHERE type = ? AND id IN ({$tagIds}) AND find_in_set('hienthi',status) 
             ORDER BY numb, id DESC",
            [$type]
        );
    }

    /**
     * Get tags for news
     * 
     * @param int $newsId News ID
     * @param string $type Tag type
     * @return array
     */
    public function getByNews(int $newsId, string $type): array
    {
        $newsTags = $this->d->rawQuery(
            "SELECT id_tags FROM #_news_tags WHERE id_parent = ?",
            [$newsId]
        );

        if (empty($newsTags)) {
            return [];
        }

        $tagIds = implode(',', array_column($newsTags, 'id_tags'));

        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, slugvi, slugen 
             FROM #_tags 
             WHERE type = ? AND id IN ({$tagIds}) AND find_in_set('hienthi',status) 
             ORDER BY numb, id DESC",
            [$type]
        );
    }

    /**
     * Create tag
     * 
     * @param array $data Tag data
     * @return bool
     */
    public function create(array $data): bool
    {
        if (!isset($data['date_created'])) {
            $data['date_created'] = time();
        }
        if (!isset($data['numb'])) {
            $data['numb'] = 0;
        }
        return $this->d->insert('tags', $data);
    }

    /**
     * Update tag
     * 
     * @param int $id Tag ID
     * @param array $data Tag data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->d->where('id', $id);
        return $this->d->update('tags', $data);
    }

    /**
     * Delete tag
     * 
     * @param int $id Tag ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->d->where('id', $id);
        return $this->d->delete('tags');
    }
}

