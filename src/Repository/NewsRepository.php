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
            "SELECT name{$this->lang} , id, slug{$this->lang} 
             FROM #_news 
             WHERE {$where} 
             ORDER BY {$order} {$limitSql}",
            $params
        );
    }

    /**
     * Alias for getDetail() - for backward compatibility
     * 
     * @param int $id News ID
     * @param string|null $type News type (optional, uses default if not provided)
     * @return array|null
     */
    public function getNewsDetail(int $id, ?string $type = null): ?array
    {
        if ($type && $type !== $this->type) {
            // Create temporary instance with different type
            $tempRepo = new self($this->d, $this->lang, $type);
            return $tempRepo->getDetail($id);
        }
        return $this->getDetail($id);
    }

    /**
     * Alias for incrementViews() - for backward compatibility
     * 
     * @param int $id News ID
     * @param int $currentView Current view count
     */
    public function updateNewsView(int $id, int $currentView): void
    {
        $this->incrementViews($id, $currentView);
    }

    /**
     * Alias for getGallery() - for backward compatibility
     * 
     * @param int $newsId News ID
     * @param string|null $type News type (optional)
     * @return array
     */
    public function getNewsGallery(int $newsId, ?string $type = null): array
    {
        if ($type && $type !== $this->type) {
            $tempRepo = new self($this->d, $this->lang, $type);
            return $tempRepo->getGallery($newsId);
        }
        return $this->getGallery($newsId);
    }

    /**
     * Get news items with filters and pagination
     * 
     * @param string|null $type News type (optional, uses default if not provided)
     * @param array $filters Filters (id_list, id_cat, id_item, id_sub, keyword, status)
     * @param int $start Start offset
     * @param int $perPage Items per page
     * @return array
     */
    public function getNewsItems(?string $type = null, array $filters = [], int $start = 0, int $perPage = 12): array
    {
        $type = $type ?: $this->type;
        [$where, $params] = $this->buildFilterClause($type, $filters);

        $params[] = $start;
        $params[] = $perPage;

        return $this->d->rawQuery(
            "select id, numb, name{$this->lang}, slug{$this->lang}, desc{$this->lang}, photo, 
                    id_list, id_cat, id_item, id_sub, type, date_created, view, status
             from #_news
             where {$where}
             order by numb,id desc
             limit ?, ?",
            $params
        );
    }

    /**
     * Count news items with filters
     * 
     * @param string|null $type News type (optional, uses default if not provided)
     * @param array $filters Filters (id_list, id_cat, id_item, id_sub, keyword, status)
     * @return int
     */
    public function countNewsItems(?string $type = null, array $filters = []): int
    {
        $type = $type ?: $this->type;
        [$where, $params] = $this->buildFilterClause($type, $filters);

        $result = $this->d->rawQueryOne(
            "select count(id) as total from #_news where {$where}",
            $params
        );

        return (int)($result['total'] ?? 0);
    }

    /**
     * Build filter clause for news queries
     * 
     * @param string $type News type
     * @param array $filters Filters
     * @return array [where clause, params]
     */
    private function buildFilterClause(string $type, array $filters): array
    {
        // KHÔNG có điều kiện status mặc định - hiển thị tất cả kể cả status rỗng
        $where = ["type = ?"];
        $params = [$type];

        // Category filters
        foreach (['id_list', 'id_cat', 'id_item', 'id_sub'] as $field) {
            if (!empty($filters[$field])) {
                $where[] = "{$field} = ?";
                $params[] = (int)$filters[$field];
            }
        }

        // Status filter
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $statuses = (array)$filters['status'];
            $statusClauses = [];
            foreach ($statuses as $status) {
                $status = trim((string)$status);
                if ($status === '') {
                    continue;
                }
                $statusClauses[] = "find_in_set(?,status)";
                $params[] = $status;
            }
            if ($statusClauses) {
                $where[] = '(' . implode(' or ', $statusClauses) . ')';
            }
        }

        // Keyword filter
        if (!empty($filters['keyword'])) {
            $keyword = '%' . $filters['keyword'] . '%';
            $where[] = "(name{$this->lang} like ? or slugvi like ? or slugen like ?)";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        return [implode(' and ', $where), $params];
    }
}

