<?php

namespace Tuezy\Admin\Controller;

use Tuezy\Repository\OrderRepository;
use Tuezy\Service\OrderService;
use Tuezy\SecurityHelper;
use Tuezy\Admin\AdminAuthHelper;
use Tuezy\Admin\AdminPermissionHelper;

/**
 * OrderAdminController - Handles order admin requests
 */
class OrderAdminController extends BaseAdminController
{
    private OrderService $orderService;
    private OrderRepository $orderRepo;

    public function __construct(
        $db,
        $cache,
        $func,
        array $config,
        AdminAuthHelper $authHelper,
        AdminPermissionHelper $permissionHelper
    ) {
        parent::__construct($db, $cache, $func, $config, $authHelper, $permissionHelper);

        $this->orderRepo = new OrderRepository($db, $cache);
        $this->orderService = new OrderService($this->orderRepo, $db);
    }

    /**
     * List orders
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
        $params = [];

        if (!empty($filters['order_status'])) {
            $where[] = 'order_status = ?';
            $params[] = (int)$filters['order_status'];
        }

        if (!empty($filters['order_payment'])) {
            $where[] = 'order_payment = ?';
            $params[] = (int)$filters['order_payment'];
        }

        if (!empty($filters['keyword'])) {
            $where[] = '(code LIKE ? OR fullname LIKE ? OR phone LIKE ? OR email LIKE ?)';
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $startpoint = ($page * $perPage) - $perPage;

        $items = $this->db->rawQuery(
            "SELECT * FROM #_order $whereClause ORDER BY id DESC LIMIT $startpoint, $perPage",
            $params
        );

        $total = $this->db->rawQueryOne(
            "SELECT COUNT(*) as total FROM #_order $whereClause",
            $params
        );

        // Build URL for pagination
        $this->urlHelper->reset();
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $this->urlHelper->addParam($key, $value);
            }
        }
        $url = $this->urlHelper->getUrl('order', 'man');
        $paging = $this->func->pagination($total['total'], $perPage, $page, $url);

        return [
            'items' => $items,
            'total' => $total['total'],
            'paging' => $paging,
        ];
    }

    /**
     * Get order detail
     * 
     * @param int $id Order ID
     * @return array|null
     */
    public function detail(int $id): ?array
    {
        $this->requireAuth();

        $order = $this->orderRepo->getById($id);
        
        if (!$order) {
            return null;
        }

        // Get order items
        $orderItems = $this->orderRepo->getOrderItems($id);

        return [
            'order' => $order,
            'items' => $orderItems,
        ];
    }

    /**
     * Update order status
     * 
     * @param int $id Order ID
     * @param int $status Status value
     * @return bool Success
     */
    public function updateStatus(int $id, int $status): bool
    {
        $this->requireAuth();

        $this->db->where('id', $id);
        return $this->db->update('order', ['order_status' => $status]);
    }

    /**
     * Update order payment status
     * 
     * @param int $id Order ID
     * @param int $payment Payment status
     * @return bool Success
     */
    public function updatePayment(int $id, int $payment): bool
    {
        $this->requireAuth();

        $this->db->where('id', $id);
        return $this->db->update('order', ['order_payment' => $payment]);
    }

    /**
     * Delete order
     * 
     * @param int $id Order ID
     * @return bool Success
     */
    public function delete(int $id): bool
    {
        $this->requireAuth();

        // Delete order items first
        $this->db->where('id_order', $id);
        $this->db->delete('order_item');

        // Delete order
        $this->db->where('id', $id);
        return $this->db->delete('order');
    }
}

