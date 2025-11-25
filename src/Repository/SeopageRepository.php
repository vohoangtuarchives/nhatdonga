<?php

namespace Tuezy\Repository;

/**
 * SeopageRepository - Data access layer for SEO pages
 */
class SeopageRepository
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
     * Get SEO page by type
     * 
     * @param string $type SEO page type
     * @return array|null
     */
    public function getByType(string $type): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_seopage WHERE type = ? LIMIT 0,1",
            [$type]
        );
    }

    /**
     * Get all SEO pages
     * 
     * @return array
     */
    public function getAll(): array
    {
        return $this->d->rawQuery("SELECT * FROM #_seopage ORDER BY id DESC");
    }

    /**
     * Create SEO page
     * 
     * @param array $data SEO page data
     * @return bool
     */
    public function create(array $data): bool
    {
        if (!isset($data['date_created'])) {
            $data['date_created'] = time();
        }
        return $this->d->insert('seopage', $data);
    }

    /**
     * Update SEO page by type
     * 
     * @param string $type SEO page type
     * @param array $data SEO page data
     * @return bool
     */
    public function updateByType(string $type, array $data): bool
    {
        if (!isset($data['date_updated'])) {
            $data['date_updated'] = time();
        }
        $this->d->where('type', $type);
        return $this->d->update('seopage', $data);
    }

    /**
     * Update SEO page photo
     * 
     * @param string $type SEO page type
     * @param string $photo Photo filename
     * @return bool
     */
    public function updatePhoto(string $type, string $photo): bool
    {
        $this->d->where('type', $type);
        return $this->d->update('seopage', ['photo' => $photo]);
    }

    /**
     * Delete SEO page
     * 
     * @param int $id SEO page ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->d->where('id', $id);
        return $this->d->delete('seopage');
    }
}

