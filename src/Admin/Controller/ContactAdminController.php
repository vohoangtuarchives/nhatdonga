<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Repository\ContactRepository;
use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * ContactAdminController - Handles contact admin requests
 */
class ContactAdminController extends BaseAdminController
{
    private ContactRepository $contactRepo;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper
    ) {
        parent::__construct($db, $cache, $func, $config, $authHelper, $permissionHelper);

        $this->contactRepo = new ContactRepository($db, $cache);
        
        // Initialize CRUD helper for contacts
        $this->crudHelper = new \Tuezy\Admin\AdminCRUDHelper(
            $db,
            $func,
            'contact',
            '', // Contact doesn't have type
            []
        );
    }

    /**
     * List contacts
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
                'clause' => '(fullname LIKE ? OR phone LIKE ? OR email LIKE ? OR subject LIKE ?)',
                'params' => [
                    "%{$filters['keyword']}%",
                    "%{$filters['keyword']}%",
                    "%{$filters['keyword']}%",
                    "%{$filters['keyword']}%"
                ]
            ];
        }

        // Contact table doesn't have type, so we need custom query
        $whereClause = '';
        $params = [];
        if (!empty($where)) {
            $whereClause = 'WHERE ' . $where[0]['clause'];
            $params = $where[0]['params'];
        }

        $startpoint = ($page * $perPage) - $perPage;
        $items = $this->db->rawQuery(
            "SELECT * FROM #_contact $whereClause ORDER BY id DESC LIMIT $startpoint, $perPage",
            $params
        );

        $total = $this->db->rawQueryOne(
            "SELECT COUNT(*) as total FROM #_contact $whereClause",
            $params
        );

        // Build URL for pagination
        $this->urlHelper->reset();
        if (!empty($filters['keyword'])) {
            $this->urlHelper->addParam('keyword', $filters['keyword']);
        }
        $url = $this->urlHelper->getUrl('contact', 'man');
        $paging = $this->func->pagination($total['total'], $perPage, $page, $url);

        return [
            'items' => $items,
            'total' => $total['total'],
            'paging' => $paging,
        ];
    }

    /**
     * Get contact by ID
     * 
     * @param int $id Contact ID
     * @return array|null
     */
    public function getContact(int $id): ?array
    {
        $this->requireAuth();
        return $this->contactRepo->getById($id);
    }

    /**
     * Delete contact
     * 
     * @param int $id Contact ID
     * @return bool Success
     */
    public function delete(int $id): bool
    {
        $this->requireAuth();
        return $this->contactRepo->delete($id);
    }
}

