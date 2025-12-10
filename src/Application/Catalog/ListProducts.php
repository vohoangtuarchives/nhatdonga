<?php

namespace Tuezy\Application\Catalog;

use Tuezy\Repository\ProductRepository;
use Tuezy\Application\Catalog\DTO\ProductListDTO;

class ListProducts
{
    public function __construct(private ProductRepository $products) {}

    public function execute(string $type, array $filters, int $page, int $perPage, string $sortBy = 'default', string $sortOrder = 'desc', bool $activeOnly = true): array
    {
        $page = max($page, 1);
        $perPage = max($perPage, 1);
        $start = ($page - 1) * $perPage;

        if ($activeOnly && empty($filters['status'])) {
            $filters['status'] = 'hienthi';
        }

        $items = $this->products->getProducts($type, $filters, $start, $perPage, $sortBy, $sortOrder);
        $total = $this->products->countProducts($type, $filters);

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
