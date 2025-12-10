<?php

namespace Tuezy\Domain\Catalog;

class Product
{
    public function __construct(
        public int $id,
        public string $type,
        public string $name,
        public string $slug,
        public ?string $photo = null,
        public ?float $salePrice = null,
        public ?float $regularPrice = null,
        public ?int $view = null
    ) {}
}

