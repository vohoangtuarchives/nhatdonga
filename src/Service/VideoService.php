<?php

namespace Tuezy\Service;

use Tuezy\Repository\PhotoRepository;

/**
 * VideoService - Business logic layer for videos
 * Videos are stored in photo table
 */
class VideoService
{
    public function __construct(
        private PhotoRepository $photos,
        private \PDODb $db
    ) {
    }

    /**
     * Get video list with filters
     * 
     * @param string $type Video type
     * @param array $filters Filters (noibat, keyword)
     * @param int $start Start offset
     * @param int $limit Limit results
     * @return array
     */
    public function getVideoList(string $type, array $filters = [], int $start = 0, int $limit = 20): array
    {
        $where = "type = ? AND act <> ? AND find_in_set('hienthi',status)";
        $params = [$type, 'photo_static'];

        if (!empty($filters['noibat'])) {
            $where .= " AND find_in_set('noibat',status)";
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (namevi LIKE ? OR nameen LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
        }

        $limitSql = $limit > 0 ? " LIMIT {$start}, {$limit}" : "";

        return $this->db->rawQuery(
            "SELECT id, namevi, nameen, photo, link_video, date_created 
             FROM #_photo 
             WHERE {$where} 
             ORDER BY numb, id DESC {$limitSql}",
            $params
        );
    }

    /**
     * Count videos with filters
     * 
     * @param string $type Video type
     * @param array $filters Filters
     * @return int
     */
    public function countVideos(string $type, array $filters = []): int
    {
        $where = "type = ? AND act <> ? AND find_in_set('hienthi',status)";
        $params = [$type, 'photo_static'];

        if (!empty($filters['noibat'])) {
            $where .= " AND find_in_set('noibat',status)";
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (namevi LIKE ? OR nameen LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
        }

        $result = $this->db->rawQueryOne(
            "SELECT COUNT(*) as total FROM #_photo WHERE {$where}",
            $params
        );

        return (int)($result['total'] ?? 0);
    }

    /**
     * Get video detail by ID
     * 
     * @param int $id Video ID
     * @return array|null
     */
    public function getVideoDetail(int $id): ?array
    {
        return $this->photos->getById($id);
    }

    /**
     * Get featured videos
     * 
     * @param string $type Video type
     * @param int $limit Limit results
     * @return array
     */
    public function getFeaturedVideos(string $type = 'video', int $limit = 0): array
    {
        return $this->photos->getFeaturedVideos($type, $limit);
    }

    /**
     * Get video link (static video)
     * 
     * @return array|null
     */
    public function getVideoLink(): ?array
    {
        return $this->photos->getVideoLink();
    }
}

