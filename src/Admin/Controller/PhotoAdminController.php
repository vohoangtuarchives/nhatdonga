<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Repository\PhotoRepository;
use Tuezy\Service\PhotoService;
use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * PhotoAdminController - Handles photo admin requests
 */
class PhotoAdminController extends BaseAdminController
{
    private PhotoService $photoService;
    private PhotoRepository $photoRepo;
    private string $type;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper,
        string $type = 'photo'
    ) {
        parent::__construct($db, $cache, $func, $config, $authHelper, $permissionHelper);

        $this->type = $type;
        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->photoRepo = new PhotoRepository($db, $lang, $sluglang);
        $this->photoService = new PhotoService($this->photoRepo, $db);
        
        // Initialize CRUD helper for photos
        $this->crudHelper = new \Tuezy\Admin\AdminCRUDHelper(
            $db,
            $func,
            'photo',
            $type,
            $config['photo'][$type] ?? []
        );
    }

    /**
     * Get watermark configuration
     * 
     * @return array|null
     */
    public function getWatermarkConfig(): ?array
    {
        $this->requireAuth();
        return $this->photoService->getWatermarkConfig();
    }

    /**
     * List photos
     * 
     * @param array $filters Filters
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function manPhoto(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $this->requireAuth();

        // Build WHERE conditions
        $where = [];
        if (!empty($filters['keyword'])) {
            $where[] = [
                'clause' => '(namevi LIKE ? OR nameen LIKE ?)',
                'params' => ["%{$filters['keyword']}%", "%{$filters['keyword']}%"]
            ];
        }

        $result = $this->crudHelper->getList($page, $perPage, $where);
        
        // Build URL for pagination
        $this->urlHelper->reset();
        if (!empty($filters['keyword'])) {
            $this->urlHelper->addParam('keyword', $filters['keyword']);
        }
        $url = $this->urlHelper->getUrl('photo', 'man_photo', $this->type);
        $paging = $this->func->pagination($result['total'], $perPage, $page, $url);

        return [
            'items' => $result['items'],
            'total' => $result['total'],
            'paging' => $paging,
            'type' => $this->type,
        ];
    }

    /**
     * Get photo by ID
     * 
     * @param int $id Photo ID
     * @return array|null
     */
    public function getPhoto(int $id): ?array
    {
        $this->requireAuth();
        return $this->crudHelper->getItem($id);
    }

    /**
     * Save photo
     * 
     * @param array $data Photo data
     * @param int|null $id Photo ID (null for new)
     * @return bool Success
     */
    public function savePhoto(array $data, ?int $id = null): bool
    {
        $this->requireAuth();
        return $this->crudHelper->save($data, $id);
    }

    /**
     * Delete photo
     * 
     * @param int $id Photo ID
     * @return bool Success
     */
    public function deletePhoto(int $id): bool
    {
        $this->requireAuth();
        return $this->crudHelper->delete($id);
    }
}

