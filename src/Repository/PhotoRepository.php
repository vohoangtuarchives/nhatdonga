<?php

namespace Tuezy\Repository;

/**
 * PhotoRepository - Data access layer cho module photo/gallery
 * Sử dụng cho certificates, banners, và các loại ảnh khác
 */
class PhotoRepository
{
    private \PDODb $d;
    private string $lang;
    private ?string $sluglang;

    public function __construct(\PDODb $d, string $lang, ?string $sluglang = null)
    {
        $this->d = $d;
        $this->lang = $lang;
        $this->sluglang = $sluglang;
    }

    /**
     * Lấy ảnh theo type (certificates, banners, etc.)
     * 
     * @param string $type Loại ảnh (chung-nhan, banner, etc.)
     * @param int $limit Số lượng
     * @param bool $active Chỉ lấy ảnh active
     * @return array
     */
    public function getByType(string $type, int $limit = 6, bool $active = true): array
    {
        $where = "type = ?";
        $params = [$type];
        
        if ($active) {
            $where .= " AND find_in_set('hienthi',status)";
        }
        
        $params[] = $limit;

        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, photo, link, options 
             FROM #_photo 
             WHERE {$where} 
             ORDER BY numb, id DESC
             LIMIT 0, ?",
            $params
        ) ?: [];
    }

    /**
     * Lấy ảnh theo ID
     * 
     * @param int $id Photo ID
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $result = $this->d->rawQueryOne(
            "SELECT id, name{$this->lang}, photo, link, options, type, status 
             FROM #_photo 
             WHERE id = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$id]
        );
        return $result ?: null;
    }

    /**
     * Lấy ảnh theo act và type
     * 
     * @param string $act Act value
     * @param string $type Type value
     * @param int $limit Số lượng
     * @return array
     */
    public function getByActAndType(string $act, string $type, int $limit = 1): array
    {
        $params = [$act, $type, $limit];

        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, photo, link, options, status 
             FROM #_photo 
             WHERE act = ? AND type = ? AND find_in_set('hienthi',status) 
             ORDER BY numb, id DESC
             LIMIT 0, ?",
            $params
        ) ?: [];
    }

    /**
     * Lấy ảnh theo type và act (alias cho getByActAndType với thứ tự tham số khác)
     * 
     * @param string $type Type value
     * @param string $act Act value
     * @param int $limit Số lượng
     * @return array|null Single result nếu limit = 1, array nếu limit > 1
     */
    public function getByTypeAndAct(string $type, string $act, int $limit = 1)
    {
        $result = $this->getByActAndType($act, $type, $limit);
        
        // Nếu limit = 1, trả về single item (null nếu không có)
        if ($limit === 1) {
            return !empty($result) ? $result[0] : null;
        }
        
        return $result;
    }

    /**
     * Lấy single photo static (act = 'photo_static')
     * 
     * @param string $type Loại photo (favicon, logo, banner, video)
     * @param string $fields Các fields cần select
     * @return array|null
     */
    private function getPhotoStatic(string $type, string $fields = 'id, photo, options'): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT {$fields} 
             FROM #_photo 
             WHERE type = ? AND act = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            [$type, 'photo_static']
        ) ?: null;
    }

    /**
     * Lấy multiple photos theo type (result)
     * 
     * @param string $type Loại photo
     * @param string|null $act Act value (null = không filter theo act)
     * @param string|null $fields Các fields cần select (null = dùng default)
     * @param int $limit Số lượng (0 = không giới hạn)
     * @return array
     */
    private function getPhotosByType(
        string $type,
        ?string $act = null,
        ?string $fields = null,
        int $limit = 0
    ): array {
        if ($fields === null) {
            $fields = "id, name{$this->lang}, photo, link, options";
        }
        $where = "type = ?";
        $params = [$type];
        
        if ($act !== null) {
            $where .= " AND act = ?";
            $params[] = $act;
        }
        
        $where .= " AND find_in_set('hienthi',status)";
        
        $orderBy = "ORDER BY numb, id DESC";
        $limitClause = $limit > 0 ? "LIMIT 0, ?" : "";
        
        if ($limit > 0) {
            $params[] = $limit;
        }
        
        return $this->d->rawQuery(
            "SELECT {$fields} 
             FROM #_photo 
             WHERE {$where} 
             {$orderBy}
             {$limitClause}",
            $params
        ) ?: [];
    }

    /**
     * Lấy favicon
     * 
     * @return array|null
     */
    public function getFavicon(): ?array
    {
        return $this->getPhotoStatic('favicon', 'id, photo, options');
    }

    /**
     * Lấy logo
     * 
     * @return array|null
     */
    public function getLogo(): ?array
    {
        return $this->getPhotoStatic('logo', 'id, photo, options');
    }

    /**
     * Lấy banner
     * 
     * @return array|null
     */
    public function getBanner(): ?array
    {
        return $this->getPhotoStatic('banner', 'id, photo, options, link');
    }

    /**
     * Lấy video link
     * 
     * @return array|null
     */
    public function getVideoLink(): ?array
    {
        return $this->getPhotoStatic('video', 'id, photo, link_video');
    }

    /**
     * Lấy slider photos
     * Lấy từ type='slide' (man_photo - act IS NULL hoặc act != 'photo_static')
     * Hoặc type='slider' với act='photo_static' (tương thích cũ)
     * 
     * @return array
     */
    public function getSlider(): array
    {
        // Ưu tiên lấy từ type='slide' (man_photo - act IS NULL hoặc act != 'photo_static')
        $where = "type = ? AND (act IS NULL OR act = '' OR act != ?) AND find_in_set('hienthi',status)";
        $params = ['slide', 'photo_static'];
        
        $slidePhotos = $this->d->rawQuery(
            "SELECT id, name{$this->lang}, photo, link, options, status 
             FROM #_photo 
             WHERE {$where} 
             ORDER BY numb, id DESC",
            $params
        ) ?: [];
        
        // Nếu không có, fallback về type='slider' với act='photo_static' (tương thích cũ)
        if (empty($slidePhotos)) {
            $slidePhotos = $this->getPhotosByType('slider', 'photo_static', "id, name{$this->lang}, photo, link, options");
        }
        
        return $slidePhotos;
    }

    /**
     * Lấy social links
     * 
     * @return array
     */
    public function getSocial(): array
    {
        return $this->getPhotosByType('social', 'photo_static', "id, name{$this->lang}, photo, link, options");
    }

    /**
     * Lấy partners (đối tác)
     * 
     * @return array
     */
    public function getPartners(): array
    {
        return $this->getPhotosByType('doitac', null, "id, name{$this->lang}, photo, link, options");
    }

    /**
     * Lấy screenshot
     * 
     * @return array|null
     */
    public function getScreenshot(): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT id, photo, options 
             FROM #_photo 
             WHERE type = ? AND find_in_set('hienthi',status) 
             LIMIT 0,1",
            ['screenshot']
        ) ?: null;
    }

    /**
     * Lấy featured videos (videos nổi bật)
     * 
     * @param string $type Loại video (thường là 'video')
     * @param int $limit Số lượng (0 = không giới hạn)
     * @return array
     */
    public function getFeaturedVideos(string $type = 'video', int $limit = 0): array
    {
        $where = "type = ? AND act <> ? AND find_in_set('hienthi',status) AND find_in_set('noibat',status)";
        $params = [$type, 'photo_static'];
        
        $orderBy = "ORDER BY numb, id DESC";
        $limitClause = $limit > 0 ? "LIMIT 0, ?" : "";
        
        if ($limit > 0) {
            $params[] = $limit;
        }
        
        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, photo, link_video, date_created 
             FROM #_photo 
             WHERE {$where} 
             {$orderBy}
             {$limitClause}",
            $params
        ) ?: [];
    }

    /**
     * Lấy videos theo type
     * 
     * @param string $type Loại video (thường là 'video')
     * @param int $limit Số lượng (0 = không giới hạn)
     * @return array
     */
    public function getVideosByType(string $type = 'video', int $limit = 0): array
    {
        $where = "type = ? AND act <> ? AND find_in_set('hienthi',status)";
        $params = [$type, 'photo_static'];
        
        $orderBy = "ORDER BY numb, id DESC";
        $limitClause = $limit > 0 ? "LIMIT 0, ?" : "";
        
        if ($limit > 0) {
            $params[] = $limit;
        }
        
        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, photo, link_video, date_created 
             FROM #_photo 
             WHERE {$where} 
             {$orderBy}
             {$limitClause}",
            $params
        ) ?: [];
    }
}
