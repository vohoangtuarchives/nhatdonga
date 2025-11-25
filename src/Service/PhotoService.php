<?php

namespace Tuezy\Service;

use Tuezy\Repository\PhotoRepository;

/**
 * PhotoService - Business logic layer for photos
 */
class PhotoService
{
    public function __construct(
        private PhotoRepository $photos,
        private \PDODb $db
    ) {
    }

    /**
     * Get photo gallery for a parent item
     * 
     * @param string $type Photo type
     * @param int $parentId Parent ID
     * @param string $kind Gallery kind
     * @param string $val Gallery value
     * @return array
     */
    public function getPhotoGallery(string $type, int $parentId, string $kind = 'man', string $val = ''): array
    {
        $where = "id_parent = ? AND com = ? AND type = ? AND kind = ?";
        $params = [$parentId, $type, $type, $kind];
        
        if ($val) {
            $where .= " AND val = ?";
            $params[] = $val;
        }

        return $this->db->rawQuery(
            "SELECT * FROM #_gallery 
             WHERE {$where} 
             ORDER BY numb, id DESC",
            $params
        );
    }

    /**
     * Get watermark configuration
     * 
     * @return array|null
     */
    public function getWatermarkConfig(): ?array
    {
        return $this->photos->getByTypeAndAct('watermark', 'photo_static');
    }

    /**
     * Get logo
     * 
     * @return array|null
     */
    public function getLogo(): ?array
    {
        return $this->photos->getLogo();
    }

    /**
     * Get favicon
     * 
     * @return array|null
     */
    public function getFavicon(): ?array
    {
        return $this->photos->getFavicon();
    }

    /**
     * Get banner
     * 
     * @return array|null
     */
    public function getBanner(): ?array
    {
        return $this->photos->getBanner();
    }

    /**
     * Get slider photos
     * 
     * @return array
     */
    public function getSlider(): array
    {
        return $this->photos->getSlider();
    }

    /**
     * Get social links
     * 
     * @return array
     */
    public function getSocial(): array
    {
        return $this->photos->getSocial();
    }

    /**
     * Get partners
     * 
     * @return array
     */
    public function getPartners(): array
    {
        return $this->photos->getPartners();
    }
}

