<?php

namespace Tuezy\Service;

use Tuezy\Repository\OrderRepository;

/**
 * OrderService - Business logic layer for orders
 */
class OrderService
{
    public function __construct(
        private OrderRepository $orders,
        private \PDODb $db
    ) {
    }

    /**
     * Get order detail with related data
     * 
     * @param int $id Order ID
     * @return array|null
     */
    public function getDetailContext(int $id): ?array
    {
        $order = $this->orders->getById($id);
        
        if (!$order) {
            return null;
        }

        // Get order details (items)
        $orderDetails = $this->db->rawQuery(
            "SELECT * FROM #_order_detail WHERE id_parent = ? ORDER BY id ASC",
            [$id]
        );

        return [
            'order' => $order,
            'details' => $orderDetails ?? [],
        ];
    }

    /**
     * Get order listing with filters and pagination
     * 
     * @param array $filters Filters
     * @param int $page Page number
     * @param int $perPage Items per page
     * @return array
     */
    public function getListing(array $filters, int $page, int $perPage): array
    {
        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $start = ($page - 1) * $perPage;

        $items = $this->orders->getOrders($filters, $start, $perPage);
        $total = $this->orders->countOrders($filters);

        return [
            'items' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'page' => $page,
            'start' => $start,
        ];
    }

    /**
     * Create order
     * 
     * @param array $data Order data
     * @return int|false Order ID on success, false on failure
     */
    public function createOrder(array $data): int|false
    {
        if (!isset($data['date_created'])) {
            $data['date_created'] = time();
        }
        
        if (!isset($data['code'])) {
            $order = $this->orders->getByCode($this->generateOrderCode());
            while ($order) {
                $order = $this->orders->getByCode($this->generateOrderCode());
            }
            $data['code'] = $this->generateOrderCode();
        }

        if (!$this->orders->create($data)) {
            return false;
        }

        return $this->db->getLastInsertId();
    }

    /**
     * Update order status
     * 
     * @param int $id Order ID
     * @param int $status Status value
     * @return bool
     */
    public function updateOrderStatus(int $id, int $status): bool
    {
        return $this->orders->updateStatus($id, (string)$status);
    }

    /**
     * Cancel order
     * 
     * @param int $id Order ID
     * @param string $reason Cancel reason
     * @return bool
     */
    public function cancelOrder(int $id, string $reason = ''): bool
    {
        $data = [
            'order_status' => 'huy',
        ];
        
        if ($reason) {
            $data['note'] = $reason;
        }

        return $this->orders->update($id, $data);
    }

    /**
     * Get orders by status
     * 
     * @param string $status Order status
     * @param int $start Start offset
     * @param int $limit Limit results
     * @return array
     */
    public function getOrdersByStatus(string $status, int $start = 0, int $limit = 0): array
    {
        return $this->orders->getOrdersByStatus($status, $start, $limit);
    }

    /**
     * Get order statistics
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'total_new' => count($this->getOrdersByStatus('dathang', 0, 0)),
            'total_confirm' => count($this->getOrdersByStatus('xacnhan', 0, 0)),
            'total_delivered' => count($this->getOrdersByStatus('giaohang', 0, 0)),
            'total_canceled' => count($this->getOrdersByStatus('huy', 0, 0)),
            'min_price' => $this->orders->getMinTotalPrice(),
            'max_price' => $this->orders->getMaxTotalPrice(),
        ];
    }

    /**
     * Generate unique order code
     * 
     * @return string
     */
    private function generateOrderCode(): string
    {
        return 'DH' . date('Ymd') . rand(1000, 9999);
    }

    /**
     * Save order details (items)
     * 
     * @param int $orderId Order ID
     * @param array $items Order items
     * @return bool
     */
    public function saveOrderDetails(int $orderId, array $items): bool
    {
        if (!$orderId || empty($items)) {
            return false;
        }

        // Delete old details
        $this->db->rawQuery(
            "DELETE FROM #_order_detail WHERE id_parent = ?",
            [$orderId]
        );

        // Insert new details
        foreach ($items as $item) {
            $data = [
                'id_parent' => $orderId,
                'id_product' => (int)($item['id_product'] ?? 0),
                'quantity' => (int)($item['quantity'] ?? 0),
                'price' => (float)($item['price'] ?? 0),
                'total' => (float)($item['total'] ?? 0),
            ];

            if (isset($item['name'])) {
                $data['name'] = $item['name'];
            }
            if (isset($item['photo'])) {
                $data['photo'] = $item['photo'];
            }
            if (isset($item['id_color'])) {
                $data['id_color'] = (int)$item['id_color'];
            }
            if (isset($item['id_size'])) {
                $data['id_size'] = (int)$item['id_size'];
            }

            $this->db->insert('order_detail', $data);
        }

        return true;
    }
}

