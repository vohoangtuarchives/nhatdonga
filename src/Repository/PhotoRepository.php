<?php

namespace Tuezy\Repository;

/**
 * PhotoRepository - Data access layer for photos
 */
class PhotoRepository
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
     * Get photos by type
     * 
     * @param string $type Photo type
     * @param bool $active Only active photos
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

        return $this->cache->get(
            "SELECT id, name{$this->lang}, photo, link, options 
             FROM #_photo 
             WHERE {$where} 
             ORDER BY {$order} {$limitSql}",
            $params,
            'result',
            7200
        );
    }

    /**
     * Get photo by type and act
     * 
     * @param string $type Photo type
     * @param string $act Photo act
     * @return array|null
     */
    public function getByTypeAndAct(string $type, string $act): ?array
    {
        $result = $this->cache->get(
            "SELECT id, photo, options 
             FROM #_photo 
             WHERE type = ? AND act = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$type, $act],
            'fetch',
            7200
        );

        return $result ?: null;
    }

    /**
     * Get logo
     * 
     * @return array|null
     */
    public function getLogo(): ?array
    {
        return $this->getByTypeAndAct('logo', 'photo_static');
    }

    /**
     * Get favicon
     * 
     * @return array|null
     */
    public function getFavicon(): ?array
    {
        return $this->getByTypeAndAct('favicon', 'photo_static');
    }

    /**
     * Get banner
     * 
     * @return array|null
     */
    public function getBanner(): ?array
    {
        return $this->getByTypeAndAct('banner', 'photo_static');
    }

    /**
     * Get screenshot
     * 
     * @return array|null
     */
    public function getScreenshot(): ?array
    {
        $result = $this->cache->get(
            "SELECT id, photo, options 
             FROM #_photo 
             WHERE type = ? 
             LIMIT 0,1",
            ['screenshot'],
            'fetch',
            7200
        );
        return $result ?: null;
    }

    /**
     * Get video link
     * 
     * @return array|null
     */
    public function getVideoLink(): ?array
    {
        return $this->getByTypeAndAct('video', 'photo_static');
    }

    /**
     * Get slider photos
     * 
     * @return array
     */
    public function getSlider(): array
    {
        return $this->getByType('slide', true, 0, "numb,id desc");
    }

    /**
     * Get social links
     * 
     * @return array
     */
    public function getSocial(): array
    {
        return $this->getByType('social', true, 0, "numb,id desc");
    }

    /**
     * Get partners (doitac)
     * 
     * @return array
     */
    public function getPartners(): array
    {
        return $this->getByType('doitac', true, 0, "numb,id desc");
    }

    /**
     * Get photo by ID
     * 
     * @param int $id Photo ID
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_photo WHERE id = ? LIMIT 0,1",
            [$id]
        );
    }

    /**
     * Create photo
     * 
     * @param array $data Photo data
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
        return $this->d->insert('photo', $data);
    }

    /**
     * Update photo
     * 
     * @param int $id Photo ID
     * @param array $data Photo data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->d->where('id', $id);
        return $this->d->update('photo', $data);
    }

    /**
     * Delete photo
     * 
     * @param int $id Photo ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->d->where('id', $id);
        return $this->d->delete('photo');
    }

    /**
     * Get photos by type (alias for getByType)
     * 
     * @param string $type Photo type
     * @param bool $active Only active photos
     * @param int $limit Limit results
     * @param string $order Order by clause
     * @return array
     */
    public function getPhotos(string $type, bool $active = true, int $limit = 0, string $order = "numb,id desc"): array
    {
        return $this->getByType($type, $active, $limit, $order);
    }

    /**
     * Get videos (photos with act != 'photo_static')
     * 
     * @param string $type Video type
     * @param int $limit Limit results
     * @param string $order Order by clause
     * @return array
     */
    public function getVideos(string $type, int $limit = 0, string $order = "numb,id desc"): array
    {
        $where = "type = ? AND act <> ? AND find_in_set('hienthi',status)";
        $params = [$type, 'photo_static'];
        
        $limitSql = $limit > 0 ? " LIMIT 0,{$limit}" : "";

        return $this->cache->get(
            "SELECT photo, link_video, name{$this->lang} 
             FROM #_photo 
             WHERE {$where} 
             ORDER BY {$order} {$limitSql}",
            $params,
            'result',
            7200
        );
    }

    /**
     * Get featured videos (with noibat status)
     * 
     * @param string $type Video type
     * @param int $limit Limit results
     * @param string $order Order by clause
     * @return array
     */
    public function getFeaturedVideos(string $type = 'video', int $limit = 0, string $order = "numb,id desc"): array
    {
        $where = "type = ? AND act <> ? AND find_in_set('noibat',status) AND find_in_set('hienthi',status)";
        $params = [$type, 'photo_static'];
        
        $limitSql = $limit > 0 ? " LIMIT 0,{$limit}" : "";

        return $this->cache->get(
            "SELECT id, link_video, name{$this->lang} 
             FROM #_photo 
             WHERE {$where} 
             ORDER BY {$order} {$limitSql}",
            $params,
            'result',
            7200
        );
    }

    /**
     * Get videos by type (simple, without featured)
     * 
     * @param string $type Video type
     * @param int $limit Limit results
     * @param string $order Order by clause
     * @return array
     */
    public function getVideosByType(string $type = 'video', int $limit = 0, string $order = "numb,id desc"): array
    {
        $where = "type = ? AND find_in_set('hienthi',status)";
        $params = [$type];
        
        $limitSql = $limit > 0 ? " LIMIT 0,{$limit}" : "";

        return $this->cache->get(
            "SELECT name{$this->lang}, link_video 
             FROM #_photo 
             WHERE {$where} 
             ORDER BY {$order} {$limitSql}",
            $params,
            'result',
            7200
        );
    }
}

