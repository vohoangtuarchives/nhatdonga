<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Repository\TagsRepository;
use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * TagsAdminController - Handles tags admin requests
 */
class TagsAdminController extends BaseAdminController
{
    private TagsRepository $tagsRepo;
    private string $type;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper,
        string $type = 'tags'
    ) {
        parent::__construct($db, $cache, $func, $config, $authHelper, $permissionHelper);

        $this->type = $type;
        $lang = $_SESSION['lang'] ?? 'vi';
        $sluglang = 'slugvi';

        $this->tagsRepo = new TagsRepository($db, $cache, $lang, $sluglang);
        
        // Initialize CRUD helper for tags
        $this->crudHelper = new \Tuezy\Admin\AdminCRUDHelper(
            $db,
            $func,
            'tags',
            $type,
            $config['tags'][$type] ?? []
        );
    }

    /**
     * List tags
     * 
     * @param array $filters Filters
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function man(array $filters = [], int $page = 1, int $perPage = 20): array
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
        $url = $this->urlHelper->getUrl('tags', 'man', $this->type);
        $paging = $this->func->pagination($result['total'], $perPage, $page, $url);

        return [
            'items' => $result['items'],
            'total' => $result['total'],
            'paging' => $paging,
            'type' => $this->type,
        ];
    }

    /**
     * Get tag by ID
     * 
     * @param int $id Tag ID
     * @return array|null
     */
    public function getTag(int $id): ?array
    {
        $this->requireAuth();
        return $this->crudHelper->getItem($id);
    }

    /**
     * Save tag
     * 
     * @param array $data Tag data
     * @param int|null $id Tag ID (null for new)
     * @return bool Success
     */
    public function save(array $data, ?int $id = null): bool
    {
        $this->requireAuth();
        return $this->crudHelper->save($data, $id);
    }

    /**
     * Delete tag
     * 
     * @param int $id Tag ID
     * @return bool Success
     */
    public function delete(int $id): bool
    {
        $this->requireAuth();
        return $this->crudHelper->delete($id);
    }
}

