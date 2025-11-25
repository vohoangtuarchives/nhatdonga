<?php

namespace Tuezy\Repository;

/**
 * NewsletterRepository - Data access layer for newsletter subscriptions
 */
class NewsletterRepository
{
    private $d;
    private $cache;

    public function __construct($d, $cache)
    {
        $this->d = $d;
        $this->cache = $cache;
    }

    /**
     * Get newsletter by ID
     * 
     * @param int $id Newsletter ID
     * @param string|null $type Newsletter type (optional)
     * @return array|null
     */
    public function getById(int $id, ?string $type = null): ?array
    {
        $where = "id = ?";
        $params = [$id];
        
        if ($type) {
            $where .= " AND type = ?";
            $params[] = $type;
        }
        
        return $this->d->rawQueryOne(
            "SELECT * FROM #_newsletter WHERE {$where} LIMIT 0,1",
            $params
        );
    }

    /**
     * Get newsletter by email
     * 
     * @param string $email Email address
     * @return array|null
     */
    public function getByEmail(string $email): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_newsletter WHERE email = ? LIMIT 0,1",
            [$email]
        );
    }

    /**
     * Get all newsletters
     * 
     * @param array $filters Filters (type, keyword, date_from, date_to)
     * @param int $start Start offset
     * @param int $limit Limit results
     * @return array
     */
    public function getAll(array $filters = [], int $start = 0, int $limit = 20): array
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['type'])) {
            $where .= " AND type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (email LIKE ? OR fullname LIKE ? OR phone LIKE ?)";
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
            "SELECT * FROM #_newsletter 
             WHERE {$where} 
             ORDER BY date_created DESC 
             LIMIT {$start}, {$limit}",
            $params
        );
    }

    /**
     * Count newsletters
     * 
     * @param array $filters Filters
     * @return int
     */
    public function count(array $filters = []): int
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['type'])) {
            $where .= " AND type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (email LIKE ? OR fullname LIKE ? OR phone LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        $result = $this->d->rawQueryOne(
            "SELECT COUNT(*) as total FROM #_newsletter WHERE {$where}",
            $params
        );

        return (int)($result['total'] ?? 0);
    }

    /**
     * Create newsletter
     * 
     * @param array $data Newsletter data
     * @return bool
     */
    public function create(array $data): bool
    {
        if (!isset($data['date_created'])) {
            $data['date_created'] = time();
        }
        if (!isset($data['type'])) {
            $data['type'] = 'dangkynhantin';
        }
        return $this->d->insert('newsletter', $data);
    }

    /**
     * Update newsletter
     * 
     * @param int $id Newsletter ID
     * @param array $data Newsletter data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->d->where('id', $id);
        return $this->d->update('newsletter', $data);
    }

    /**
     * Delete newsletter
     * 
     * @param int $id Newsletter ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->d->where('id', $id);
        return $this->d->delete('newsletter');
    }

    /**
     * Check if email exists
     * 
     * @param string $email Email address
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        $result = $this->getByEmail($email);
        return !empty($result);
    }
}

