<?php
namespace Tuezy\Repository;

/**
 * ProductRepository - Data access layer chuẩn hóa cho module sản phẩm.
 */
class ProductRepository
{
    private \PDODb $d;
    private ?\Cache $cache;
    private string $lang;
    private string $sluglang;
    private string $defaultType;

    public function __construct(
        \PDODb $d,
        ?\Cache $cache,
        string $lang,
        string $sluglang = 'slugvi',
        string $defaultType = 'san-pham'
    ) {
        $this->d = $d;
        $this->cache = $cache;
        $this->lang = $lang;
        $this->sluglang = $sluglang;
        $this->defaultType = $defaultType;
    }

    private function resolveType(?string $type = null): string
    {
        return $type ?: $this->defaultType;
    }

    public function getProductDetail(int $id, ?string $type = null, bool $activeOnly = true): ?array
    {
        $type = $this->resolveType($type);

        $statusClause = $activeOnly ? " and find_in_set('hienthi',status)" : "";

        return $this->d->rawQueryOne(
            "select type, id, name{$this->lang}, slugvi, slugen, desc{$this->lang}, content{$this->lang},
                    code, view, id_brand, id_list, id_cat, id_item, id_sub, photo, options, discount,
                    sale_price, regular_price, status, date_created, status
             from #_product
             where id = ? and type = ?{$statusClause}
             limit 0,1",
            [$id, $type]
        );
    }

    public function updateProductView(int $id, int $currentView): void
    {
        $this->d->where('id', $id);
        $this->d->update('product', ['view' => $currentView + 1]);
    }

    public function getProductColors(int $productId, ?string $type = null): array
    {
        $colorIds = $this->pluckIds(
            $this->d->rawQuery(
                "select id_color from #_product_sale where id_parent = ?",
                [$productId]
            ),
            'id_color'
        );

        if (empty($colorIds)) {
            return [];
        }

        $type = $this->resolveType($type);
        $placeholders = $this->buildInClause(count($colorIds));

        return $this->d->rawQuery(
            "select type_show, photo, color, id
             from #_color
             where type = ? and id in ({$placeholders}) and find_in_set('hienthi',status)
             order by numb,id desc",
            array_merge([$type], $colorIds)
        );
    }

    public function getProductSizes(int $productId, ?string $type = null): array
    {
        $sizeIds = $this->pluckIds(
            $this->d->rawQuery(
                "select id_size from #_product_sale where id_parent = ?",
                [$productId]
            ),
            'id_size'
        );

        if (empty($sizeIds)) {
            return [];
        }

        $type = $this->resolveType($type);
        $placeholders = $this->buildInClause(count($sizeIds));

        return $this->d->rawQuery(
            "select id, name{$this->lang}
             from #_size
             where type = ? and id in ({$placeholders}) and find_in_set('hienthi',status)
             order by numb,id desc",
            array_merge([$type], $sizeIds)
        );
    }

    public function getProductGallery(int $productId, ?string $type = null): array
    {
        $type = $this->resolveType($type);

        return $this->d->rawQuery(
            "select id, photo, id_parent, id_color
             from #_gallery
             where id_parent = ? and com = 'product' and type = ? and kind = 'man' and val = ?
             and find_in_set('hienthi',status)
             order by numb,id desc",
            [$productId, $type, $type]
        );
    }

    public function getRelatedProducts(int $productId, ?int $listId, ?string $type = null, int $limit = 8): array
    {
        $type = $this->resolveType($type);
        $where = ["id != ?", "type = ?", "find_in_set('hienthi',status)"];
        $params = [$productId, $type];

        if ($listId) {
            $where[] = "id_list = ?";
            $params[] = $listId;
        }

        $params[] = $limit;

        return $this->d->rawQuery(
            "select id, name{$this->lang}, slugvi, slugen, photo, sale_price, regular_price, discount
             from #_product
             where " . implode(' and ', $where) . "
             order by numb,id desc
             limit 0, ?",
            $params
        );
    }

    public function getProducts(?string $type = null, array $filters = [], int $start = 0, int $perPage = 12, string $sortBy = 'default', string $sortOrder = 'desc'): array
    {
        $type = $this->resolveType($type);
        [$where, $params] = $this->buildFilterClause($type, $filters);

        // Build ORDER BY clause
        $orderBy = $this->buildOrderBy($sortBy, $sortOrder);

        $params[] = $start;
        $params[] = $perPage;

        return $this->d->rawQuery(
            "select id, name{$this->lang}, slugvi, slugen, photo, sale_price, regular_price, discount,
                    id_list, id_cat, id_item, id_sub, type, date_created, view, id_brand, numb, status
             from #_product
             where {$where}
             {$orderBy}
             limit ?, ?",
            $params
        );
    }

    private function buildOrderBy(string $sortBy, string $sortOrder): string
    {
        $order = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        switch ($sortBy) {
            case 'price_asc':
                return "ORDER BY (CASE WHEN sale_price > 0 THEN sale_price ELSE regular_price END) ASC, numb ASC, id DESC";
            case 'price_desc':
                return "ORDER BY (CASE WHEN sale_price > 0 THEN sale_price ELSE regular_price END) DESC, numb ASC, id DESC";
            case 'name_asc':
                return "ORDER BY name{$this->lang} ASC, numb ASC, id DESC";
            case 'name_desc':
                return "ORDER BY name{$this->lang} DESC, numb ASC, id DESC";
            case 'newest':
                return "ORDER BY date_created DESC, numb ASC, id DESC";
            case 'oldest':
                return "ORDER BY date_created ASC, numb ASC, id DESC";
            case 'popular':
                return "ORDER BY view DESC, numb ASC, id DESC";
            default:
                return "ORDER BY numb ASC, id DESC";
        }
    }

    public function countProducts(?string $type = null, array $filters = []): int
    {
        $type = $this->resolveType($type);
        [$where, $params] = $this->buildFilterClause($type, $filters);

        $result = $this->d->rawQueryOne(
            "select count(id) as total from #_product where {$where}",
            $params
        );

        return (int)($result['total'] ?? 0);
    }

    private function buildFilterClause(string $type, array $filters): array
    {
        // KHÔNG có điều kiện status mặc định - hiển thị tất cả kể cả status rỗng
        $where = ["type = ?"];
        $params = [$type];

        foreach (['id_list', 'id_cat', 'id_item', 'id_sub', 'id_brand'] as $field) {
            if (!empty($filters[$field])) {
                $where[] = "{$field} = ?";
                $params[] = (int)$filters[$field];
            }
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $statuses = (array)$filters['status'];
            $statusClauses = [];
            foreach ($statuses as $status) {
                $status = trim((string)$status);
                if ($status === '') {
                    continue;
                }
                $statusClauses[] = "find_in_set(?,status)";
                $params[] = $status;
            }
            if ($statusClauses) {
                $where[] = '(' . implode(' or ', $statusClauses) . ')';
            }
        }

        // Filter by discount (khuyến mãi)
        if (!empty($filters['has_discount']) && $filters['has_discount'] == true) {
            $where[] = "discount > 0";
        }

        // Filter by price range
        if (!empty($filters['price_min'])) {
            $priceMin = (float)$filters['price_min'];
            $where[] = "(sale_price >= ? OR (sale_price = 0 AND regular_price >= ?))";
            $params[] = $priceMin;
            $params[] = $priceMin;
        }
        if (!empty($filters['price_max'])) {
            $priceMax = (float)$filters['price_max'];
            $where[] = "(sale_price <= ? OR (sale_price = 0 AND regular_price <= ?))";
            $params[] = $priceMax;
            $params[] = $priceMax;
        }

        if (!empty($filters['keyword'])) {
            $keyword = '%' . $filters['keyword'] . '%';
            $where[] = "(name{$this->lang} like ? or slugvi like ? or slugen like ?)";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        return [implode(' and ', $where), $params];
    }

    /**
     * Get all brands for a product type
     * 
     * @param string $type Product type
     * @return array
     */
    public function getBrands(string $type): array
    {
        return $this->d->rawQuery(
            "SELECT DISTINCT pb.id, pb.name{$this->lang}, pb.slug{$this->lang}, pb.numb
             FROM #_product_brand pb
             INNER JOIN #_product p ON p.id_brand = pb.id
             WHERE pb.type = ? AND p.type = ? 
             AND find_in_set('hienthi', pb.status) 
             AND find_in_set('hienthi', p.status)
             ORDER BY pb.numb, pb.id DESC",
            [$type, $type]
        ) ?: [];
    }

    private function pluckIds(array $rows, string $key): array
    {
        if (empty($rows)) {
            return [];
        }

        $ids = array_filter(array_map('intval', array_column($rows, $key)));
        return array_values(array_unique($ids));
    }

    private function buildInClause(int $count): string
    {
        return implode(',', array_fill(0, $count, '?'));
    }

    /**
     * Lấy sản phẩm nổi bật (featured products)
     * 
     * @param int $limit Số lượng sản phẩm
     * @param string|null $type Loại sản phẩm
     * @return array
     */
    public function getFeaturedProducts(int $limit = 12, ?string $type = null): array
    {
        $type = $this->resolveType($type);
        
        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, slugvi, slugen, photo, regular_price, sale_price, discount 
             FROM #_product 
             WHERE type = ? AND find_in_set('hienthi',status) AND find_in_set('noibat',status) 
             ORDER BY numb, id DESC 
             LIMIT 0, ?",
            [$type, $limit]
        ) ?: [];
    }

    /**
     * Lấy sản phẩm theo danh mục
     * 
     * @param int $categoryId ID danh mục
     * @param int $limit Số lượng sản phẩm
     * @param string|null $type Loại sản phẩm
     * @return array
     */
    public function getProductsByCategory(int $categoryId, int $limit = 8, ?string $type = null): array
    {
        $type = $this->resolveType($type);
        
        return $this->d->rawQuery(
            "SELECT id, name{$this->lang}, slugvi, slugen, photo, regular_price, sale_price, discount 
             FROM #_product 
             WHERE type = ? AND find_in_set('hienthi',status) AND id_list = ? 
             ORDER BY numb, id DESC 
             LIMIT 0, ?",
            [$type, $categoryId, $limit]
        ) ?: [];
    }
}

