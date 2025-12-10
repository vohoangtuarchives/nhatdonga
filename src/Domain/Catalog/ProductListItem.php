<?php

namespace Tuezy\Domain\Catalog;

class ProductListItem
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public ?string $photo = null,
        public ?float $salePrice = null,
        public ?float $regularPrice = null,
        public ?int $discount = null
    ) {}
}
