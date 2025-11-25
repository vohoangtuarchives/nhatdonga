<?php

namespace Tuezy\Repository;

/**
 * StaticRepository - Data access layer for static content
 */
class StaticRepository
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
     * Get static content by type
     * 
     * @param string $type Static type
     * @return array|null
     */
    public function getByType(string $type): ?array
    {
        $result = $this->cache->get(
            "SELECT id, type, name{$this->lang}, content{$this->lang}, photo, date_created, date_updated, options 
             FROM #_static 
             WHERE type = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$type],
            'fetch',
            7200
        );
        return $result ?: null;
    }

    /**
     * Get static content by ID
     * 
     * @param int $id Static ID
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $result = $this->d->rawQueryOne(
            "SELECT id, type, name{$this->lang}, content{$this->lang}, photo, date_created, date_updated, options 
             FROM #_static 
             WHERE id = ? 
             LIMIT 0,1",
            [$id]
        );
        return $result ?: null;
    }

    /**
     * Get all static content by type
     * 
     * @param string $type Static type
     * @param bool $active Only active items
     * @return array
     */
    public function getAllByType(string $type, bool $active = true): array
    {
        $where = "type = ?";
        $params = [$type];
        
        if ($active) {
            $where .= " AND find_in_set('hienthi',status)";
        }

        return $this->cache->get(
            "SELECT id, type, name{$this->lang}, content{$this->lang}, photo, date_created, date_updated 
             FROM #_static 
             WHERE {$where} 
             ORDER BY numb, id DESC",
            $params,
            'result',
            7200
        );
    }

    /**
     * Create static content
     * 
     * @param array $data Static data
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
        return $this->d->insert('static', $data);
    }

    /**
     * Update static content
     * 
     * @param int $id Static ID
     * @param array $data Static data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $data['date_updated'] = time();
        $this->d->where('id', $id);
        return $this->d->update('static', $data);
    }

    /**
     * Delete static content
     * 
     * @param int $id Static ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->d->where('id', $id);
        return $this->d->delete('static');
    }
}

