<?php

namespace Tuezy\Repository;

/**
 * CommentRepository - Data access layer for comments
 * Handles all database operations for comments
 */
class CommentRepository
{
    private $db;
    private $cache;
    private string $lang;
    private string $sluglang;

    public function __construct($db, $cache, string $lang = 'vi', string $sluglang = 'slugvi')
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->lang = $lang;
        $this->sluglang = $sluglang;
    }

    /**
     * Get comments by variant ID and type
     * 
     * @param int $idVariant Variant ID (product ID, etc.)
     * @param string $type Type (product, news, etc.)
     * @param bool $isAdmin Include hidden comments if admin
     * @param int $limitFrom Offset
     * @param int $limitGet Limit
     * @return array Comments
     */
    public function getByVariant(int $idVariant, string $type, bool $isAdmin = false, int $limitFrom = 0, int $limitGet = 10): array
    {
        $where = $isAdmin ? "" : "find_in_set('hienthi',status) and";
        $sql = "SELECT * FROM #_comment 
                WHERE $where id_parent = 0 AND id_variant = ? AND type = ? 
                ORDER BY date_posted DESC 
                LIMIT $limitFrom, $limitGet";
        
        return $this->db->rawQuery($sql, [$idVariant, $type]) ?: [];
    }

    /**
     * Get replies for a comment
     * 
     * @param int $idParent Parent comment ID
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @param bool $isAdmin Include hidden if admin
     * @param int $limitFrom Offset
     * @param int $limitGet Limit
     * @return array Replies
     */
    public function getReplies(int $idParent, int $idVariant, string $type, bool $isAdmin = false, int $limitFrom = 0, int $limitGet = 10): array
    {
        $where = $isAdmin ? "" : "find_in_set('hienthi',status) and";
        $sql = "SELECT * FROM #_comment 
                WHERE $where id_parent = ? AND id_variant = ? AND type = ? 
                ORDER BY date_posted DESC 
                LIMIT $limitFrom, $limitGet";
        
        return $this->db->rawQuery($sql, [$idParent, $idVariant, $type]) ?: [];
    }

    /**
     * Get total count of comments
     * 
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @param bool $isAdmin Include hidden if admin
     * @return int Count
     */
    public function getTotal(int $idVariant, string $type, bool $isAdmin = false): int
    {
        $where = $isAdmin ? "" : "find_in_set('hienthi',status) and";
        $row = $this->db->rawQueryOne(
            "SELECT COUNT(id) as num FROM #_comment 
             WHERE $where id_parent = 0 AND id_variant = ? AND type = ?",
            [$idVariant, $type]
        );
        
        return (int)($row['num'] ?? 0);
    }

    /**
     * Get total count of replies
     * 
     * @param int $idParent Parent comment ID
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @param bool $isAdmin Include hidden if admin
     * @return int Count
     */
    public function getTotalReplies(int $idParent, int $idVariant, string $type, bool $isAdmin = false): int
    {
        $where = $isAdmin ? "" : "find_in_set('hienthi',status) and";
        $row = $this->db->rawQueryOne(
            "SELECT COUNT(id) as num FROM #_comment 
             WHERE $where id_parent = ? AND id_variant = ? AND type = ?",
            [$idParent, $idVariant, $type]
        );
        
        return (int)($row['num'] ?? 0);
    }

    /**
     * Get star count for a rating
     * 
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @param int $star Star rating (1-5)
     * @return int Count
     */
    public function getStarCount(int $idVariant, string $type, int $star): int
    {
        $row = $this->db->rawQueryOne(
            "SELECT COUNT(id) as num FROM #_comment 
             WHERE find_in_set('hienthi',status) AND id_variant = ? AND type = ? AND star = ?",
            [$idVariant, $type, $star]
        );
        
        return (int)($row['num'] ?? 0);
    }

    /**
     * Get total stars sum
     * 
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @return int Total stars
     */
    public function getTotalStars(int $idVariant, string $type): int
    {
        $row = $this->db->rawQueryOne(
            "SELECT SUM(star) as total_star FROM #_comment 
             WHERE find_in_set('hienthi',status) AND id_variant = ? AND type = ?",
            [$idVariant, $type]
        );
        
        return (int)($row['total_star'] ?? 0);
    }

    /**
     * Get comment photos
     * 
     * @param int $idParent Comment ID
     * @return array Photos
     */
    public function getPhotos(int $idParent): array
    {
        return $this->db->rawQuery(
            "SELECT id, photo FROM #_comment_photo WHERE id_parent = ?",
            [$idParent]
        ) ?: [];
    }

    /**
     * Get comment video
     * 
     * @param int $idParent Comment ID
     * @return array|null Video data
     */
    public function getVideo(int $idParent): ?array
    {
        $result = $this->db->rawQueryOne(
            "SELECT id, photo, video FROM #_comment_video WHERE id_parent = ? LIMIT 0,1",
            [$idParent]
        );
        return $result ?: null;
    }

    /**
     * Create comment
     * 
     * @param array $data Comment data
     * @return int|false Comment ID or false on failure
     */
    public function create(array $data)
    {
        if ($this->db->insert('comment', $data)) {
            return $this->db->getLastInsertId();
        }
        return false;
    }

    /**
     * Update comment
     * 
     * @param int $id Comment ID
     * @param array $data Comment data
     * @return bool Success
     */
    public function update(int $id, array $data): bool
    {
        $this->db->where('id', $id);
        return $this->db->update('comment', $data);
    }

    /**
     * Delete comment
     * 
     * @param int $id Comment ID
     * @return bool Success
     */
    public function delete(int $id): bool
    {
        $this->db->where('id', $id);
        return $this->db->delete('comment');
    }

    /**
     * Delete comment replies
     * 
     * @param int $idParent Parent comment ID
     * @return bool Success
     */
    public function deleteReplies(int $idParent): bool
    {
        return $this->db->rawQuery(
            "DELETE FROM #_comment WHERE id_parent = ?",
            [$idParent]
        ) !== false;
    }

    /**
     * Delete comment photos
     * 
     * @param int $idParent Comment ID
     * @return bool Success
     */
    public function deletePhotos(int $idParent): bool
    {
        return $this->db->rawQuery(
            "DELETE FROM #_comment_photo WHERE id_parent = ?",
            [$idParent]
        ) !== false;
    }

    /**
     * Delete comment video
     * 
     * @param int $idParent Comment ID
     * @return bool Success
     */
    public function deleteVideo(int $idParent): bool
    {
        return $this->db->rawQuery(
            "DELETE FROM #_comment_video WHERE id_parent = ?",
            [$idParent]
        ) !== false;
    }

    /**
     * Get comment by ID
     * 
     * @param int $id Comment ID
     * @return array|null Comment data
     */
    public function getById(int $id): ?array
    {
        $result = $this->db->rawQueryOne(
            "SELECT * FROM #_comment WHERE id = ? LIMIT 0,1",
            [$id]
        );
        return $result ?: null;
    }

    /**
     * Count new posts by status
     * 
     * @param int $idVariant Variant ID
     * @param string $type Type
     * @param string $status Status to check
     * @return int Count
     */
    public function countNewPosts(int $idVariant, string $type, string $status): int
    {
        $rows = $this->db->rawQuery(
            "SELECT id_variant FROM #_comment 
             WHERE id_variant = ? AND type = ? AND find_in_set(?, status)",
            [$idVariant, $type, $status]
        );
        
        return $rows ? count($rows) : 0;
    }

    /**
     * Create comment photo
     * 
     * @param int $idParent Comment ID
     * @param string $photo Photo filename
     * @return int|false Photo ID or false on failure
     */
    public function createPhoto(int $idParent, string $photo)
    {
        $data = [
            'id_parent' => $idParent,
            'photo' => $photo,
        ];
        
        if ($this->db->insert('comment_photo', $data)) {
            return $this->db->getLastInsertId();
        }
        return false;
    }

    /**
     * Create comment video
     * 
     * @param array $data Video data
     * @return int|false Video ID or false on failure
     */
    public function createVideo(array $data)
    {
        if ($this->db->insert('comment_video', $data)) {
            return $this->db->getLastInsertId();
        }
        return false;
    }
}

