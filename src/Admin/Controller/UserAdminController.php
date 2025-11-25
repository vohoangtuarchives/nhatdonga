<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Repository\UserRepository;
use Tuezy\Service\UserService;
use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * UserAdminController - Handles user admin requests
 */
class UserAdminController extends BaseAdminController
{
    private UserService $userService;
    private UserRepository $userRepo;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper,
        string $loginAdmin
    ) {
        parent::__construct($db, $cache, $func, $config, $authHelper, $permissionHelper);

        $this->userRepo = new UserRepository($db, $cache);
        $this->userService = new UserService($this->userRepo, $db);
    }

    /**
     * List admin users
     * 
     * @param array $filters Filters
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function manAdmin(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $this->requireAuth();
        $this->requirePermission('user.manage_admin');

        // Build WHERE conditions
        $where = ['role = ?'];
        $params = ['admin'];

        if (!empty($filters['keyword'])) {
            $where[] = '(username LIKE ? OR email LIKE ? OR fullname LIKE ?)';
            $params[] = "%{$filters['keyword']}%";
            $params[] = "%{$filters['keyword']}%";
            $params[] = "%{$filters['keyword']}%";
        }

        $whereClause = implode(' AND ', $where);
        $startpoint = ($page * $perPage) - $perPage;

        $items = $this->db->rawQuery(
            "SELECT * FROM #_user WHERE $whereClause ORDER BY id DESC LIMIT $startpoint, $perPage",
            $params
        );

        $total = $this->db->rawQueryOne(
            "SELECT COUNT(*) as total FROM #_user WHERE $whereClause",
            $params
        );

        // Build URL for pagination
        $this->urlHelper->reset();
        if (!empty($filters['keyword'])) {
            $this->urlHelper->addParam('keyword', $filters['keyword']);
        }
        $url = $this->urlHelper->getUrl('user', 'man_admin');
        $paging = $this->func->pagination($total['total'], $perPage, $page, $url);

        return [
            'items' => $items,
            'total' => $total['total'],
            'paging' => $paging,
        ];
    }

    /**
     * List member users
     * 
     * @param array $filters Filters
     * @param int $page Current page
     * @param int $perPage Items per page
     * @return array View data
     */
    public function manMember(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $this->requireAuth();
        $this->requirePermission('user.manage_member');

        // Build WHERE conditions
        $where = ['role = ?'];
        $params = ['member'];

        if (!empty($filters['keyword'])) {
            $where[] = '(username LIKE ? OR email LIKE ? OR fullname LIKE ?)';
            $params[] = "%{$filters['keyword']}%";
            $params[] = "%{$filters['keyword']}%";
            $params[] = "%{$filters['keyword']}%";
        }

        $whereClause = implode(' AND ', $where);
        $startpoint = ($page * $perPage) - $perPage;

        $items = $this->db->rawQuery(
            "SELECT * FROM #_user WHERE $whereClause ORDER BY id DESC LIMIT $startpoint, $perPage",
            $params
        );

        $total = $this->db->rawQueryOne(
            "SELECT COUNT(*) as total FROM #_user WHERE $whereClause",
            $params
        );

        // Build URL for pagination
        $this->urlHelper->reset();
        if (!empty($filters['keyword'])) {
            $this->urlHelper->addParam('keyword', $filters['keyword']);
        }
        $url = $this->urlHelper->getUrl('user', 'man_member');
        $paging = $this->func->pagination($total['total'], $perPage, $page, $url);

        return [
            'items' => $items,
            'total' => $total['total'],
            'paging' => $paging,
        ];
    }

    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|null
     */
    public function getUser(int $id): ?array
    {
        $this->requireAuth();

        return $this->userRepo->getById($id);
    }

    /**
     * Save user (admin or member)
     * 
     * @param array $data User data
     * @param int|null $id User ID (null for new)
     * @param string $role User role (admin or member)
     * @return bool Success
     */
    public function save(array $data, ?int $id = null, string $role = 'member'): bool
    {
        $this->requireAuth();
        
        if ($role === 'admin') {
            $this->requirePermission('user.manage_admin');
        } else {
            $this->requirePermission('user.manage_member');
        }

        // Sanitize data
        $data = SecurityHelper::sanitizeArray($data);
        $data['role'] = $role;

        if ($id) {
            // Update
            $this->db->where('id', $id);
            return $this->db->update('user', $data);
        } else {
            // Insert
            if (!isset($data['date_created'])) {
                $data['date_created'] = time();
            }
            return $this->db->insert('user', $data);
        }
    }

    /**
     * Delete user
     * 
     * @param int $id User ID
     * @param string $role User role
     * @return bool Success
     */
    public function delete(int $id, string $role = 'member'): bool
    {
        $this->requireAuth();
        
        if ($role === 'admin') {
            $this->requirePermission('user.manage_admin');
        } else {
            $this->requirePermission('user.manage_member');
        }

        $this->db->where('id', $id);
        $this->db->where('role', $role);
        return $this->db->delete('user');
    }
}

