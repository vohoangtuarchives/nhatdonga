<?php

namespace Tuezy\Repository;

/**
 * UserRepository - Data access layer for users (members)
 */
class UserRepository
{
    private $d;
    private $cache;

    public function __construct($d, $cache)
    {
        $this->d = $d;
        $this->cache = $cache;
    }

    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_member WHERE id = ? LIMIT 0,1",
            [$id]
        ) ?: null;
    }

    /**
     * Get user by email
     * 
     * @param string $email Email address
     * @return array|null
     */
    public function getByEmail(string $email): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_member WHERE email = ? LIMIT 0,1",
            [$email]
        ) ?: null;
    }

    /**
     * Get user by username
     * 
     * @param string $username Username
     * @return array|null
     */
    public function getByUsername(string $username): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_member WHERE username = ? LIMIT 0,1",
            [$username]
        ) ?: null;
    }

    /**
     * Get user by username or email
     * 
     * @param string $identifier Username or email
     * @return array|null
     */
    public function getByUsernameOrEmail(string $identifier): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_member WHERE (username = ? OR email = ?) AND find_in_set('hienthi',status) LIMIT 0,1",
            [$identifier, $identifier]
        ) ?: null;
    }

    /**
     * Create user
     * 
     * @param array $data User data
     * @return int|false User ID on success, false on failure
     */
    public function create(array $data): int|false
    {
        if (!isset($data['date_created'])) {
            $data['date_created'] = time();
        }
        if (!isset($data['numb'])) {
            $data['numb'] = 0;
        }
        
        if ($this->d->insert('member', $data)) {
            return $this->d->getLastInsertId();
        }
        
        return false;
    }

    /**
     * Update user
     * 
     * @param int $id User ID
     * @param array $data User data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->d->where('id', $id);
        return $this->d->update('member', $data);
    }

    /**
     * Update password
     * 
     * @param int $id User ID
     * @param string $password Password (will be hashed)
     * @return bool
     */
    public function updatePassword(int $id, string $password): bool
    {
        return $this->update($id, ['password' => md5($password)]);
    }

    /**
     * Update last login
     * 
     * @param int $id User ID
     * @return bool
     */
    public function updateLastLogin(int $id): bool
    {
        return $this->update($id, ['lastlogin' => time()]);
    }

    /**
     * Check if email exists
     * 
     * @param string $email Email address
     * @param int|null $excludeId User ID to exclude
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $where = "email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $where .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->d->rawQueryOne(
            "SELECT id FROM #_member WHERE {$where} LIMIT 0,1",
            $params
        );
        
        return !empty($result);
    }

    /**
     * Check if username exists
     * 
     * @param string $username Username
     * @param int|null $excludeId User ID to exclude
     * @return bool
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $where = "username = ?";
        $params = [$username];
        
        if ($excludeId) {
            $where .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->d->rawQueryOne(
            "SELECT id FROM #_member WHERE {$where} LIMIT 0,1",
            $params
        );
        
        return !empty($result);
    }

    /**
     * Get all users with filters
     * 
     * @param array $filters Filters (status, keyword)
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

        return $this->d->rawQuery(
            "SELECT * FROM #_member 
             WHERE {$where} 
             ORDER BY date_created DESC 
             LIMIT {$start}, {$limit}",
            $params
        );
    }

    /**
     * Count users
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
            "SELECT COUNT(*) as total FROM #_member WHERE {$where}",
            $params
        );

        return (int)($result['total'] ?? 0);
    }

    /**
     * Delete user
     * 
     * @param int $id User ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->d->where('id', $id);
        return $this->d->delete('member');
    }
}

