<?php

namespace Tuezy\Application\Catalog\DTO;

class ProductListDTO
{
    public function __construct(
        public array $items,
        public int $total,
        public int $perPage,
        public int $page,
        public int $start
    ) {}
}

