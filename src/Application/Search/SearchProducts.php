<?php

namespace Tuezy\Application\Search;

use Tuezy\Domain\Catalog\ProductRepository;
use Tuezy\Application\Catalog\DTO\ProductListDTO;

class SearchProducts
{
    public function __construct(private ProductRepository $repo) {}
    public function execute(string $type, string $keyword, int $page = 1, int $perPage = 12): array
    {
        $filters = ['keyword' => $keyword];
        $page = max($page, 1); $perPage = max($perPage, 1); $start = ($page - 1) * $perPage;
        $items = $this->repo->getProducts($type, $filters, $start, $perPage);
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

