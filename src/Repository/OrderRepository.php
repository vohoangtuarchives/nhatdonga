<?php

namespace Tuezy\Repository;

/**
 * OrderRepository - Data access layer for orders
 */
class OrderRepository
{
    private $d;
    private $cache;

    public function __construct($d, $cache)
    {
        $this->d = $d;
        $this->cache = $cache;
    }

    /**
     * Get order by ID
     * 
     * @param int $id Order ID
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_order WHERE id = ? LIMIT 0,1",
            [$id]
        );
    }

    /**
     * Get order by code
     * 
     * @param string $code Order code
     * @return array|null
     */
    public function getByCode(string $code): ?array
    {
        return $this->d->rawQueryOne(
            "SELECT * FROM #_order WHERE code = ? LIMIT 0,1",
            [$code]
        );
    }

    /**
     * Get all orders
     * 
     * @param array $filters Filters (status, keyword, date_from, date_to)
     * @param int $start Start offset
     * @param int $limit Limit results
     * @return array
     */
    public function getAll(array $filters = [], int $start = 0, int $limit = 20): array
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND find_in_set(?, status)";
            $params[] = $filters['status'];
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (code LIKE ? OR fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        if (!empty($filters['date_from'])) {
            $where .= " AND date_created >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where .= " AND date_created <= ?";
            $params[] = $filters['date_to'];
        }

        return $this->d->rawQuery(
            "SELECT * FROM #_order 
             WHERE {$where} 
             ORDER BY date_created DESC 
             LIMIT {$start}, {$limit}",
            $params
        );
    }

    /**
     * Count orders
     * 
     * @param array $filters Filters
     * @return int
     */
    public function count(array $filters = []): int
    {
        $where = "1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $where .= " AND find_in_set(?, status)";
            $params[] = $filters['status'];
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (code LIKE ? OR fullname LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        $result = $this->d->rawQueryOne(
            "SELECT COUNT(*) as total FROM #_order WHERE {$where}",
            $params
        );

        return (int)($result['total'] ?? 0);
    }

    /**
     * Create order
     * 
     * @param array $data Order data
     * @return bool
     */
    public function create(array $data): bool
    {
        if (!isset($data['date_created'])) {
            $data['date_created'] = time();
        }
        if (!isset($data['code'])) {
            $data['code'] = $this->generateOrderCode();
        }
        return $this->d->insert('order', $data);
    }

    /**
     * Update order
     * 
     * @param int $id Order ID
     * @param array $data Order data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->d->where('id', $id);
        return $this->d->update('order', $data);
    }

    /**
     * Delete order
     * 
     * @param int $id Order ID
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->d->where('id', $id);
        return $this->d->delete('order');
    }

    /**
     * Update order status
     * 
     * @param int $id Order ID
     * @param string $status Status value
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Generate unique order code
     * 
     * @return string
     */
    private function generateOrderCode(): string
    {
        do {
            $code = 'DH' . date('Ymd') . rand(1000, 9999);
            $exists = $this->getByCode($code);
        } while ($exists);

        return $code;
    }

    /**
     * Get orders with advanced filters
     * 
     * @param array $filters Filters (order_status, order_payment, order_date, range_price, city, district, ward, keyword)
     * @param int $start Start offset
     * @param int $limit Limit results
     * @return array
     */
    public function getOrders(array $filters = [], int $start = 0, int $limit = 20): array
    {
        $where = "id<>0";
        $params = [];

        if (!empty($filters['listid'])) {
            $where .= " AND id IN (" . $filters['listid'] . ")";
        }

        if (!empty($filters['order_status'])) {
            $where .= " AND order_status = ?";
            $params[] = (int)$filters['order_status'];
        }

        if (!empty($filters['order_payment'])) {
            $where .= " AND order_payment = ?";
            $params[] = (int)$filters['order_payment'];
        }

        if (!empty($filters['order_date'])) {
            $order_date = explode("-", $filters['order_date']);
            $date_from = trim($order_date[0] . ' 12:00:00 AM');
            $date_to = trim($order_date[1] . ' 11:59:59 PM');
            $date_from = strtotime(str_replace("/", "-", $date_from));
            $date_to = strtotime(str_replace("/", "-", $date_to));
            $where .= " AND date_created <= ? AND date_created >= ?";
            $params[] = $date_to;
            $params[] = $date_from;
        }

        if (!empty($filters['range_price'])) {
            $range_price = explode(";", $filters['range_price']);
            $price_from = trim($range_price[0]);
            $price_to = trim($range_price[1]);
            $where .= " AND total_price <= ? AND total_price >= ?";
            $params[] = (float)$price_to;
            $params[] = (float)$price_from;
        }

        if (!empty($filters['city'])) {
            $where .= " AND city = ?";
            $params[] = (int)$filters['city'];
        }

        if (!empty($filters['district'])) {
            $where .= " AND district = ?";
            $params[] = (int)$filters['district'];
        }

        if (!empty($filters['ward'])) {
            $where .= " AND ward = ?";
            $params[] = (int)$filters['ward'];
        }

        if (!empty($filters['keyword'])) {
            $where .= " AND (fullname LIKE ? OR email LIKE ? OR phone LIKE ? OR code LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        return $this->d->rawQuery(
            "SELECT * FROM #_order WHERE {$where} ORDER BY date_created DESC LIMIT {$start}, {$limit}",
            $params
        );
    }

    /**
     * Count orders with filters
     * 
     * @param array $filters Filters
     * @return int
     */
    public function countOrders(array $filters = []): int
    {
        $where = "id<>0";
        $params = [];

        // Same filter logic as getOrders
        if (!empty($filters['order_status'])) {
            $where .= " AND order_status = ?";
            $params[] = (int)$filters['order_status'];
        }
        if (!empty($filters['order_payment'])) {
            $where .= " AND order_payment = ?";
            $params[] = (int)$filters['order_payment'];
        }
        if (!empty($filters['order_date'])) {
            $order_date = explode("-", $filters['order_date']);
            $date_from = trim($order_date[0] . ' 12:00:00 AM');
            $date_to = trim($order_date[1] . ' 11:59:59 PM');
            $date_from = strtotime(str_replace("/", "-", $date_from));
            $date_to = strtotime(str_replace("/", "-", $date_to));
            $where .= " AND date_created <= ? AND date_created >= ?";
            $params[] = $date_to;
            $params[] = $date_from;
        }
        if (!empty($filters['range_price'])) {
            $range_price = explode(";", $filters['range_price']);
            $price_from = trim($range_price[0]);
            $price_to = trim($range_price[1]);
            $where .= " AND total_price <= ? AND total_price >= ?";
            $params[] = (float)$price_to;
            $params[] = (float)$price_from;
        }
        if (!empty($filters['city'])) {
            $where .= " AND city = ?";
            $params[] = (int)$filters['city'];
        }
        if (!empty($filters['district'])) {
            $where .= " AND district = ?";
            $params[] = (int)$filters['district'];
        }
        if (!empty($filters['ward'])) {
            $where .= " AND ward = ?";
            $params[] = (int)$filters['ward'];
        }
        if (!empty($filters['keyword'])) {
            $where .= " AND (fullname LIKE ? OR email LIKE ? OR phone LIKE ? OR code LIKE ?)";
            $keyword = "%{$filters['keyword']}%";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        $result = $this->d->rawQueryOne(
            "SELECT COUNT(*) as total FROM #_order WHERE {$where}",
            $params
        );

        return (int)($result['total'] ?? 0);
    }

    /**
     * Get min total price
     * 
     * @return float
     */
    public function getMinTotalPrice(): float
    {
        $result = $this->d->rawQueryOne("SELECT MIN(total_price) as min_price FROM #_order");
        return (float)($result['min_price'] ?? 0);
    }

    /**
     * Get max total price
     * 
     * @return float
     */
    public function getMaxTotalPrice(): float
    {
        $result = $this->d->rawQueryOne("SELECT MAX(total_price) as max_price FROM #_order");
        return (float)($result['max_price'] ?? 0);
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
        $where = "order_status = ?";
        $params = [$status];
        $limitSql = $limit > 0 ? " LIMIT {$start}, {$limit}" : "";
        
        return $this->d->rawQuery(
            "SELECT * FROM #_order WHERE {$where} ORDER BY date_created DESC {$limitSql}",
            $params
        );
    }
}

