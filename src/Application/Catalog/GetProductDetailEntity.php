<?php

namespace Tuezy\Application\Catalog;

use Tuezy\Repository\ProductRepository;

class GetProductDetailEntity
{
    public function __construct(private ProductRepository $repo) {}

    public function execute(int $id, string $type, bool $activeOnly = true)
    {
        return $this->repo->getProductDetailEntity($id, $type, $activeOnly);
    }
}

