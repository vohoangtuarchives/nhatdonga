<?php

namespace Tuezy\Application\Catalog;

use Tuezy\Domain\Catalog\ProductRepository;
use Tuezy\Application\Catalog\DTO\ProductListDTO;

class ListProductsByHierarchy
{
    public function __construct(private ProductRepository $repo) {}

    public function execute(string $type, string $level, int $id, int $page, int $perPage, string $sortBy = 'default', string $sortOrder = 'desc', bool $activeOnly = true): array
    {
        $filters = [];
        switch ($level) {
            case 'list':
                $filters['id_list'] = $id; break;
            case 'cat':
                $filters['id_cat'] = $id; break;
            case 'item':
                $filters['id_item'] = $id; break;
            case 'sub':
                $filters['id_sub'] = $id; break;
            case 'brand':
                $filters['id_brand'] = $id; break;
            default:
                // no-op
        }
        if ($activeOnly && empty($filters['status'])) {
            $filters['status'] = 'hienthi';
        }
        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $start = ($page - 1) * $perPage;
        $items = $this->repo->getProducts($type, $filters, $start, $perPage, $sortBy, $sortOrder);
        $total = $this->repo->countProducts($type, $filters);
        return [
            'items' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'page' => $page,
            'start' => $start,
            'dto' => new ProductListDTO($items, $total, $perPage, $page, $start),
        ];
    }
}

