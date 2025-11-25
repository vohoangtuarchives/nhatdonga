<?php

namespace Tuezy\Repository;

/**
 * NewsRepository - Data access layer for news/articles
 * Refactors repetitive news queries in sources/news.php
 */
class NewsRepository
{
    private $d;
    private string $lang;
    private string $type;

    public function __construct($d, string $lang, string $type = 'tin-tuc')
    {
        $this->d = $d;
        $this->lang = $lang;
        $this->type = $type;
    }

    /**
     * Get news detail by ID
     * 
     * @param int $id News ID
     * @return array|null
     */
    public function getDetail(int $id): ?array
    {
        return $this->d->rawQueryOne(
            "select id, view, date_created, id_list, id_cat, id_item, id_sub, type, 
             name{$this->lang}, slugvi, slugen, desc{$this->lang}, content{$this->lang}, 
             photo, options 
             from #_news 
             where id = ? and type = ? and find_in_set('hienthi',status) 
             limit 0,1",
            [$id, $this->type]
        );
    }

    /**
     * Increment news view count
     * 
     * @param int $id News ID
     * @param int $currentViews Current view count
     */
    public function incrementViews(int $id, int $currentViews): void
    {
        $this->d->where('id', $id);
        $this->d->update('news', ['view' => $currentViews + 1]);
    }

    /**
     * Get news category hierarchy
     * 
     * @param array $newsDetail News detail array
     * @return array
     */
    public function getCategoryHierarchy(array $newsDetail): array
    {
        return [
            'list' => $this->getCategoryItem('news_list', $newsDetail['id_list'] ?? 0),
            'cat' => $this->getCategoryItem('news_cat', $newsDetail['id_cat'] ?? 0),
            'item' => $this->getCategoryItem('news_item', $newsDetail['id_item'] ?? 0),
            'sub' => $this->getCategoryItem('news_sub', $newsDetail['id_sub'] ?? 0),
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

        return $this->d->rawQueryOne(
            "select id, name{$this->lang}, slugvi, slugen 
             from #_$table 
             where id = ? and type = ? and find_in_set('hienthi',status) 
             limit 0,1",
            [$id, $this->type]
        );
    }

    /**
     * Get news gallery images
     * 
     * @param int $newsId News ID
     * @return array
     */
    public function getGallery(int $newsId): array
    {
        return $this->d->rawQuery(
            "select photo 
             from #_gallery 
             where id_parent = ? and com='news' and type = ? and kind='man' and val = ? 
             and find_in_set('hienthi',status) 
             order by numb,id desc",
            [$newsId, $this->type, $this->type]
        );
    }

    /**
     * Get related news
     * 
     * @param int $excludeId News ID to exclude
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array
     */
    public function getRelated(int $excludeId, int $page = 1, int $perPage = 12): array
    {
        $startpoint = ($page * $perPage) - $perPage;
        $limit = " limit $startpoint, $perPage";

        return $this->d->rawQuery(
            "select * 
             from #_news 
             where id <> ? and type = ? and find_in_set('hienthi',status) 
             order by numb,id desc $limit",
            [$excludeId, $this->type]
        );
    }

    /**
     * Get news by type
     * 
     * @param string $type News type (overrides default type)
     * @param bool $active Only active news
     * @param int $limit Limit results
     * @param string $order Order by clause
     * @return array
     */
    public function getByType(string $type, bool $active = true, int $limit = 0, string $order = "numb,id desc"): array
    {
        $where = "type = ?";
        $params = [$type];
        
        if ($active) {
            $where .= " AND find_in_set('hienthi',status)";
        }

        $limitSql = $limit > 0 ? " LIMIT 0,{$limit}" : "";

        return $this->d->rawQuery(
            "SELECT name{$this->lang}, id 
             FROM #_news 
             WHERE {$where} 
             ORDER BY {$order} {$limitSql}",
            $params
        );
    }
}

