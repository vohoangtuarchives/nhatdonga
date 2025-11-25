<?php

namespace Tuezy\Repository;

/**
 * ContactRepository - Data access layer for contact submissions
 */
class ContactRepository
{
    private $d;
    private $cache;

    public function __construct($d, $cache)
    {
        $this->d = $d;
        $this->cache = $cache;
    }

    /**
     * Get contact by ID
     * 
     * @param int $id Contact ID
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_contact WHERE id = ? LIMIT 0,1",
            [$id]
        );
    }

    /**
     * Get all contacts
     * 
     * @param array $filters Filters (status, keyword, date_from, date_to)
     * @param int $start Start offset
     * @param int $limit Limit results
     * @return array
     */
    public function getAll(array $filters = [], int $start = 0, int $limit = 20): array
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND find_in_set(?, status)";
            $params[] = $filters['status'];
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        if (!empty($filters['date_from'])) {
            $where .= " AND date_created >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where .= " AND date_created <= ?";
            $params[] = $filters['date_to'];
        }

        return $this->d->rawQuery(
            "SELECT * FROM #_contact 
             WHERE {$where} 
             ORDER BY date_created DESC 
             LIMIT {$start}, {$limit}",
            $params
        );
    }

    /**
     * Count contacts
     * 
     * @param array $filters Filters
     * @return int
     */
    public function count(array $filters = []): int
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND find_in_set(?, status)";
            $params[] = $filters['status'];
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        $result = $this->d->rawQueryOne(
            "SELECT COUNT(*) as total FROM #_contact WHERE {$where}",
            $params
        );

        return (int)($result['total'] ?? 0);
    }

    /**
     * Create contact
     * 
     * @param array $data Contact data
     * @return bool
     */
    public function create(array $data): bool
    {
        if (!isset($data['date_created'])) {
            $data['date_created'] = time();
        }
        if (!isset($data['numb'])) {
            $data['numb'] = 1;
        }
        return $this->d->insert('contact', $data);
    }

    /**
     * Update contact
     * 
     * @param int $id Contact ID
     * @param array $data Contact data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->d->where('id', $id);
        return $this->d->update('contact', $data);
    }

    /**
     * Delete contact
     * 
     * @param int $id Contact ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->d->where('id', $id);
        return $this->d->delete('contact');
    }

    /**
     * Mark as read
     * 
     * @param int $id Contact ID
     * @return bool
     */
    public function markAsRead(int $id): bool
    {
        return $this->update($id, ['status' => 'hienthi,daxem']);
    }
}

