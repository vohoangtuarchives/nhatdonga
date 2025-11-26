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

    public function getProductDetail(int $id, ?string $type = null): ?array
    {
        $type = $this->resolveType($type);

        return $this->d->rawQueryOne(
            "select type, id, name{$this->lang}, slugvi, slugen, desc{$this->lang}, content{$this->lang},
                    code, view, id_brand, id_list, id_cat, id_item, id_sub, photo, options, discount,
                    sale_price, regular_price, status, date_created
             from #_product
             where id = ? and type = ? and find_in_set('hienthi',status)
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

    public function getProducts(?string $type = null, array $filters = [], int $start = 0, int $perPage = 12): array
    {
        $type = $this->resolveType($type);
        [$where, $params] = $this->buildFilterClause($type, $filters);

        $params[] = $start;
        $params[] = $perPage;

        return $this->d->rawQuery(
            "select id, name{$this->lang}, slugvi, slugen, photo, sale_price, regular_price, discount,
                    id_list, id_cat, id_item, id_sub, type, date_created
             from #_product
             where {$where}
             order by numb,id desc
             limit ?, ?",
            $params
        );
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
        $where = ["type = ?", "find_in_set('hienthi',status)"];
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

        if (!empty($filters['keyword'])) {
            $keyword = '%' . $filters['keyword'] . '%';
            $where[] = "(name{$this->lang} like ? or slugvi like ? or slugen like ?)";
            $params[] = $keyword;
            $params[] = $keyword;
            $params[] = $keyword;
        }

        return [implode(' and ', $where), $params];
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
}

